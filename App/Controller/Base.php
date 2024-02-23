<?php

namespace WSPC\App\Controller;

defined("ABSPATH") or die();
class Base
{
    private static $disable_strikeout = [];

    function renderWholeSalePrice($wholesale_price_html, $price, $product, $user_wholesale_role, $wholesale_price_title_text, $raw_wholesale_price, $source): string
    {
        self::$disable_strikeout[$product->get_id()] = true;
//        $result = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price',
//            $raw_wholesale_price, $product, 1, $raw_wholesale_price, 'discounted_price', true);
        $result = apply_filters('wdr_modify_product_price_html', true, $product, $wholesale_price_html);
        return ($result !== false) ? "<del>{$wholesale_price_html}</del><ins>{$wholesale_price_title_text} " . wc_price($result) . "</ins>"
            : $wholesale_price_html;
    }

    function renderModifiedPrice($enable, $price_html, $product, $quantity): bool
    {
        return isset(self::$disable_strikeout[$product->get_id()]) && self::$disable_strikeout[$product->get_id()] ? false : $enable;
    }

}