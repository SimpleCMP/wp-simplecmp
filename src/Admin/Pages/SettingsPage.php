<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Service\EffectiveSettingsResolver;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ActiveSettingsRepository;

class SettingsPage
{
    public static function render(): void
    {
        global $wpdb;
        $resolver = new EffectiveSettingsResolver();
        $repo = new ActiveSettingsRepository($wpdb);

        $site = 'default';
        $saved = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'simplecmp_settings_save')) {
            $active = [];

            $editorKeys = EffectiveSettingsResolver::EDITOR_CONTENT_KEYS;
            foreach ($editorKeys as $key) {
                $shortKey = str_replace('simplecmp_', '', $key);
                $value = $_POST[$shortKey] ?? null;
                if ($value !== null && $value !== '') {
                    $active[$key] = sanitize_text_field($value);
                }
            }

            $userId = get_current_user_id();
            $repo->upsert($site, wp_json_encode($active, JSON_UNESCAPED_UNICODE), $userId);
            $saved = true;
        }

        $activeRow = $repo->findBySite($site);
        $active = $activeRow ? json_decode($activeRow->active_json, true) : [];

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('SimpleCMP Settings', 'wp-simplecmp') . '</h1>';
        echo '<p class="description">' . esc_html__('Settings in this section are banner-content (DSGVO-relevant) and override YAML/deployment defaults when confirmed.', 'wp-simplecmp') . '</p>';

        if ($saved) {
            echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved.', 'wp-simplecmp') . '</p></div>';
        }

        echo '<form method="post">';
        wp_nonce_field('simplecmp_settings_save');
        echo '<table class="form-table">';

        self::field('enabled', __('Enable SimpleCMP', 'wp-simplecmp'), 'checkbox', $active);
        self::field('storage_name', __('Storage Name', 'wp-simplecmp'), 'text', $active, 'simplecmp-default');
        self::field('privacy_policy_url', __('Privacy Policy URL', 'wp-simplecmp'), 'url', $active);
        self::field('imprint_url', __('Imprint URL', 'wp-simplecmp'), 'url', $active);
        self::field('floating_trigger_label', __('Floating Trigger Label', 'wp-simplecmp'), 'text', $active);
        self::field('respect_gpc', __('Respect GPC Signal', 'wp-simplecmp'), 'checkbox', $active);
        self::field('regime_default', __('Default Consent Regime', 'wp-simplecmp'), 'select', $active, null, ['opt-in' => 'Opt-in (GDPR)', 'opt-out' => 'Opt-out (CCPA)', 'none' => 'None']);
        self::field('hide_decline_all', __('Hide "Decline All"', 'wp-simplecmp'), 'checkbox', $active);
        self::field('universal_blocking_enabled', __('Universal Blocking', 'wp-simplecmp'), 'checkbox', $active);
        self::field('universal_blocking_block_stylesheets', __('Block Third-Party Stylesheets', 'wp-simplecmp'), 'checkbox', $active);
        self::field('universal_blocking_allowlist', __('Universal Blocking Allowlist', 'wp-simplecmp'), 'textarea', $active, 'one host per line, e.g. cdn.example.com');

        echo '</table>';
        submit_button(__('Save Settings', 'wp-simplecmp'));
        echo '</form>';

        // Generate bridge secret
        echo '<h2>' . esc_html__('Bridge Secret', 'wp-simplecmp') . '</h2>';
        echo '<p class="description">' . esc_html__('HMAC secret for CMS-bridge webhook authentication.', 'wp-simplecmp') . '</p>';
        $secret = get_option('simplecmp_bridge_secret', '');
        if ($secret) {
            echo '<p><code>' . esc_html(substr($secret, 0, 8)) . '... (64 chars)</code></p>';
        } else {
            echo '<p style="color:red">' . esc_html__('No bridge secret configured. Webhook endpoints are unauthenticated.', 'wp-simplecmp') . '</p>';
        }
        echo '<p><a href="' . esc_url(wp_nonce_url(admin_url('admin.php?page=simplecmp-settings&action=generate_secret'), 'simplecmp_generate_secret')) . '" class="button">' . esc_html__('Generate Bridge Secret', 'wp-simplecmp') . '</a></p>';

        if (isset($_GET['action']) && $_GET['action'] === 'generate_secret' && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'simplecmp_generate_secret')) {
            $provider = new \SimpleCMP\WpSimpleCmp\Service\BridgeSecretProvider();
            $newSecret = $provider->generate();
            echo '<div class="notice notice-success"><p>' . esc_html__('Bridge secret generated successfully.', 'wp-simplecmp') . '</p></div>';
            echo '<p><code>' . esc_html(substr($newSecret, 0, 4) . '...' . substr($newSecret, -4)) . '</code></p>';
        }

        echo '</div>';
    }

    private static function field(string $key, string $label, string $type, array $active, string $default = '', ?array $options = null): void
    {
        $optionKey = 'simplecmp_' . $key;
        $value = $active[$optionKey] ?? get_option($optionKey, $default);

        echo '<tr><th><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td>';

        switch ($type) {
            case 'checkbox':
                echo '<input type="checkbox" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="1"' . checked($value, '1', false) . '>';
                break;
            case 'text':
            case 'url':
                echo '<input type="' . esc_attr($type) . '" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text">';
                break;
            case 'textarea':
                echo '<textarea name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" rows="4" class="large-text">' . esc_textarea($value) . '</textarea>';
                break;
            case 'select':
                echo '<select name="' . esc_attr($key) . '" id="' . esc_attr($key) . '">';
                foreach ($options as $optKey => $optLabel) {
                    echo '<option value="' . esc_attr($optKey) . '"' . selected($value, $optKey, false) . '>' . esc_html($optLabel) . '</option>';
                }
                echo '</select>';
                break;
        }

        echo '</td></tr>';
    }
}
