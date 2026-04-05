<?php
/**
* PVE_Init — registers all hooks and filters after plugins_loaded.
* 
* Handles plugin initialisation only. This means running the WooCommerce availability check on plugins_loaded, 
* registering PVE_Gateway with WooCommerce via the woocommerce_payment_gateways filter, 
* and instantiating PVE_Admin when in the admin context. It does not contain any business logic. 
* It does not fetch prices, process payments, or render anything. Its only job is to wire the other classes into WordPress at the correct moment.
*
* Hooks Registered:
*   add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'register_gateway' ) )
*
* Options read: // via get_option()
*   none
*
* Options written: // via update_option() or add_option()
*   none
*
* Order meta read: // via get_post_meta()
*   none
*
* Order meta written: // via update_post_meta()
*   none
*
* Constants defined:
*   none
*
* Files loaded:
*   none
*
* @package Payments_Via_Ethereum
* @since 1.420.69
*/

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

class PVE_Init {

	/**
	 * Initialise the plugin. Called on plugins_loaded.
	 * Returns early if WooCommerce is not active.
	 */
	public static function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		require_once PVE_DIR . 'includes/class-pve-gateway.php'; //Load PVE_Gateway class

		add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'register_gateway' ) );

		if ( is_admin() ) {
			$admin = new PVE_Admin();
			$admin->init();
		}
	}

	/**
	 * Register PVE_Gateway with WooCommerce.
	 *
	 * @param array $gateways Registered payment gateways.
	 * @return array
	 */
	public static function register_gateway( $gateways ) {
		$gateways[] = 'PVE_Gateway';
		return $gateways;
	}
}
