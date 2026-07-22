<?php

namespace SimpleCMP\WpSimpleCmp\Service;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\LibraryCacheRepository;
use SimpleCMP\WpSimpleCmp\Library\ServicesLibrary;

class ClassifierLookup
{
    private ServiceRepository $serviceRepo;
    private LibraryCacheRepository $cacheRepo;
    private ServicesLibrary $library;

    public function __construct()
    {
        global $wpdb;
        $this->serviceRepo = new ServiceRepository($wpdb);
        $this->cacheRepo = new LibraryCacheRepository($wpdb);
        $this->library = new ServicesLibrary();
    }

    /**
     * Three-tier classifier: registry → bundled library → upstream.
     */
    public function lookup(array $identifiers): array
    {
        $results = [];

        foreach ($identifiers as $identifier) {
            $results[$identifier] = $this->lookupSingle($identifier);
        }

        return $results;
    }

    /**
     * Lookup a single cookie/origin identifier.
     * Returns: 'curated' | 'recognised' | 'unknown'
     */
    public function lookupSingle(string $identifier): string
    {
        // Tier 1: Admin-curated DB registry
        $registryServices = $this->serviceRepo->findAll();
        foreach ($registryServices as $service) {
            if ($this->matchesService($identifier, $service)) {
                return 'curated';
            }
        }

        // Tier 2: Bundled JSON library
        foreach ($this->library->all() as $libraryService) {
            if ($this->matchesLibraryEntry($identifier, $libraryService)) {
                return 'recognised';
            }
        }

        // Tier 3: Upstream (server-to-server, cached)
        $upstreamUrl = get_option('simplecmp_library_upstream_url', '');
        if ($upstreamUrl) {
            $upstreamResult = $this->lookupUpstream($identifier, $upstreamUrl);
            if ($upstreamResult === 'recognised') {
                return 'recognised';
            }
        }

        return 'unknown';
    }

    private function matchesService(string $identifier, object $service): bool
    {
        $origins = json_decode($service->origins ?? '[]', true) ?: [];
        $cookies = json_decode($service->cookies ?? '[]', true) ?: [];

        foreach ($origins as $origin) {
            if ($this->originMatches($identifier, $origin)) {
                return true;
            }
        }

        foreach ($cookies as $cookie) {
            if (is_string($cookie) && $cookie === $identifier) {
                return true;
            }
            if (is_array($cookie) && isset($cookie['name']) && $cookie['name'] === $identifier) {
                return true;
            }
        }

        return false;
    }

    private function matchesLibraryEntry(string $identifier, array $entry): bool
    {
        $matches = $entry['matches'] ?? [];

        // Check origins
        foreach ($matches['origins'] ?? [] as $origin) {
            if ($this->originMatches($identifier, $origin)) {
                return true;
            }
        }

        foreach ($matches['aliasOrigins'] ?? [] as $origin) {
            if ($this->originMatches($identifier, $origin)) {
                return true;
            }
        }

        // Check cookies
        foreach ($matches['cookies'] ?? [] as $cookie) {
            if (is_string($cookie) && $cookie === $identifier) {
                return true;
            }
            if (is_array($cookie) && isset($cookie['name']) && $cookie['name'] === $identifier) {
                return true;
            }
        }

        return false;
    }

    private function originMatches(string $identifier, string $origin): bool
    {
        // Normalise: strip leading "www." from both sides
        $identifier = preg_replace('/^www\./', '', $identifier);
        $origin = preg_replace('/^www\./', '', $origin);

        if ($identifier === $origin) {
            return true;
        }

        // Wildcard match: *.example.com
        if (str_starts_with($origin, '*.')) {
            $domain = substr($origin, 2);
            if (str_ends_with($identifier, $domain)) {
                return true;
            }
        }

        // Domain is suffix of identifier
        if (str_ends_with($identifier, '.' . $origin)) {
            return true;
        }

        // Origin is suffix of identifier (e.g. example.com matches www.example.com)
        if (str_ends_with($identifier, $origin) && strlen($identifier) > strlen($origin)) {
            // Only match if the character before is a dot (proper subdomain)
            $prefix = substr($identifier, 0, -strlen($origin));
            if (str_ends_with($prefix, '.')) {
                return true;
            }
        }

        return false;
    }

    private function lookupUpstream(string $identifier, string $upstreamUrl): ?string
    {
        $cacheKey = 'lookup_' . $identifier;
        $cached = $this->cacheRepo->find('lookup', $cacheKey);
        if ($cached) {
            $response = json_decode($cached->response_json, true);
            return ($response['status'] ?? '') === 'recognised' ? 'recognised' : 'unknown';
        }

        $dailyBudget = (int) get_option('simplecmp_library_upstream_daily_budget', 1000);
        $todayKey = 'simplecmp_upstream_calls_' . date('Ymd');
        $todayCalls = (int) get_transient($todayKey);

        if ($todayCalls >= $dailyBudget) {
            return null;
        }

        set_transient($todayKey, $todayCalls + 1, DAY_IN_SECONDS);

        $response = wp_remote_post($upstreamUrl . '/lookup', [
            'timeout' => 5,
            'body' => json_encode(['identifiers' => [$identifier]]),
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        $status = ($data['results'][$identifier]['status'] ?? '') === 'known' ? 'recognised' : 'unknown';

        $this->cacheRepo->upsert('lookup', $cacheKey, json_encode(['status' => $status]), 86400);

        return $status;
    }
}
