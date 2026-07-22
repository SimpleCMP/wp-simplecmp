<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ThemeRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\TranslationOverrideRepository;

class ThemeDesignerPage
{
    public static function render(): void
    {
        global $wpdb;
        $themeRepo = new ThemeRepository($wpdb);
        $overrideRepo = new TranslationOverrideRepository($wpdb);

        $site = 'default';
        $saved = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'simplecmp_theme_save')) {
            $tokens = wp_json_encode([
                'preset' => sanitize_text_field($_POST['preset'] ?? 'default'),
                'brandColor' => sanitize_hex_color($_POST['brand_color'] ?? '#1a73e8'),
                'surfaceColor' => sanitize_hex_color($_POST['surface_color'] ?? '#ffffff'),
                'position' => sanitize_text_field($_POST['position'] ?? 'bottom-center'),
                'tone' => sanitize_text_field($_POST['tone'] ?? 'sie'),
            ], JSON_UNESCAPED_UNICODE);

            $themeRepo->upsert($site, $tokens);
            $saved = true;
        }

        $theme = $themeRepo->findBySite($site);
        $tokens = $theme ? json_decode($theme->tokens, true) : [];

        $preset = $tokens['preset'] ?? 'default';
        $brandColor = $tokens['brandColor'] ?? '#1a73e8';
        $surfaceColor = $tokens['surfaceColor'] ?? '#ffffff';
        $position = $tokens['position'] ?? 'bottom-center';
        $tone = $tokens['tone'] ?? 'sie';

        $presets = ['default' => 'Default', 'bootstrap5' => 'Bootstrap 5', 'tailwind' => 'Tailwind', 'bulma' => 'Bulma', 'pico' => 'Pico'];
        $positions = [
            'top-left' => __('Top Left', 'wp-simplecmp'),
            'top-center' => __('Top Center', 'wp-simplecmp'),
            'top-right' => __('Top Right', 'wp-simplecmp'),
            'center-left' => __('Center Left', 'wp-simplecmp'),
            'center-center' => __('Center', 'wp-simplecmp'),
            'center-right' => __('Center Right', 'wp-simplecmp'),
            'bottom-left' => __('Bottom Left', 'wp-simplecmp'),
            'bottom-center' => __('Bottom Center', 'wp-simplecmp'),
            'bottom-right' => __('Bottom Right', 'wp-simplecmp'),
        ];

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Theme Designer', 'wp-simplecmp') . '</h1>';

        if ($saved) {
            echo '<div class="notice notice-success"><p>' . esc_html__('Theme saved.', 'wp-simplecmp') . '</p></div>';
        }

        echo '<form method="post">';
        wp_nonce_field('simplecmp_theme_save');
        echo '<table class="form-table">';
        echo '<tr><th>' . esc_html__('CSS Framework Preset', 'wp-simplecmp') . '</th><td>';
        echo '<select name="preset">';
        foreach ($presets as $k => $label) {
            echo '<option value="' . esc_attr($k) . '"' . selected($preset, $k, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select></td></tr>';

        echo '<tr><th>' . esc_html__('Brand Color', 'wp-simplecmp') . '</th><td>';
        echo '<input type="color" name="brand_color" value="' . esc_attr($brandColor) . '">';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__('Surface Color', 'wp-simplecmp') . '</th><td>';
        echo '<input type="color" name="surface_color" value="' . esc_attr($surfaceColor) . '">';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__('Banner Position', 'wp-simplecmp') . '</th><td>';
        echo '<select name="position">';
        foreach ($positions as $k => $label) {
            echo '<option value="' . esc_attr($k) . '"' . selected($position, $k, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select></td></tr>';

        echo '<tr><th>' . esc_html__('Address Tone', 'wp-simplecmp') . '</th><td>';
        echo '<select name="tone">';
        echo '<option value="sie"' . selected($tone, 'sie', false) . '>Sie</option>';
        echo '<option value="du"' . selected($tone, 'du', false) . '>Du</option>';
        echo '</select></td></tr>';

        echo '</table>';
        submit_button(__('Save Theme', 'wp-simplecmp'));
        echo '</form>';

        // Reset button
        echo '<form method="post" action="' . esc_url(admin_url('admin.php?page=simplecmp-theme&action=reset')) . '">';
        wp_nonce_field('simplecmp_theme_reset');
        echo '<input type="hidden" name="action" value="reset">';
        echo '<p>' . esc_html__('Reset to default theme settings.', 'wp-simplecmp') . '</p>';
        submit_button(__('Reset to Defaults', 'wp-simplecmp'), 'delete');
        echo '</form>';

        if (isset($_GET['action']) && $_GET['action'] === 'reset' && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'simplecmp_theme_reset')) {
            $themeRepo->deleteBySite($site);
            echo '<div class="notice notice-info"><p>' . esc_html__('Theme reset to defaults.', 'wp-simplecmp') . '</p></div>';
            echo '<meta http-equiv="refresh" content="1;url=' . esc_url(admin_url('admin.php?page=simplecmp-theme')) . '">';
        }

        echo '</div>';
    }
}
