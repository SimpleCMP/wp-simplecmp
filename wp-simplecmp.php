<?php
/**
 * Plugin Name: SimpleCMP – Consent Manager
 * Plugin URI: https://github.com/SimpleCMP
 * Description: Open-source consent manager with tracker auto-detection, service database, CMS-bridge webhook, and DSGVO-compliant audit trail.
 * Version: 0.1.0
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Author: SimpleCMP
 * License: BSD-3-Clause
 * License URI: https://opensource.org/licenses/BSD-3-Clause
 * Text Domain: wp-simplecmp
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WP_SIMPLECMP_VERSION', '0.1.0');
define('WP_SIMPLECMP_FILE', __FILE__);
define('WP_SIMPLECMP_DIR', plugin_dir_path(__FILE__));
define('WP_SIMPLECMP_URL', plugin_dir_url(__FILE__));

require_once WP_SIMPLECMP_DIR . 'vendor/autoload.php';

register_activation_hook(__FILE__, [\SimpleCMP\WpSimpleCmp\Core\Activator::class, 'activate']);
register_deactivation_hook(__FILE__, [\SimpleCMP\WpSimpleCmp\Core\Deactivator::class, 'deactivate']);

require_once __DIR__ . '/includes/test-shortcodes.php';

\SimpleCMP\WpSimpleCmp\Core\Plugin::getInstance()->boot();
