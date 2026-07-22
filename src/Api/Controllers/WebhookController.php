<?php

namespace SimpleCMP\WpSimpleCmp\Api\Controllers;

use SimpleCMP\WpSimpleCmp\Service\BridgeSecretProvider;
use SimpleCMP\WpSimpleCmp\Service\BridgeNonceService;
use SimpleCMP\WpSimpleCmp\Service\BridgeRateLimiter;
use SimpleCMP\WpSimpleCmp\Service\WebhookPayloadValidator;
use SimpleCMP\WpSimpleCmp\Database\Repositories\DetectionRepository;

class WebhookController
{
    public static function registerRoutes(): void
    {
        register_rest_route('simplecmp', '/webhook', [
            'methods' => 'POST',
            'callback' => [self::class, 'handle'],
            'permission_callback' => [self::class, 'authenticate'],
        ]);
    }

    public static function authenticate(\WP_REST_Request $request): bool
    {
        $secretProvider = new BridgeSecretProvider();
        if (!$secretProvider->hasSecret()) {
            return true; // No secret configured — allow for setup phase
        }

        $nonce = $request->get_header('X-SimpleCMP-Nonce');
        if (empty($nonce)) {
            return false;
        }

        $source = $request->get_param('_source') ?: $request->get_header('X-SimpleCMP-Source') ?: 'simplecmp-default';

        $nonceService = new BridgeNonceService();
        return $nonceService->verify($nonce, $source);
    }

    public static function handle(\WP_REST_Request $request): \WP_REST_Response
    {
        $limiter = new BridgeRateLimiter();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$limiter->isAllowed('webhook', $ip, 100)) {
            return new \WP_REST_Response(
                ['error' => 'rate_limit_exceeded'],
                429
            );
        }

        $body = $request->get_json_params();
        if (!$body) {
            return new \WP_REST_Response(
                ['error' => 'invalid_json'],
                400
            );
        }

        $validator = new WebhookPayloadValidator();
        $errors = $validator->validate($body);

        if (!empty($errors)) {
            return new \WP_REST_Response(
                ['error' => 'validation_failed', 'details' => $errors],
                400
            );
        }

        global $wpdb;
        $detectionRepo = new DetectionRepository($wpdb);
        $ingested = 0;

        foreach ($body['detections'] as $detection) {
            $detectionRepo->upsert([
                'source' => $detection['source'],
                'kind' => $detection['kind'],
                'identifier' => $detection['identifier'],
                'origin' => $detection['origin'] ?? null,
                'page_url' => $detection['pageUrl'] ?? null,
                'first_seen_on' => $detection['firstSeenOn'] ?? null,
                'sent_at' => $detection['sentAt'] ?? null,
                'library_version' => $detection['libraryVersion'] ?? null,
                'user_agent' => $detection['userAgent'] ?? null,
                'referrer' => $detection['referrer'] ?? null,
                'payload' => json_encode($detection),
            ]);
            $ingested++;
        }

        return new \WP_REST_Response([
            'status' => 'ok',
            'ingested' => $ingested,
        ]);
    }
}
