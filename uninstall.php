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

//Remove Plugin Logs (At some point pve logs should be moved and stored in wp-content/logs/pve/)
$log_dir = WP_CONTENT_DIR . '/uploads/pve-logs/';
if ( is_dir( $log_dir ) ) {
    $files = glob( $log_dir . '*.log' );
    if ( $files ) {
        foreach ( $files as $file ) {
            wp_delete_file( $file );
        }
    }
    rmdir( $log_dir );
}
// ..etc., based on what needs to be removed

