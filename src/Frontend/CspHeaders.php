<?php

namespace SimpleCMP\WpSimpleCmp\Frontend;

use SimpleCMP\WpSimpleCmp\Service\EffectiveSettingsResolver;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;

class CspHeaders
{
    /**
     * Opt-in CSP: only active when admin enables it explicitly.
     * When enabled, adds adopted services' origins to the relevant directives
     * AFTER the visitor has consented. This is complementary to Universal Blocking,
     * not a replacement.
     *
     * Default: OFF (no CSP emitted).
     * Enable via: wp option update simplecmp_csp_enabled 1
     */
    public static function extend(array $headers): array
    {
        $resolver = new EffectiveSettingsResolver();
        $cspEnabled = get_option('simplecmp_csp_enabled', false);

        if (!$cspEnabled) {
            // Check if the setting key is in active_settings
            $enabled = $resolver->get('simplecmp_csp_enabled', false);
            if (!$enabled) {
                return $headers;
            }
        }

        global $wpdb;
        $serviceRepo = new ServiceRepository($wpdb);
        $services = $serviceRepo->findAll();

        if (empty($services)) {
            return $headers;
        }

        // Collect origins grouped by type
        $scriptOrigins = ["'self'", "'unsafe-inline'"];
        $styleOrigins = ["'self'", "'unsafe-inline'"];
        $connectOrigins = ["'self'"];
        $imgOrigins = ["'self'", 'data:'];
        $frameOrigins = ["'self'"];
        $fontOrigins = ["'self'", 'data:'];

        foreach ($services as $service) {
            $origins = json_decode($service->origins ?? '[]', true) ?: [];
            $serviceId = $service->service_id;

            foreach ($origins as $origin) {
                $scheme = 'https';
                if (str_starts_with($origin, 'http://')) {
                    $scheme = 'http';
                }
                $host = parse_url('scheme://' . ltrim($origin, '*.'), PHP_URL_HOST) ?: $origin;
                if (!$host) continue;

                $originUrl = $scheme . '://' . $host;
                $wildcardUrl = $scheme . '://*.' . $host;

                // Media services (YouTube, Vimeo, Spotify) → frame-src primarily
                if (in_array($serviceId, ['youtube', 'vimeo', 'spotify', 'google-maps'], true)) {
                    $frameOrigins[] = $originUrl;
                    $frameOrigins[] = $wildcardUrl;
                    continue;
                }

                // Analytics/tracking → connect-src, script-src
                if (in_array($serviceId, ['google-analytics', 'google-tag-manager', 'hotjar', 'matomo', 'meta-pixel', 'microsoft-uet'], true)) {
                    $connectOrigins[] = $originUrl;
                    $connectOrigins[] = $wildcardUrl;
                    $scriptOrigins[] = $originUrl;
                    $scriptOrigins[] = $wildcardUrl;
                    continue;
                }

                // Security (hCaptcha, Stripe) → script-src, frame-src
                if (in_array($serviceId, ['hcaptcha', 'stripe'], true)) {
                    $scriptOrigins[] = $originUrl;
                    $scriptOrigins[] = $wildcardUrl;
                    $frameOrigins[] = $originUrl;
                    $frameOrigins[] = $wildcardUrl;
                    continue;
                }

                // Fallback: add to all
                $scriptOrigins[] = $originUrl;
                $connectOrigins[] = $originUrl;
                $imgOrigins[] = $originUrl;
                $frameOrigins[] = $originUrl;
                $fontOrigins[] = $originUrl;
            }
        }

        $parts = [];

        $parts[] = 'default-src ' . implode(' ', ["'self'"]) . ';';
        $parts[] = 'script-src ' . implode(' ', array_unique($scriptOrigins)) . ';';
        $parts[] = 'style-src ' . implode(' ', array_unique($styleOrigins)) . ';';
        $parts[] = 'connect-src ' . implode(' ', array_unique($connectOrigins)) . ';';
        $parts[] = 'img-src ' . implode(' ', array_unique($imgOrigins)) . ';';
        $parts[] = 'frame-src ' . implode(' ', array_unique($frameOrigins)) . ';';
        $parts[] = 'font-src ' . implode(' ', array_unique($fontOrigins)) . ';';
        $parts[] = "worker-src 'self' blob:;";

        $cspValue = implode(' ', $parts);

        $headers['Content-Security-Policy'] = $cspValue;

        return $headers;
    }
}
