<?php
/*
* Plugin Name:       Payments Via Ethereum
* Plugin URI:        https://viaeth.io
* Description:       Ethereum Payment Plugin
* Version:           1.420.69
* Requires at least: 6.4
* Requires PHP:      8.0
* Requires Plugins:  woocommerce
* Author:            Tyler Thomas
* License:           GPLv3
* License URI:       https://www.gnu.org/licenses/gpl-3.0.html
* Domain Path:       /languages
* Text Domain:       pay-via-eth
*/

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

/**
* Payments Via Ethereum — Bootstrap
*
* Main plugin file. Responsible for bootstrap only — defines constants,
* loads required files, and registers top-level hooks. No business logic lives here.
*
* Hooks registered:
*   add_action('admin_notices', ... ) //conditional, only fires if a required file is missing. Doesn't check for class-pve-gateway. Will change at some point.
*   add_action( 'plugins_loaded', 'pve_load_plugin_textdomain' )
*   add_action( 'plugins_loaded', array( 'PVE_Init', 'init' ) )
*   add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pve_plugin_add_settings_link' )
*   register_activation_hook(   __FILE__, 'pve_activation' )
*   register_deactivation_hook( __FILE__, 'pve_deactivation' )
*
* Options read:        // via get_option()
*   none
*
* Options written:     // via update_option() or add_option()
*   none
*
* Order meta read:     // via get_post_meta()
*   none
*
* Order meta written:  // via update_post_meta()
*   none
*
* Constants defined:
*   PVE_VERSION — plugin version
*   PVE_URL — plugin directory URL
*   PVE_DIR — plugin directory path
*
* Files loaded:
*   wp_wc_pve_logging.php
*   includes/class-pve-converter.php
*   includes/class-pve-price.php
*   includes/class-pve-admin.php
*   includes/class-pve-init.php
*   Note: class-pve-gateway.php loaded by PVE_Init::init() after plugins_loaded
*
* @package Payments_Via_Ethereum
* @since   1.420.69
*/

//Constants, defined before anything that might need them
define( 'PVE_VERSION', get_file_data( __FILE__, array( 'Version' => 'Version' ) )['Version'] );
define('PVE_URL', plugin_dir_url( __FILE__ ));// Define constant for the plugin directory URL
define('PVE_DIR', plugin_dir_path( __FILE__ ));// Define constant for the plugin directory path

//Load required files and classes for the plugin, if a required file is missing the plugin auto deactivates.
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

//Hooks, registered after files are loaded so callbacks exist.
add_action( 'plugins_loaded', 'pve_load_plugin_textdomain' );//Adds an action to load the plugin's text domain when plugins are loaded.
add_action( 'plugins_loaded', array( 'PVE_Init', 'init' ) );//Initialises the plugin after all plugins have loaded — ensures WooCommerce is available before PVE_Init::init() runs.
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'pve_plugin_add_settings_link' );//Add the settings link filter to the plugin action links for this plugin

//Activation and Deactivation hooks
register_activation_hook(__FILE__, 'pve_activation');//Register activation hook for this plugin to be called upon activation.
register_deactivation_hook(__FILE__, 'pve_deactivation');//Register deactivation hook for this plugin to be called upon deactivation.

//Function Definitions, callbacks referenced in hooks above
function pve_load_plugin_textdomain() {
	load_plugin_textdomain( 'pay-via-eth', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}//Loads plugin textdomain.
function pve_plugin_add_settings_link( $links ) {
	// Set the URL for the settings page
	$url=admin_url('admin.php?page=wc-settings&tab=checkout&section=pve_gateway');
	// Create the settings link HTML
	$settings_link = '<a href="'.$url.'">' . __( 'Settings' ) . '</a>';
	// Add the settings link to the beginning of the $links array
	array_unshift($links, $settings_link);
	// Return the modified $links array
	return $links;
}//Adds a settings link to the plugin action links on the WordPress plugin page.
function pve_activation() {
	//Nothing to do.
}//Function called on plugin activation.
function pve_deactivation() {
	//Nothing to do. Cron removed for manual verification per specs.
	//Data preserved intentionally. Uninstall.php handles cleanup on delete.
}//Function called on plugin deactivation.

