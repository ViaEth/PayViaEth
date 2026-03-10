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

// Define constant for the plugin directory URL
define('PVE_URL', plugin_dir_url( __FILE__ ));

// Define constant for the plugin directory path
define('PVE_DIR', dirname( __FILE__ ));

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
require_once PVE_DIR . '/ethereumpay/ethereumpay-init.php'; // Load EthereumPay initialization file
require_once PVE_DIR . '/check_transaction_status-cronjob.php'; // Load cron job for checking transaction status

// This code section checks if the user is in the WordPress admin area.
// If the user is in the admin area, it loads the admin.php file.
// If the user is not in the admin area, it does not load anything.
if ( is_admin() ) {
    require_once PVE_DIR .'/admin/admin.php';
}

// Register activation hook for this plugin to be called upon activation.
register_activation_hook(__FILE__, 'pve_activation');
// Function called on plugin activation.
function pve_activation() {
    //Logs that the plugin has been activated.
    if (! defined('WC_Version')){
	return;
    }
    $log_string = ('Plugin Activated with WooComerce Version: '.WC_VERSION);
    wp_wc_pve_write_log($log_string, E_USER_NOTICE);
}

// Register deactivation hook for this plugin to be called upon deactivation.
register_deactivation_hook(__FILE__, 'pve_deactivation');
// Function called on plugin deactivation.
function pve_deactivation() {
    // Clear any scheduled cron jobs for checking transaction status
    wp_clear_scheduled_hook('pve_check_transaction_status_cron_hook');
    //Logs that the plugin has been deactivated.
    wp_wc_pve_write_log('Plugin Deactivated', E_USER_NOTICE);
}

// Adds a settings link to the plugin action links on the WordPress plugin page
// Add the settings link filter to the plugin action links for this plugin
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'pve_plugin_add_settings_link' );
function pve_plugin_add_settings_link( $links ) {
    // Set the URL for the settings page
    $url=admin_url('admin.php?page=wc-settings&tab=checkout&section=ethereumpay');
    // Create the settings link HTML
    $settings_link = '<a href="'.$url.'">' . __( 'Settings' ) . '</a>';
    // Add the settings link to the beginning of the $links array
    array_unshift($links, $settings_link);
    // Return the modified $links array
    return $links;
}

