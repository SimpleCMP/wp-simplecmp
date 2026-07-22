<?php

namespace SimpleCMP\WpSimpleCmp\Tracker;

class GtmProvider implements TrackerProviderInterface
{
    public function getType(): string
    {
        return 'gtm';
    }

    public function getDefaultServiceId(): string
    {
        return 'google-tag-manager';
    }

    public function buildServiceData(array $config): array
    {
        return [
            'service_id' => $this->getDefaultServiceId(),
            'name' => 'Google Tag Manager',
            'vendor' => 'Google LLC',
            'vendor_country' => 'US',
        ];
    }

    public function getLoaderUrl(array $config): string
    {
        $containerId = $config['container_id'] ?? '';
        return "https://www.googletagmanager.com/gtm.js?id={$containerId}";
    }

    public function getBootstrapInlineScript(array $config): string
    {
        $containerId = wp_json_encode($config['container_id'] ?? '');
        return "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});})(window,document,'script','dataLayer',{$containerId});
gtag('config', {$containerId});
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
