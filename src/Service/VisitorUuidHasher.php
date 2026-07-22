<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class VisitorUuidHasher
{
    private BridgeSecretProvider $secretProvider;

    public function __construct()
    {
        $this->secretProvider = new BridgeSecretProvider();
    }

    /**
     * Pseudonymize a visitor UUID via HMAC-SHA256 with the bridge secret.
     */
    public function hash(string $uuid): string
    {
        $secret = $this->secretProvider->get();
        if (empty($secret)) {
            throw new \RuntimeException('Bridge secret not configured');
        }

        return hash_hmac('sha256', $uuid, $secret);
    }
}
