<?php
//https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// remove plugin options
$wp_wc_pve_options = array('woocommerce_ethereumpay_settings', 'c9wep_ethereum_payments_db_version');
foreach ( $wp_wc_pve_options as $option ) {
	delete_option( $option );
}

// remove plugin transients

// remove plugin cron events

// ..etc., based on what needs to be removed

// drop plugin specific database tables
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}c9wep_ethereum_payments" );


