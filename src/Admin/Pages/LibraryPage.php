<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Library\ServicesLibrary;
use SimpleCMP\WpSimpleCmp\Service\ServiceCurator;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;

class LibraryPage
{
    public static function render(): void
    {
        global $wpdb;
        $library = new ServicesLibrary();
        $curator = new ServiceCurator();
        $repo = new ServiceRepository($wpdb);

        $action = $_GET['action'] ?? '';
        if ($action && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'simplecmp_library_action')) {
            $serviceId = sanitize_text_field($_GET['service_id'] ?? '');
            if ($action === 'adopt' && $serviceId) {
                $curator->adoptFromLibrary($serviceId);
                echo '<div class="notice notice-success"><p>' . sprintf(esc_html__('Service "%s" adopted.', 'wp-simplecmp'), esc_html($serviceId)) . '</p></div>';
            }
        }

        $page = max(1, (int) ($_GET['paged'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $allServices = iterator_to_array($library->services());
        $total = count($allServices);
        $totalPages = ceil($total / $perPage);
        $services = array_slice($allServices, $offset, $perPage);

        $adoptedIds = [];
        foreach ($repo->findAll() as $s) {
            $adoptedIds[$s->service_id] = true;
        }

        $search = sanitize_text_field($_GET['search'] ?? '');
        if ($search) {
            $services = array_filter($services, function ($s) use ($search) {
                return str_contains(strtolower($s['name'] ?? ''), strtolower($search))
                    || str_contains(strtolower($s['service_id'] ?? ''), strtolower($search))
                    || str_contains(strtolower($s['vendor']['name'] ?? ''), strtolower($search));
            });
        }

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">' . esc_html__('Service Library', 'wp-simplecmp') . '</h1>';
        echo '<span class="simplecmp-library-meta">';
        echo sprintf(__('%d services bundled', 'wp-simplecmp'), $library->fileCount());
        echo '</span>';
        echo '<hr class="wp-header-end">';

        // Search
        echo '<form method="get" action="' . esc_url(admin_url('admin.php')) . '">';
        echo '<input type="hidden" name="page" value="simplecmp-library">';
        echo '<p class="search-box">';
        echo '<input type="search" name="search" value="' . esc_attr($search) . '" placeholder="' . esc_attr__('Search services...', 'wp-simplecmp') . '">';
        submit_button(__('Search', 'wp-simplecmp'), '', '', false);
        echo '</p>';
        echo '</form>';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Service ID', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Name', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Vendor', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Country', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Status', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Actions', 'wp-simplecmp') . '</th>';
        echo '</tr></thead><tbody>';

        if (empty($services)) {
            echo '<tr><td colspan="6">' . esc_html__('No services found.', 'wp-simplecmp') . '</td></tr>';
        }

        foreach ($services as $service) {
            $serviceId = $service['service_id'] ?? '';
            $name = $service['name'] ?? $serviceId;
            $vendor = $service['vendor']['name'] ?? '-';
            $country = $service['vendor']['country'] ?? '-';
            $isAdopted = isset($adoptedIds[$serviceId]);

            echo '<tr>';
            echo '<td><code>' . esc_html($serviceId) . '</code></td>';
            echo '<td><strong>' . esc_html($name) . '</strong></td>';
            echo '<td>' . esc_html($vendor) . '</td>';
            echo '<td>' . esc_html($country) . '</td>';
            echo '<td>';
            if ($isAdopted) {
                echo '<span class="simplecmp-badge source-aus_bibliothek">' . esc_html__('Adopted', 'wp-simplecmp') . '</span>';
            } else {
                echo '<span class="simplecmp-badge state-unknown">' . esc_html__('Available', 'wp-simplecmp') . '</span>';
            }
            echo '</td>';
            echo '<td>';
            if (!$isAdopted) {
                echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp-library&action=adopt&service_id={$serviceId}"), 'simplecmp_library_action')) . '" class="button button-small button-primary">' . esc_html__('Adopt', 'wp-simplecmp') . '</a>';
            } else {
                echo '<a href="' . esc_url(admin_url("admin.php?page=simplecmp-services&action=edit&service_id={$serviceId}")) . '" class="button button-small">' . esc_html__('Edit', 'wp-simplecmp') . '</a>';
            }
            echo '</td></tr>';
        }

        echo '</tbody></table>';

        // Pagination
        if ($totalPages > 1) {
            echo '<div class="tablenav">';
            echo '<div class="tablenav-pages">';
            echo '<span class="displaying-num">' . sprintf(__('%d items', 'wp-simplecmp'), $total) . '</span>';
            for ($i = 1; $i <= $totalPages; $i++) {
                $cls = $i === $page ? 'current' : '';
                echo '<a class="page-numbers ' . $cls . '" href="' . esc_url(admin_url("admin.php?page=simplecmp-library&paged={$i}&search=" . urlencode($search))) . '">' . $i . '</a>';
            }
            echo '</div></div>';
        }

        echo '</div>';
    }
}
