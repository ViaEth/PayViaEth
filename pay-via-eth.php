<?php
/*
Plugin Name: Payments Via Ethereum 
Plugin URI:  https://viaeth.io
Description: Ethereum Payment Plugin
Version:     0.420.69 
Requires Plugins: woocommerce
Author:      Tyler Thomas
License:     GPLv3
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

// Load required files and classes for the plugin
require_once PVE_DIR . '/wp_wc_pve_logging.php'; //Plugin Logging
require_once PVE_DIR . '/functions.php'; // Load main plugin functions
require_once PVE_DIR . '/etherscan-api/etherscan-functions.php'; // Load etherscan API functions
require_once PVE_DIR . '/admin/ajax/ft_check_transaction_status/ft_check_transaction_status.php'; // Load transaction status check AJAX function
require_once PVE_DIR . '/includes/class-pve-gateway.php'; // Load PVE payment gateway class for WooCommerce
require_once PVE_DIR . '/includes/form-fields.php'; // Load form field classes for the plugin
require_once PVE_DIR . '/includes/db-functions.php'; // Load database functions for the plugin
require_once PVE_DIR . '/ethereum_payments/ethereum_payments-init.php'; // Load Ethereum payments initialization file
require_once PVE_DIR . '/includes/class-pve-init.php'; // Load pay-via-eth initialization file
require_once PVE_DIR . '/includes/class-pve-admin.php'; // Load admin class
require_once PVE_DIR . '/check_transaction_status-cronjob.php'; // Load cron job for checking transaction status

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

