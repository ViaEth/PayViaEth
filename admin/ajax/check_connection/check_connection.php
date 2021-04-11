<?php
function c9wep_check_connection_ajax_admin_fun(){
    $state='success';
    $msg='It is success';
    $data='';
    try {
        $network=$_POST['network'];
        $apikey=$_POST['apikey'];
        $_wpnonce=$_POST['_wpnonce'];
        $_wp_http_referer=$_POST['_wp_http_referer'];

        ob_start();
        print_r($_POST);
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
        $data1=ob_get_clean();
        file_put_contents(dirname(__FILE__)  . '/_POST.log',$data1,FILE_APPEND);
        if(empty($apikey)){
            $state='failed';
            $msg='API key is empty';
        }else if(empty($network)){
            $state='failed';
            $msg='Network is empty';
        }else{
            $args=[
                'endpoint'=>$network,
                'apikey'=>$apikey,
            ];

            $transactions=c9wep_get_latest_transactions($args, '0x8E6B0e896a4525C36a7f047644585b887d75D3Da');
            $return_price=c9wep_get_enther_price($args);
            $msg='Success! ' . "network: $network, apikey: $apikey, ether price: $return_price";
        }
        // if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'c9wep_options-options' ) ) { 
        //     $state='failed';
        //     $msg='Failed with wrong _wpnonce';
        // }else{
            // $msg='Loaded successfully!';
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

add_action("wp_ajax_c9wep_check_connection_ajax_admin_fun","c9wep_check_connection_ajax_admin_fun");
// add_action("wp_ajax_nopriv_c9wep_check_connection_ajax_admin_fun","c9wep_check_connection_ajax_admin_fun");

function c9wep_check_connection_ajax_js($hook) {
    //please make sure the $hook is correct by uncomment following statement to check it,
    // var_dump($hook);die();
    if( strpos($hook,'woocommerce_page_wc-settings') !== false){
        wp_enqueue_script( 'c9wep_check_connection_ajax_js', plugins_url('/check_connection.js', __FILE__),array('jquery'),1.0 );
        wp_register_script('c9wep-jquery-ba-throttle-debounce-min', C9WEP_URL . 'assets/lib/jquery/jquery.ba-throttle-debounce.min.js', array('jquery'), '1.0', true);
        /* you can use following enqueue in a shortcode to load as required */ 
        wp_enqueue_script('c9wep-jquery-ba-throttle-debounce-min');
    }
}
add_action( 'admin_enqueue_scripts', 'c9wep_check_connection_ajax_js' );
