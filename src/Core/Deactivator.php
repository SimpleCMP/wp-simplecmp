<?php

namespace SimpleCMP\WpSimpleCmp\Core;

class Deactivator
{
    public static function deactivate(): void
    {
        // Cleanup transient caches
        delete_transient('simplecmp_upstream_health');
        delete_transient('simplecmp_rate_limit_buckets');
        wp_cache_flush();
    }
}
