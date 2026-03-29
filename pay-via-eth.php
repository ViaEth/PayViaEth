<?php
/*
Plugin Name: Payments Via Ethereum 
Plugin URI:  https://viaeth.io
Description: Ethereum Payment Plugin
Version:	 1.420.69 
Requires Plugins: woocommerce
Author:		 Tyler Thomas
License:	 GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: pay-via-eth
*/

defined( 'ABSPATH' ) || exit;

// Define constant for the plugin directory URL
define('PVE_URL', plugin_dir_url( __FILE__ ));

// Define constant for the plugin directory path
define('PVE_DIR', plugin_dir_path( __FILE__ ));

// Adds an action to load the plugin's text domain when plugins are loaded.
add_action( 'plugins_loaded', 'pve_load_plugin_textdomain' );
function pve_load_plugin_textdomain() {
	load_plugin_textdomain( 'pay-via-eth', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Load required files and classes for the plugin, if a required file is missing the plugin auto deactivates.
foreach ( array(
	'wp_wc_pve_logging.php',//Plugin Logging, Will be transformed into pve specific logging at some point.
	'includes/class-pve-converter.php',
	'includes/class-pve-price.php',
	'includes/class-pve-admin.php',//Load admin class
	'includes/class-pve-init.php',//Load pay-via-eth initialization files including the PVE_Gateway class
) as $file ) {
	if ( ! file_exists( PVE_DIR . $file ) ) {
		add_action( 'admin_notices', function() use ( $file ) {
			echo '<div class="notice notice-error"><p>' .
				esc_html( 'Payments Via Ethereum has been deactivated — required file missing: ' . $file ) .
				'</p></div>';
		} );
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		deactivate_plugins( plugin_basename( __FILE__ ) );
		return;
	}
	require_once PVE_DIR . $file;
}

// Initialises the plugin after all plugins have loaded — ensures WooCommerce is available before PVE_Init::init() runs.
add_action( 'plugins_loaded', array( 'PVE_Init', 'init' ) );

// Register activation hook for this plugin to be called upon activation.
register_activation_hook(__FILE__, 'pve_activation');
// Function called on plugin activation.
function pve_activation() {
	//Nothing to do.
}

// Register deactivation hook for this plugin to be called upon deactivation.
register_deactivation_hook(__FILE__, 'pve_deactivation');
// Function called on plugin deactivation.
function pve_deactivation() {
	//Nothing to do. Cron removed for manual verification per specs.
	//Data preserved intentionally. Uninstall.php handles cleanup on delete.
}

// Adds a settings link to the plugin action links on the WordPress plugin page
// Add the settings link filter to the plugin action links for this plugin
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'pve_plugin_add_settings_link' );
function pve_plugin_add_settings_link( $links ) {
	// Set the URL for the settings page
	$url=admin_url('admin.php?page=wc-settings&tab=checkout&section=pve_gateway');
	// Create the settings link HTML
	$settings_link = '<a href="'.$url.'">' . __( 'Settings' ) . '</a>';
	// Add the settings link to the beginning of the $links array
	array_unshift($links, $settings_link);
	// Return the modified $links array
	return $links;
}

