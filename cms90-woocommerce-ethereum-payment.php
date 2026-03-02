<?php
/*
Plugin Name: PayViaEth 
Plugin URI:  https://viaeth.io
Description: Woocommerce Ethereum Payment Plugin
Version:     0.420.69 
Requires Plugins: woocommerce
Author:      Tyler Thomas, Xufeng Wang
License:     GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: c9wep
*/

// Define constant for the plugin directory URL
define('C9WEP_URL', plugin_dir_url( __FILE__ ));

// Define constant for the plugin directory path
define('C9WEP_DIR', dirname( __FILE__ ));

// Adds an action to load the plugin's text domain when plugins are loaded.
add_action( 'plugins_loaded', 'c9wep_load_plugin_textdomain' );
function c9wep_load_plugin_textdomain() {
    load_plugin_textdomain( 'c9wep', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Load required files and classes for the plugin
require_once C9WEP_DIR . '/wp_wc_pve_logging.php'; //Plugin Logging
require_once C9WEP_DIR . '/functions.php'; // Load main plugin functions
require_once C9WEP_DIR . '/etherscan-api/etherscan-functions.php'; // Load etherscan API functions
require_once C9WEP_DIR . '/admin/ajax/ft_check_transaction_status/ft_check_transaction_status.php'; // Load transaction status check AJAX function
require_once C9WEP_DIR . '/cms90-woocommerce-ethereum-payment-gateway.php'; // Load Ethereum payment gateway class for WooCommerce
require_once C9WEP_DIR . '/includes/form-fields.php'; // Load form field classes for the plugin
require_once C9WEP_DIR . '/includes/db-functions.php'; // Load database functions for the plugin
require_once C9WEP_DIR . '/ethereum_payments/ethereum_payments-init.php'; // Load Ethereum payments initialization file
require_once C9WEP_DIR . '/ethereumpay/ethereumpay-init.php'; // Load EthereumPay initialization file
require_once C9WEP_DIR . '/check_transaction_status-cronjob.php'; // Load cron job for checking transaction status
// The following files are currently commented out and not loaded:
// require_once C9WEP_DIR . '/woo-functions.php';
// require_once C9WEP_DIR . '/admin/ajax/update_transaction_status/update_transaction_status.php'; 
// require_once C9WEP_DIR . '/admin/ajax/frontend_check_transaction_status/frontend_check_transaction_status.php'; 
// require_once C9WEP_DIR .'/frontend.php';
// require_once C9WEP_DIR . '/tests/tests.php';

// This code section checks if the user is in the WordPress admin area.
// If the user is in the admin area, it loads the admin.php file.
// If the user is not in the admin area, it does not load anything.
// The commented out line includes the c9wep-install.php file, but it is currently not being used
if ( is_admin() ) {
    require_once C9WEP_DIR .'/admin/admin.php';
}

// Register activation hook for this plugin to be called upon activation.
register_activation_hook(__FILE__, 'c9wep_activation');
// Function called on plugin activation.
function c9wep_activation() {
    //Logs that the plugin has been activated.
    if (! defined('WC_Version')){
	return;
    }
    $log_string = ('Plugin Activated with WooComerce Version: '.WC_VERSION);
    wp_wc_pve_write_log($log_string, E_USER_NOTICE);
}

// Register deactivation hook for this plugin to be called upon deactivation.
register_deactivation_hook(__FILE__, 'c9wep_deactivation');
// Function called on plugin deactivation.
function c9wep_deactivation() {
    // Clear any scheduled cron jobs for checking transaction status
    wp_clear_scheduled_hook('c9wep_check_transaction_status_cron_hook');
    //Logs that the plugin has been deactivated.
    wp_wc_pve_write_log('Plugin Deactivated', E_USER_NOTICE);
}

// Adds a settings link to the plugin action links on the WordPress plugin page
// Add the settings link filter to the plugin action links for this plugin
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'c9wep_plugin_add_settings_link' );
function c9wep_plugin_add_settings_link( $links ) {
    // Set the URL for the settings page
    $url=admin_url('admin.php?page=wc-settings&tab=checkout&section=ethereumpay');
    // Create the settings link HTML
    $settings_link = '<a href="'.$url.'">' . __( 'Settings' ) . '</a>';
    // Add the settings link to the beginning of the $links array
    array_unshift($links, $settings_link);
    // Return the modified $links array
    return $links;
}

// Disable the EthereumPay payment gateway if the cart total is zero.
function c9wep_payment_gateway_disable_total_amount( $available_gateways ) {
    global $woocommerce;
    if ( isset( $available_gateways['ethereumpay'] ) && $woocommerce->cart->total == 0 ) {
        unset(  $available_gateways['ethereumpay'] );
    }
    ob_start();
    print_r($available_gateways);
    echo PHP_EOL;
    echo PHP_EOL;
    echo PHP_EOL;
    echo PHP_EOL;
    $data1=ob_get_clean();
    // Log the available gateways to a file for debugging purposes.
    file_put_contents(dirname(__FILE__)  . '/available_gateways.log',$data1,FILE_APPEND);
    // Return the modified list of available gateways.
    return $available_gateways;
}

// Uncomment the following line to apply the filter to the 'woocommerce_available_payment_gateways' hook.
// add_filter( 'woocommerce_available_payment_gateways', 'c9wep_payment_gateway_disable_total_amount' );
