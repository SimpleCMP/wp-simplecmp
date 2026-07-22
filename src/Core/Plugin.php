<?php

namespace SimpleCMP\WpSimpleCmp\Core;

use SimpleCMP\WpSimpleCmp\Admin\Menu;
use SimpleCMP\WpSimpleCmp\Frontend\Assets as FrontendAssets;
use SimpleCMP\WpSimpleCmp\Frontend\Rewriter;
use SimpleCMP\WpSimpleCmp\Frontend\CspHeaders;
use SimpleCMP\WpSimpleCmp\Api\Router as ApiRouter;
use SimpleCMP\WpSimpleCmp\Service\ClassifierLookup;
use SimpleCMP\WpSimpleCmp\Service\EffectiveSettingsResolver;
use SimpleCMP\WpSimpleCmp\CLI\Commands;

class Plugin
{
    private static ?self $instance = null;

    private array $services = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function boot(): void
    {
        add_action('init', [$this, 'initPlugin']);
        add_action('admin_menu', [Menu::class, 'register']);
        add_action('admin_enqueue_scripts', [\SimpleCMP\WpSimpleCmp\Admin\Assets::class, 'enqueue']);
        add_action('wp_enqueue_scripts', [FrontendAssets::class, 'enqueue']);
        add_action('init', function() {
            if (is_admin() || wp_doing_ajax() || wp_doing_cron()) return;
            $resolver = new \SimpleCMP\WpSimpleCmp\Service\EffectiveSettingsResolver();
            if ($resolver->get('simplecmp_universal_blocking_enabled', false)) {
                ob_start(function ($buf) {
                    return \SimpleCMP\WpSimpleCmp\Frontend\Rewriter::rewrite($buf);
                });
            }
        }, 1);
        add_filter('wp_headers', [CspHeaders::class, 'extend']);
        add_action('rest_api_init', [ApiRouter::class, 'register']);

        load_plugin_textdomain('wp-simplecmp', false, dirname(plugin_basename(WP_SIMPLECMP_FILE)) . '/languages');

        if (\defined('WP_CLI') && \WP_CLI) {
            Commands::register();
        }
    }

    public function initPlugin(): void
    {
    }

    /**
     * Get a service instance by class name (simple service locator).
     */
    public function get(string $class): object
    {
        if (!isset($this->services[$class])) {
            $this->services[$class] = new $class();
        }
        return $this->services[$class];
    }
}
