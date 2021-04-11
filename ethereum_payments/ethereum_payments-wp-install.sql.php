<?php
if( is_admin() ) {
    //c9wep_db_remove_ethereum_payments_table();
    c9wep_db_install_ethereum_payments_table();
    // c9wep_db_install_ethereum_payments_table_dummy_data_import();
    //c9wep_db_import_predefine_ethereum_payments();
}

function c9wep_db_install_ethereum_payments_table()
{
    define('C9WEP_ETHEREUM_PAYMENTS_DB_VERSION', '1.9');
    $c9wep_installed = get_option('c9wep_ethereum_payments_db_version');
    if( $c9wep_installed != C9WEP_ETHEREUM_PAYMENTS_DB_VERSION ) { 
        GLOBAL $wpdb;
        
        $wp_prefix = $wpdb->prefix;
        // This includes the dbDelta function from WordPress.
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $create_ethereum_payments_table = ("
CREATE TABLE `{$wp_prefix}c9wep_ethereum_payments` (
    `id` int(11) UNSIGNED NOT NULL auto_increment,
     PRIMARY KEY  (`id`),

     `payment_status` varchar(255) NOT NULL default '',
     `order_status` varchar(255) NOT NULL default '',
     `payment_mode` varchar(255) NOT NULL default '',
     `store_currency` varchar(255) NOT NULL default '',
     `transaction_id` varchar(255) NOT NULL default '',
     `track_id` varchar(255) NOT NULL default '',
     `transaction_network` varchar(255) NOT NULL default '',
     `transaction_status` varchar(255) NOT NULL default '',
     `transaction_hash` varchar(255) NOT NULL default '',
     `blockHash` varchar(255) NOT NULL default '',
     `from_address` varchar(255) NOT NULL default '',
     `my_address` varchar(255) NOT NULL default '',
     `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
     `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
     `expired_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
     `transaction_init` text NOT NULL default '',
     `transaction_confirm` text NOT NULL default '',
     `order_id` int UNSIGNED NOT NULL default 0,
     `confirmations` int UNSIGNED NOT NULL default 0,
     `blockNumber` int UNSIGNED NOT NULL default 0,
     `order_total` decimal(12,2) NOT NULL default 0.0,
     `exchange_rate` decimal(12,2) NOT NULL default 0.0,
     `amount` decimal(12,2) NOT NULL default 0.0,
     `eth_amount` decimal(27,18) NOT NULL default 0.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        
        // Create/update the plugin tables.
        dbDelta($create_ethereum_payments_table);
        update_option('c9wep_ethereum_payments_db_version', C9WEP_ETHEREUM_PAYMENTS_DB_VERSION);
    }
}

function c9wep_db_install_ethereum_payments_table_dummy_data_import()
{
    define('C9WEP_ETHEREUM_PAYMENTS_DB_DATA_VERSION', '1.0');
    $c9wep_data_installed = get_option('c9wep_ethereum_payments_db_data_version');
    if( $c9wep_data_installed != C9WEP_ETHEREUM_PAYMENTS_DB_DATA_VERSION ) {
        $e=array();
        require_once C9WEP_DIR . '/ethereum_payments/tests/ethereum_payments_dummy_data.array.php';
        require_once C9WEP_DIR . '/ethereum_payments/ethereum_payments-db-functions.php';
        $del_args['where']=array('id'=>'>0');
        c9wep_delete_ethereum_payments($del_args);
        foreach ($e as $key => $item) {
            c9wep_insert_ethereum_payments($item);
        }
        update_option('c9wep_ethereum_payments_db_data_version', C9WEP_ETHEREUM_PAYMENTS_DB_DATA_VERSION);
    }//end of if( $c9wep_data_installed != C9WEP_ETHEREUM_PAYMENTS_DB_DATA_VERSION ) { 
}

function c9wep_db_import_predefine_ethereum_payments() {
    define('C9WEP_ETHEREUM_PAYMENTS_IMPORTED_VERSION', '1.0');
    $c9wep_ethereum_payments_imported = get_option('c9wep_ethereum_payments_imported_version');
    if( $c9wep_ethereum_payments_imported != C9WEP_ETHEREUM_PAYMENTS_IMPORTED_VERSION ) {
        require_once C9WEP_DIR . '/ethereum_payments/ethereum_payments-db-functions.php';
        //insert all predefined data
        update_option('c9wep_ethereum_payments_imported_version', C9WEP_ETHEREUM_PAYMENTS_IMPORTED_VERSION);
    }
}

function c9wep_db_remove_ethereum_payments_table() {
    define('C9WEP_ETHEREUM_PAYMENTS_DELETED_TABLE_VERSION', '1.0');
    $c9wep_ethereum_payments_deleted = get_option('c9wep_ethereum_payments_deleted_version');
    if( $c9wep_ethereum_payments_deleted != C9WEP_ETHEREUM_PAYMENTS_DELETED_TABLE_VERSION ) {
     global $wpdb;
     $table_name = $wpdb->prefix . "c9wep_ethereum_payments";
     $sql = "DROP TABLE IF EXISTS $table_name;";
     $wpdb->query($sql);
        update_option('c9wep_ethereum_payments_deleted_version', C9WEP_ETHEREUM_PAYMENTS_DELETED_TABLE_VERSION);
    }
}