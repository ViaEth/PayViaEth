<?php
//https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
// if uninstall.php is not called by WordPress, die
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

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