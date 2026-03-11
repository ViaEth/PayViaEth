<?php
//https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
// if uninstall.php is not called by WordPress, die
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// remove plugin options
$wp_wc_pve_options = array('woocommerce_ethereumpay_settings', 'pve_ethereum_payments_db_version');
foreach ( $wp_wc_pve_options as $option ) {
	delete_option( $option );
}

// remove plugin options
delete_option( 'pve_eth_price_history' );
delete_option( 'pve_eth_fetch_failures' );

// remove plugin transients
delete_transient( 'pve_eth_usd_price' );

// WooCommerce gateway plugin
delete_option( 'woocommerce_pve_gateway_settings' );

// remove plugin logs

// ..etc., based on what needs to be removed

