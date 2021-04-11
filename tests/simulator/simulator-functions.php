<?php
function c9wep_get_simulator_api($args){
  require_once C9WEP_DIR . '/etherscan-api/class-etherscan-api.php';

  $endpoint='http://localhost/2021/T-Thomas-Woocommerce-Ethereum-payment/site/wp-content/plugins/cms90-woocommerce-ethereum-payment/tests/simulator/';

  $networks=[
    'main'=>$endpoint,
    'kovan'=>$endpoint,
    'ropsten'=>$endpoint,
    'rinkeby'=>$endpoint,
    'goerli'=>$endpoint,
  ];

  $networks = apply_filters( 'api_networks', $networks );

  if(!empty($networks[$args['endpoint']])){
    $args['endpoint']=$networks[$args['endpoint']];
  }

  $api=new Etherscan_API($args);

  return $api;
}

if(!function_exists('c9wep_get_latest_transactions')){
  function c9wep_get_latest_transactions($args, $wallet_address, $number=20){
    $args['resource']='get_latest_transactions/endpoint.php?';
    $api=c9wep_get_simulator_api($args);
    return $api->get_latest_transactions($wallet_address, $number);
  }
}//end if !function_exists('c9wep_get_latest_transactions')

if(!function_exists('c9wep_get_transaction_status')){
  function c9wep_get_transaction_status($args, $transaction_hash){
    $args['resource']='get_transaction_status/endpoint.php?';
    $api=c9wep_get_simulator_api($args);
    return $api->get_transaction_status($transaction_hash);
  }
}//end if !function_exists('c9wep_get_transaction_status')


function c9wep_c9wep_pay_for_order_bottom( $order_id ) {
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  function c9wep_api_networks( $networks ) {
      $networks = c9wep_get_api_networks();//we use real networks temprary
      return $networks;
  }
  add_filter( 'api_networks', 'c9wep_api_networks', 10 );
  
  $payment=c9wep_get_payment_gateway_by_order_id($order_id);
  $args=$payment->get_api_args();
  // $args=$payment->get_api_args('test');
  $wallet_address=get_post_meta($order_id, ETHER_MY_WALLET_ADDRESS, true);//c9wep_get_wallet_address_with_order_id($order_id);
  $url=c9wep_get_latest_transactions_url($args, $wallet_address);

  $link=sprintf('<a href="%s" target="_blank">get latest transactions</a>', $url);
  echo $link;
}
add_action( 'c9wep_pay_for_order_bottom', 'c9wep_c9wep_pay_for_order_bottom' );    
