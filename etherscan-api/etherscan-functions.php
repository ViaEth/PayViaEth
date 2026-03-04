<?php
if(file_exists(PVE_DIR . '/tests/simulator-on.flag')){
  //use simulator functions
  require_once PVE_DIR . '/tests/simulator/simulator-functions.php';
  //below functions will be overrided by the functions in above file
}

function pve_get_api($args){
  require_once __DIR__ . '/class-etherscan-api.php';

  $networks = pve_get_api_networks();

  $networks = apply_filters( 'api_networks', $networks );

  if(!empty($networks[$args['endpoint']])){
    $args['endpoint']=$networks[$args['endpoint']];
  }

  $api=new Etherscan_API($args);

  return $api;
}

if(!function_exists('pve_get_latest_transactions')){
  function pve_get_latest_transactions($args, $wallet_address, $number=20){
    $api=pve_get_api($args);
    return $api->get_latest_transactions($wallet_address, $number);
  }
}//end if !function_exists('c9wep_get_latest_transactions')

if(!function_exists('pve_get_transaction_status')){
  function pve_get_transaction_status($args, $transaction_hash){
    $api=pve_get_api($args);
    return $api->get_transaction_status($transaction_hash);
  }
}//end if !function_exists('c9wep_get_transaction_status')

function pve_get_api_networks($network=''){
  $networks=[
    'main'=>'https://api.etherscan.io/api?',
    'kovan'=>'https://api-kovan.etherscan.io/api?',
    'ropsten'=>'https://api-ropsten.etherscan.io/api?',
    'rinkeby'=>'https://api-rinkeby.etherscan.io/api?',
    'goerli'=>'https://api-goerli.etherscan.io/api?',
  ];

  if(empty($network)){
    return $networks;
  }else{
    if(isset($networks[$network])){
      return $networks[$network];
    }
  }
}

function pve_get_ether_price($args){
  $api=pve_get_api($args);
  $price=$api->get_ether_price();
  return $price;
}

function pve_get_ether_price_url($args){
  $api=pve_get_api($args);
  return $api->get_ether_price_url();
}

function pve_get_latest_transactions_url($args,$wallet_address, $number=20){
  $api=pve_get_api($args);
  return $api->get_latest_transactions_url($wallet_address, $number);
}

