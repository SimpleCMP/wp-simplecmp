<?php

namespace SimpleCMP\WpSimpleCmp\Api\Controllers;

use SimpleCMP\WpSimpleCmp\Service\BridgeSecretProvider;
use SimpleCMP\WpSimpleCmp\Service\BridgeNonceService;
use SimpleCMP\WpSimpleCmp\Service\BridgeRateLimiter;
use SimpleCMP\WpSimpleCmp\Service\ConsentLogPayloadValidator;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ConsentLogRepository;

class ConsentLogController
{
    public static function registerRoutes(): void
    {
        register_rest_route('simplecmp/v1', '/consent-log', [
            'methods' => 'POST',
            'callback' => [self::class, 'handle'],
            'permission_callback' => [self::class, 'authenticate'],
        ]);
    }

    public static function authenticate(\WP_REST_Request $request): bool
    {
        $secretProvider = new BridgeSecretProvider();
        if (!$secretProvider->hasSecret()) {
            return true;
        }

        $nonce = $request->get_header('X-SimpleCMP-Nonce');
        if (empty($nonce)) {
            return false;
        }

        $source = $request->get_header('X-SimpleCMP-Source') ?: 'simplecmp-default';
        $nonceService = new BridgeNonceService();
        return $nonceService->verify($nonce, $source);
    }

    public static function handle(\WP_REST_Request $request): \WP_REST_Response
    {
        $limiter = new BridgeRateLimiter();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$limiter->isAllowed('consent_log', $ip, 50)) {
            return new \WP_REST_Response(
                ['error' => 'rate_limit_exceeded'],
                429
            );
        }

        $body = $request->get_json_params();

        $validator = new ConsentLogPayloadValidator();
        $errors = $validator->validate($body);

        if (!empty($errors)) {
            return new \WP_REST_Response(
                ['error' => 'validation_failed', 'details' => $errors],
                400
            );
        }

        $decisionHash = hash('sha256', $body['decisions_json']);
        $body['decision_hash'] = $decisionHash;

        global $wpdb;
        $consentLogRepo = new ConsentLogRepository($wpdb);
        $inserted = $consentLogRepo->upsert($body);

        return new \WP_REST_Response([
            'status' => 'ok',
            'recorded' => $inserted,
        ]);

    }
}
