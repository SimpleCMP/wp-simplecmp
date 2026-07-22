<?php

namespace SimpleCMP\WpSimpleCmp\Tracker;

class MetaProvider implements TrackerProviderInterface
{
    public function getType(): string
    {
        return 'meta';
    }

    public function getDefaultServiceId(): string
    {
        return 'meta-pixel';
    }

    public function buildServiceData(array $config): array
    {
        return [
            'service_id' => $this->getDefaultServiceId(),
            'name' => 'Meta Pixel',
            'vendor' => 'Meta Platforms, Inc.',
            'vendor_country' => 'US',
        ];
    }

    public function getLoaderUrl(array $config): string
    {
        $pixelId = $config['pixel_id'] ?? '';
        return "https://connect.facebook.net/en_US/fbevents.js";
    }

    public function getBootstrapInlineScript(array $config): string
    {
        $pixelId = wp_json_encode($config['pixel_id'] ?? '');
        return "!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', {$pixelId});
fbq('consent', 'revoke');";
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
