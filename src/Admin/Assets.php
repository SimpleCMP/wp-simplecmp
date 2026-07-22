<?php

namespace SimpleCMP\WpSimpleCmp\Admin;

class Assets
{
    public static function enqueue(string $hookSuffix): void
    {
        if (!str_contains($hookSuffix, 'simplecmp')) {
            return;
        }

        wp_enqueue_style(
            'simplecmp-admin',
            WP_SIMPLECMP_URL . 'assets/css/admin.css',
            [],
            WP_SIMPLECMP_VERSION
        );

        wp_enqueue_script(
            'simplecmp-admin',
            WP_SIMPLECMP_URL . 'assets/js/admin/bulk-select.js',
            [],
            WP_SIMPLECMP_VERSION,
            true
        );
    }
}
