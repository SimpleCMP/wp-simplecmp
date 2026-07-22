<?php

namespace SimpleCMP\WpSimpleCmp\Tracker;

class MatomoProvider implements TrackerProviderInterface
{
    public function getType(): string
    {
        return 'matomo';
    }

    public function getDefaultServiceId(): string
    {
        return 'matomo';
    }

    public function buildServiceData(array $config): array
    {
        return [
            'service_id' => $this->getDefaultServiceId(),
            'name' => 'Matomo',
            'vendor' => 'InnoCraft Ltd',
            'vendor_country' => 'NZ',
        ];
    }

    public function getLoaderUrl(array $config): string
    {
        $url = rtrim($config['matomo_url'] ?? '', '/');
        return $url . '/matomo.js';
    }

    public function getBootstrapInlineScript(array $config): string
    {
        $siteId = wp_json_encode($config['site_id'] ?? '');
        $url = wp_json_encode(rtrim(($config['matomo_url'] ?? ''), '/'));
        return "var _paq = window._paq = window._paq || [];
_paq.push(['setSiteId', {$siteId}]);
_paq.push(['setTrackerUrl', {$url} + '/matomo.php']);
(function() {
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src={$url} + '/matomo.js'; s.parentNode.insertBefore(g,s);
})();";
    }

    public function wantsLoadGate(): bool
    {
        return true;
    }

    public function wantsConsentMode(): bool
    {
        return false;
    }
}
