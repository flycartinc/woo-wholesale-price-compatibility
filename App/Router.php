<?php

namespace WSPC\App;

use WDR\Core\Helpers\Settings;

defined( "ABSPATH" ) or die();

class Router {

	/**
	 * @return void
	 */
	public static function init() {

		add_action( 'wp_loaded', function () {

			if ( \WSPC\App\Controller\Base::check() ) {
				add_filter( 'wwp_filter_wholesale_price_html', 'WSPC\App\Controller\Base::renderWholeSalePrice', 10, 7 );
				add_filter( 'wdr_modify_product_price_html', 'WSPC\App\Controller\Base::renderModifiedPrice', 10, 3 );
			}
			if ( Settings::get( 'suppress_other_discount_plugins' ) ) {
				add_filter( 'wdr_suppress_allowed_hooks', 'WSPC\App\Controller\Base::removeSuppressedHooks', 10, 1 );
			}

		} );
	}
}