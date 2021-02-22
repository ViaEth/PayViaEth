<?php
if(isset($_GET['WSDL'])){
  require_once dirname(__FILE__) . '/wsdl.php';
  die();
}

// if(isset($_GET['client_information'])){
//   require_once dirname(__FILE__) . '/wsdl.php';
//     require_once dirname(__FILE__) . '/client_information.php';
//   die();
// }
// ob_start();
// var_export($_GET);
// var_export($_REQUEST);
// $data=ob_get_clean();
// file_put_contents(dirname(__FILE__) . '/request.log',$data);