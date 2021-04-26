<?php
/*
Plugin Name: PayViaEth 
Plugin URI:  https://viaeth.io
Description: Woocommerce Ethereum Payment Plugin
Version:     0.2.5
Author:      Tyler Thomas(https://www.SpaceAgeMinds.com), Xufeng Wang(http://www.upwork.com/fl/albertw6)
License:     GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: c9wep
*/

define('C9WEP_URL', plugin_dir_url( __FILE__ ));
define('C9WEP_DIR', dirname( __FILE__ ));

function c9wep_load_plugin_textdomain() {
    load_plugin_textdomain( 'c9wep', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'c9wep_load_plugin_textdomain' );

require_once C9WEP_DIR . '/php-errors-log.php'; //uncomments to turn on php errors log
require_once C9WEP_DIR . '/functions.php';
// require_once C9WEP_DIR . '/woo-functions.php';
require_once C9WEP_DIR . '/etherscan-api/etherscan-functions.php';
require_once C9WEP_DIR . '/admin/ajax/ft_check_transaction_status/ft_check_transaction_status.php'; 

// require_once C9WEP_DIR . '/admin/ajax/update_transaction_status/update_transaction_status.php'; 
// require_once C9WEP_DIR . '/admin/ajax/frontend_check_transaction_status/frontend_check_transaction_status.php'; 

require_once C9WEP_DIR . '/cms90-woocommerce-ethereum-payment-gateway.php';
require_once C9WEP_DIR . '/includes/form-fields.php';
require_once C9WEP_DIR . '/includes/db-functions.php';
require_once C9WEP_DIR . '/ethereum_payments/ethereum_payments-init.php';
require_once C9WEP_DIR . '/ethereumpay/ethereumpay-init.php';

require_once C9WEP_DIR . '/check_transaction_status-cronjob.php';

// require_once C9WEP_DIR .'/frontend.php';
// require_once C9WEP_DIR . '/tests/tests.php';
//https://developer.wordpress.org/plugins/the-basics/best-practices/
if ( is_admin() ) {
    // require_once C9WEP_DIR . '/c9wep-install.php';
    require_once C9WEP_DIR .'/admin/admin.php';
}else{
  
}

register_deactivation_hook(__FILE__, 'c9wep_deactivation');
function c9wep_deactivation() {
    wp_clear_scheduled_hook('c9wep_check_transaction_status_cron_hook');
}

register_activation_hook(__FILE__, 'c9wep_activation');
function c9wep_activation() {
    // $_file='';
    // if(!file_exists($_file)){
    //     die($_file . 'is required to run this plugin');
    // }
}

function c9wep_plugin_add_settings_link( $links ) {
    $url=admin_url('admin.php?page=wc-settings&tab=checkout&section=ethereumpay');
    $settings_link = '<a href="'.$url.'">' . __( 'Settings' ) . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'c9wep_plugin_add_settings_link' );

function c9wep_my_error_notice() {
  $errors=c9wep_check_sys_requirments();
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
add_action( 'admin_notices', 'c9wep_my_error_notice' );

function c9wep_check_sys_requirments() {
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  $errors=[];
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
  return $errors;
}
