<?php
define('ETHER_NETWORK', 'c9wep_network');
define('ETHER_TXHASH', 'c9wep_transaction_hash');
define('ETHER_AMOUNT', 'c9wep_ether_amount');
define('ETHER_WEI_AMOUNT', 'c9wep_ether_wei_amount');

define('PAYMENT_MODE', 'c9wep_mode');
define('PAYMENT_EXPIRED_TIME', 'c9wep_payment_expired_time');

define('ETHER_TRACK_ID', 'c9wep_track_id');

define('ETHER_FROM_ADDRESS', 'c9wep_from_address');
define('ETHER_MY_WALLET_ADDRESS', 'c9wep_my_wallet_address');
define('ETHER_EXCHANGE_RATE', 'c9wep_exchange_rate');

add_action( 'woocommerce_email_order_meta', 'c9wep_add_email_order_meta', 90, 3 );
/*
 * @param $order_obj Order Object
 * @param $sent_to_admin If this email is for administrator or for a customer
 * @param $plain_text HTML or Plain text (can be configured in WooCommerce > Settings > Emails)
 */
function c9wep_add_email_order_meta( $order_obj, $sent_to_admin, $plain_text ){
  $order_id=$order_obj->get_order_number();
  $metas=c9wep_get_order_metas($order_id);

  if(empty($metas[ETHER_TXHASH])) return;

  $txhash=$metas[ETHER_TXHASH];
  $network=$metas[ETHER_NETWORK];

  if ( $plain_text === false ) {
    echo 'Transaction: ' . c9wep_get_transaction_view_link($network, $txhash);
  } else {
    echo 'Transaction Hash: ' . $txhash;
  }
}

function c9wep_update_transactions_status_all_orders($order_status='pending') {
  $args['where']=[
    'order_status'=>$order_status,
  ];

  $orders=c9wep_get_all_ethereum_payments($args);

  if(empty($orders)) return;

  foreach ($orders as $order_obj) {
    $order_id=$order_obj->order_id;
    c9wep_check_transaction_status($order_id);
  }
}

function c9wep_get_all_orders_with_track_id($track_id, $order_status='pending') {
  $args['where']=[
    'track_id'=>$track_id,
    'order_status'=>$order_status,
  ];

  $orders=c9wep_get_all_ethereum_payments($args);

  return $orders;
}
function c9wep_get_transaction_view_link($network, $transaction_hash, $view_lbl=''){
  if(empty($transaction_hash)) return '';

  $tx_url=c9wep_get_transaction_view_url($network, $transaction_hash);
  if(empty($view_lbl)){
    $view_lbl=$transaction_hash;
  }
  return sprintf( '<a href="%s" target="_blank">%s</a>', $tx_url, $view_lbl);
}

function c9wep_get_wallet_address_transaction_view_link($network, $wallet_address, $view_lbl=''){
  if(empty($wallet_address)) return '';

  $ad_url=c9wep_get_wallet_address_transaction_view_url($network, $wallet_address);
  if(empty($view_lbl)){
    $view_lbl=$wallet_address;
  }
  return sprintf( '<a href="%s" target="_blank">%s</a>', $ad_url, $view_lbl);
}

function c9wep_get_transaction_view_url($network, $transaction_hash){
  return c9wep_get_transaction_networks($network) . 'tx/'. $transaction_hash;
}

function c9wep_get_wallet_address_transaction_view_url($network, $wallet_address){
  return c9wep_get_transaction_networks($network) . 'address/'. $wallet_address;
}

function c9wep_get_transaction_networks($network=''){
  $networks=[
    'main'=>'https://etherscan.io/',
    'kovan'=>'https://kovan.etherscan.io/',
    'ropsten'=>'https://ropsten.etherscan.io/',
    'rinkeby'=>'https://rinkeby.etherscan.io/',
    'goerli'=>'https://goerli.etherscan.io/',
  ];

  if(empty($network)){
    return $networks;
  }else{
    if(isset($networks[$network])){
      return $networks[$network];
    }
  }
}

function c9wep_check_transaction_status($order_id, $order_status='pending') {
  if(empty($order_id)) return false;
  $order=wc_get_order($order_id);
  //check order status
  $_order_status=$order->get_status();
  if($_order_status != $order_status) return $_order_status;
  //if it's expired
  $metas=c9wep_get_order_metas($order_id);
  $expired_time=$metas[PAYMENT_EXPIRED_TIME];
  $expired_time_with_offset=strtotime($expired_time);
  $current_time=current_time( 'timestamp' );
  if($current_time>$expired_time_with_offset){//the order is expired definitely
    //set order as failed or expired
    $order->update_status('failed');
    $status=$order->get_status();
    $args=[
      'order_id' => $order_id,
      'payment_status'=>'expired',
      'order_status'=>$status,
    ];

    c9wep_insert_ethereum_payments($args);

    c9wep_process_expired_ether_payment($order_id);
    return $status;
  }

  // $track_id=$metas[ETHER_TRACK_ID];
  $created_time=c9wep_get_order_created_time($order_id);
  $wei_amount=$metas[ETHER_WEI_AMOUNT];
  $wallet_address=$metas[ETHER_MY_WALLET_ADDRESS];
  //get lastest transactions
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  $api_args=$payment->get_api_args();

  $latest_transactions_arr=c9wep_get_latest_transactions($api_args, $wallet_address);

  $created_timestamp=strtotime($created_time);
  $expired_timestamp=strtotime($expired_time);

  $gmt_offset=get_option('gmt_offset');

  $utc_created_timestamp=$created_timestamp - $gmt_offset*60*60;
  $utc_expired_timestamp=$expired_timestamp - $gmt_offset*60*60;

  $transaction_found=c9wep_get_transaction_with_params($latest_transactions_arr, $wei_amount, $wallet_address, $utc_created_timestamp, $utc_expired_timestamp);

  if(!empty($transaction_found)){
    //update database related code
/* sample of $transaction_found
    'blockNumber'=>'24093145',
    'timeStamp'=>'1617025964',
    'hash'=>'0xf1a8858491535c5347181ed1635e2dac17e8f40f52cc622749d3754632af21eb',
    'nonce'=>'31',
    'blockHash'=>'0x8acb8964cc2fd2da69679a79ff9c46d95c35542ba174419b453f8777b48c54d9',
    'transactionIndex'=>'9',
    'from'=>'0xe2c57ba100837ca234ffb8539e8e6007b2eddfaa',
    'to'=>'0x8e6b0e896a4525c36a7f047644585b887d75d3da',
    'value'=>'7745000000000000',
    'gas'=>'21000',
    'gasPrice'=>'19950000000',
    'isError'=>'0',
    'txreceipt_status'=>'1',
    'input'=>'0x',
    'contractAddress'=>'',
    'cumulativeGasUsed'=>'1088224',
    'gasUsed'=>'21000',
    'confirmations'=>'1',
*/
    $args=[
        // 'payment_status' => '',
        // 'order_status' => '',
        // 'payment_mode' => '',
        // 'store_currency' => '',
        // 'track_id' => '',
        // 'transaction_id' => '',
        // 'transaction_status' => '',
        'transaction_hash' => $transaction_found['hash'],
        'blockHash' => $transaction_found['blockHash'],
        'from_address' => $transaction_found['from'],
        'my_address' => $transaction_found['to'],
//        'created_at' => '',
//        'updated_at' => '',
        // 'transaction_init' => '',
        // 'transaction_confirm' => '',
        'order_id' => $order_id,
        'confirmations' => $transaction_found['confirmations'],
        'blockNumber' => $transaction_found['blockNumber'],
        // 'order_total' => '',
        // 'exchange_rate' => '',
        // 'amount' => '',
        // 'eth_amount' => '',
    ];

    if(intval($transaction_found['confirmations'])>=1){
      $args['transaction_confirm']=serialize($transaction_found);
      $args['payment_status']='success';
      $args['transaction_status']='success';

      update_post_meta( $order_id, ETHER_TXHASH, $transaction_found['hash'] );
      update_post_meta( $order_id, ETHER_FROM_ADDRESS, $transaction_found['from'] );
      $order->update_status('processing');//payment is confirmed
      //final to process payment
      c9wep_process_ether_payment($order_id);
    }else{
      $args['transaction_init']=serialize($transaction_found);
    }

    $status=$order->get_status();
    $args['order_status']=$status;
    c9wep_insert_ethereum_payments($args);

    return $status;
  }
}

function c9wep_process_expired_ether_payment($order_id){
    global $woocommerce;
    $order = new WC_Order($order_id);  

    $order->add_order_note('Payment Expired, No Transaction Found', true);

    if(!empty($woocommerce->cart)){
      $woocommerce->cart->empty_cart();
    }
}

function c9wep_process_ether_payment($order_id){
    global $woocommerce;
    $order = new WC_Order($order_id);  

    $order->payment_complete();
    if(function_exists('wc_reduce_stock_levels')){
      wc_reduce_stock_levels($order_id);
    }else{
      $order->reduce_order_stock();
    }

    $metas=c9wep_get_order_metas($order_id);
    // ob_start();
    // print_r($metas);
    // echo PHP_EOL;
    // echo PHP_EOL;
    // echo PHP_EOL;
    // echo PHP_EOL;
    // $data1=ob_get_clean();
    // file_put_contents(dirname(__FILE__)  . '/metas.log',$data1,FILE_APPEND);
    if(!empty($metas[ETHER_TXHASH])){
      $txhash=$metas[ETHER_TXHASH];
      // some notes to customer (replace true with false to make it private)
      $order->add_order_note( 'Transaction Hash: ' . $txhash, true );
    }

    // Empty cart
    if(!empty($woocommerce->cart)){
      $woocommerce->cart->empty_cart();
    }

    // Redirect to the thank you page

  // $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  // $payment->process_ether_payment($order_id);
}

function c9wep_wc_clear_cart_after_payment( $methods ) {
    global $wp, $woocommerce;

    if ( ! empty( $wp->query_vars['order-received'] ) ) {

        $order_id = absint( $wp->query_vars['order-received'] );

        if ( isset( $_GET['key'] ) )
            $order_key = $_GET['key'];
        else
            $order_key = '';

        if ( $order_id > 0 ) {
            $order = wc_get_order( $order_id );

            if ( $order->order_key == $order_key ) {
               WC()->cart->empty_cart();
            }
        }

    }

    if ( WC()->session->order_awaiting_payment > 0 ) {

        $order = wc_get_order( WC()->session->order_awaiting_payment );

        if ( $order->id > 0 ) {
            // If the order has not failed, or is not pending, the order must have gone through
            if ( ! $order->has_status( array( 'failed', 'pending','pending-st-cleared-funds','on-hold' ) ) ) { ///// <- add your custom status here....
                WC()->cart->empty_cart();
            }
         }
    }
}
function override_wc_clear_cart_after_payment() {
    remove_filter('get_header', 'wc_clear_cart_after_payment' );
    add_action('get_header', 'c9wep_wc_clear_cart_after_payment' );
}
add_action('init', 'override_wc_clear_cart_after_payment');

// function c9wep_process_a_paid_order($order){
//   global $woocommerce;

//   $order->payment_complete();
//   $order->reduce_order_stock();

//   // some notes to customer (replace true with false to make it private)
//   $order->add_order_note( 'Hey, your order is paid! Thank you!', true );

//   // Empty cart
//   $woocommerce->cart->empty_cart();

//   $payment_gateway = wc_get_payment_gateway_by_order( $order );
//   $redirect_url= $payment_gateway->get_return_url( $order );
//   wp_redirect( $redirect_url );
// exit;
//   // Redirect to the thank you page
//   // return array(
//   //     'result' => 'success',
//   //     'redirect' => $this->get_return_url( $order )
//   // );
// }

function c9wep_handle_order_redirect($order_id, $no_redirect_status='pending'){
  global $woocommerce;
  $order=wc_get_order($order_id);
  $status=$order->get_status();
  
  if($status != $no_redirect_status){
    // Empty cart
    $woocommerce->cart->empty_cart();
    $redirect_url = $order->get_view_order_url();
    wp_redirect( $redirect_url );
    exit;
  }
}

function c9wep_get_transaction_with_params($transactions_arr, $wei_amount, $wallet_address, $created_timestamp, $expired_timestamp){

  $wallet_address=strtolower($wallet_address);
  // $transactions_arr=json_decode($transactions_json, true);

  $trans_found=[];
  if(!empty($transactions_arr['result'])){
    $transactions=$transactions_arr['result'];
    foreach ($transactions as $tran) {
      //the creation time of transaction must behind the creation of order created time
      $trans_created_timestamp=$tran['timeStamp'];
      if(intval($trans_created_timestamp) > intval($expired_timestamp)){//in the expired time frame
        continue;
      }
      if(intval($trans_created_timestamp) < intval($created_timestamp)){//it's old transactions, we can break;
        break;
      }
      $trans_to_address=$tran['to'];
      $trans_value=$tran['value'];
      if($trans_value==$wei_amount && $wallet_address==$trans_to_address){//we found one
        $trans_found[]=$tran;
      }
    }
  }

  //if there are more than one transaction were found, we take the time closed one
  $trans_final=[];
  if(count($trans_found)>0){
    $min_time_offset=$expired_timestamp-$created_timestamp;
    foreach ($trans_found as $fd_tran) {
      $fd_trans_created_timestamp=$fd_tran['timeStamp'];
      $fd_time_offset=$created_timestamp-$fd_trans_created_timestamp;
      if($fd_time_offset<$min_time_offset){
        $min_time_offset=$fd_time_offset;
        $trans_final[]=$fd_tran;
      }
    }
  }

  return isset($trans_final[0]) ? $trans_final[0] : [];
}

// add_filter( 'woocommerce_payment_complete_order_status', array($this,'update_order_status'), 10, 2 );
// add_action('woocommerce_order_status_pending',array($this,'wc_order_pending'),10);
// add_action('woocommerce_order_status_failed',array($this,'wc_order_failed'),10);
// add_action('woocommerce_order_status_on-hold',array($this,'wc_order_hold'),10);
//add_action('woocommerce_order_status_processing',array($this,'wc_order_processing'),10);
// add_action('woocommerce_order_status_completed',array($this,'wc_order_completed'),10);
// add_action('woocommerce_order_status_refunded',array($this,'wc_order_refunded'),10);
// add_action('woocommerce_order_status_cancelled',array($this,'wc_order_cancelled'),10);

// add_action( 'woocommerce_thankyou', array($this,'woocommerce_thankyou'));
// add_action( 'woocommerce_new_order', array($this,'wc_order_processing'));

function c9wep_eth_to_wei($eth_amount) {
  require_once C9WEP_DIR . '/includes/ethereum-convertor.php';
  $convertor=new Converter();
  $wei_amount=$convertor->toWei($eth_amount);
//1 ether = 1,000,000,000,000,000,000 wei (1018)
  return $wei_amount;//$eth_amount * 1000000000000000000;
}

function c9wep_get_track_id($wei_amount, $wallet_address) {
  return $wei_amount . '_' . $wallet_address;
}

add_action('woocommerce_new_order','c9wep_woocommerce_new_order',90);
function c9wep_woocommerce_new_order($order_id) {
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  if('ethereumpay' != $payment->id) return;
  //update order meta
  $ether_amount=c9wep_get_order_amount_ether($order_id);
  $is_test_mode=c9wep_get_payment_mode_by_order_id($order_id);
  $wallet_address=c9wep_get_wallet_address_with_order_id($order_id);
  $expired_time = c9wep_get_payment_expired_time($order_id);
  $is_payment_expired=c9wep_is_payment_expired($order_id);
  $wei_amount=c9wep_eth_to_wei($ether_amount);
  $track_id=c9wep_get_track_id($wei_amount, $wallet_address );

  $fields=[
    PAYMENT_MODE=>$is_test_mode ? 'test' : '',
    ETHER_NETWORK=>c9wep_get_ether_network_by_order_id($order_id),
    ETHER_AMOUNT=>$ether_amount,
    ETHER_WEI_AMOUNT=>$wei_amount,
    PAYMENT_EXPIRED_TIME=>$expired_time,
    ETHER_TRACK_ID=>$track_id,
    ETHER_MY_WALLET_ADDRESS=>$wallet_address,
    ETHER_EXCHANGE_RATE=>'',
  ];

  foreach ($fields as $field => $value) {
    update_post_meta( $order_id, $field, $value );
  }

  $args=[
      'payment_mode' => ($is_test_mode) ? 'test' : '',
      'store_currency' => get_woocommerce_currency(),
      // 'transaction_status' => $ether_transaction_status,
      'created_at' => current_time('mysql'),
      'updated_at' => current_time('mysql'),
      'expired_time' => $expired_time,
      'my_address' => $wallet_address,
      'payment_status'=>'pending',
      'transaction_network'=>c9wep_get_ether_network_by_order_id($order_id),
      // 'transaction_init' => serialize($ether_transaction_init_obj),
      // 'transaction_confirm' => serialize($ether_transaction_confirm_obj),
      'order_id' => $order_id,
      'order_status'=>'pending',
      'order_total' => c9wep_get_order_total($order_id),
      'exchange_rate' => '',
      'amount' => c9wep_get_order_total($order_id),
      'eth_amount' => $ether_amount,
      'track_id' => $track_id,
  ];            
  c9wep_insert_ethereum_payments($args);
}

function c9wep_get_order_metas($order_id){
  $fields=c9wep_get_order_meta_fields();

  $metas=[];
  foreach ($fields as $field) {
    $metas[$field]=get_post_meta( $order_id, $field, true);
  }

  return $metas;
}

function c9wep_get_test_networks_by_order_id($order_id){
  $network=c9wep_get_ether_network_by_order_id($order_id);
  return c9wep_get_test_networks($network);
}

function c9wep_get_test_networks($network=''){
  $all_networks=[
    'kovan'=>'Kovan Testnet',
    'ropsten'=>'Ropsten Testnet',
    'rinkeby'=>'Rinkeby Testnet',
    'goerli'=>'Goerli Testnet',
  ];
  if(empty($network)){
    return $all_networks;
  }else if(isset($all_networks[$network])){
    return $all_networks[$network];
  }

  return false;
}

function c9wep_get_order_meta_fields(){
  $fields=[
    PAYMENT_MODE,
    ETHER_NETWORK,
    ETHER_TXHASH,
    ETHER_AMOUNT,
    ETHER_WEI_AMOUNT,
    PAYMENT_EXPIRED_TIME,
    ETHER_TRACK_ID,
    ETHER_FROM_ADDRESS,
    ETHER_MY_WALLET_ADDRESS,
    ETHER_EXCHANGE_RATE,
  ];

  return $fields;
}

function c9wep_get_order_total($order_id){
  if(empty($order_id)) return false;

  $order = wc_get_order( $order_id );
  $total = $order->get_total();

  return $total;
}

function c9wep_get_ether_network_by_order_id($order_id){
  $ether_network=c9wep_get_payment_ether_network_by_order_id($order_id);
  return $ether_network;
  // return c9wep_get_transaction_networks($ether_network);
}

function c9wep_get_payment_ether_network_by_order_id($order_id){
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  return $payment->get_ether_network();
}

function c9wep_get_timeleft_minutes_settings_by_order_id($order_id){
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  return $payment->get_total_time_transaction_timeout();
}

function c9wep_get_checking_interval_by_order_id($order_id){
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  return $payment->get_interval_check_status();
}

function c9wep_get_payment_expired_time($order_id){
  $expired_time = get_post_meta( $order_id, PAYMENT_EXPIRED_TIME, true );
  if(empty($expired_time)){
    $minutes = c9wep_get_timeleft_minutes_settings_by_order_id($order_id);
    $created_time = c9wep_get_order_created_time( $order_id );
    $new_time = date( 'Y-m-d H:i:s', strtotime( $created_time ) + $minutes * 60 );

    update_post_meta( $order_id, PAYMENT_EXPIRED_TIME, $new_time );

    $expired_time = get_post_meta( $order_id, PAYMENT_EXPIRED_TIME, true );
  }

  return $expired_time;
}

function c9wep_get_order_status($order_id){
  $order=wc_get_order($order_id);
  //check order status
  $order_status=$order->get_status();

  return $order_status;
}

function c9wep_is_payment_expired($order_id){
  $expired_time = c9wep_get_payment_expired_time($order_id);
  $now=current_time('mysql');

  if(strtotime($expired_time) <= strtotime($now)){
    return true;
  }
  return false;
}

function c9wep_get_order_created_time($order_id){
  $order = wc_get_order( $order_id );
  // Get Order Dates
  $date_created=$order->get_date_created();
  $date = $date_created->date("Y-m-d H:i:s");
  return $date;
}

function c9wep_get_payment_gateway_by_order_id($order_id){
    if(empty($order_id)) return false;
    // $order_id=$order->get_id();
    $order = wc_get_order( $order_id );
    $payment_gateway = wc_get_payment_gateway_by_order( $order );   
    return $payment_gateway;
}

function c9wep_get_payment_mode_by_order_id($order_id){
    if(empty($order_id)) return false;
    $payment_gateway = c9wep_get_payment_gateway_by_order_id($order_id);
    return $payment_gateway->is_test_mode();
}


function c9wep_convert_to_eth_amount($amount, $currency=''){
  if(empty($currency)){
    $currency=get_woocommerce_currency();
  }

  try {
    require_once C9WEP_DIR . '/includes/currencyconvertor.php';
    $convertor = new CurrencyConvertor( $currency, 'ETH' );
    $eth_value = $convertor->convert( $amount );
    return $eth_value;
  } catch ( \Exception $e ) {
    ob_start();
    print_r($e);
    echo PHP_EOL;
    $data1=ob_get_clean();
    file_put_contents(dirname(__FILE__)  . '/e.log',$data1,FILE_APPEND);
  }
  ob_start();
  print_r($eth_value);
  echo PHP_EOL;
  $data1=ob_get_clean();
  file_put_contents(dirname(__FILE__)  . '/eth_value.log',$data1,FILE_APPEND);
  return $eth_value;
}
// function c9wep_get_current_wallet($order_id){
//   return '0x8E6B0e896a4525C36a7f047644585b887d75D3Da';
// }

function c9wep_get_wallet_address_with_order_id($order_id){
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);

  $ether_amount=c9wep_get_order_amount_ether($order_id);
  $wei_amount=c9wep_eth_to_wei($ether_amount);

  $wallet_addresses=$payment->get_wallet_addresses();
  $same_track_id_orders=[];
  foreach ($wallet_addresses as $_my_address) {
    if(empty($_my_address)) continue;

    $_my_track_id=c9wep_get_track_id($wei_amount, $_my_address );
    $_same_track_id_order=c9wep_get_all_orders_with_track_id($_my_track_id);
    if(count($_same_track_id_order)<=0){//doesn't exist order with the same track_id
      return $_my_address;
    }else{
      $same_track_id_orders[$_my_track_id]=[
        'count'=>count($_same_track_id_order),
        'my_address'=>$_my_address,
      ];
    }
  }

  //get the min count to avoid conflict
  $count=9999;
  $_my_address='';
  foreach ($same_track_id_orders as $sti_order) {
    $_count=$sti_order['count'];
    if($_count<$count){
      $_count=$count;
      $_my_address=$sti_order['my_address'];
    }
  }

  return $_my_address;
  // return '0x8E6B0e896a4525C36a7f047644585b887d75D3Da';
}

function c9wep_get_order_amount_ether($order_id){
  $total=c9wep_get_order_total($order_id);
  $eth_total=c9wep_convert_to_eth_amount($total);
  return $eth_total;
}

function c9wep_string_line_formatter($string_line){
  if(empty($string_line)) return false;

  $clean_dash_name=preg_replace('/[^A-Z0-9]+/i','-',$string_line);
  $clean_dash_name=trim($clean_dash_name,'-');

  $clean_id_name=preg_replace('/[^A-Z0-9]+/i','_',$string_line);
  $clean_id_name=trim($clean_id_name,'_');

  $parts=explode("_",$clean_id_name);
  $parts=array_map('ucfirst',$parts);
  $name=implode("_",$parts);
  $class_name=$name;

  $low_case_class_name=lcfirst($class_name);

  $id=strtolower($clean_id_name);

  $constant=strtoupper($clean_id_name);

  $dash_id=strtolower($clean_dash_name);

  $parts=explode('-',$clean_dash_name);
  $parts=array_map('ucfirst',$parts);
  //for some special strings
  foreach ($parts as $key => $part) {
      if('Id'==$part){
          $parts[$key]='ID';
      }
  }
  $title=implode(' ',$parts);

  $result=compact('constant','id','dash_id','title');

  return $result;
}