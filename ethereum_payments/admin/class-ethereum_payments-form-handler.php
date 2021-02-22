<?php

/**
 * Handle the form submissions
 *
 * @package Package
 * @subpackage Sub Package
 */
class C9wep_Ethereum_payments_Form_Handler {

    /**
     * Hook 'em all
     */
    public function __construct() {
        add_action( 'admin_init', array( $this, 'handle_form' ) );
    }

    /**
     * Handle the ethereum_payments new and edit form
     *
     * @return void
     */
    public function handle_form() {
        // echo '<div style="display1:none;"><pre>';
        // var_dump(__METHOD__);
        // echo '</pre></div>';
        // die();
        if ( ! isset( $_POST['submit_ethereum_payments'] ) ) {
            return;
        }
        // echo '<div style="display1:none;"><pre>';
        // var_dump(__METHOD__);
        // echo '</pre></div>';
        // die();
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'c9wep_ethereum_payments_nonce' ) ) {
            die( __( 'Wrong nonce!', 'c9wep' ) );
        }

        if ( ! current_user_can( 'read' ) ) {
            wp_die( __( 'Permission Denied!', 'c9wep' ) );
        }

        $errors   = array();
        $page_url = admin_url( 'admin.php?page=c9wep-ethereum_payments' );
        $field_id = isset( $_POST['field_id'] ) ? intval( $_POST['field_id'] ) : 0;

        // $clicks = isset( $_POST['clicks'] ) ? sanitize_text_field( $_POST['clicks'] ) : '';
        $payment_status = isset( $_POST['payment_status'] ) ? sanitize_text_field( $_POST['payment_status'] ) : '';
        $store_currency = isset( $_POST['store_currency'] ) ? sanitize_text_field( $_POST['store_currency'] ) : '';
        $transaction_id = isset( $_POST['transaction_id'] ) ? sanitize_text_field( $_POST['transaction_id'] ) : '';
        $created_at = isset( $_POST['created_at'] ) ? sanitize_text_field( $_POST['created_at'] ) : '';
        $updated_at = isset( $_POST['updated_at'] ) ? sanitize_text_field( $_POST['updated_at'] ) : '';
        $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
        $order_total = isset( $_POST['order_total'] ) ? sanitize_text_field( $_POST['order_total'] ) : '';
        $exchange_rate = isset( $_POST['exchange_rate'] ) ? sanitize_text_field( $_POST['exchange_rate'] ) : '';
        $amount = isset( $_POST['amount'] ) ? sanitize_text_field( $_POST['amount'] ) : '';
        // some basic validation
        // if ( ! $clicks ) {
        //     $errors[] = __( 'Error: Clicks is required', 'c9wep' );
        // }

        if (empty($payment_status) ) {
            $errors[] = __( 'Error: Payment Status is required', 'c9wep' );
        }
        if (empty($store_currency) ) {
            $errors[] = __( 'Error: Store Currency is required', 'c9wep' );
        }
        if (empty($transaction_id) ) {
            $errors[] = __( 'Error: Transaction ID is required', 'c9wep' );
        }
/*        if (empty($created_at) ) {
            $errors[] = __( 'Error: Created At is required', 'c9wep' );
        }*/
/*        if (empty($updated_at) ) {
            $errors[] = __( 'Error: Updated At is required', 'c9wep' );
        }*/
        if (empty($order_id) ) {
            $errors[] = __( 'Error: Order ID is required', 'c9wep' );
        }
        if (empty($order_total) ) {
            $errors[] = __( 'Error: Order Total is required', 'c9wep' );
        }
        if (empty($exchange_rate) ) {
            $errors[] = __( 'Error: Exchange Rate is required', 'c9wep' );
        }
        if (empty($amount) ) {
            $errors[] = __( 'Error: Amount is required', 'c9wep' );
        }

        $fields = array(
            'payment_status' => $payment_status,
            'store_currency' => $store_currency,
            'transaction_id' => $transaction_id,
//            'created_at' => $created_at,
//            'updated_at' => $updated_at,
            'order_id' => $order_id,
            'order_total' => $order_total,
            'exchange_rate' => $exchange_rate,
            'amount' => $amount,
        );
        // bail out if error found
        if ( $errors ) {
            $first_error = reset( $errors );
            if(empty($field_id)){
                $query_arg=$fields + array( 'error' => urlencode($first_error), 'action' =>'new' ); 
                $redirect_to = add_query_arg($query_arg, $page_url );
            }else{
                $query_arg=$fields + array( 'error' => urlencode($first_error), 'action' =>'edit','id'=>$field_id ); 
                $redirect_to = add_query_arg($query_arg, $page_url );
            }
            wp_safe_redirect( $redirect_to );
            exit;
        }

        // New or edit?
        if ( ! $field_id ) {

            $insert_id = c9wep_insert_ethereum_payments( $fields );

        } else {

            $fields['id'] = $field_id;

            $insert_id = c9wep_insert_ethereum_payments( $fields );
        }

        if ( is_wp_error( $insert_id ) ) {
            $redirect_to = add_query_arg(
                array( 'error' => urlencode($insert_id->get_error_message()) ),
                $page_url
            );
        } else {
            do_action('ethereum_payments_data_was_saved_successfully',$insert_id,$fields);
            $redirect_to = add_query_arg(
                array( 'success' => urlencode(__( 'Succesfully saved!', 'c9wep' )) ),
                $page_url
            );
        }

        wp_safe_redirect( $redirect_to );
        exit;
    }
}

new C9wep_Ethereum_payments_Form_Handler();