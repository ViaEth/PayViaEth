<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
get_header();
?>
<?php
$order=null;
$ORDER_ID=$_REQUEST['ORDER_ID'];
if(empty($ORDER_ID)){
    $ORDER_ID=$_REQUEST['order_id'];
}
if(!empty($ORDER_ID)){
    $user_id=get_current_user_id();
    if(!empty($user_id) && !empty($ORDER_ID)){
        $order          = wc_get_order( $ORDER_ID );
        if($user_id != $order->user_id){
            $order=null;
        }
    }
}
?>
<div id="primary" class="content-area full-width-page no-sidebar">
    <main id="main" class="site-main" role="main">  
    <?php if ( $order ) : ?>

        <?php if ( $order->has_status( 'failed' ) ) : ?>

            <p class="woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

            <p class="woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'woocommerce' ); ?></a>
                <?php endif; ?>
            </p>

        <?php else : ?>

            <p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></p>

            <ul class="woocommerce-thankyou-order-details order_details">
                <li class="order">
                    <?php _e( 'Order Number:', 'woocommerce' ); ?>
                    <strong><?php echo $order->get_order_number(); ?></strong>
                </li>
                <li class="date">
                    <?php _e( 'Date:', 'woocommerce' ); ?>
                    <strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
                </li>
                <li class="total">
                    <?php _e( 'Total:', 'woocommerce' ); ?>
                    <strong><?php echo $order->get_formatted_order_total(); ?></strong>
                </li>
                <?php if ( $order->payment_method_title ) : ?>
                <li class="method">
                    <?php _e( 'Payment Method:', 'woocommerce' ); ?>
                    <strong><?php echo $order->payment_method_title; ?></strong>
                </li>
                <?php endif; ?>
            </ul>
            <div class="clear"></div>

        <?php endif; ?>

        <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
        <?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

    <?php else : ?>

        <p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

    <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->
<?php 
get_footer();
?>