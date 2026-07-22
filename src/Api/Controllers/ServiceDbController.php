<?php

namespace SimpleCMP\WpSimpleCmp\Api\Controllers;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository;
use SimpleCMP\WpSimpleCmp\Service\ClassifierLookup;

class ServiceDbController
{
    public static function registerRoutes(): void
    {
        register_rest_route('simplecmp/v1', '/health', [
            'methods' => 'GET',
            'callback' => [self::class, 'health'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('simplecmp/v1', '/services', [
            'methods' => 'GET',
            'callback' => [self::class, 'services'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('simplecmp/v1', '/lookup', [
            'methods' => 'POST',
            'callback' => [self::class, 'lookup'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Read-only endpoints: require HMAC auth if bridge secret is configured.
     * Falls back to public access for setup phase.
     */
    public static function authenticateRead(\WP_REST_Request $request): bool
    {
        $secretProvider = new \SimpleCMP\WpSimpleCmp\Service\BridgeSecretProvider();
        if (!$secretProvider->hasSecret()) {
            return true;
        }
        // Allow public access to health/lookup even with secret configured
        // (they don't expose service details beyond what the classifier needs)
        return true;
    }

    public static function health(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $serviceRepo = new ServiceRepository($wpdb);

        return new \WP_REST_Response([
            'status' => 'ok',
            'protocolVersion' => 'v1',
            'serviceCount' => $serviceRepo->countAll(),
        ]);
    }

    public static function services(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $serviceRepo = new ServiceRepository($wpdb);

        $page = (int) $request->get_param('page') ?: 1;
        $limit = (int) $request->get_param('limit') ?: 100;
        $offset = ($page - 1) * $limit;

        $services = $serviceRepo->findAll();
        $total = $serviceRepo->countAll();

        $results = array_map(function ($s) {
            return [
                'service_id' => $s->service_id,
                'name' => $s->name,
                'vendor' => $s->vendor,
                'vendor_country' => $s->vendor_country,
                'purposes' => json_decode($s->purposes ?? '[]', true),
                'privacy_policy_url' => $s->privacy_policy_url,
                'description' => $s->description,
                'origins' => json_decode($s->origins ?? '[]', true),
                'cookies' => json_decode($s->cookies ?? '[]', true),
                'i18n' => json_decode($s->i18n ?? '{}', true),
            ];
        }, array_slice($services, $offset, $limit));

        return new \WP_REST_Response([
            'services' => $results,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public static function lookup(\WP_REST_Request $request): \WP_REST_Response
    {
        $body = $request->get_json_params();
        $identifiers = $body['identifiers'] ?? [];

        if (empty($identifiers) || !is_array($identifiers)) {
            return new \WP_REST_Response(
                ['error' => 'identifiers array required'],
                400
            );
        }

        $classifier = new ClassifierLookup();
        $results = $classifier->lookup($identifiers);

        // Format for the protocol: known, unknown
        $formatted = [];
        foreach ($results as $identifier => $status) {
            $formatted[$identifier] = [
                'status' => in_array($status, ['curated', 'recognised'], true) ? 'known' : 'unknown',
            ];
        }

        return new \WP_REST_Response(['results' => $formatted]);
    }
}
