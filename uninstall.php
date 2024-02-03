<?php
//https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// remove plugin options
//$option_name = 'pve_option';

//delete_option( $option_name );

// remove plugin transients

// remove plugin cron events

// ..etc., based on what needs to be removed

// drop plugin specific database tables
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}c9wep_ethereum_payments" );
