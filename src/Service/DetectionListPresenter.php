<?php

namespace SimpleCMP\WpSimpleCmp\Service;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;
use SimpleCMP\WpSimpleCmp\Library\ServicesLibrary;

class DetectionListPresenter
{
    private ServiceRepository $serviceRepo;
    private ServicesLibrary $library;

    public function __construct()
    {
        global $wpdb;
        $this->serviceRepo = new ServiceRepository($wpdb);
        $this->library = new ServicesLibrary();
    }

    /**
     * Enrich a detection row with its classifier state.
     *
     * @return string 'curated' | 'recognised' | 'unknown' | 'dismissed'
     */
    public function determineState(object $detection): string
    {
        if (!empty($detection->dismissed_at) && $detection->dismissed_at > 0) {
            return 'dismissed';
        }

        $identifier = $detection->identifier;
        $kind = $detection->kind;

        // Check registry first
        $registryServices = $this->serviceRepo->findAll();
        foreach ($registryServices as $service) {
            if ($this->matchesService($kind, $identifier, $service)) {
                return 'curated';
            }
        }

        // Check bundled library
        foreach ($this->library->all() as $entry) {
            if ($this->matchesLibraryEntry($kind, $identifier, $entry)) {
                return 'recognised';
            }
        }

        return 'unknown';
    }

    private function matchesService(string $kind, string $identifier, object $service): bool
    {
        if ($kind === 'cookie') {
            $cookies = json_decode($service->cookies ?? '[]', true) ?: [];
            foreach ($cookies as $cookie) {
                if (is_string($cookie) && $cookie === $identifier) {
                    return true;
                }
                if (is_array($cookie) && ($cookie['name'] ?? '') === $identifier) {
                    return true;
                }
            }
        }

        if ($kind === 'origin' || $kind === 'stylesheet') {
            $origins = json_decode($service->origins ?? '[]', true) ?: [];
            foreach ($origins as $origin) {
                if ($this->originMatches($identifier, $origin)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function matchesLibraryEntry(string $kind, string $identifier, array $entry): bool
    {
        $matches = $entry['matches'] ?? [];

        if ($kind === 'cookie') {
            foreach ($matches['cookies'] ?? [] as $cookie) {
                if (is_string($cookie) && $cookie === $identifier) {
                    return true;
                }
                if (is_array($cookie) && ($cookie['name'] ?? '') === $identifier) {
                    return true;
                }
            }
        }

        if ($kind === 'origin' || $kind === 'stylesheet') {
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
        }

        return false;
    }

    private function originMatches(string $identifier, string $origin): bool
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
