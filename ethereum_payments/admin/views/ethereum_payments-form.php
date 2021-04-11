<div class="wrap">
    <h2><?php _e( $form_title, 'c9wep' ); ?></h2>
    <?php if (array_key_exists('error', $_GET)): ?>
        <div class="notice notice-error"><p><?php echo $_GET['error']; ?></p></div>
    <?php endif; ?>
    <?php if (array_key_exists('success', $_GET)): ?>
        <div class="notice notice-success"><p><?php echo $_GET['success']; ?></p></div>
    <?php endif; ?>

    <form action="" method="post">
        <?php
        echo c9wep_text('payment_status','Payment status',$item->payment_status,true); 
        echo c9wep_text('store_currency','Store currency',$item->store_currency,true); 
        echo c9wep_text('transaction_id','Transaction id',$item->transaction_id,true); 
        echo c9wep_text('transaction_status','Transaction status',$item->transaction_status,true); 
        echo c9wep_text('transaction_hash','Transaction hash',$item->transaction_hash,true); 
        echo c9wep_text('blockHash','Block hash',$item->blockHash,true); 
        echo c9wep_text('from_address','From address',$item->from_address,true); 
        echo c9wep_text('my_address','My address',$item->my_address,true); 
//        echo c9wep_datetimepicker('created_at','Created at',$item->created_at,true); 
//        echo c9wep_datetimepicker('updated_at','Updated at',$item->updated_at,true); 
        echo c9wep_textarea('transaction_init','Transaction init',$item->transaction_init,true); 
        echo c9wep_textarea('transaction_confirm','Transaction confirm',$item->transaction_confirm,true); 
        echo c9wep_text('order_id','Order id',$item->order_id,true); 
        echo c9wep_text('confirmations','Confirmations',$item->confirmations,true); 
        echo c9wep_text('blockNumber','Block number',$item->blockNumber,true); 
        echo c9wep_number('order_total','Order total',$item->order_total,true); 
        echo c9wep_number('exchange_rate','Exchange rate',$item->exchange_rate,true); 
        echo c9wep_number('amount','Amount',$item->amount,true); 
        echo c9wep_number('eth_amount','Eth amount',$item->eth_amount,true); 
        ?>
        <input type="hidden" name="field_id" value="<?php echo $item->id; ?>">

        <?php wp_nonce_field( 'c9wep_ethereum_payments_nonce' ); ?>
        <?php submit_button( $submit_button_title, 'primary', 'submit_ethereum_payments' ); ?>

    </form>
</div>