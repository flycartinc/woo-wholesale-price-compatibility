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
     * @param array $hooks
     * @return array
     */
    static function removeSuppressedHooks($hooks){
        if (empty($hooks) || !is_array($hooks)) return $hooks;
        if (isset($hooks['woocommerce_get_price_html'])) unset($hooks['woocommerce_get_price_html']);
        if (isset($hooks['woocommerce_before_calculate_totals'])) unset($hooks['woocommerce_before_calculate_totals']);
        return $hooks;
    }

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
    static function renderWholeSalePrice($wholesale_price_html, $price, $product, $user_wholesale_role, $wholesale_price_title_text, $raw_wholesale_price, $source): string
    {
        self::$disable_strikeout[$product->get_id()] = true;
        if ($product->is_type('grouped')) {
            return $wholesale_price_html;
        }
        $result = apply_filters('wdr_get_product_discounted_price', false, $product, 1, $raw_wholesale_price);
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
    static function renderModifiedPrice(bool $enable, $product, string $price_html): bool
    {
        return isset(self::$disable_strikeout[$product->get_id()]) && self::$disable_strikeout[$product->get_id()] ? false : $enable;
    }

}