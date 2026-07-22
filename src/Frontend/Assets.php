<?php

namespace SimpleCMP\WpSimpleCmp\Frontend;

use SimpleCMP\WpSimpleCmp\Service\EffectiveSettingsResolver;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ThemeRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\TranslationOverrideRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ManagedTrackerRepository;
use SimpleCMP\WpSimpleCmp\Tracker\TrackerRegistry;

class Assets
{
    public static function enqueue(): void
    {
        if (is_admin()) return;

        $resolver = new EffectiveSettingsResolver();

        if (!$resolver->get('simplecmp_enabled', false)) {
            return;
        }

        $initConfig = self::buildInitConfig($resolver);

        $bundleUrl = WP_SIMPLECMP_URL . 'assets/js/frontend/simplecmp.global.js';
        $useSlim = $resolver->get('simplecmp_use_slim_bundle', false);

        if ($useSlim && file_exists(WP_SIMPLECMP_DIR . 'assets/js/frontend/simplecmp.core.global.js')) {
            $bundleUrl = WP_SIMPLECMP_URL . 'assets/js/frontend/simplecmp.core.global.js';
        }

        // Preload bundle
        if ($resolver->get('simplecmp_preload_bundle', true)) {
            add_action('wp_head', function () use ($bundleUrl) {
                echo '<link rel="preload" as="script" href="' . esc_url($bundleUrl) . '">' . "\n";
            }, 1);
        }

        wp_enqueue_script(
            'simplecmp',
            $bundleUrl,
            [],
            WP_SIMPLECMP_VERSION,
            ['strategy' => 'defer']
        );

        wp_add_inline_script(
            'simplecmp',
            'SimpleCMP.init(' . wp_json_encode($initConfig, JSON_UNESCAPED_UNICODE) . ');',
            'after'
        );

        // Inject tracker scripts
        self::injectTrackers($resolver);
    }

    private static function buildInitConfig(EffectiveSettingsResolver $resolver): array
    {
        global $wpdb;
        $serviceRepo = new ServiceRepository($wpdb);
        $themeRepo = new ThemeRepository($wpdb);
        $overrideRepo = new TranslationOverrideRepository($wpdb);

        $site = 'default';
        $currentLang = substr(get_locale(), 0, 2);

        $config = [
            'enabled' => (bool) $resolver->get('simplecmp_enabled', false),
            'storageName' => $resolver->get('simplecmp_storage_name', 'simplecmp-default'),
            'privacyPolicyUrl' => $resolver->get('simplecmp_privacy_policy_url', ''),
            'imprintUrl' => $resolver->get('simplecmp_imprint_url', ''),
            'floatingTriggerLabel' => $resolver->get('simplecmp_floating_trigger_label', ''),
            'respectGPC' => (bool) $resolver->get('simplecmp_respect_gpc', false),
            'regimeDefault' => $resolver->get('simplecmp_regime_default', 'opt-in'),
            'hideDeclineAll' => (bool) $resolver->get('simplecmp_hide_decline_all', false),
        ];

        // Universal blocking config
        if ($resolver->get('simplecmp_universal_blocking_enabled', false)) {
            $config['universalBlocking'] = [
                'enabled' => true,
                'blockStylesheets' => (bool) $resolver->get('simplecmp_universal_blocking_block_stylesheets', false),
                'allowlist' => explode("\n", str_replace("\r", '', $resolver->get('simplecmp_universal_blocking_allowlist', ''))),
            ];
        }

        // CMS bridge config
        $bridgeUrl = $resolver->get('simplecmp_cms_bridge_url', '');
        if ($bridgeUrl) {
            $config['cmsBridgeUrl'] = $bridgeUrl;
        }

        // Consent log URL
        $consentLogUrl = $resolver->get('simplecmp_consent_log_url', '');
        if ($consentLogUrl) {
            $config['consentLogUrl'] = $consentLogUrl;
        }

        // Services array
        $services = $serviceRepo->findAll();
        $config['services'] = array_map(function ($s) {
            return [
                'service_id' => $s->service_id,
                'name' => $s->name,
                'purposes' => json_decode($s->purposes ?? '[]', true),
                'privacyPolicyUrl' => $s->privacy_policy_url,
                'description' => $s->description,
                'cookies' => json_decode($s->cookies ?? '[]', true),
                'origins' => json_decode($s->origins ?? '[]', true),
                'i18n' => json_decode($s->i18n ?? '{}', true),
                'placeholder' => [
                    'title' => $s->placeholder_title,
                    'description' => $s->placeholder_description,
                ],
                'placeholderTitle' => $s->placeholder_title,
                'placeholderDescription' => $s->placeholder_description,
            ];
        }, $services);

        // Theme tokens
        $theme = $themeRepo->findBySite($site);
        if ($theme && $theme->tokens) {
            $config['theme'] = json_decode($theme->tokens, true);
        }

        // Translation overrides
        $overrides = $overrideRepo->getOverrides($site);
        if ($overrides) {
            $config['translations'] = [
                $currentLang => json_decode($overrides, true),
            ];
        }

        return $config;
    }

    private static function injectTrackers(EffectiveSettingsResolver $resolver): void
    {
        global $wpdb;
        $trackerRepo = new ManagedTrackerRepository($wpdb);
        $registry = new TrackerRegistry();

        $site = 'default';
        $trackers = $trackerRepo->findBySite($site);

        foreach ($trackers as $tracker) {
            $provider = $registry->get($tracker->tracker_type);
            if (!$provider) continue;

            $config = json_decode($tracker->config ?? '{}', true) ?: [];

            if ($provider->wantsLoadGate()) {
                $loaderUrl = $provider->getLoaderUrl($config);
                $handle = 'simplecmp-tracker-' . $tracker->tracker_type . '-' . $tracker->id;
                $serviceId = $tracker->service_id;

                wp_enqueue_script(
                    $handle,
                    $loaderUrl,
                    [],
                    null,
                    ['strategy' => 'defer']
                );

                // Add consent-gating attribute
                add_filter('script_loader_tag', function ($tag, $scriptHandle) use ($handle, $serviceId) {
                    if ($scriptHandle === $handle) {
                        return str_replace('<script ', '<script data-simplecmp-name="' . esc_attr($serviceId) . '" data-simplecmp-src="' . esc_url($tag) . '" ', $tag);
                    }
                    return $tag;
                }, 10, 2);

                $bootstrap = $provider->getBootstrapInlineScript($config);
                if ($bootstrap) {
                    wp_add_inline_script($handle, $bootstrap, 'before');
                }
            }
        }
    }
}
