<?php

namespace SimpleCMP\WpSimpleCmp\Tracker;

class MicrosoftUetProvider implements TrackerProviderInterface
{
    public function getType(): string
    {
        return 'microsoft_uet';
    }

    public function getDefaultServiceId(): string
    {
        return 'microsoft-uet';
    }

    public function buildServiceData(array $config): array
    {
        return [
            'service_id' => $this->getDefaultServiceId(),
            'name' => 'Microsoft UET',
            'vendor' => 'Microsoft Corporation',
            'vendor_country' => 'US',
        ];
    }

    public function getLoaderUrl(array $config): string
    {
        $tagId = $config['tag_id'] ?? '';
        return "https://bat.bing.com/bat.js";
    }

    public function getBootstrapInlineScript(array $config): string
    {
        $tagId = wp_json_encode($config['tag_id'] ?? '');
        return "(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){
var o={ti:{$tagId}, enableAutoSpaTracking: true};
o.q=w[u],w[u]=new UET(o),w[u].push('pageLoad')},
n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){
var s=this.readyState;s&&s!=='loaded'&&s!=='complete'||(f(),n.onload=n.onreadystatechange=null)},
i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})
(window,document,'script','https://bat.bing.com/bat.js','uetq');
window.uetq = window.uetq || [];
uetq.push('consent', 'default', {'ad_storage': 'denied'});";
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
