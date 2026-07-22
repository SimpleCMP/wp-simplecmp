<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Service\VisitorAuskunftService;

class AuskunftPage
{
    public static function render(): void
    {
        $results = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'simplecmp_auskunft')) {
            $rawUuid = sanitize_text_field($_POST['visitor_uuid'] ?? '');
            $site = sanitize_text_field($_POST['site'] ?? 'default');

            if (empty($rawUuid)) {
                $error = __('Visitor UUID is required.', 'wp-simplecmp');
            } else {
                try {
                    $service = new VisitorAuskunftService();
                    $results = $service->query($site, $rawUuid);
                } catch (\RuntimeException $e) {
                    $error = __('Bridge secret not configured. Run wp simplecmp generate-bridge-secret.', 'wp-simplecmp');
                }
            }
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('DSGVO Art. 15 — Auskunft', 'wp-simplecmp') . '</h1>';
        echo '<p class="description">' . esc_html__('Admin-mediated visitor data lookup. The visitor provides their UUID from localStorage, the server pseudonymizes it with the bridge secret.', 'wp-simplecmp') . '</p>';

        if ($error) {
            echo '<div class="notice notice-error"><p>' . esc_html($error) . '</p></div>';
        }

        echo '<form method="post">';
        wp_nonce_field('simplecmp_auskunft');
        echo '<table class="form-table">';
        echo '<tr><th><label for="visitor_uuid">' . esc_html__('Visitor UUID', 'wp-simplecmp') . '</label></th><td>';
        echo '<input type="text" name="visitor_uuid" id="visitor_uuid" value="" class="regular-text" placeholder="00000000-0000-0000-0000-000000000000">';
        echo '<p class="description">' . esc_html__('From the browser localStorage: simplecmp-<site>-visitor-uuid', 'wp-simplecmp') . '</p>';
        echo '</td></tr>';

        echo '<tr><th><label for="site">' . esc_html__('Site', 'wp-simplecmp') . '</label></th><td>';
        echo '<input type="text" name="site" id="site" value="default" class="regular-text">';
        echo '</td></tr>';
        echo '</table>';
        submit_button(__('Lookup', 'wp-simplecmp'));
        echo '</form>';

        if ($results) {
            echo '<h2>' . esc_html__('Results', 'wp-simplecmp') . '</h2>';
            echo '<p>' . esc_html__('Visitor Hash:', 'wp-simplecmp') . ' <code>' . esc_html($results['visitor_hash']) . '</code></p>';

            echo '<h3>' . sprintf(esc_html__('Decisions (%d)', 'wp-simplecmp'), count($results['decisions'])) . '</h3>';
            if (empty($results['decisions'])) {
                echo '<p>' . esc_html__('No decisions found for this visitor on this site.', 'wp-simplecmp') . '</p>';
            } else {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__('Date', 'wp-simplecmp') . '</th>';
                echo '<th>' . esc_html__('Type', 'wp-simplecmp') . '</th>';
                echo '<th>' . esc_html__('Version Hash', 'wp-simplecmp') . '</th>';
                echo '<th>' . esc_html__('Browser', 'wp-simplecmp') . '</th>';
                echo '</tr></thead><tbody>';
                foreach ($results['decisions'] as $d) {
                    echo '<tr>';
                    echo '<td>' . esc_html($d->created_at) . '</td>';
                    echo '<td>' . esc_html($d->decision_type) . '</td>';
                    echo '<td><code>' . esc_html(substr($d->version_hash, 0, 12)) . '...</code></td>';
                    echo '<td>' . esc_html($d->ua_family ?? '-') . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }

            echo '<h3>' . sprintf(esc_html__('Referenced Snapshots (%d)', 'wp-simplecmp'), count($results['snapshots'])) . '</h3>';
            foreach ($results['snapshots'] as $snap) {
                $canonical = json_decode($snap->canonical_json, true);
                echo '<details>';
                echo '<summary><code>' . esc_html(substr($snap->version_hash, 0, 16)) . '...</code> — ' . esc_html($snap->created_at) . '</summary>';
                echo '<pre style="max-height:200px;overflow:auto;background:#f0f0f1;padding:8px;font-size:11px">' . esc_html(json_encode($canonical, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                echo '</details>';
            }
        }

        echo '</div>';
    }
}
