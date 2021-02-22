<?php
function c9wep_ethereum_payments_init(){
    require_once dirname( __FILE__ ) . '/ethereum_payments-functions.php';
    require_once dirname( __FILE__ ) . '/ethereum_payments-db-functions.php';
    if(is_admin()){
        require_once dirname( __FILE__ ) . '/ethereum_payments-wp-install.sql.php'; 
        require_once dirname( __FILE__ ) . '/admin/class-ethereum_payments-list-table.php';
        require_once dirname( __FILE__ ) . '/admin/class-ethereum_payments-form-handler.php';
        require_once dirname( __FILE__ ) . '/admin/class-ethereum_payments.php'; 
    }
}//end c9wep_init

add_action('init','c9wep_ethereum_payments_init',10);

function c9wep_ethereum_payments_data_was_saved_successfully($insert_id,$fields)
{
    // your code here
}
add_action('ethereum_payments_data_was_saved_successfully','c9wep_ethereum_payments_data_was_saved_successfully',10,2);
