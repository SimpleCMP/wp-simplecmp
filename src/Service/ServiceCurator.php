<?php

namespace SimpleCMP\WpSimpleCmp\Service;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;
use SimpleCMP\WpSimpleCmp\Library\ServicesLibrary;

class ServiceCurator
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
     * Create a service from library data (adopt).
     */
    public function adoptFromLibrary(string $serviceId): ?int
    {
        $entry = $this->library->find($serviceId);
        if (!$entry) return null;

        $existing = $this->serviceRepo->findByServiceId($serviceId);
        if ($existing) return null; // Already exists

        $data = [
            'service_id' => $serviceId,
            'name' => $entry['name'] ?? $serviceId,
            'vendor' => $entry['vendor']['name'] ?? null,
            'vendor_country' => $entry['vendor']['country'] ?? null,
            'vendor_address' => isset($entry['vendor']['address']) ? json_encode($entry['vendor']['address']) : null,
            'vendor_opt_out_url' => $entry['vendor']['optOutUrl'] ?? null,
            'vendor_partner' => isset($entry['vendor']['partner']) ? json_encode($entry['vendor']['partner']) : null,
            'vendor_description' => $entry['vendor']['description'] ?? null,
            'purposes' => isset($entry['purposes']) ? json_encode($entry['purposes']) : null,
            'privacy_policy_url' => $entry['privacyPolicyUrl'] ?? null,
            'description' => $entry['description'] ?? null,
            'retention' => $entry['retention'] ?? null,
            'i18n' => isset($entry['i18n']) ? json_encode($entry['i18n']) : null,
            'cookies' => isset($entry['matches']['cookies']) ? json_encode($entry['matches']['cookies']) : null,
            'origins' => $this->extractOrigins($entry),
            'extensions' => isset($entry['extensions']) ? json_encode($entry['extensions']) : null,
            'placeholder_title' => $entry['name'] ?? null,
            'placeholder_description' => $entry['placeholderDescription'] ?? $entry['i18n']['placeholderDescription']['en'] ?? null,
            'library_adopted_at' => time(),
        ];

        return $this->serviceRepo->insert($data);
    }

    /**
     * Create a custom service from manual data.
     */
    public function createCustom(array $data): int
    {
        return $this->serviceRepo->insert($data);
    }

    /**
     * Edit an existing service.
     */
    public function update(int $id, array $data): bool
    {
        return $this->serviceRepo->update($id, $data);
    }

    private function extractOrigins(array $entry): string
    {
        $origins = $entry['matches']['origins'] ?? [];
        $aliasOrigins = $entry['matches']['aliasOrigins'] ?? [];
        $merged = array_unique(array_merge($origins, $aliasOrigins));
        return json_encode(array_values($merged));
    }
}
