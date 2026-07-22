<?php

namespace SimpleCMP\WpSimpleCmp\Api;

class Router
{
    public static function register(): void
    {
        $controllers = [
            Controllers\ServiceDbController::class,
            Controllers\WebhookController::class,
            Controllers\ConsentLogController::class,
            Controllers\BridgeNonceController::class,
        ];

        foreach ($controllers as $controllerClass) {
            if (method_exists($controllerClass, 'registerRoutes')) {
                $controllerClass::registerRoutes();
            }
        }
    }
}
