<?php

namespace WSPC\App;

defined( "ABSPATH" ) or die();

use WSPC\App\Controller\Base;

class Router {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_loaded', function () {
			if (Base::check()) {
				add_filter( 'wwp_filter_wholesale_price_html', 'WSPC\App\Controller\Base::renderWholeSalePrice', 10, 7 );
				add_filter( 'wdr_modify_product_price_html', 'WSPC\App\Controller\Base::renderModifiedPrice', 10, 3 );
            }
            add_filter( 'wdr_discount_get_product_price', 'WSPC\App\Controller\Base::getWholesalePrice', 10, 4 );
            add_filter( 'wdr_suppress_allowed_hooks', 'WSPC\App\Controller\Base::removeSuppressedHooks', 10, 1 );
		} );
	}
}