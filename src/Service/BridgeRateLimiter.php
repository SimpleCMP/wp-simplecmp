<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class BridgeRateLimiter
{
    public function isAllowed(string $endpoint, string $ip, int $maxRequests = 100): bool
    {
        $key = 'simplecmp_ratelimit_' . $endpoint . '_' . md5($ip);
        $hourKey = $key . '_' . date('YmdH');

        $count = (int) get_transient($hourKey);

        if ($count >= $maxRequests) {
            return false;
        }

        set_transient($hourKey, $count + 1, HOUR_IN_SECONDS);
        return true;
    }

    public function getRemaining(string $endpoint, string $ip, int $maxRequests = 100): int
    {
        $key = 'simplecmp_ratelimit_' . $endpoint . '_' . md5($ip);
        $hourKey = $key . '_' . date('YmdH');

        $count = (int) get_transient($hourKey);
        return max(0, $maxRequests - $count);
    }
}
