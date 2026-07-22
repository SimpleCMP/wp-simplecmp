<?php

namespace SimpleCMP\WpSimpleCmp\Tracker;

interface TrackerProviderInterface
{
    public function getType(): string;
    public function getDefaultServiceId(): string;
    public function buildServiceData(array $config): array;
    public function getLoaderUrl(array $config): string;
    public function getBootstrapInlineScript(array $config): string;
    public function wantsLoadGate(): bool;
    public function wantsConsentMode(): bool;
}
