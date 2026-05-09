<?php
/**
 * Payments Via Ethereum — Uninstall.
 *
 * Runs only when the plugin is deleted (not deactivated). Removes all
 * plugin-specific options, transients, and the log directory. Invoked
 * by WordPress via the WP_UNINSTALL_PLUGIN constant — file is never
 * loaded in the normal request lifecycle.
 *
 * Reference: https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
 *
 * Hooks registered:
 *   none
 *
 * Options read:
 *   none
 *
 * Options written:
 *   none
 *
 * Options deleted:
 *   pve_eth_price_history — rolling 50-entry ETH price history (created P04.T1.2)
 *   pve_eth_fetch_failures — counter for ETH price fetch failures (created P04.T1.4)
 *   woocommerce_pve_gateway_settings — WooCommerce gateway settings (merchant addresses, block explorer URL)
 *
 * Transients deleted:
 *   pve_eth_usd_price — cached ETH/USD price (created P04.T1.1)
 *
 * Order meta read:
 *   none
 *
 * Order meta written:
 *   none
 *
 * Constants defined:
 *   none
 *
 * Constants used:
 *   WP_UNINSTALL_PLUGIN — entry guard, defined by WordPress when file invoked during uninstall
 *
 * Files loaded:
 *   wp-admin/includes/file.php — for WP_Filesystem (lazy-loaded)
 *
 * Filesystem operations:
 *   wp-content/uploads/pve-logs/ — recursively removed via WP_Filesystem->rmdir()
 *
 * @package Payments_Via_Ethereum
 * @since   1.420.69
 */

// WP_UNINSTALL_PLUGIN guard, must be first executable line, no exception
defined( 'WP_UNINSTALL_PLUGIN' ) || exit; //If uninstall.php is not called by WordPress, die

//Remove Plugin Options
$options = array(
	'pve_eth_price_history',
	'pve_eth_fetch_failures',
	'woocommerce_pve_gateway_settings'//WooCommerce gateway plugin settings
);
foreach ( $options as $option ) {
	delete_option( $option );
}

//Remove Plugin Transients
delete_transient( 'pve_eth_usd_price' ); //Should be a for each loop at some point

//Remove plugin logs from wp-content/uploads/pve-logs/
$upload_dir = wp_upload_dir();
$log_dir    = trailingslashit( $upload_dir['basedir'] ) . 'pve-logs';

global $wp_filesystem;
if ( empty( $wp_filesystem ) ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
}

if ( $wp_filesystem->is_dir( $log_dir ) ) {
	$wp_filesystem->rmdir( $log_dir, true ); // true = recursive, removes directory and all contents
}

// ..etc., based on what needs to be removed
