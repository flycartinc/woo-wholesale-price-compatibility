<?php
/**
 * Plugin Name:         Woo wholesale price compatibility
 * Plugin URI:          https://www.flycart.org
 * Description:         This add-on used for woocommerce wholesale prices RymeraWebCo compatibility for woo discount rules.
 * Version:             1.0.0
 * Requires at least:   5.3
 * Requires PHP:        5.6
 * Author:              Flycart
 * Author URI:          https://www.flycart.org
 * Slug:                woo-wholesale-price-compatibility
 * Text Domain:         woo-wholesale-price-compatibility
 * Domain path:         /i18n/languages/
 * License:             GPL v3 or later
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 4.3
 * WC tested up to:     8.0
 */

defined("ABSPATH") or die();
/**
 * Check woocommerce plugin active or not.
 */
if (!function_exists('isWoocommerceActive')) {
    function isWoocommerceActive()
    {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins', array()));
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array('woocommerce/woocommerce.php', $active_plugins, false) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }
}
/**
 * Check Discount rules plugin active or not.
 */
if (!function_exists('isDiscountRulesActive')) {
    function isDiscountRulesActive()
    {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins', array()));
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array('woo-discount-rules-pro/woo-discount-rules-pro.php', $active_plugins, false) || in_array('woo-discount-rules/woo-discount-rules.php', $active_plugins, false);
    }
}

if (!isWoocommerceActive() || !isDiscountRulesActive()) return;
/**
 * Check discount rules plugin is latest.
 */
if (!function_exists('isWooDiscountLatestVersion')) {
    function isWooDiscountLatestVersion()
    {
        $db_version = get_option('wdr_version', '');
        if (defined('WDR_PLUGIN_VERSION') && !empty($db_version)) {
            return (version_compare($db_version, WDR_PLUGIN_VERSION, '>='));
        }
        return false;
    }
}
if (!isWooDiscountLatestVersion()) return;
if (!class_exists('\WDR\Core\Helpers\Plugin') && file_exists(WP_PLUGIN_DIR . '/woo-discount-rules/vendor/autoload.php')) {
    require_once WP_PLUGIN_DIR . '/woo-discount-rules/vendor/autoload.php';
} elseif (file_exists(WP_PLUGIN_DIR . '/woo-discount-rules-pro/vendor/autoload.php')) {
    require_once WP_PLUGIN_DIR . '/woo-discount-rules-pro/vendor/autoload.php';
}
if (!class_exists('\WDR\Core\Helpers\Plugin')) {
    return;
}
$plugin = new \WDR\Core\Helpers\Plugin();
$check = $plugin::isActive('woocommerce-wholesale-prices/woocommerce-wholesale-prices.bootstrap.php')
    || $plugin::isActive('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php');
if (!$check) return;

defined('WSPC_PLUGIN_NAME') or define('WSPC_PLUGIN_NAME', 'Woo wholesale prices compatibility');
defined('WSPC_PLUGIN_VERSION') or define('WSPC_PLUGIN_VERSION', '1.0.0');
defined('WSPC_PLUGIN_SLUG') or define('WSPC_PLUGIN_SLUG', 'woo-wholesale-price-compatibility');

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    return;
}
require __DIR__ . '/vendor/autoload.php';

if (!function_exists('checkRulesExists')) {
    function checkRulesExists()
    {
        $rules = \WDR\Core\Models\Custom\StoreRule::getRules('item');
        if (empty($rules)) {
            return false;
        }
        return true;
    }
}
if (!checkRulesExists()) return;

if (class_exists(\WSPC\App\Router::class)) {
    $plugin = new \WSPC\App\Router();
    if (method_exists($plugin, 'init')) $plugin->init();
}


