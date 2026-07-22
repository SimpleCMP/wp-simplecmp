<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class BridgeNonceService
{
    private BridgeSecretProvider $secretProvider;
    private const TTL = 3600; // 1 hour

    public function __construct()
    {
        $this->secretProvider = new BridgeSecretProvider();
    }

    public function issue(string $source): array
    {
        $secret = $this->secretProvider->get();

        if (!$secret) {
            return ['error' => 'bridge_secret_unconfigured'];
        }

        // Validate source charset
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $source)) {
            return ['error' => 'invalid_source'];
        }

        $timestamp = time();
        $nonce = $this->computeNonce($secret, $source, $timestamp);

        return [
            'token' => $nonce,
            'expiresInMs' => self::TTL * 1000,
            'source' => $source,
            'issuedAt' => $timestamp,
        ];
    }

    public function verify(string $nonce, string $source): bool
    {
        $secret = $this->secretProvider->get();
        if (!$secret) return false;

        // Validate source charset (consistent with issue())
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $source)) {
            return false;
        }

        // Try current and previous tick to account for clock drift
        $now = time();
        for ($ts = $now; $ts >= $now - 5; $ts--) {
            if (hash_equals($this->computeNonce($secret, $source, $ts), $nonce)) {
                // Check TTL
                return ($now - $ts) <= self::TTL;
            }
        }

        return false;
    }

    private function computeNonce(string $secret, string $source, int $timestamp): string
    {
        $interval = floor($timestamp / self::TTL);
        return hash_hmac('sha256', $source . ':' . $interval, $secret);
    }
}
