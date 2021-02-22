<?php
/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'c9wep_add_gateway_class' );
function c9wep_add_gateway_class( $gateways ) {
    $gateways[] = 'Cms90_Woocommerce_Ethereum_Payment_Gateway'; // your class name is here
    return $gateways;
}
/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'c9wep_init_gateway_class' );
function c9wep_init_gateway_class() {
    if( !class_exists('WC_Payment_Gateway') )  return;

    class Cms90_Woocommerce_Ethereum_Payment_Gateway extends WC_Payment_Gateway {
 
        /**
         * Class constructor, more about it in Step 3
         */
        public function __construct() {
         
            $this->id = 'ethereumpay'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Ethereum Payment';
            $this->method_description = 'Description of Ethereum Payment'; // will be displayed on the options page
         
            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );
         
            // Method with all the options fields
            $this->init_form_fields();
         
            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            $this->wallet_address = $this->testmode ? $this->get_option( 'test_wallet_address' ) : $this->get_option( 'wallet_address' );
            $this->api_key = $this->testmode ? $this->get_option( 'test_api_key' ) : $this->get_option( 'api_key' );
         
            if($this->testmode){
                $this->simulator_mode = 'yes' === $this->get_option( 'simulator_mode' );
            }
            if($this->simulator_mode){
                //$this->password=c9wep_ethereumpay_get_simulator_password();
            }
	    
            $this->callback_url = $this->get_callback_url();//home_url('/wc-api/' . $this->id);
            add_action('woocommerce_api_' . $this->id, array($this, 'check_payment_response'));  
	    
            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
         
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));   
            // We need custom JavaScript to obtain a token
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            //add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) ); 
            // You can also register a webhook here
            // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
         }
 
        public function get_callback_url(){
            // return c9wep_get_callback_url($this->id);
        }
        /**
         * Plugin options, we deal with it in Step 3 too
         */
        public function init_form_fields(){
         
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Ethereum Payment',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Ethereum Payment',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with etherum.',
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'simulator_mode' => array(
                        'title'   => __( 'Test With Simulator', 'woocommerce' ),
                        'type'    => 'checkbox',
                        'label'   => __( 'Enable Simulator', 'woocommerce' ),
                        'description' => 'Enable this option to allow you test payment workflow without sending data to actually wallet address(only work in test mode)',
                        'default' => 'no'
                ),
                'test_api_key' => array(
                    'title'       => 'Test API Key',
                    'type'        => 'text'
                ),
                'test_wallet_address' => array(
                    'title'       => 'Test Wallet Address',
                    'type'        => 'text',
                ),
                'api_key' => array(
                    'title'       => 'Live API Key',
                    'type'        => 'password'
                ),
                'wallet_address' => array(
                    'title'       => 'Live Wallet Address',
                    'type'        => 'text'
                )
            );
        }

        private function apply_markup( $price ) {
          $markup_percent = $this->settings['markup_percent'];
          $markup_percent = ! empty( $markup_percent ) ? $markup_percent : 0;
          $multiplier     = ( $markup_percent / 100 ) + 1;

          return round( $price * $multiplier, 5, PHP_ROUND_HALF_UP );
        }

        public function get_eth_amount() {
          require_once C9WEP_DIR . '/includes/currencyconvertor.php';
          $total    = WC()->cart->total;
          $currency = get_woocommerce_currency();
          try {
            $convertor = new CurrencyConvertor( $currency, 'ETH' );
            $eth_value = $convertor->convert( $total );
            $eth_value = $this->apply_markup( $eth_value );
            // Set the value in the session so we can log it against the order.
            WC()->session->set(
              'c9wep_calculated_value',
              array(
                'eth_value' => $eth_value,
                'timestamp' => time(),
              )
            );

            return $eth_value;
          } catch ( \Exception $e ) {
            $GLOBALS['c9wep_etherumpay_value']->log(
              sprintf(
                __( 'Problem performing currency conversion: %s', 'c9wep_etherumpay_value' ),
                $e->getMessage()
              )
            );
            echo '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">';
            echo '<ul class="woocommerce-error">';
            echo '<li>';
            _e(
              'Unable to provide an order value in ETH at this time. Please contact support.',
              'c9wep_etherumpay_value'
            );
            echo '</li>';
            echo '</ul>';
            echo '</div>';
          }
          return false;
        }
        /**
         * You will need it if you want your custom credit card form, Step 4 is about it
         */
        public function payment_fields() {
         
            // ok, let's display some description before the payment form
            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ( $this->testmode ) {
                    if($this->simulator_mode){
                    $this->description .= '<br/> <b style="color:red;">THIS IS SIMULATOR MODE</b> <br/>There are no data will be sent to any etherum net';
                    $this->description  = trim( $this->description );
                    }else{
                    $this->description .= '<br/> <b style="color:red;">TEST MODE ENABLED.</b>';
                    $this->description  = trim( $this->description );
                    }

                }
                // display the description with <p> tags etc.
                echo wpautop( wp_kses_post( $this->description ) );
            }
            //return ;//we skip the credit card list here         
            ?>
            <div class="eth-amount-wapper">
              <div class="eth-amount-title"><span>
                <?php echo 'Send: ' . $this->get_eth_amount() . ' ETH'; ?>
              </span></div>
              <input type="hidden" name="eth-amount" id="eth-amount" class="form-control" value="<?php echo $this->get_eth_amount(); ?>" required="required" pattern="" title="">
            </div>
            <div class="eth-wallet-address-wapper">
              <div class="eth-wallet-address-title"><span>
                <?php echo 'To:' . $this->wallet_address; ?>
              </span></div>
              <input type="hidden" name="eth-wallet-address" id="eth-wallet-address" class="form-control" value="<?php echo $this->wallet_address; ?>" required="required" pattern="" title="">
            </div>
            <?php
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
            if ( empty( $this->wallet_address ) || empty( $this->api_key ) ) {
                return;
            }
         
            // do not work with card detailes without SSL unless your website is in a test mode
            if ( ! $this->testmode && ! is_ssl() ) {
                return;
            }
         
            // let's suppose it is our payment processor JavaScript that allows to obtain a token
            wp_enqueue_script( 'c9wep_js', 'https://www.c9weppayments.com/api/token.js' );
         
            // and this is our custom JS in your plugin directory that works with token.js
            wp_register_script( 'woocommerce_c9wep', plugins_url( 'c9wep.js', __FILE__ ), array( 'jquery', 'c9wep_js' ) );
         
            // in most payment processors you have to use PUBLIC KEY to obtain a token
            wp_localize_script( 'woocommerce_c9wep', 'c9wep_params', array(
                'publishableKey' => $this->api_key
            ) );
         
            wp_enqueue_script( 'woocommerce_c9wep' );
         
        }
 
        /*
         * Fields validation, more in Step 5
         */
        public function validate_fields(){
         
            if( empty( $_POST[ 'billing_first_name' ]) ) {
                wc_add_notice(  'First name is required!', 'error' );
                return false;
            }
            return true;
         
        }

        function receipt_page($order_id) {         
            // echo $this -> generate_payment_request_form($order_id);
        }
        
        public function generate_payment_request_form($order_id) {  
            $order = new WC_Order($order_id);
            $args=[
                'MERCHANT_ACC_NO'=>$this->MERCHANT_ACC_NO,
                'MERCHANT_TRANID'=>$order_id/*order id*/,
                'AMOUNT'=>number_format($order->get_total(), 2, '.', ''),
                'TRANSACTION_TYPE'=>2,//2  Sales
                'RESPONSE_TYPE'=>'HTTP', //HTTP  Response via HTTP redirection. If this option is used, the parameter RETURN_URL must be specified.
                'RETURN_URL'=>$this->get_callback_url(),
                'TXN_DESC'=>$this->TXN_DESC
            ];

            /*signature fields 
            $args=[
              'AMOUNT'=>'30.00',
              'MERCHANT_ACC_NO'=>'000000000000001',
              'MERCHANT_TRANID'=>'20160225_142142',
              'RESPONSE_TYPE'=>'HTTP',
              'RETURN_URL'=>'https://localhost:9088/BPG/txn_office/merchant_return_page.jsp',
              'TRANSACTION_TYPE'=>'3',
              'TXN_DESC'=>'Order from Merchant Test Store',
            ];
            */
            $args['SECURE_SIGNATURE']=c9wep_ethereumpay_secure_signature($args, $this->password);

            $args['post_url']=$this->get_payment_request_endpoint();
            $args['CUSTOMER_ID']=$order->get_customer_id();//bug of payment API, CUSTOMER_ID is optional in request body, but it's Mandatory in response body of API
            $args['PYMT_IND']=$this->PYMT_IND;
            $args['PYMT_CRITERIA']=$this->PYMT_CRITERIA;
            if($this->simulator_mode){
                $args['simulator_mode']='yes';//allow we check the post data on post form page
                $args['password']=$this->password;//c9wep_ethereumpay_get_simulator_password();//$sm_args['Password'];//test password in API document
            }

            foreach ($args as $field => $val) {
                $_GET[$field]=$val;
            }
            require WPAB_DIR . '/ethereumpay/post-form.php';
        }
 
        /*
         * We're processing the payments here, everything about it is in Step 5
         */
        public function get_payment_request_endpoint() {  
            if($this->simulator_mode){
                return c9wep_get_ethereumpay_simulator_url();
            }
            return $this->bank_url;
        }
        
        function check_payment_response() {
            $authorised = false;            
            $sha512 = "";
            
            $txnSecureHash = array_key_exists("SECURE_SIGNATURE", $_REQUEST ) ? $_REQUEST["SECURE_SIGNATURE"] : "";
            
            $order_id = (int) $_REQUEST['MERCHANT_TRANID'];
            $order = new WC_Order($order_id);
            
            // $DR = $this->parseDigitalReceipt();
            //$ThreeDSecureData = $this->parse3DSecureData();
            
            /* Make sure user entered Transaction Success message otherwise use the default one */
            if( trim( $this->success_message ) == "" || $this->success_message == null ) {
                $this->success_message = "Thank you for shopping with us. Your account has been charged and your transaction is successful. We will be shipping your order to you soon.";
            }
            
            /* Make sure user entered Transaction Faild message otherwise use the default one */
            if( trim( $this->failed_message ) == "" || $this->failed_message == null ) {
                $this->failed_message = "Thank you for shopping with us. However, the transaction has been declined.";
            }
            
            $msg = array();         
            $msg['class']   = 'error';
            $msg['message'] = $this->failed_message;
            
            if ( $_REQUEST['RESPONSE_CODE'] == "0") {
                $signature_fields=c9wep_ethereumpay_response_signature_fields();
                $signature_args=[];
                foreach ($signature_fields as $field) {
                    $signature_args[$field]=$_REQUEST[$field];
                }
                $sha512=c9wep_ethereumpay_secure_signature($signature_args, $this->password);

                // ob_start();
                // print_r($signature_args);
                // echo PHP_EOL;
                // print_r($_REQUEST);
                // echo PHP_EOL;
                // print_r($sha512);
                // echo PHP_EOL;
                // echo PHP_EOL;
                // echo PHP_EOL;
                // echo PHP_EOL;
                // $data1=ob_get_clean();
                // file_put_contents(dirname(__FILE__)  . '/sha512.log',$data1,FILE_APPEND);
                if ( strtoupper( $txnSecureHash ) != strtoupper( $sha512 ) ) {
                    $authorised = false;
                } else {                    
                    if( $_REQUEST['RESPONSE_CODE'] == "0" ) {                                   
                        $authorised = true;
                    } else {
                        $authorised = false;
                    }
                }
            
            } else {
                $authorised = false;
            }
            
            if( $authorised ) {
                try {                   
                    if( $order -> status !== 'completed' ) {                        
                        $msg['message'] = $this->success_message;
                        $msg['class'] = 'success';
                        if( $order -> status != 'processing' ) {
                            $order -> payment_complete();
                            $order -> add_order_note('Payment successful<br/>Receipt Number: '.$_REQUEST["TRANSACTION_ID"]);
                            WC()->cart->empty_cart();
                        }
                    }
                } catch( Exception $e ) {
                    $msg['class'] = 'error';
                    $msg['message'] = $this->failed_message;
                
                    $order -> update_status('failed');
                    $order -> add_order_note('Payment Transaction Failed');
                    //$order -> add_order_note($this->msg['message']);
                }
            } else {
                $msg['class'] = 'error';
                $msg['message'] = $this->failed_message;
                
                $order -> update_status('failed');
                $order -> add_order_note('Payment Transaction Failed');
                //$order -> add_order_note($this->msg['message']);
            }
        
            if ( function_exists( 'wc_add_notice' ) ) {
                wc_add_notice( $msg['message'], $msg['class'] );
            }
            else {
                if($msg['class']=='success') {
                    WC()->add_message( $msg['message']);
                }else {
                    WC()->add_error( $msg['message'] );
                }
                WC()->set_messages();
            }
        
            wp_redirect( $order->get_checkout_order_received_url() );
            exit;       
        }

        /*
         * We're processing the payments here, everything about it is in Step 5
         */
        function process_payment($order_id) {           
            $order = new WC_Order($order_id);  
            //we don't redirect default recipt page, we direct to post form page
            // return array('result' => 'success', 'redirect' => c9wep_get_ethereumpay_post_form_url($args));
            return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url( true ));
        }
        public function process_payment_b0( $order_id ) {
         
            global $woocommerce;
         
            // we need it to get any order detailes
            $order = wc_get_order( $order_id );
         
         
            /*
             * Array with parameters for API interaction
             */
            $args = array(
         
         
            );
         
            /*
             * Your API interaction could be built with wp_remote_post()
             */
             $response = wp_remote_post( '{payment processor endpoint}', $args );
         
         
             if( !is_wp_error( $response ) ) {
         
                 $body = json_decode( $response['body'], true );
         
                 // it could be different depending on your payment processor
                 if ( $body['response']['responseCode'] == 'APPROVED' ) {
         
                    // we received the payment
                    $order->payment_complete();
                    $order->reduce_order_stock();
         
                    // some notes to customer (replace true with false to make it private)
                    $order->add_order_note( 'Hey, your order is paid! Thank you!', true );
         
                    // Empty cart
                    $woocommerce->cart->empty_cart();
         
                    // Redirect to the thank you page
                    return array(
                        'result' => 'success',
                        'redirect' => $this->get_return_url( $order )
                    );
         
                 } else {
                    wc_add_notice(  'Please try again.', 'error' );
                    return;
                }
         
            } else {
                wc_add_notice(  'Connection error.', 'error' );
                return;
            }
         
        }
 
        /*
         * In case you need a webhook, like PayPal IPN etc
         */
        public function webhook() {
         
            $order = wc_get_order( $_GET['id'] );
            $order->payment_complete();
            $order->reduce_order_stock();
         
            update_option('webhook_debug', $_GET);
        }
    }
}