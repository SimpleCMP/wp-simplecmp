<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class EffectiveSettingsResolver
{
    /**
     * Settings keys that are editor-content (banner-facing, DSGVO-relevant).
     * These can be overridden via active_settings table.
     */
    public const EDITOR_CONTENT_KEYS = [
        'simplecmp_enabled',
        'simplecmp_storage_name',
        'simplecmp_privacy_policy_url',
        'simplecmp_imprint_url',
        'simplecmp_floating_trigger_label',
        'simplecmp_respect_gpc',
        'simplecmp_regime_default',
        'simplecmp_hide_decline_all',
        'simplecmp_universal_blocking_enabled',
        'simplecmp_universal_blocking_block_stylesheets',
        'simplecmp_universal_blocking_allowlist',
        'simplecmp_trackers',
    ];

    private array $resolved = [];

    public function __construct()
    {
    }

    /**
     * Resolve a setting value: DB-active > WP option (YAML-equivalent) > default.
     */
    public function get(string $key, $default = null)
    {
        // Check per-request cache
        $cacheKey = 'simplecmp_' . md5($key);
        if (isset($this->resolved[$cacheKey])) {
            return $this->resolved[$cacheKey];
        }

        // For multisite, get the site identifier
        $site = $this->getCurrentSite();

        // Editor-content keys: check active_settings table first
        if (in_array($key, self::EDITOR_CONTENT_KEYS, true)) {
            $activeValue = $this->getActiveSetting($site, $key);
            if ($activeValue !== null) {
                $this->resolved[$cacheKey] = $activeValue;
                return $activeValue;
            }
        }

        // Fall back to WordPress option
        $value = get_option($key, $default);
        $this->resolved[$cacheKey] = $value;
        return $value;
    }

    private function getActiveSetting(string $site, string $key)
    {
        global $wpdb;
        $repo = new \SimpleCMP\WpSimpleCmp\Database\Repositories\ActiveSettingsRepository($wpdb);
        $json = $repo->getActiveJson($site);

        if (!$json) return null;

        $active = json_decode($json, true);
        if (!is_array($active)) return null;

        // Convert dotted key to array access
        $parts = explode('_', $key);
        $value = $active;

        // Try the full key first
        if (array_key_exists($key, $active)) {
            return $active[$key];
        }

        return null;
    }

    private function getCurrentSite(): string
    {
        if (is_multisite()) {
            $blogId = get_current_blog_id();
            return 'site_' . $blogId;
        }

        return 'default';
    }

    /**
     * Get all current settings as a merged array.
     */
    public function all(): array
    {
        $result = [];

        $keys = array_merge(
            self::EDITOR_CONTENT_KEYS,
            [
                'simplecmp_region',
                'simplecmp_region_header',
                'simplecmp_service_db_url',
                'simplecmp_cms_bridge_url',
                'simplecmp_consent_log_url',
                'simplecmp_consent_log_rate_limit',
                'simplecmp_bridge_rate_limit',
                'simplecmp_service_db_rate_limit',
                'simplecmp_library_upstream_url',
                'simplecmp_library_upstream_daily_budget',
                'simplecmp_use_slim_bundle',
                'simplecmp_preload_bundle',
            ]
        );

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, false);
        }

        return $result;
    }
}
