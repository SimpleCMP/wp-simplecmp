<?php

namespace SimpleCMP\WpSimpleCmp\Tracker;

class TrackerRegistry
{
    /** @var TrackerProviderInterface[] */
    private array $providers = [];

    public function __construct()
    {
        // Default providers are registered via filter
        $this->providers = apply_filters('simplecmp_tracker_providers', []);
    }

    public function register(TrackerProviderInterface $provider): void
    {
        $this->providers[$provider->getType()] = $provider;
    }

    /**
     * @return TrackerProviderInterface[]
     */
    public function all(): array
    {
        return $this->providers;
    }

    public function get(string $type): ?TrackerProviderInterface
    {
        return $this->providers[$type] ?? null;
    }
}
