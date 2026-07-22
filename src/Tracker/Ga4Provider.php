<?php

namespace SimpleCMP\WpSimpleCmp\Tracker;

class Ga4Provider implements TrackerProviderInterface
{
    public function getType(): string
    {
        return 'ga4';
    }

    public function getDefaultServiceId(): string
    {
        return 'google-analytics';
    }

    public function buildServiceData(array $config): array
    {
        return [
            'service_id' => $this->getDefaultServiceId(),
            'name' => 'Google Analytics 4',
            'vendor' => 'Google LLC',
            'vendor_country' => 'US',
        ];
    }

    public function getLoaderUrl(array $config): string
    {
        $measurementId = $config['measurement_id'] ?? '';
        return "https://www.googletagmanager.com/gtag/js?id={$measurementId}";
    }

    public function getBootstrapInlineScript(array $config): string
    {
        $measurementId = wp_json_encode($config['measurement_id'] ?? '');
        return "window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', {$measurementId});
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'analytics_storage': 'denied',
    'functionality_storage': 'granted',
    'personalization_storage': 'denied',
    'security_storage': 'granted',
});";
    }

    public function wantsLoadGate(): bool
    {
        return true;
    }

    public function wantsConsentMode(): bool
    {
        return true;
    }
}
