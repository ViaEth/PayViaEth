<?php
//https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// $option_name = 'plugin_option_name';
 
// delete_option( $option_name );
 
// // For site options in Multisite
// delete_site_option( $option_name );  