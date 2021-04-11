<?php
function c9wep_update_transaction_status_ajax_admin_fun(){
    $state='success';
    $msg='It is success';
    $data='';
    try {
        $order_id=$_POST['order_id'];
        $ether_transaction_status=$_POST['ether_transaction_status'];
        $ether_amount=$_POST['ether_amount'];
        $ether_transaction_init=$_POST['ether_transaction_init'];
        $ether_transaction_confirm=$_POST['ether_transaction_confirm'];

        // ob_start();
        // print_r($_POST);
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // $data1=ob_get_clean();
        // file_put_contents(dirname(__FILE__)  . '/_POST.log',$data1,FILE_APPEND);

        $ether_transaction_confirm_obj=json_decode( html_entity_decode( stripslashes ($ether_transaction_confirm ) ) );
        $ether_transaction_init_obj=json_decode( html_entity_decode( stripslashes ($ether_transaction_init ) ) );
/*
stdClass Object
(
    [to] => 0x8E6B0e896a4525C36a7f047644585b887d75D3Da
    [from] => 0xe2C57BA100837Ca234Ffb8539e8E6007b2eddFAA
    [contractAddress] => 
    [transactionIndex] => 1
    [gasUsed] => stdClass Object
        (
            [type] => BigNumber
            [hex] => 0x5208
        )

    [blockHash] => 0x0381249707a144beb237dae3e2ea05cc809d5a2f53d6caf97279295a19165365
    [transactionHash] => 0xeea4ac68117362dee3e5047d3043a9997608ad9f014583b560d11f9474e919e3
    [logs] => Array
        (
        )

    [blockNumber] => 24062011
    [confirmations] => 1
    [cumulativeGasUsed] => stdClass Object
        (
            [type] => BigNumber
            [hex] => 0x048d3c
        )

    [status] => 1
    [byzantium] => 1
*/
        // $payment_status='pending';

        if(isset($ether_transaction_confirm_obj->transactionHash)){
            $transaction_hash=$ether_transaction_confirm_obj->transactionHash;
            $args=[
                'payment_status' => $ether_transaction_status,
                'payment_mode' => 'test',
                'store_currency' => get_woocommerce_currency(),
                'transaction_id' => $transaction_hash,
                'transaction_status' => $ether_transaction_status,
                'transaction_hash' => $transaction_hash,
                'blockHash' => $ether_transaction_confirm_obj->blockHash,
                'from_address' => $ether_transaction_confirm_obj->from,
                'my_address' => $ether_transaction_confirm_obj->to,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
                'transaction_init' => serialize($ether_transaction_init_obj),
                'transaction_confirm' => serialize($ether_transaction_confirm_obj),
                'order_id' => $order_id,
                'confirmations' => $ether_transaction_confirm_obj->confirmations,
                'blockNumber' => $ether_transaction_confirm_obj->blockNumber,
                'order_total' => c9wep_get_order_total($order_id),
                'exchange_rate' => '',
                'amount' => c9wep_get_order_total($order_id),
                'eth_amount' => $ether_amount,
            ];
        }else{
            $args=[
                'payment_status' => $ether_transaction_status,
                'payment_mode' => 'test',
                'store_currency' => get_woocommerce_currency(),
                'transaction_status' => $ether_transaction_status,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
                'transaction_init' => serialize($ether_transaction_init_obj),
                'transaction_confirm' => serialize($ether_transaction_confirm_obj),
                'order_id' => $order_id,
                'order_total' => c9wep_get_order_total($order_id),
                'exchange_rate' => '',
                'amount' => c9wep_get_order_total($order_id),
                'eth_amount' => $ether_amount,
            ];            
        }
        c9wep_insert_ethereum_payments($args);
        // $_wpnonce=$_POST['_wpnonce'];
        // $_wp_http_referer=$_POST['_wp_http_referer'];

        // if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'c9wep_options-options' ) ) { 
        //     $state='failed';
        //     $msg='Failed with wrong _wpnonce';
        // }else{
            // $msg='Loaded successfully!';
            $msg='Success! ' . "ether_transaction_status: $ether_transaction_status, ether_transaction_init: $ether_transaction_init, ether_transaction_confirm: $ether_transaction_confirm";
        // }

        //do something here
    } catch (Exception $e) {
        $state='failed';
        $msg=$e->getMessage();
    }

    $return=array('state'=>$state,'msg'=>$msg,'data'=>$data);
    echo json_encode($return);
    die();
}

add_action("wp_ajax_c9wep_update_transaction_status_ajax_admin_fun","c9wep_update_transaction_status_ajax_admin_fun");
// add_action("wp_ajax_nopriv_c9wep_update_transaction_status_ajax_admin_fun","c9wep_update_transaction_status_ajax_admin_fun");

function c9wep_update_transaction_status_ajax_js($hook) {
    //please make sure the $hook is correct by uncomment following statement to check it,
    var_dump($hook);die();
    if( strpos($hook,'page_cms90_northstar_newbook_settings') !== false){
        wp_enqueue_script( 'c9wep_update_transaction_status_ajax_js', plugins_url('/update_transaction_status.js', __FILE__),array('jquery'),1.0 );
        wp_register_script('c9wep-jquery-ba-throttle-debounce-min', C9WEP_URL . 'assets/lib/jquery/jquery.ba-throttle-debounce.min.js', array('jquery'), '1.0', true);
        /* you can use following enqueue in a shortcode to load as required */ 
        wp_enqueue_script('c9wep-jquery-ba-throttle-debounce-min');
    }
}
// add_action( 'admin_enqueue_scripts', 'c9wep_update_transaction_status_ajax_js' );
