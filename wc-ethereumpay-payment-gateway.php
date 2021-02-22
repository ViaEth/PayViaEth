<?php
class WC_EthereumPay_Payment_Gateway extends WC_Payment_Gateway {
    protected $simulator_mode = false;
    
    protected $PROD_ID = '';
    protected $prod = 'ethereumpay';
    protected $is_subpayment = true;
    protected $currency = 'TWD'; //USD, CNY, TWD
    public $title = '';
    public $description = '';
    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct() {
        // ob_start();
        // print_r($this);
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // $data1=ob_get_clean();
        // file_put_contents(dirname(__FILE__) . '/this.log',$data1,FILE_APPEND);
        // $this->is_subpayment = true;
        $class_name = get_class($this);
        if (strlen($class_name) == strlen('WC_EthereumPay_Payment_Gateway')) {
        // if ($class_name == 'WC_EthereumPay_Payment_Gateway') {
            $this->is_subpayment = false;
        }
        // if($this->is_subpayment){
        //     $index = strrpos($class_name, '_');
        //     $this->prod = substr($class_name, $index + 1);
        // }


        $this->id                 = 'c9hpp-' . strtolower($this->is_subpayment ? 'ethereumpay-' . $this->prod : $this->prod);
        $this->icon               = apply_filters( 'woocommerce_' . $this->prod . '_icon', plugins_url( 'assets/images/' .$this->prod . '.png', __FILE__ ) );
        // ob_start();
        // print_r($this);
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // $data1=ob_get_clean();
        // file_put_contents(dirname(__FILE__) . '/this.log',$data1,FILE_APPEND);
        $this->has_fields         = true;
        $this->order_button_text  = __( 'Proceed to ' . $this->prod, 'woocommerce' );
        // $this->method_title = 'EthereumPay Payment';
        // $this->method_description = 'Description of EthereumPay Payment payment gateway'; // will be displayed on the options page
        $this->method_title       =  __( $this->is_subpayment ? 'EthereumPay ' . $this->getMethodTitle() : 'EthereumPay', 'woocommerce' ); 
        $this->method_description = __( $this->is_subpayment ? '' : 'EthereumPay provides a global payment solution.', 'woocommerce' ); 
        // $this->id = 'c9hpp_ethereumpay'; // payment gateway plugin ID
        // $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
        // $this->has_fields = true; // in case you need a custom credit card form
     
        // gateways can support subscriptions, refunds, saved payment methods,
        // but in this tutorial we begin with simple payments
        $this->supports = array(
            'products'
        );
     
        // Method with all the options fields
        $this->init_form_fields();
     
        // Load the settings.
        $this->init_settings();

        $this->init_ethereumpay_global_settings();//to get global settings

        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->enabled = $this->get_option( 'enabled' );

        $this->SYS_TRUST_CODE = $this->get_option('SYS_TRUST_CODE');
        $this->SHOP_TRUST_CODE = $this->get_option('SHOP_TRUST_CODE');
        $this->SHOP_ID = $this->get_option('SHOP_ID');
        // $this->PROD_ID = $this->get_option('PROD_ID');
        // $this->testmode = 'yes' === $this->get_option( 'testmode' );
        // $this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
        // $this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
     
        // This action hook saves the settings
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
     
        // We need custom JavaScript to obtain a token
        add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
        // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) ); 
        // You can also register a webhook here
        // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
        // ob_start();
        // print_r($this);
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // $data1=ob_get_clean();
        // file_put_contents(dirname(__FILE__) . '/this.log',$data1,FILE_APPEND);
     }

    protected function init_ethereumpay_global_settings() {
        if ($this->is_subpayment) {
            $ethereumpay = new WC_EthereumPay_Payment_Gateway();
            $this->simulator_mode = 'yes' === $ethereumpay->get_option( 'simulator_mode', 'no' );
        } else {
            $this->simulator_mode = 'yes' === $this->get_option( 'simulator_mode', 'no' );
        }
    }

    protected function getMethodTitle() {
        $method_title = '';
        if ($this->title) {
            $method_title = $this->title;
        } else {
            $method_title = __( $this->prod, 'woocommerce' );
            $index = strrpos($this->PROD_ID, '_');
            if ($index && substr($this->PROD_ID, $index + 1) == substr($method_title, strlen($method_title) - 2)) {
                $method_title = substr($method_title, 0, strlen($method_title) - 2);
            }
        }
        
        return $method_title;
    }
    /**
     * Plugin options, we deal with it in Step 3 too
     */
    public function init_form_fields(){
        $method_title = $this->getMethodTitle();
        if ($this->is_subpayment) {
            $this->form_fields = array(
                    'enabled' => array(
                            'title'   => __( 'Enable/Disable', 'woocommerce' ),
                            'type'    => 'checkbox',
                            'label'   => __( 'Enable ' . $method_title, 'woocommerce' ),
                            'default' => 'no'
                    ),
                    'title' => array(
                            'title'       => __( 'Title', 'woocommerce' ),
                            'type'        => 'text',
                            'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                            'default'     => $method_title,
                            'desc_tip'    => true,
                    ),
                    'description' => array(
                            'title'       => __( 'Description', 'woocommerce' ),
                            'type'        => 'text',
                            'desc_tip'    => true,
                            'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
                            'default'     => __( $this->description ? $this->description : ('Pay via ' . $method_title), 'woocommerce' )
                    ),
                    'checkout_fee_cal_way' =>array(),//empty array as placeholder to keep the display order
                    'checkout_fee' =>array(),//empty array as placeholder to keep the display order
                    'exchange_rate' => array(),//empty array as placeholder to keep the display order
            );
        } else {
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable EthereumPay Payment',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'EthereumPay',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with ethereumpay payment gateway.',
                ),
                'checkout_fee_cal_way' =>array(),//empty array as placeholder to keep the display order
                'checkout_fee' =>array(),//empty array as placeholder to keep the display order
                'exchange_rate' => array(),//empty array as placeholder to keep the display order
                'simulator_mode' => array(
                        'title'   => __( 'Enable/Disable', 'woocommerce' ),
                        'type'    => 'checkbox',
                        'label'   => __( 'Enable Simulator', 'woocommerce' ),
                        'description' => 'Enable this option to allow you test payment workflow without needing following account settings',
                        'default' => 'no'
                ),
                'receive01' => array(
                    'title'       => 'Receive01: ' . c9hpp_get_receive01_url(),
                    'type'        => 'title',
                ),
                'receive02' => array(
                    'title'       => 'Receive02: ' . c9hpp_get_receive02_url(),
                    'type'        => 'title',
                ),
                'SHOP_ID' => array(
                    'title'       => 'HAPPY PAY 所提供廠商的廠商代碼',
                    'type'        => 'text',
                    'description' => 'To get this ID, you may need contact EthereumPay with above receive01, receive02',
                ),
                'SYS_TRUST_CODE' => array(
                    'title'       => 'SYS_TRUST_CODE(系統交易信任碼)',
                    'type'        => 'password',
                    'description' => 'To get this CODE, you may need contact EthereumPay with above receive01, receive02',
                ),
                'SHOP_TRUST_CODE' => array(
                    'title'       => 'SHOP_TRUST_CODE(廠商交易信任碼)',
                    'type'        => 'password',
                    'description' => 'To get this CODE, you may need contact EthereumPay with above receive01, receive02',
                ),
            );
        }

        //exchange rate, checkout fee, checkout fee calculated way
        $this->form_fields['checkout_fee_cal_way'] = array(
            'title'       => 'How to Calculate Checkout Fee',
            'type'        => 'select',
            'options'     => array(
                'fixed'=>'Fixed Fee',
                'percentage'=>'Percentage',
            ),
        );

        $this->form_fields['checkout_fee'] = array(
            'title'       => 'Checkout Fee',
            'type'        => 'text',
            'description' => 'Store Currency ' . get_woocommerce_currency()
        );
        if(get_woocommerce_currency() !== $this->get_currency()){
            $this->form_fields['exchange_rate'] =  array(
                'title'       => 'Exchange Rate',
                'type'        => 'text',
                'description' => 'How much above ' . $this->get_currency() . ' will equal to 100 ' . get_woocommerce_currency()
            );
        }else{
            unset($this->form_fields['exchange_rate']);
        }
    }

    function get_SYS_TRUST_CODE(){
        if($this->simulator_mode){
            return 'smsystrust';
        }
        return $this->SYS_TRUST_CODE;
    }
    function get_SHOP_TRUST_CODE(){
        if($this->simulator_mode){
            return 'smshoptrust';
        }
        return $this->SHOP_TRUST_CODE;
    }
    function get_SHOP_ID(){
        if($this->simulator_mode){
            return 'smshopid';
        }
        return $this->SHOP_ID;
    }
    function get_PROD_ID(){
        return $this->PROD_ID;
    }
    function get_simulator_mode(){
        return $this->simulator_mode;
    }
    function get_currency(){
        return $this->currency;
    }
    function get_checkout_fee(){
        return $this->get_option( 'checkout_fee' );
    }
    function get_checkout_fee_cal_way(){
        return $this->get_option( 'checkout_fee_cal_way' );
    }
    function get_exchange_rate(){
        if(get_woocommerce_currency() !== $this->get_currency()){
            return ($this->get_option( 'exchange_rate' )/100);
        }
        return 1;//default, the same currency
    }
    /**
     * You will need it if you want your custom credit card form, Step 4 is about it
     */
    public function payment_fields() {
        // ok, let's display some description before the payment form
        if($this->simulator_mode){
            $this->description = '<b style="color:red;">You are running in simulator mode</b><br/>' . $this->description;
        }
        if ( $this->description ) {
            // display the description with <p> tags etc.
            echo wpautop( wp_kses_post( $this->description ) );
        }
     
    }

    /*
     * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
     */
    public function payment_scripts() {
     
        // we need JavaScript to process a token only on cart/checkout pages, right?
        if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
            return;
        }
     
        // if our payment gateway is disabled, we do not have to enqueue JS too
        if ( 'no' === $this->enabled ) {
            return;
        }
     
        // no reason to enqueue JavaScript if API keys are not set
        if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
            return;
        }
     
        // do not work with card detailes without SSL unless your website is in a test mode
        if ( ! $this->testmode && ! is_ssl() ) {
            return;
        }
     
        // let's suppose it is our payment processor JavaScript that allows to obtain a token
        // wp_enqueue_script( 'c9hpp_js', 'https://www.c9hpppayments.com/api/token.js' );
     
        // and this is our custom JS in your plugin directory that works with token.js
        // wp_register_script( 'woocommerce_c9hpp', plugins_url( 'c9hpp.js', __FILE__ ), array( 'jquery', 'c9hpp_js' ) );
     
        // in most payment processors you have to use PUBLIC KEY to obtain a token
        wp_localize_script( 'woocommerce_c9hpp', 'c9hpp_params', array(
            'publishableKey' => $this->publishable_key
        ) );
     
        wp_enqueue_script( 'woocommerce_c9hpp' );
     
    }

    /*
     * Fields validation, more in Step 5
     */
    public function validate_fields(){
     
        // if( empty( $_POST[ 'billing_first_name' ]) ) {
        //     wc_add_notice(  'First name is required!', 'error' );
        //     return false;
        // }
        // return true;
     
    }

    /*
     * We're processing the payments here, everything about it is in Step 5
     */
    public function process_payment( $order_id ) {
        include_once C9HPP_DIR . '/ethereumpay/class-wc-gateway-ethereumpay-request.php';

        $order          = wc_get_order( $order_id );
        $EthereumPay_request = new WC_Gateway_EthereumPay_Request( $this );

        return array(
            'result'   => 'success',
            'redirect' => $EthereumPay_request->get_request_url( $order )
        );
        // global $woocommerce;
        // $order = new WC_Order( $order_id );

        // // Mark as on-hold (we're awaiting the cheque)
        // $order->update_status('on-hold', __( 'Awaiting cheque payment', 'woocommerce' ));

        // // Reduce stock levels
        // $order->reduce_order_stock();

        // // Remove cart
        // $woocommerce->cart->empty_cart();

        // $args=array(
        //     'SYS_TRUST_CODE'=>$this->SYS_TRUST_CODE,
        //     'SHOP_TRUST_CODE'=>$this->SHOP_TRUST_CODE,
        //     'SHOP_ID'=>$this->SHOP_ID,
        //     'PROD_ID'=>$this->PROD_ID,
        //     'simulator_mode'=>$this->simulator_mode,
        // );

        // $happy_api=new EthereumPay_API($args);
        // $params = array();
        // $params['name'] = $hamster_args['payer_name'];
        // $params['email'] = $hamster_args['payer_email'];
        // $params['phone'] = $hamster_args['payer_phone'];
        // $params['address'] = $hamster_args['payer_address'];
        // $params['amount'] = $hamster_args['total'];

        // Return thankyou redirect
        // return array(
        //     'result' => 'success',
        //     'redirect' => $this->get_return_url( $order )
        // );
    }
}
