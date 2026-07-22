<?php

namespace SimpleCMP\WpSimpleCmp\Frontend;

use SimpleCMP\WpSimpleCmp\Service\EffectiveSettingsResolver;
use SimpleCMP\WpSimpleCmp\Service\ClassifierLookup;
use SimpleCMP\WpSimpleCmp\Database\Repositories\AllowedStylesheetHostRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;

class Rewriter
{
    private static bool $active = false;

    public static function startBufferingHeader(): void
    {
        if (self::$active) return;

        if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
            return;
        }

        $resolver = new EffectiveSettingsResolver();
        if (!$resolver->get('simplecmp_universal_blocking_enabled', false)) {
            return;
        }

        self::$active = true;
        ob_start([self::class, 'rewriteCallback']);
    }

    public static function rewriteCallback(string $buffer): string
    {
        return self::rewrite($buffer);
    }

    public static function rewrite(string $buffer): string
    {
        $startTime = microtime(true);

        if (!preg_match('#<html|<body#i', $buffer)) {
            return $buffer;
        }

        $resolver = new EffectiveSettingsResolver();
        $allowlistStr = $resolver->get('simplecmp_universal_blocking_allowlist', '');
        $allowlist = array_filter(array_map('trim', explode("\n", str_replace("\r", '', $allowlistStr))));
        $blockStylesheets = $resolver->get('simplecmp_universal_blocking_block_stylesheets', false);

        $siteHost = wp_parse_url(home_url(), PHP_URL_HOST);

        global $wpdb;
        $stylesheetRepo = new AllowedStylesheetHostRepository($wpdb);
        $allowedStylesheetHosts = array_map(fn($row) => $row->host, $stylesheetRepo->findBySource('default'));

        // Preload classifier for fast lookups
        $classifier = new ClassifierLookup();
        $serviceRepo = new ServiceRepository($wpdb);
        $registryServices = $serviceRepo->findAll();

        $dom = new \DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        $doc = $dom->loadHTML(
            '<!DOCTYPE html>' . $buffer,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR
        );
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        if (!$doc) return $buffer;

        // Rewrite third-party <script src="..."> → type="text/plain" + data-name + data-src
        foreach (iterator_to_array($dom->getElementsByTagName('script')) as $el) {
            $src = $el->getAttribute('src');
            if (empty($src) || $el->hasAttribute('data-name') || $el->hasAttribute('data-no-rewrite')) continue;
            $host = parse_url($src, PHP_URL_HOST);
            if (!$host || $host === $siteHost || self::isAllowed($host, $allowlist)) continue;

            $resolution = self::resolveService($host, $classifier, $registryServices);
            if (!$resolution) continue;

            $el->setAttribute('data-name', $resolution['name']);
            $el->setAttribute('data-src', $src);
            $el->setAttribute('data-blocked-source', $host);
            $el->setAttribute('type', 'text/plain');
            $el->removeAttribute('src');
        }

        // Rewrite third-party <iframe src="..."> → src="about:blank" + data-name + data-src
        foreach (iterator_to_array($dom->getElementsByTagName('iframe')) as $el) {
            $src = $el->getAttribute('src');
            if (empty($src) || $src === 'about:blank' || $el->hasAttribute('data-name') || $el->hasAttribute('data-no-rewrite')) continue;
            $host = parse_url($src, PHP_URL_HOST);
            if (!$host || $host === $siteHost || self::isAllowed($host, $allowlist)) continue;

            $resolution = self::resolveService($host, $classifier, $registryServices);
            if (!$resolution) continue;

            $el->setAttribute('data-name', $resolution['name']);
            $el->setAttribute('data-src', $src);
            $el->setAttribute('data-blocked-source', $host);
            $el->setAttribute('src', 'about:blank');
        }

        // Rewrite tagged <img data-name="..."> images
        foreach (iterator_to_array($dom->getElementsByTagName('img')) as $el) {
            $dataName = $el->getAttribute('data-name');
            if (!$dataName || $el->hasAttribute('data-src')) continue;
            $src = $el->getAttribute('src');
            if (empty($src) || $src === 'about:blank') continue;
            $host = parse_url($src, PHP_URL_HOST);
            if (!$host || $host === $siteHost || self::isAllowed($host, $allowlist)) continue;

            $el->setAttribute('data-src', $src);
            $el->setAttribute('data-blocked-source', $host);
            $el->setAttribute('src', 'about:blank');
        }

        // Rewrite third-party <link rel="stylesheet"> (opt-in)
        if ($blockStylesheets) {
            foreach (iterator_to_array($dom->getElementsByTagName('link')) as $el) {
                if ($el->getAttribute('rel') !== 'stylesheet') continue;
                $href = $el->getAttribute('href');
                if (empty($href) || $el->hasAttribute('data-href') || $el->hasAttribute('data-no-rewrite')) continue;
                $host = parse_url($href, PHP_URL_HOST);
                if (!$host || $host === $siteHost || self::isAllowed($host, $allowlist)) continue;
                if (in_array($host, $allowedStylesheetHosts, true)) continue;

                $resolution = self::resolveService($host, $classifier, $registryServices);
                if (!$resolution) continue;

                $el->setAttribute('data-name', $resolution['name']);
                $el->setAttribute('data-href', $href);
                $el->setAttribute('data-blocked-source', $host);
                $el->removeAttribute('href');
            }
        }

        $result = $dom->saveHTML();
        $result = preg_replace('#^<!DOCTYPE html>#i', '', $result);

        $duration = (int) ((microtime(true) - $startTime) * 1000);
        @header("Server-Timing: rewriter;dur={$duration}", false);

        return $result;
    }

    /**
     * Resolve a hostname to a service name for the data-name attribute.
     */
    private static function resolveService(string $host, ClassifierLookup $classifier, array $registryServices): ?array
    {
        // First check if a registry service matches this host
        foreach ($registryServices as $service) {
            $origins = json_decode($service->origins ?? '[]', true) ?: [];
            foreach ($origins as $origin) {
                if (self::originMatches($host, $origin)) {
                    return [
                        'service_id' => $service->service_id,
                        'name' => $service->name,
                        'source' => $host,
                    ];
                }
            }
        }

        // Then try the library for a service name
        $library = new \SimpleCMP\WpSimpleCmp\Library\ServicesLibrary();
        foreach ($library->all() as $entry) {
            $serviceId = $entry['service_id'] ?? null;
            if (!$serviceId) continue;
            $matches = $entry['matches'] ?? [];
            foreach (array_merge($matches['origins'] ?? [], $matches['aliasOrigins'] ?? []) as $origin) {
                if (self::originMatches($host, $origin)) {
                    return [
                        'service_id' => $serviceId,
                        'name' => $entry['name'] ?? $serviceId,
                        'source' => $host,
                    ];
                }
            }
        }

        // No match — use hostname-derived identifier with auto- prefix
        $name = 'auto-' . str_replace('.', '-', preg_replace('/[^a-zA-Z0-9.-]/', '', $host));
        return [
            'service_id' => $name,
            'name' => $name,
            'source' => $host,
        ];
    }

    private static function isAllowed(string $host, array $allowlist): bool
    {
        foreach ($allowlist as $allowed) {
            $allowed = trim($allowed);
            if (empty($allowed)) continue;
            if ($host === $allowed) return true;
            if (str_starts_with($allowed, '*.')) {
                if (str_ends_with($host, substr($allowed, 2))) return true;
            }
        }
        return false;
    }

    private static function originMatches(string $identifier, string $origin): bool
    {
        $identifier = preg_replace('/^www\./', '', $identifier);
        $origin = preg_replace('/^www\./', '', $origin);

        if ($identifier === $origin) return true;

        if (str_starts_with($origin, '*.')) {
            $domain = substr($origin, 2);
            if (str_ends_with($identifier, $domain)) return true;
        }

        if (str_ends_with($identifier, '.' . $origin)) return true;

        if (str_ends_with($identifier, $origin) && strlen($identifier) > strlen($origin)) {
            $prefix = substr($identifier, 0, -strlen($origin));
            if (str_ends_with($prefix, '.')) return true;
        }

        return false;
    }
}
