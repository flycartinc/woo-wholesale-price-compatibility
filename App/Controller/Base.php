<?php

namespace WSPC\App\Controller;

defined("ABSPATH") or die();

class Base
{
    /**
     * @var array
     */
    private static $disable_strikeout = [];

    /**
     * @param $wholesale_price_html
     * @param $price
     * @param $product
     * @param $user_wholesale_role
     * @param $wholesale_price_title_text
     * @param $raw_wholesale_price
     * @param $source
     * @return string
     */
    function renderWholeSalePrice($wholesale_price_html, $price, $product, $user_wholesale_role, $wholesale_price_title_text, $raw_wholesale_price, $source): string
    {
        self::$disable_strikeout[$product->get_id()] = true;
        if ($product->is_type('grouped')) {
            return $wholesale_price_html;
        }
        $result = apply_filters('wdr_get_product_discounted_price', $price, $product, 1, $raw_wholesale_price);
        return ($result !== false) ? "<del>{$wholesale_price_html}</del><ins>{$wholesale_price_title_text} " . wc_price($result) . "</ins>"
            : $wholesale_price_html;
    }

    /**
     * Get modified price.
     *
     * @param bool $enable
     * @param $product
     * @param string $price_html
     * @return bool
     */
    function renderModifiedPrice(bool $enable, $product, string $price_html): bool
    {
        return isset(self::$disable_strikeout[$product->get_id()]) && self::$disable_strikeout[$product->get_id()] ? false : $enable;
    }


    /**
     * @param $price
     * @param $from
     * @param $to
     * @return void
     */
    function setPriceRange($price, $from, $to)
    {
        $result = apply_filters('wdr_get_product_discounted_price', $from, $product, 1, $raw_wholesale_price);
        return $price;
    }
}