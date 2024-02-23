<?php

namespace WSPC\App;
use WDR\Core\Helpers\Plugin;
use WSPC\App\Controller\Base;

defined("ABSPATH") or die();
class Router
{
    /**
     * @var Base
     */
    private static $main;

    function init()
    {
        if (!Plugin::isActive('woocommerce-wholesale-prices/woocommerce-wholesale-prices.bootstrap.php')
            || !Plugin::isActive('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php')) {
            return;
        }

        self::$main = empty(self::$main) ? new Base() : self::$main;

        add_filter('wwp_filter_wholesale_price_html', [self::$main, 'renderWholeSalePrice'], 10, 7);

        add_filter('advanced_woo_discount_rules_modify_price_html', [self::$main, 'renderModifiedPrice'], 10, 4);

    }
}