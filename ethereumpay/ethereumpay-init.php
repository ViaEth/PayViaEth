<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function c9wep_hook_woocommerce_receipt_ethereumpay($order_id)
{
    require_once __DIR__ . '/ethereumpay.tpl.php';
}
add_action('woocommerce_receipt_ethereumpay','c9wep_hook_woocommerce_receipt_ethereumpay',10);