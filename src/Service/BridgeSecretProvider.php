<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class BridgeSecretProvider
{
    public function get(): string
    {
        $secret = get_option('simplecmp_bridge_secret', '');
        if (!$secret && defined('SIMPLECMP_BRIDGE_SECRET')) {
            $secret = SIMPLECMP_BRIDGE_SECRET;
        }
        return $secret;
    }

    public function hasSecret(): bool
    {
        return $this->get() !== '';
    }

    public function setSecret(string $secret): void
    {
        update_option('simplecmp_bridge_secret', $secret);
    }

    public function generate(): string
    {
        $secret = bin2hex(random_bytes(32));
        $this->setSecret($secret);
        return $secret;
    }
}
