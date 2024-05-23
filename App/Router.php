<?php

namespace WSPC\App;

use WSPC\App\Controller\Base;

defined("ABSPATH") or die();

class Router
{
    /**
     * @var Base
     */
    private static $main;

    /**
     * @return void
     */
    function init()
    {
        self::$main = empty(self::$main) ? new Base() : self::$main;
        if (self::$main->check()) {
            add_filter('wwp_filter_wholesale_price_html', [self::$main, 'renderWholeSalePrice'], 10, 7);
            add_filter('wdr_modify_product_price_html', [self::$main, 'renderModifiedPrice'], 10, 3);
        }
        add_filter('wdr_suppress_allowed_hooks', [self::$main, 'removeSuppressedHooks'], 10, 1);
    }
}