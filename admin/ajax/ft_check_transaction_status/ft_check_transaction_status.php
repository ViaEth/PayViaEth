<?php
function c9wep_ft_check_transaction_status_ajax_admin_fun(){
    $state='success';
    $msg='It is success';
    $data='';
    try {
        $order_id=$_POST['order_id'];
        $status=c9wep_check_transaction_status($order_id);
        // $data=$status;
        // ob_start();
        // print_r($status);
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // $data1=ob_get_clean();
        // file_put_contents(dirname(__FILE__)  . '/status.log',$data1,FILE_APPEND);
        if(empty($status) || 'pending'==$status){
            $msg="No Transaction Found";
        }else{
            $msg='Status: ' . $status;
            $order=wc_get_order($order_id);
            $data=[
                'redirect'=>$order->get_view_order_url(),
            ];
        }
        // $_wpnonce=$_POST['_wpnonce'];
        // $_wp_http_referer=$_POST['_wp_http_referer'];

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

add_action("wp_ajax_c9wep_ft_check_transaction_status_ajax_admin_fun","c9wep_ft_check_transaction_status_ajax_admin_fun");
// add_action("wp_ajax_nopriv_c9wep_ft_check_transaction_status_ajax_admin_fun","c9wep_ft_check_transaction_status_ajax_admin_fun");

function c9wep_ft_check_transaction_status_ajax_js($hook) {
    //please make sure the $hook is correct by uncomment following statement to check it,
    var_dump($hook);die();
    if( strpos($hook,'page_cms90_northstar_newbook_settings') !== false){
        wp_enqueue_script( 'c9wep_ft_check_transaction_status_ajax_js', plugins_url('/ft_check_transaction_status.js', __FILE__),array('jquery'),1.0 );
        wp_register_script('c9wep-jquery-ba-throttle-debounce-min', C9WEP_URL . 'assets/lib/jquery/jquery.ba-throttle-debounce.min.js', array('jquery'), '1.0', true);
        /* you can use following enqueue in a shortcode to load as required */ 
        wp_enqueue_script('c9wep-jquery-ba-throttle-debounce-min');
    }
}
// add_action( 'admin_enqueue_scripts', 'c9wep_ft_check_transaction_status_ajax_js' );
