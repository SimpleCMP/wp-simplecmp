<?php

namespace SimpleCMP\WpSimpleCmp\Api\Controllers;

use SimpleCMP\WpSimpleCmp\Service\BridgeNonceService;
use SimpleCMP\WpSimpleCmp\Service\BridgeRateLimiter;

class BridgeNonceController
{
    public static function registerRoutes(): void
    {
        register_rest_route('simplecmp/v1', '/bridge-nonce', [
            'methods' => 'GET',
            'callback' => [self::class, 'handle'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function handle(\WP_REST_Request $request): \WP_REST_Response
    {
        $limiter = new BridgeRateLimiter();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$limiter->isAllowed('bridge_nonce', $ip, 30)) {
            return new \WP_REST_Response(
                ['error' => 'rate_limit_exceeded'],
                429
            );
        }

        $source = $request->get_param('source') ?: 'simplecmp-default';

        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $source)) {
            return new \WP_REST_Response(
                ['error' => 'invalid_source'],
                400
            );
        }

        $nonceService = new BridgeNonceService();
        $result = $nonceService->issue($source);

        if (isset($result['error'])) {
            $status = $result['error'] === 'bridge_secret_unconfigured' ? 503 : 400;
            return new \WP_REST_Response($result, $status);
        }

        $response = new \WP_REST_Response($result);
        $response->header('Cache-Control', 'no-store');

        return $response;
    }
}
