<?php
/*
Plugin Name: PayViaEth 
Plugin URI:  https://viaeth.io
Description: Woocommerce Ethereum Payment Plugin
Version:     0.420.69 
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

// Loads the plugin's text domain
function c9wep_load_plugin_textdomain() {
    load_plugin_textdomain( 'c9wep', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
// Adds an action to load the plugin's text domain when plugins are loaded
add_action( 'plugins_loaded', 'c9wep_load_plugin_textdomain' );

// Load required files and classes for the plugin
require_once C9WEP_DIR . '/php-errors-log.php'; //uncomments to turn on php errors log
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
    // require_once C9WEP_DIR . '/c9wep-install.php';
    require_once C9WEP_DIR .'/admin/admin.php';
}else{
  
}

// Register the 'c9wep_deactivation' function to be called upon deactivation of the plugin
register_deactivation_hook(__FILE__, 'c9wep_deactivation');
// Function to be called upon deactivation of the plugin
function c9wep_deactivation() {
    // Clear any scheduled cron jobs for checking transaction status
    wp_clear_scheduled_hook('c9wep_check_transaction_status_cron_hook');
}

// Register activation hook for this plugin file and call c9wep_activation function.
register_activation_hook(__FILE__, 'c9wep_activation');
// Function called on plugin activation.
function c9wep_activation() {
}

// Adds a settings link to the plugin action links on the WordPress plugin page
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
// Add the settings link filter to the plugin action links for this plugin
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'c9wep_plugin_add_settings_link' );

// Define a function to check system requirements for the plugin
function c9wep_my_error_notice() {
  // Call the c9wep_check_sys_requirments function to get any errors
  $errors=c9wep_check_sys_requirments();
  // If there are errors, display an error notice
  if(!empty($errors)){
    ?>
    <div class="error notice" style="background-color:#dc3232;color:#fff;">
      <p><?php _e('<b>Cms90 Woocommerce Ethereum Payment</b> Need following plugins', 'c9wep' ); ?></p>
      <?php foreach ($errors as $key => $err): ?>
        <p style="background-color:orange;"><?php _e($err, 'c9wep' ); ?></p>
      <?php endforeach ?>
    </div>
    <?php
  }
}
// Add the error notice function to the admin notices hook
add_action( 'admin_notices', 'c9wep_my_error_notice' );

// Function to check system requirements for the plugin
function c9wep_check_sys_requirments() {
  // Includes the WordPress plugin.php file
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  // Initializes an empty array to store errors
  $errors=[];
  // If in admin area, checks for required plugins and adds errors if they are not active
  if ( is_admin() ) {
    $required_plugins=[
      'WooCommerce'=>'woocommerce/woocommerce.php',
    ];

    foreach ($required_plugins as $plugin_name => $plugin_file) {
      if(!is_plugin_active( $plugin_file )){
        $errors[$plugin_name]=$plugin_name . ' plugin is required to install and activate';
      }
    }

    // if(!class_exists('WFOCU_Gateway')){
    //   $errors['WFOCU_Gateway']='WFOCU_Gateway class cannot be found, please make sure UpStroke: WooCommerce One Click Upsells is licensed';
    // }
  }
  // Returns array of errors
  return $errors;
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
