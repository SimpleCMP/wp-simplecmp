<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ManagedTrackerRepository;
use SimpleCMP\WpSimpleCmp\Tracker\TrackerRegistry;

class TrackerSetupPage
{
    public static function render(): void
    {
        global $wpdb;
        $repo = new ManagedTrackerRepository($wpdb);
        $registry = new TrackerRegistry();

        $site = 'default';
        $action = $_GET['action'] ?? 'list';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'simplecmp_tracker_save')) {
            $trackerType = sanitize_text_field($_POST['tracker_type'] ?? '');
            $configParts = [];

            switch ($trackerType) {
                case 'ga4':
                    $configParts['measurement_id'] = sanitize_text_field($_POST['measurement_id'] ?? '');
                    break;
                case 'gtm':
                    $configParts['container_id'] = sanitize_text_field($_POST['container_id'] ?? '');
                    break;
                case 'matomo':
                    $configParts['matomo_url'] = sanitize_text_field($_POST['matomo_url'] ?? '');
                    $configParts['site_id'] = sanitize_text_field($_POST['site_id'] ?? '');
                    break;
                case 'meta':
                    $configParts['pixel_id'] = sanitize_text_field($_POST['pixel_id'] ?? '');
                    break;
                case 'microsoft_uet':
                    $configParts['tag_id'] = sanitize_text_field($_POST['tag_id'] ?? '');
                    break;
            }

            $repo->insert([
                'site' => $site,
                'tracker_type' => $trackerType,
                'service_id' => sanitize_text_field($_POST['service_id'] ?? ''),
                'config' => wp_json_encode($configParts),
            ]);

            echo '<div class="notice notice-success"><p>' . esc_html__('Tracker saved.', 'wp-simplecmp') . '</p></div>';
        }

        if ($action === 'delete' && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'simplecmp_tracker_delete')) {
            $id = (int) ($_GET['id'] ?? 0);
            $repo->softDelete($id);
            echo '<div class="notice notice-success"><p>' . esc_html__('Tracker deleted.', 'wp-simplecmp') . '</p></div>';
        }

        $trackers = $repo->findBySite($site);

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">' . esc_html__('Tracker Setup', 'wp-simplecmp') . '</h1>';
        echo '<a href="' . esc_url(admin_url('admin.php?page=simplecmp-trackers&action=new')) . '" class="page-title-action">' . esc_html__('Add Tracker', 'wp-simplecmp') . '</a>';
        echo '<hr class="wp-header-end">';

        if ($action === 'new') {
            self::renderForm($registry);
        } else {
            self::renderList($trackers, $registry);
        }

        echo '</div>';
    }

    private static function renderList(array $trackers, TrackerRegistry $registry): void
    {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Type', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Service ID', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Config', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Actions', 'wp-simplecmp') . '</th>';
        echo '</tr></thead><tbody>';

        if (empty($trackers)) {
            echo '<tr><td colspan="4">' . esc_html__('No trackers configured.', 'wp-simplecmp') . '</td></tr>';
        }

        foreach ($trackers as $tracker) {
            $config = json_decode($tracker->config ?? '{}', true) ?: [];
            $provider = $registry->get($tracker->tracker_type);

            echo '<tr>';
            echo '<td>' . esc_html($tracker->tracker_type) . '</td>';
            echo '<td>' . esc_html($tracker->service_id) . '</td>';
            echo '<td><pre style="margin:0;font-size:11px">' . esc_html(json_encode($config, JSON_PRETTY_PRINT)) . '</pre></td>';
            echo '<td>';
            echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp-trackers&action=delete&id={$tracker->id}"), 'simplecmp_tracker_delete')) . '" class="button button-small">' . esc_html__('Delete', 'wp-simplecmp') . '</a>';
            echo '</td></tr>';
        }

        echo '</tbody></table>';
    }

    private static function renderForm(TrackerRegistry $registry): void
    {
        echo '<form method="post">';
        wp_nonce_field('simplecmp_tracker_save');
        echo '<table class="form-table">';

        echo '<tr><th>' . esc_html__('Tracker Type', 'wp-simplecmp') . '</th><td>';
        echo '<select name="tracker_type" id="simplecmp-tracker-type">';
        echo '<option value="ga4" selected>Google Analytics 4</option>';
        echo '<option value="gtm">Google Tag Manager</option>';
        echo '<option value="matomo">Matomo</option>';
        echo '<option value="meta">Meta Pixel</option>';
        echo '<option value="microsoft_uet">Microsoft UET</option>';
        echo '</select></td></tr>';

        echo '<tr><th>' . esc_html__('Service ID', 'wp-simplecmp') . '</th><td>';
        echo '<input type="text" name="service_id" value="" class="regular-text" placeholder="google-analytics">';
        echo '</td></tr>';

        echo '<tr class="simplecmp-field-set ga4"><th><label for="measurement_id">' . esc_html__('Measurement ID', 'wp-simplecmp') . '</label></th><td>';
        echo '<input type="text" name="measurement_id" id="measurement_id" value="" class="regular-text" placeholder="G-XXXXXXXXXX">';
        echo '</td></tr>';

        echo '<tr class="simplecmp-field-set gtm" style="display:none"><th><label for="container_id">' . esc_html__('Container ID', 'wp-simplecmp') . '</label></th><td>';
        echo '<input type="text" name="container_id" id="container_id" value="" class="regular-text" placeholder="GTM-XXXXXXX">';
        echo '</td></tr>';

        echo '<tr class="simplecmp-field-set matomo" style="display:none"><th>' . esc_html__('Matomo URL', 'wp-simplecmp') . '</th><td>';
        echo '<input type="text" name="matomo_url" value="" class="regular-text" placeholder="https://matomo.example.com">';
        echo '<br><label>' . esc_html__('Site ID', 'wp-simplecmp') . '</label><br>';
        echo '<input type="text" name="site_id" value="" class="regular-text" placeholder="1">';
        echo '</td></tr>';

        echo '<tr class="simplecmp-field-set meta" style="display:none"><th><label for="pixel_id">' . esc_html__('Pixel ID', 'wp-simplecmp') . '</label></th><td>';
        echo '<input type="text" name="pixel_id" id="pixel_id" value="" class="regular-text" placeholder="1234567890">';
        echo '</td></tr>';

        echo '<tr class="simplecmp-field-set microsoft_uet" style="display:none"><th><label for="tag_id">' . esc_html__('Tag ID', 'wp-simplecmp') . '</label></th><td>';
        echo '<input type="text" name="tag_id" id="tag_id" value="" class="regular-text" placeholder="12345678">';
        echo '</td></tr>';

        echo '</table>';
        submit_button(__('Save Tracker', 'wp-simplecmp'));
        echo '</form>';

        // JavaScript to toggle fields based on selected tracker type
        echo '<script>
        document.getElementById("simplecmp-tracker-type").addEventListener("change", function() {
            document.querySelectorAll(".simplecmp-field-set").forEach(function(el) { el.style.display = "none"; });
            document.querySelectorAll(".simplecmp-field-set." + this.value).forEach(function(el) { el.style.display = ""; });
        });
        </script>';
    }
}
