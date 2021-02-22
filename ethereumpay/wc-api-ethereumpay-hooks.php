<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function c9wep_get_order_total($order){
    $order_total =$order->order_total;
    $order_total =number_format($order_total, 2, '.', '');
    return $order_total;
}

function c9wep_get_order_pay_amount($order){
    $payment_gateway = wc_get_payment_gateway_by_order( $order );
    if(empty($payment_gateway)){
        $order_id=$order->get_id();
        $order = wc_get_order( $order_id );
        $payment_gateway = wc_get_payment_gateway_by_order( $order );
    }
    // ob_start();
    // print_r($order);
    // echo PHP_EOL;
    // print_r($payment_gateway);
    // echo PHP_EOL;
    // echo PHP_EOL;
    // echo PHP_EOL;
    // echo PHP_EOL;
    // $data1=ob_get_clean();
    // file_put_contents(dirname(__FILE__) . '/payment_gateway.log',$data1,FILE_APPEND);
    $order_total =$order->order_total*$payment_gateway->get_exchange_rate();
    $order_total =(int)$order_total;//number_format($order_total, 0, '.', '');//for TWD, no cents part
    return $order_total;
}

function c9wep_get_api_with_order_id($order_id){
    if(empty($order_id)) return false;

    $order = wc_get_order( $order_id );
    $payment_gateway = wc_get_payment_gateway_by_order( $order );
    $args=array(
        'SYS_TRUST_CODE'=>$payment_gateway->get_SYS_TRUST_CODE(),
        'SHOP_TRUST_CODE'=>$payment_gateway->get_SHOP_TRUST_CODE(),
        'SHOP_ID'=>$payment_gateway->get_SHOP_ID(),
        'PROD_ID'=>$payment_gateway->get_PROD_ID(),
        'CURRENCY' => $payment_gateway->get_currency(),
        'simulator_mode'=>$payment_gateway->get_simulator_mode(),
    );

    $api=new EthereumPay_API($args);
    return $api;
}

function c9wep_get_receive02_check_code($order_id,$sess_id){
    if(empty($order_id)) return false;

    $order = wc_get_order( $order_id );
    $amount=c9wep_get_order_pay_amount($order);//$order total
    $user_id = $order->get_user_id();

    $api=c9wep_get_api_with_order_id($order_id);//new EthereumPay_API($args);
    $CHECK_CODE=$api->get_receive02_check_code($order_id,$amount,$sess_id,$user_id);//post to receive01
    return $CHECK_CODE;
}

function c9wep_get_receive01_post_check_code($order_id,$sess_id){
    if(empty($order_id)) return false;

    $order = wc_get_order( $order_id );

    $amount=c9wep_get_order_pay_amount($order);//$order total
    $api=c9wep_get_api_with_order_id($order_id);//new EthereumPay_API($args);
    $CHECK_CODE=$api->post_to_receive01_check_code($order_id,$amount,$sess_id);//post to receive01
    return $CHECK_CODE;
}

function c9wep_get_receive01_url(){
    return get_site_url() . '/wc-api/ethereumpay_receive01';
}

function c9wep_ethereumpay_receive01()
{
    require_once C9HPP_DIR . '/ethereumpay/receive01.php';
    die();
}
add_action('woocommerce_api_ethereumpay_receive01','c9wep_ethereumpay_receive01');

function c9wep_process_receive01_data($post=array()){
    if(empty($post)){
        $post=$_POST;
    }
    //save to payments table
    $args=array(
        'ORDER_ID' => $post['ORDER_ID'],
        'SHOP_ID' => $post['SHOP_ID'],
        'CURRENCY' => $post['CURRENCY'],
        'SESS_ID' => $post['SESS_ID'],
        'PROD_ID' => $post['PROD_ID'],
        'AMOUNT' => $post['AMOUNT'],
        'CHECK_CODE' => $post['CHECK_CODE'],
        'updated_at' => current_time('mysql'),
        'created_at' => current_time('mysql'),
        'receive01_data' => serialize($post),
    );
    //update order status
    $order = wc_get_order( $post['ORDER_ID'] );
    $order->update_status( 'processing', 'Paid, SESS_ID: ' . $post['SESS_ID'] );
    $payment_gateway = wc_get_payment_gateway_by_order( $order );
    $args['payment_gateway_name'] = $payment_gateway->title;
    $args['store_currency'] = get_woocommerce_currency();
    $args['exchange_rate'] = $payment_gateway->get_exchange_rate();
    $args['order_total'] = c9wep_get_order_total($order);
    return c9wep_insert_ethereumpay_payments($args);
}

function c9wep_get_receive02_url(){
    return get_site_url() . '/wc-api/ethereumpay_receive02';
}

function c9wep_ethereumpay_receive02()
{
    require_once C9HPP_DIR . '/ethereumpay/receive02.php';
    die();
}
add_action('woocommerce_api_ethereumpay_receive02','c9wep_ethereumpay_receive02');

function c9wep_process_receive02_data($get=array()){
    if(empty($get)){
        $get=$_GET;
    }
    $var=array(
        'receive02_data' => serialize($get),
    );
    $where=array(
        'ORDER_ID' => $get['ORDER_ID']
    );
    return c9wep_update_ethereumpay_payments_by_vars_where($var,$where);
}

function c9wep_get_ethereumpay_simulator_url(){
    return get_site_url() . '/wc-api/ethereumpay_simulator';
}
function c9wep_ethereumpay_simulator()
{
    require_once C9HPP_DIR . '/ethereumpay/ethereumpay-simulator.php';
    die();
}
add_action('woocommerce_api_ethereumpay_simulator','c9wep_ethereumpay_simulator');

function c9wep_get_ethereumpay_processer_url(){
    return get_site_url() . '/wc-api/ethereumpay_processer';
}
function c9wep_ethereumpay_processer()
{
    require_once C9HPP_DIR . '/ethereumpay/post-form.php';
    die();
}
add_action('woocommerce_api_ethereumpay_processer','c9wep_ethereumpay_processer');

function c9wep_get_return_thank_you_check_code($order_id,$sess_id){
    return c9wep_get_receive02_check_code($order_id,$sess_id);
}

function c9wep_get_ethereumpay_thank_you_url($order_id=''){
     $url=get_site_url() . '/wc-api/ethereumpay_thank_you';
     if(!empty($order_id)){
        $url .='?ORDER_ID=' . $order_id;
     }
     return $url;
}
function c9wep_ethereumpay_thank_you()
{
    require_once C9HPP_DIR . '/ethereumpay/thank-you.php';
    die();
}
add_action('woocommerce_api_ethereumpay_thank_you','c9wep_ethereumpay_thank_you');

function c9wep_process_return_thank_you_data($post=array()){
    if(empty($post)){
        $post=$_POST;
    }
    $var=array(
        'return_url_data' => serialize($post),
    );
    $where=array(
        'ORDER_ID' => $post['ORDER_ID']
    );
    return c9wep_update_ethereumpay_payments_by_vars_where($var,$where);
}

