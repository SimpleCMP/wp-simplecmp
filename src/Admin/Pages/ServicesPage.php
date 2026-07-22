<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;
use SimpleCMP\WpSimpleCmp\Service\RegistryListPresenter;

class ServicesPage
{
    public static function render(): void
    {
        global $wpdb;
        $repo = new ServiceRepository($wpdb);
        $presenter = new RegistryListPresenter();

        $action = $_GET['action'] ?? '';

        if ($action && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'simplecmp_service_action')) {
            $id = (int) ($_GET['id'] ?? 0);
            if ($action === 'delete' && $id) {
                $repo->delete($id);
                echo '<div class="notice notice-success"><p>' . esc_html__('Service deleted.', 'wp-simplecmp') . '</p></div>';
            }
        }

        $services = $repo->findAll();

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">' . esc_html__('Services', 'wp-simplecmp') . '</h1>';
        echo '<a href="' . esc_url(admin_url('admin.php?page=simplecmp-services&action=new')) . '" class="page-title-action">' . esc_html__('Add New', 'wp-simplecmp') . '</a>';
        echo '<hr class="wp-header-end">';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Name', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Service ID', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Vendor', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Source', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Actions', 'wp-simplecmp') . '</th>';
        echo '</tr></thead><tbody>';

        if (empty($services)) {
            echo '<tr><td colspan="5">' . esc_html__('No services yet. Browse the Library tab to adopt services.', 'wp-simplecmp') . '</td></tr>';
        }

        foreach ($services as $service) {
            $source = $presenter->determineSource($service);
            $sourceLabel = [
                'eigene' => __('Custom', 'wp-simplecmp'),
                'aus_bibliothek' => __('From Library', 'wp-simplecmp'),
                'verwaist' => __('Orphaned', 'wp-simplecmp'),
            ][$source] ?? $source;

            $rowClass = $source === 'verwaist' ? 'simplecmp-verwaist' : '';

            echo '<tr class="' . esc_attr($rowClass) . '">';
            echo '<td><strong>' . esc_html($service->name) . '</strong></td>';
            echo '<td><code>' . esc_html($service->service_id) . '</code></td>';
            echo '<td>' . esc_html($service->vendor ?? '-') . '</td>';
            echo '<td><span class="simplecmp-badge source-' . esc_attr($source) . '">' . esc_html($sourceLabel) . '</span></td>';
            echo '<td>';
            echo '<a href="' . esc_url(admin_url("admin.php?page=simplecmp-services&action=edit&id={$service->id}")) . '" class="button button-small">' . esc_html__('Edit', 'wp-simplecmp') . '</a> ';
            echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp-services&action=delete&id={$service->id}"), 'simplecmp_service_action')) . '" class="button button-small" onclick="return confirm(\'' . esc_js(__('Delete this service?', 'wp-simplecmp')) . '\')">' . esc_html__('Delete', 'wp-simplecmp') . '</a>';
            echo '</td></tr>';
        }

        echo '</tbody></table>';
        echo '</div>';
    }
}
