<?php 
$item=new stdClass();
$item->id=0;

$fields=array('payment_status','store_currency','transaction_id','transaction_status','transaction_hash','blockHash','from_address','my_address','created_at','updated_at','transaction_init','transaction_confirm','order_id','confirmations','blockNumber','order_total','exchange_rate','amount','eth_amount');

foreach ($fields as $field) {
    if(isset($_GET[$field])){
        if(is_array($_GET[$field])){
            $item->$field=$_GET[$field];
        }else{
            $item->$field=urldecode($_GET[$field]);
        }
    }
}

$submit_button_title=__( 'Add New Ethereum Payments', 'c9wep' );
$form_title=__( 'New Ethereum Payments', 'c9wep' );

require dirname(__FILE__) . '/ethereum_payments-form.php';
