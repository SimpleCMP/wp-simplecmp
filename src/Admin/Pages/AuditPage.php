<?php

namespace SimpleCMP\WpSimpleCmp\Admin\Pages;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ConfigSnapshotRepository;

class AuditPage
{
    public static function render(): void
    {
        global $wpdb;
        $repo = new ConfigSnapshotRepository($wpdb);
        $site = 'default';

        $page = max(1, (int) ($_GET['paged'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $snapshots = $repo->findBySite($site, $perPage, $offset);
        $total = $repo->countBySite($site);
        $totalPages = ceil($total / $perPage);

        $action = $_GET['action'] ?? '';
        $detailId = (int) ($_GET['id'] ?? 0);

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Audit Trail — Config Snapshots', 'wp-simplecmp') . '</h1>';

        if ($action === 'show' && $detailId) {
            self::renderDetail($repo, $detailId, $site);
            echo '</div>';
            return;
        }

        echo '<p class="description">' . esc_html__('Append-only snapshots of the resolved banner configuration, written on every editor save.', 'wp-simplecmp') . '</p>';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('ID', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Date', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Version Hash', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Trigger', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Created By', 'wp-simplecmp') . '</th>';
        echo '<th>' . esc_html__('Actions', 'wp-simplecmp') . '</th>';
        echo '</tr></thead><tbody>';

        if (empty($snapshots)) {
            echo '<tr><td colspan="6">' . esc_html__('No snapshots yet.', 'wp-simplecmp') . '</td></tr>';
        }

        foreach ($snapshots as $snap) {
            $user = get_userdata((int) $snap->creator_be_user);
            $userName = $user ? $user->display_name : 'System';

            echo '<tr>';
            echo '<td>' . (int) $snap->id . '</td>';
            echo '<td>' . esc_html($snap->created_at) . '</td>';
            echo '<td><code>' . esc_html(substr($snap->version_hash, 0, 16)) . '...</code></td>';
            echo '<td>' . esc_html($snap->trigger_event) . '</td>';
            echo '<td>' . esc_html($userName) . '</td>';
            echo '<td><a href="' . esc_url(admin_url("admin.php?page=simplecmp-audit&action=show&id={$snap->id}")) . '" class="button button-small">' . esc_html__('View', 'wp-simplecmp') . '</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        if ($totalPages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            for ($i = 1; $i <= $totalPages; $i++) {
                $cls = $i === $page ? 'current' : '';
                echo '<a class="page-numbers ' . $cls . '" href="' . esc_url(admin_url("admin.php?page=simplecmp-audit&paged={$i}")) . '">' . $i . '</a>';
            }
            echo '</div></div>';
        }

        echo '</div>';
    }

    private static function renderDetail(ConfigSnapshotRepository $repo, int $id, string $site): void
    {
        $snapshots = $repo->findBySite($site, 1000);
        $current = null;

        foreach ($snapshots as $s) {
            if ((int) $s->id === $id) {
                $current = $s;
                break;
            }
        }

        if (!$current) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Snapshot not found.', 'wp-simplecmp') . '</p></div>';
            return;
        }

        $previous = $repo->findPrevious($site, $current->version_hash);
        $canonical = json_decode($current->canonical_json, true);
        $prevCanonical = $previous ? json_decode($previous->canonical_json, true) : null;

        echo '<p><a href="' . esc_url(admin_url('admin.php?page=simplecmp-audit')) . '">&larr; ' . esc_html__('Back to list', 'wp-simplecmp') . '</a></p>';
        echo '<h2>' . sprintf(esc_html__('Snapshot #%d — %s', 'wp-simplecmp'), (int) $current->id, esc_html($current->created_at)) . '</h2>';
        echo '<p>' . esc_html__('Version Hash:', 'wp-simplecmp') . ' <code>' . esc_html($current->version_hash) . '</code></p>';
        echo '<p>' . esc_html__('Trigger:', 'wp-simplecmp') . ' ' . esc_html($current->trigger_event) . '</p>';

        // Canonical JSON
        echo '<h3>' . esc_html__('Canonical Configuration', 'wp-simplecmp') . '</h3>';
        echo '<pre style="max-height:400px;overflow:auto;background:#f0f0f1;padding:10px;font-size:12px">' . esc_html(json_encode($canonical, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';

        // Diff if previous exists
        if ($prevCanonical) {
            echo '<h3>' . esc_html__('Changes from previous', 'wp-simplecmp') . '</h3>';
            echo '<pre style="max-height:400px;overflow:auto;background:#f0f0f1;padding:10px;font-size:12px">';
            echo esc_html(self::simpleDiff(
                json_encode($prevCanonical, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                json_encode($canonical, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ));
            echo '</pre>';
        }
    }

    private static function simpleDiff(string $old, string $new): string
    {
        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $new);
        $result = [];

        $maxLen = max(count($oldLines), count($newLines));
        for ($i = 0; $i < $maxLen; $i++) {
            $oldLine = $oldLines[$i] ?? '';
            $newLine = $newLines[$i] ?? '';
            if ($oldLine !== $newLine) {
                if ($oldLine) $result[] = '- ' . $oldLine;
                if ($newLine) $result[] = '+ ' . $newLine;
            } else {
                $result[] = '  ' . $oldLine;
            }
        }

        return implode("\n", $result);
    }
}
