<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$post_url=urldecode($_GET['post_url']);
if(empty($post_url)){
    die('Error! Post url is empty');
}
$fields_keys=array('SHOP_ID','ORDER_ID','ORDER_ITEM','AMOUNT','CURRENCY','SHOP_PARA','PROD_ID','CHECK_CODE');
$fields=array();
foreach ($fields_keys as $key) {
    // if(empty($_GET[$key])){
    //     die('Error!'. $key . ' is empty');
    // }
    $fields[$key]=$_GET[$key];
}
ob_start();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;" />
</head>
<body>
<p>You are redirected to ethereumpay to process payment</p>
<form id="pay_form" name="pay_form" action="<?php echo $post_url; ?>" method="POST" role="form">
    <div class="fields-wrapper" style="display:none;">
    <?php foreach ($fields as $name => $value): ?>
    <label for="<?php echo $name; ?>"><?php echo $name; ?></label><br/>
    <input type="text" name="<?php echo $name; ?>" value="<?php echo $value; ?>"><br/>
    <?php endforeach ?>
    </div>
    <button type="submit" class="btn btn-primary">Press this button if the redirection doesn't happen</button>
</form>
</body>
<script type="text/javascript">
window.onload = function(){
  document.forms['pay_form'].submit();
}
</script>
</html>
<?php
$html=ob_get_clean();
header("Content-Type: text/html;");
echo $html;
?>
