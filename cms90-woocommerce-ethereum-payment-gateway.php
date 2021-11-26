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
            $this->method_title = 'Pay via Eth';
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
            $this->testmode = $this->is_test_mode();
            $this->wallet_addresses = $this->testmode ? $this->get_option( 'test_wallet_addresses' ) : $this->get_option( 'wallet_addresses' );
            // $this->apikey = $this->testmode ? $this->get_option( 'test_apikey' ) : $this->get_option( 'apikey' );
            $this->apikey = $this->testmode ? $this->get_option( 'test_apikey' ) : $this->get_option( 'apikey' );
         
            // $test_wallet_addresses=$this->get_option('test_wallet_addresses');
            // ob_start();
            // print_r($test_wallet_addresses);
            // echo PHP_EOL;
            // echo PHP_EOL;
            // echo PHP_EOL;
            // echo PHP_EOL;
            // $data1=ob_get_clean();
            // file_put_contents(dirname(__FILE__)  . '/test_wallet_addresses.log',$data1,FILE_APPEND);
            // if($this->testmode){
            //     $this->simulator_mode = 'yes' === $this->get_option( 'simulator_mode' );
            // }
            // if($this->simulator_mode){
            //     //$this->password=c9wep_ethereumpay_get_simulator_password();
            // }

        	$this->icon = C9WEP_URL . 'assets/images/64px-Ethereum-icon-purple.svg.png'; 

            // $this->callback_url = $this->get_callback_url();//home_url('/wc-api/' . $this->id);
            // add_action('woocommerce_api_' . $this->id, array($this, 'check_payment_response'));  
	    
            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
         
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));   
            // We need custom JavaScript to obtain a token
            // add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            //add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) ); 
            // You can also register a webhook here
            // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
         }
 
        public function get_callback_url(){
            // return c9wep_get_callback_url($this->id);
        }

        public function get_callback_url_b0(){
            // return c9wep_get_callback_url($this->id);
        }

        public function get_api_description(){
            return 'You can get apikey from <a href="https://etherscan.io/myapikey" target="_blank">https://etherscan.io/myapikey</a>, the same apikey can be used for both test and live mode if you want,<br/>for a <b>free API plan</b>, there is a limitation on number of API call(<b>5 calls per second</b>), so, if you use a <b>free API plan</b> in a high traffic site, most of API call may failed since the limitation of API plan';
        }

        public function get_wallet_addresses_description(){
            return 'When a customer make a transaction by scanning QR Code, the combination of ether amount and one of above wallet address is the only way that we can use to track the transaction from ethereum network, in short, if two customers pay the same ether amount to the same wallet address, we have no idea who paid the order, to avoid such kind of potential collision, as many as wallet addresses will be a reasonable solution';
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
                    'description' => ($this->is_test_mode()) ? '<b style="color:red;">This payment is in Test Mode</b>' : '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Pay via Eth',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with ether.',
                ),
                // 'wallet_address' => array(
                //     'title'       => 'Live Wallet Address',
                //     'type'        => 'text'
                // ),
                'wallet_addresses' => array(
                  'title'             => __( 'Wallet Addresses', 'woocommerce-integration-demo' ),
                  'type'              => 'ether_addresses',
                  'addresses' => $this->get_option('wallet_addresses'),
                  'description'       => __( $this->get_wallet_addresses_description(), 'woocommerce-integration-demo' ),
                  'sanitize_callback'=>array($this, 'sanitize_wallet_address'),//'sanitize_wallet_address',
                  'desc_tip'          => true,
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'test_network' => array(
                    'title'       => 'Test Network',
                    'type'        => 'select',
                    'options'=>c9wep_get_test_networks(),
                    // [
                    //     'kovan'=>'Kovan Testnet',
                    //     'ropsten'=>'Ropsten Testnet',
                    //     'rinkeby'=>'Rinkeby Testnet',
                    //     'goerli'=>'Goerli Testnet',
                    // ],
                    'default'     => 'kovan',
                    'description' => 'Please make sure set your test wallet address to the same network with above setting',
                ),
                'apikey' => array(
                    'title'       => 'Etherscan API Key',
                    'type'        => 'password',
                    'description' => $this->get_api_description(),
                ),
                'check_connection' => array(
                    'title'       => 'Check Connection',
                    'type'        => 'link',
                    'description' => 'Check Connection to etherscan.io with above API Key',
                ),
                'total_time_transaction_timeout' => array(
                    'title'       => 'Total transaction lifetime timeout',
                    'type'        => 'select',
                    'options'=>[
                        15 => '15 Minutes',
                        20 => '20 Minutes',
                        25 => '25 Minutes',
                        30 => '30 Minutes',
                        35 => '35 Minutes',
                    ],
                    'default'     => 15,
                    'description' => 'If there is no transaction was confrimed in above time threshod, the payment will be set as payment expired, the default value is 15 minutes',
                ),
                'interval_to_check_transaction_status' => array(
                    'title'       => 'The inverval to check transaction status',
                    'type'        => 'select',
                    'options'=>[
                        15 => 'Every 15 Seconds',
                        20 => 'Every 20 Seconds',
                        25 => 'Every 25 Seconds',
                        30 => 'Every 30 Seconds',
                    ],
                    'default'     => 15,
                    'description' => 'The interval that we scan the etherscan.io to get the transaction status by retrieving a transaction list with our wallet address, the default value is every 15 seconds since the time of a bock creation on ethereum network may take 13 seconds, so, there is no need to set a short period time than that',
                ),
                'c9wep_check_transaction_status_interval' => array(
                    'title'       => 'The inverval to check transaction status(cronjob)',
                    'type'        => 'select',
                    'options'=>[
                      '3_minutes'=>__('3 Minutes','c9wep'),
                      '5_minutes'=>__('5 Minutes','c9wep'),
                      '8_minutes'=>__('8 Minutes','c9wep'),
                      '10_minutes'=>__('10 Minutes','c9wep'),
                    ],
                    'sanitize_callback'=>array($this, 'sanitize_c9wep_check_transaction_status_interval'),
                    'default'     => 5,
                    'description' => 'If the browser was closed accidently when customer try to make a payment, we use this cronjob to scan the ethereum network for the order which is not expired on payment',
                ),
                // 'simulator_mode' => array(
                //         'title'   => __( 'Test With Simulator', 'woocommerce' ),
                //         'type'    => 'checkbox',
                //         'label'   => __( 'Enable Simulator', 'woocommerce' ),
                //         'description' => 'Enable this option to allow you test payment workflow without sending data to actually wallet address(only work in test mode)',
                //         'default' => 'no'
                // ),
                // 'test_apikey' => array(
                //     'title'       => 'Etherscan API Key(test mode)',
                //     'type'        => 'password',
                //     'description' => $this->get_api_description(),
                // ),
                // 'check_live_connection' => array(
                //     'title'       => 'Check Live Connection',
                //     'type'        => 'link',
                //     'description' => 'Check Live Connection to etherscan.io with above live API Key',
                // ),
                // 'check_test_connection' => array(
                //     'title'       => 'Check Test Mode Connection',
                //     'type'        => 'link',
                //     'description' => 'Check Test Mode Connection to etherscan.io with above test API Key and above test network',
                // ),
                // 'test_wallet_addresses' => array(
                //   'title'             => __( 'Test Wallet Addresses', 'woocommerce-integration-demo' ),
                //   'type'              => 'ether_addresses',
                //   'addresses' => $this->get_option('test_wallet_addresses'),
                //   'description'       => __( $this->get_wallet_addresses_description(), 'woocommerce-integration-demo' ),
                //   'sanitize_callback'=>array($this, 'sanitize_test_wallet_address'),
                //   'desc_tip'          => true,
                // ),
            );
        }

        public function get_wallet_addresses() {
          // if($this->is_test_mode()){
          //   return $this->get_option('test_wallet_addresses');
          // }else{
            return $this->get_option('wallet_addresses');
          // }
        }

        public function sanitize_c9wep_check_transaction_status_interval( $input ) {
            c9wep_setup_check_transaction_status_cron_job('c9wep_check_transaction_status_cron_hook', $input);
            return $input; 
        }

        public function sanitize_test_wallet_address( $input ) {
            return $input;
        }

        public function sanitize_wallet_address( $input ) {
            return $input;
        }

        public function get_total_time_transaction_timeout(){
          return $this->get_option( 'total_time_transaction_timeout' );
        }

        public function get_interval_check_status(){
          return $this->get_option( 'interval_to_check_transaction_status' );
        }
        /**
         * Initialize integration settings form fields.
         *
         * @return void
         */
        // public function init_form_fields_b0() {
        //   $this->form_fields = array(
        //     // don't forget to put your other settings here
                
        //     'test_wallet_addresses' => array(
        //       'title'             => __( 'Customize!', 'woocommerce-integration-demo' ),
        //       'type'              => 'ether_addresses',
        //       'custom_attributes' => array(
        //         'onclick' => "location.href='http://www.woothemes.com'",
        //       ),
        //       'description'       => __( 'Customize your settings by going to the integration site directly.', 'woocommerce-integration-demo' ),
        //       'desc_tip'          => true,
        //     )
        //   );
        // }
        
        public function get_ether_address_view_root_with_key( $key ) {
          if($this->is_test_mode()){
            $network=$this->get_option( 'test_network' );
          }else{
            $network='main';
          }

          return $network;//c9wep_get_transaction_networks($network);
        }
        
        public function get_form_field_with_key( $key ) {
          $field    = $this->plugin_id . $this->id . '_' . $key;
          return $field;
        }

        /**
         * Generate Button HTML.
         *
         * @access public
         * @param mixed $key
         * @param mixed $data
         * @since 1.0.0
         * @return string
         */
        public function generate_ether_addresses_html( $key, $data ) {
          // $field    = $this->plugin_id . $this->id . '_' . $key;
          $field    = $this->get_form_field_with_key($key);
          $defaults = array(
            'class'             => '',
            'css'               => '',
            'addresses' => array(),
            'desc_tip'          => false,
            'description'       => '',
            'title'             => '',
          );
        
          $data = wp_parse_args( $data, $defaults );
        
          ob_start();
          ?>
          <tr valign="top">
            <th scope="row" class="titledesc">
              <label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
              <?php echo $this->get_tooltip_html( $data ); ?>
            </th>
            <td class="forminp">
                <div class="address-wrapper">
                    <table class="table table-hover table-addresses">
                      <thead>
                        <tr>
                          <th class="th-no">No</th>
                          <th class="th-address">Address</th>
                          <th class="th-action">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        for ($i=1; $i <11 ; $i++):
                        ?>
                            <tr>
                              <td>
                                  <?php echo '#' . $i; ?>
                              </td>
                              <td class="td-address">
                                 <input type="text" name="<?php echo esc_attr( $field ); ?>[<?php echo $i; ?>]" id="<?php echo esc_attr( $field ); ?>_<?php echo $i; ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo $data['addresses'][$i]; ?>"> 
                              </td>
                              <td class="td-action">
                                <?php 
                                  $network=$this->get_ether_network();//$this->get_ether_address_view_root_with_key($key);
                                  $link=c9wep_get_wallet_address_transaction_view_link($network,$data['addresses'][$i], 'view');
                                  echo $link;
                                ?>
                                <?php if(false): ?>
                                 <input type="text" name="<?php echo esc_attr( $field ); ?>[<?php echo $i; ?>]" id="<?php echo esc_attr( $field ); ?>_<?php echo $i; ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo $data['addresses'][$i]; ?>"> 
                                <?php endif;//end false ?>
                              </td>
                            </tr>
                        <?php
                        endfor;
                        ?>
                      </tbody>
                    </table>
                </div>
                <p class="description">
                    <?php echo $data['description'];//$this->get_description_html(  ); ?>
                </p>
                <style type="text/css">
                    .address-wrapper{
                        height: 12rem;
                        overflow-y: scroll;
                        max-width: 500px;
                        background: #fff;
                        padding: 5px;
                        border: 1px solid #999;
                        border-radius: 5px;
                    }

                    .table-addresses th,
                    .table-addresses td{
                        padding: 5px !important;
                    }

                    .woocommerce .table-addresses th.th-no{
                        width: 30px;
                    }
                    .woocommerce .table-addresses th.th-address{
                        width: 99%;
                    }

                    .woocommerce .table-addresses .td-address input{
                        width: 100% !important;
                    }
                </style>
                <?php if(false): ?>
              <fieldset>
                <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
                <button class="<?php echo esc_attr( $data['class'] ); ?>" type="button" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo wp_kses_post( $data['title'] ); ?></button>
                <?php echo $this->get_description_html( $data ); ?>
              </fieldset>
                <?php endif;//end false ?>
            </td>
          </tr>
          <?php
          return ob_get_clean();
        }

        public function empty_apikey_notice() {
            return '<b>Please set above apikey first if you want to check connection</b>';
        }

        public function get_ether_network() {
          if($this->is_test_mode()){
            return $this->get_option( 'test_network' );
          }else{
            return 'main';
          }
        }

        public function get_api_args() {
          if($this->is_test_mode()){
              $args=[
                  'endpoint'=>$this->get_option( 'test_network' ),
                  // 'apikey'=>$this->get_option( 'test_apikey' ),
                  'apikey'=>$this->get_option( 'apikey' ),
              ];
          }else{
              $args=[
                  'endpoint'=>'main',
                  'apikey'=>$this->get_option( 'apikey' ),
              ];
          }

          return $args;
        }
        /**
         * Generate Button HTML.
         *
         * @access public
         * @param mixed $key
         * @param mixed $data
         * @since 1.0.0
         * @return string
         */
        public function generate_link_html( $key, $data ) {
          $field    = $this->plugin_id . $this->id . '_' . $key;
          $defaults = array(
            'class'             => 'button-secondary',
            'css'               => '',
            'custom_attributes' => array(),
            'desc_tip'          => false,
            'description'       => '',
            'title'             => '',
          );
        
          $data = wp_parse_args( $data, $defaults );
        
          ob_start();
          ?>
          <tr valign="top">
            <th scope="row" class="titledesc">
              <label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
              <?php echo $this->get_tooltip_html( $data ); ?>
            </th>
            <td class="forminp">
              <fieldset>
                <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
                <?php 
                    // if('check_live_connection' == $key){
                    //   $args=$this->get_api_args();
                    // }elseif('check_test_connection' == $key){
                    //   $args=$this->get_api_args('test');
                    // }
                      $args=$this->get_api_args();
                ?>
                <?php if(empty($args['apikey'])): ?>
                    <?php echo $this->empty_apikey_notice(); ?>
                <?php else: ?>
                    <a href="<?php echo c9wep_get_enther_price_url($args); ?>" target="_blank" class="button button-default btn btn-primary"><?php echo wp_kses_post( $data['title'] ); ?></a>
                <?php endif;//end empty() ?>

                <?php if(false): ?>
                    <a href="#" target="_blank" class="button button-default btn btn-primary"><?php echo wp_kses_post( $data['title'] ); ?></a>
                <button class="<?php echo esc_attr( $data['class'] ); ?>" type="button" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo wp_kses_post( $data['title'] ); ?></button>
                <?php endif;//end false ?>
                <?php echo $this->get_description_html( $data ); ?>
              </fieldset>
            </td>
          </tr>
          <?php
          return ob_get_clean();
        }

        private function apply_markup( $price ) {
          $markup_percent = $this->settings['markup_percent'];
          $markup_percent = ! empty( $markup_percent ) ? $markup_percent : 0;
          $multiplier     = ( $markup_percent / 100 ) + 1;

          return round( $price * $multiplier, 5, PHP_ROUND_HALF_UP );
        }

        public function is_test_mode() {
            return 'yes' === $this->get_option( 'testmode' );
        }

        public function get_eth_amount() {
            $total    = WC()->cart->total;
            $eth_value = c9wep_convert_to_eth_amount($total);
            return $eth_value;
        }

        /**
         * You will need it if you want your custom credit card form, Step 4 is about it
         */
        public function payment_fields() {
         
            // ok, let's display some description before the payment form
            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ( $this->testmode ) {
                    // if($this->simulator_mode){
                    // $this->description .= '<br/> <b style="color:red;">THIS IS SIMULATOR MODE</b> <br/>There are no data will be sent to any etherum net';
                    // $this->description  = trim( $this->description );
                    // }else{
                    $this->description .= '<br/> <b style="color:red;">THIS IS TEST MODE</b>';
                    $this->description  = trim( $this->description );
                    // }

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
            <?php if(false): ?>
            <div class="eth-wallet-address-wapper">
              <div class="eth-wallet-address-title"><span>
                <?php echo 'To:' . $this->wallet_address; ?>
              </span></div>
              <input type="hidden" name="eth-wallet-address" id="eth-wallet-address" class="form-control" value="<?php echo $this->wallet_address; ?>" required="required" pattern="" title="">
            </div>
            <?php endif;//end false ?>
            <?php
        }

        function receipt_page($order_id) {         
            // echo $this -> generate_payment_request_form($order_id);
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

        function process_ether_payment($order_id) {           
            global $woocommerce;
            $order = new WC_Order($order_id);  

            $order->payment_complete();
            if(function_exists('wc_reduce_stock_levels')){
              wc_reduce_stock_levels($order_id);
            }else{
              $order->reduce_order_stock();
            }
 
            // some notes to customer (replace true with false to make it private)
            $order->add_order_note( 'Hey, your order is paid! Thank you!', true );
 
            // Empty cart
            $woocommerce->cart->empty_cart();
 
            // Redirect to the thank you page
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );
            //we don't redirect default recipt page, we direct to post form page
            // return array('result' => 'success', 'redirect' => c9wep_get_ethereumpay_post_form_url($args));
            // return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url( true ));
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
 
    }
}