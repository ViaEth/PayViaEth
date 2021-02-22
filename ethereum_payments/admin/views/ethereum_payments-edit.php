<?php 
$item = c9wep_get_ethereum_payments_by_id( $id ); 
$submit_button_title=__( 'Update Ethereum Payments', 'c9wep' );
$form_title=__( 'Edit Ethereum Payments', 'c9wep' );

require dirname(__FILE__) . '/ethereum_payments-form.php';
