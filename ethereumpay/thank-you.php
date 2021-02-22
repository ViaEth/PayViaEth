<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$order=null;
// ob_start();
// print_r($_REQUEST);
// echo PHP_EOL;
// echo PHP_EOL;
// echo PHP_EOL;
// echo PHP_EOL;
// $data1=ob_get_clean();
// file_put_contents(dirname(__FILE__) . '/thank-you_REQUEST.log',$data1,FILE_APPEND);
$ORDER_ID=$_REQUEST['ORDER_ID'];
if(empty($ORDER_ID)){
    $ORDER_ID=$_REQUEST['order_id'];
}
if(!empty($ORDER_ID)){
    $our_check_code=c9hpp_get_return_thank_you_check_code($ORDER_ID,$SESS_ID);
    $received_check_code=$_REQUEST['CHECK_CODE'];
    if($received_check_code == $our_check_code){
        c9hpp_process_return_thank_you_data();
    }
}
require_once c9hpp_get_template_files('thank-you.tpl.php');
?>
