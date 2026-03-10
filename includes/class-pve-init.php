<?php
/**
 * Plugin initialisation — registers all hooks and filters after plugins_loaded.
 *
 * @package Payments_Via_Ethereum
 */

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