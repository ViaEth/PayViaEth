<?php 
$item=new stdClass();
$item->id=0;

$fields=array('payment_status','store_currency','transaction_id','created_at','updated_at','order_id','order_total','exchange_rate','amount');

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
