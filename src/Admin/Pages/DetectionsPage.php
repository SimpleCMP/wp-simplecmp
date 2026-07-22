<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Database\Repositories\DetectionRepository;
use SimpleCMP\WpSimpleCmp\Service\DetectionListPresenter;

class DetectionsPage
{
    public static function render(): void
    {
        global $wpdb;
        $repo = new DetectionRepository($wpdb);
        $presenter = new DetectionListPresenter();

        $action = $_GET['action'] ?? 'list';
        $nonce = $_POST['_wpnonce'] ?? $_GET['_wpnonce'] ?? '';

        if ($action && $nonce && wp_verify_nonce($nonce, 'simplecmp_action')) {
            self::handleAction($action, $repo);
        }

        $filter = $_GET['filter'] ?? 'pending';
        $dismissed = $filter === 'dismissed';

        $page = max(1, (int) ($_GET['paged'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $detections = $repo->findAll([
            'dismissed' => $dismissed,
            'limit' => $perPage,
            'offset' => $offset,
        ]);

        $total = $repo->countAll(['dismissed' => $dismissed]);
        $totalPages = ceil($total / $perPage);

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">' . esc_html__('Detections', 'wp-simplecmp') . '</h1>';
        echo '<a href="' . esc_url(admin_url('admin.php?page=simplecmp&action=discover')) . '" class="page-title-action">' . esc_html__('Discover Trackers', 'wp-simplecmp') . '</a>';
        echo '<hr class="wp-header-end">';

        // Filter tabs
        $filters = [
            'pending' => __('Pending', 'wp-simplecmp'),
            'dismissed' => __('Dismissed', 'wp-simplecmp'),
        ];
        echo '<ul class="subsubsub">';
        foreach ($filters as $key => $label) {
            $current = $filter === $key ? ' current' : '';
            $count = $repo->countAll(['dismissed' => $key === 'dismissed']);
            echo '<li><a href="' . esc_url(admin_url("admin.php?page=simplecmp&filter={$key}")) . '" class="' . $current . '">' . esc_html($label) . ' <span class="count">(' . (int) $count . ')</span></a> |</li>';
        }
        echo '</ul>';

        // Bulk actions form
        echo '<form method="post" action="' . esc_url(admin_url('admin.php?page=simplecmp&filter=' . $filter)) . '">';
        wp_nonce_field('simplecmp_action');

        echo '<div class="tablenav top">';
        echo '<div class="alignleft actions bulkactions">';
        echo '<select name="bulk_action">';
        echo '<option value="">' . esc_html__('Bulk Actions', 'wp-simplecmp') . '</option>';
        if ($filter === 'pending') {
            echo '<option value="bulk_dismiss">' . esc_html__('Dismiss', 'wp-simplecmp') . '</option>';
        } else {
            echo '<option value="bulk_undismiss">' . esc_html__('Restore', 'wp-simplecmp') . '</option>';
            echo '<option value="bulk_purge">' . esc_html__('Delete Permanently', 'wp-simplecmp') . '</option>';
        }
        echo '</select>';
        submit_button(__('Apply', 'wp-simplecmp'), 'secondary', 'do_bulk', false);
        echo '</div>';

        // Pagination
        if ($totalPages > 1) {
            echo '<div class="tablenav-pages">';
            echo '<span class="displaying-num">' . sprintf(__('%d items', 'wp-simplecmp'), $total) . '</span>';
            for ($i = 1; $i <= $totalPages; $i++) {
                $cls = $i === $page ? 'current' : '';
                echo '<a class="page-numbers ' . $cls . '" href="' . esc_url(admin_url("admin.php?page=simplecmp&filter={$filter}&paged={$i}")) . '">' . $i . '</a>';
            }
            echo '</div>';
        }
        echo '</div>';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<td class="manage-column column-cb check-column"><input type="checkbox"></td>';
        echo '<th>' . esc_html__('State', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Identifier', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Kind', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Origin', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Page', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Occurrences', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Last Seen', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Actions', 'wp-simplecmp') . '</th>';
        echo '</tr></thead><tbody>';

        if (empty($detections)) {
            echo '<tr><td colspan="9">' . esc_html__('No detections found.', 'wp-simplecmp') . '</td></tr>';
        }

        foreach ($detections as $detection) {
            $state = $presenter->determineState($detection);
            $stateLabel = [
                'curated' => __('Curated', 'wp-simplecmp'),
                'recognised' => __('Recognised', 'wp-simplecmp'),
                'unknown' => __('Unknown', 'wp-simplecmp'),
                'dismissed' => __('Dismissed', 'wp-simplecmp'),
            ][$state] ?? $state;

            $stateClass = 'state-' . $state;

            echo '<tr>';
            echo '<th class="check-column"><input type="checkbox" name="ids[]" value="' . (int) $detection->id . '"></th>';
            echo '<td><span class="simplecmp-badge ' . esc_attr($stateClass) . '">' . esc_html($stateLabel) . '</span></td>';
            echo '<td>' . esc_html($detection->identifier) . '</td>';
            echo '<td>' . esc_html($detection->kind) . '</td>';
            echo '<td>' . esc_html($detection->origin ?? '-') . '</td>';
            echo '<td>' . esc_html(wp_parse_url($detection->page_url ?? '', PHP_URL_PATH) ?? '-') . '</td>';
            echo '<td>' . (int) $detection->occurrences . '</td>';
            echo '<td>' . esc_html($detection->last_seen ? wp_date('Y-m-d H:i', $detection->last_seen) : '-') . '</td>';
            echo '<td>';

            if ($state === 'recognised') {
                echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp&action=approve&id={$detection->id}"), 'simplecmp_action')) . '" class="button button-small">' . esc_html__('Approve', 'wp-simplecmp') . '</a> ';
            }
            if ($state === 'unknown') {
                echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp&action=curate&id={$detection->id}"), 'simplecmp_action')) . '" class="button button-small">' . esc_html__('Curate', 'wp-simplecmp') . '</a> ';
            }
            if ($state !== 'dismissed') {
                echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp&action=dismiss&id={$detection->id}"), 'simplecmp_action')) . '" class="button button-small">' . esc_html__('Dismiss', 'wp-simplecmp') . '</a>';
            } else {
                echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp&action=undismiss&id={$detection->id}"), 'simplecmp_action')) . '" class="button button-small">' . esc_html__('Restore', 'wp-simplecmp') . '</a> ';
                echo '<a href="' . esc_url(wp_nonce_url(admin_url("admin.php?page=simplecmp&action=purge&id={$detection->id}"), 'simplecmp_action')) . '" class="button button-small" onclick="return confirm(\'' . esc_js(__('Delete permanently?', 'wp-simplecmp')) . '\')">' . esc_html__('Delete', 'wp-simplecmp') . '</a>';
            }

            echo '</td></tr>';
        }

        echo '</tbody></table>';
        echo '</form>';
        echo '</div>';
    }

    private static function handleAction(string $action, DetectionRepository $repo): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        switch ($action) {
            case 'dismiss':
                $repo->dismiss($id);
                break;
            case 'undismiss':
                $repo->undismiss($id);
                break;
            case 'purge':
                $repo->purge($id);
                break;
            case 'bulk_dismiss':
                $ids = array_map('intval', $_POST['ids'] ?? []);
                $repo->bulkDismiss($ids);
                break;
            case 'bulk_undismiss':
                $ids = array_map('intval', $_POST['ids'] ?? []);
                foreach ($ids as $id) $repo->undismiss($id);
                break;
            case 'bulk_purge':
                $ids = array_map('intval', $_POST['ids'] ?? []);
                $repo->bulkPurge($ids);
                break;
            case 'approve':
                // Approve: create service from library based on detection
                self::approveDetection($id);
                break;
        }
    }

    private static function approveDetection(int $detectionId): void
    {
        global $wpdb;
        $repo = new DetectionRepository($wpdb);
        $detection = $repo->findById($detectionId);

        if (!$detection) return;

        $serviceCurator = new \SimpleCMP\WpSimpleCmp\Service\ServiceCurator();
        $library = new \SimpleCMP\WpSimpleCmp\Library\ServicesLibrary();

        // Try to find the matching library entry
        foreach ($library->all() as $entry) {
            $matches = $entry['matches'] ?? [];
            foreach ($matches['cookies'] ?? [] as $cookie) {
                if (($cookie['name'] ?? '') === $detection->identifier) {
                    $serviceCurator->adoptFromLibrary($entry['service_id'] ?? '');
                    return;
                }
            }
            foreach (array_merge($matches['origins'] ?? [], $matches['aliasOrigins'] ?? []) as $origin) {
                if ($detection->identifier === $origin || str_ends_with($detection->identifier, $origin)) {
                    $serviceCurator->adoptFromLibrary($entry['service_id'] ?? '');
                    return;
                }
            }
        }
    }
}
