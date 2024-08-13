<?php

namespace WSPC\App\Controller;

defined( "ABSPATH" ) or die();

use WDR\Core\Models\Custom\StoreRule;

class Base {
    /**
     * @var array
     */
    private static $disable_strikeout = [];

    /**
     * Checks available rules.
     *
     * @return bool
     */
    public static function check(): bool
    {
        $rules = StoreRule::getRules();
        if ( empty( $rules ) ) {
            return false;
        }
        $available_rules = [];
        foreach ( $rules as $rule )
        {
            if ($rule->getDiscountContext() == 'item' && !in_array( $rule->getType(), ['buy_x_get_x', 'buy_x_get_y']))
            {
                $available_rules[] = $rule;
            }
        }

        if ( empty( $available_rules ) ) {
            return false;
        }
        return true;
    }

    /**
     * Removed the suppressed hook
     *
     * @param array $hooks list hook name
     *
     * @return array
     */
    public static function removeSuppressedHooks(array $hooks ): array
    {
        if (empty( $hooks )) {
            return $hooks;
        }
        if ( isset( $hooks['woocommerce_get_price_html'] ) ) {
            unset( $hooks['woocommerce_get_price_html'] );
        }
        if ( isset( $hooks['woocommerce_before_calculate_totals'] ) ) {
            unset( $hooks['woocommerce_before_calculate_totals'] );
        }
        return $hooks;
    }

    /**
     *  Strikeout the wholesale price for product page.
     *
     * @param $wholesale_price_html
     * @param $price
     * @param $product
     * @param $user_wholesale_role
     * @param $wholesale_price_title_text
     * @param $raw_wholesale_price
     * @param $source
     *
     * @return string
     */
    public static function renderWholeSalePrice( $wholesale_price_html, $price, $product, $user_wholesale_role, $wholesale_price_title_text, $raw_wholesale_price, $source ): string {
        self::$disable_strikeout[ $product->get_id() ] = true;
        if ( $product->is_type( 'grouped' ) ) {
            return $wholesale_price_html;
        }
	    $converted_price = self::getConvertAmount($raw_wholesale_price);
	    $result = apply_filters( 'wdr_get_product_discounted_price', false, $product, 1, $converted_price );

        return ( $result !== false ) ? "<del>{$wholesale_price_html}</del><ins>{$wholesale_price_title_text} " . wc_price( $result ) . "</ins>"
            : $wholesale_price_html;
    }

    /**
     * Get modified price.
     *
     * @param bool $enable
     * @param $product
     * @param string $price_html
     *
     * @return bool
     */
    public static function renderModifiedPrice( bool $enable, $product, string $price_html ): bool {
        return is_object($product) && !empty($product) && !empty( self::$disable_strikeout[ $product->get_id() ] ) && self::$disable_strikeout[ $product->get_id() ] ? false : $enable;
    }

    /**
     *  Get Wholesale price.
     *
     * @param $product_price
     * @param $product
     * @param $source_id
     * @param $context
     * @return mixed
     */
    public static function getWholesalePrice($product_price, $product, $source_id, $context) {
        $user_id = get_current_user_id();
        $user = !empty($user_id) ? get_userdata($user_id) : 0;
        if (!empty($user) && !in_array('wholesale_customer', (array) $user->roles)) {
            return $product_price;
        }
        return is_object($product) && !empty($product) ? $product->get_meta( 'wholesale_customer_wholesale_price', true ) : 0;
    }


	/**
	 * Convert the price based on currency.
	 *
	 * @param string $price Price.
	 *
	 * @return mixed
	 */
	public static function getConvertAmount($price){
		if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			global $woocommerce_wpml;
			if ( ! defined( 'WCML_MULTI_CURRENCIES_INDEPENDENT' ) ) {
				include_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'wpml-woocommerce' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'constants.php';
			}
			if ( WCML_MULTI_CURRENCIES_INDEPENDENT === $woocommerce_wpml->settings['enable_multi_currency'] ) {
				/**
				 * Wrap raw price with 'wcml_formatted_price' filter so that conversion will be correct.
				 * Only do this only if the currency is not the same as the default currency.
				 */
				$currency = $woocommerce_wpml->multi_currency->get_client_currency();
				if ( $currency !== get_option( 'woocommerce_currency' ) ) {
					return $woocommerce_wpml->multi_currency->prices->raw_price_filter($price, $currency) ;
				}
			}
		}
		return  $price;
	}
}