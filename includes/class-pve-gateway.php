<?php

/**
 * PVE_Gateway
 *
 * Handles the WooCommerce payment gateway integration. This means extending WC_Payment_Gateway, defining the gateway ID pve_eth, 
 * rendering the settings fields in WooCommerce, and handling the checkout payment fields display. 
 * It coordinates between the price class and the converter class at checkout to produce the final ETH amount and EIP-681 URI, 
 * then passes those to the front end. It does not fetch prices directly and does not do arithmetic directly — it delegates those responsibilities to PVE_Price and PVE_Converter. 
 * It does not render the admin pending payments page.
 *
 * Hooks registered by this class:
 *
 * Options read:
 *
 * Order meta read:
 *
 * @package Payments_Via_Ethereum
 * @since 1.420.69
 */
add_filter( 'woocommerce_payment_gateways', 'pve_add_gateway_class' );
function pve_add_gateway_class( $gateways ) {
    $gateways[] = 'PVE_Gateway'; // your class name is here
    return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'pve_init_gateway_class' );
function pve_init_gateway_class() {
    if( !class_exists('WC_Payment_Gateway') )  return;

    class PVE_Gateway extends WC_Payment_Gateway {
 
        /**
         * Class constructor, more about it in Step 3
         */
        public function __construct() {
         
            $this->id = 'pve_gateway'; // payment gateway ID
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Pay Via Eth';
            $this->method_description = 'Ethereum Payments for Wordpress/WooCommerce'; // will be displayed on the options page
         
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
            $this->icon = PVE_URL . 'assets/images/64px-Ethereum-icon-purple.svg.png'; 

            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, function()
                       {
                           $this->process_admin_options();
                           wp_wc_pve_write_log('Plugin Settings Saved', E_USER_NOTICE);
                       });

            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));   
        }

        /**
     * Descriptions, used on settings page.
     */
        public function get_wallet_addresses_description(){
            return 'When a customer make a transaction by scanning QR Code, the combination of ether amount and one of above wallet address is the only way that we can use to track the transaction from ethereum network, in short, if two customers pay the same ether amount to the same wallet address, we have no idea who paid the order, to avoid such kind of potential collision, as many as wallet addresses will be a reasonable solution';
        }

        /**
         * Plugin options.
         */
        public function init_form_fields(){
         
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Ethereum Payment',
                    'type'        => 'checkbox',
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
                'pve_check_transaction_status_interval' => array(
                    'title'       => 'The inverval to check transaction status(cronjob)',
                    'type'        => 'select',
                    'options'=>[
                      '3_minutes'=>__('3 Minutes','pve'),
                      '5_minutes'=>__('5 Minutes','pve'),
                      '8_minutes'=>__('8 Minutes','pve'),
                      '10_minutes'=>__('10 Minutes','pve'),
                    ],
                    'sanitize_callback'=>array($this, 'sanitize_pve_check_transaction_status_interval'),
                    'default'     => 5,
                    'description' => 'If the browser was closed accidently when customer try to make a payment, we use this cronjob to scan the ethereum network for the order which is not expired on payment',
                ),
            );
        }

        public function pve_get_wallet_addresses() {
            return $this->get_option('wallet_addresses');
          // }
        }

        public function pve_sanitize_pve_check_transaction_status_interval( $input ) {
            pve_setup_check_transaction_status_cron_job('pve_check_transaction_status_cron_hook', $input);
            return $input; 
        }

        public function pve_sanitize_wallet_address( $input ) {
            return $input;
        }

        public function pve_get_form_field_with_key( $key ) {
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
        public function pve_generate_ether_addresses_html( $key, $data ) {
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
				 <input 
                                    type="text" 
                                    name="<?php echo esc_attr( $field ); ?>[<?php echo $i; ?>]" 
                                    id="<?php echo esc_attr( $field ); ?>_<?php echo $i; ?>" 
                                    style="<?php echo esc_attr( $data['css'] ); ?>" 
				    value="<?php
                                           //Check if $data['addresses'] is an array before accessing it.
                                           if (is_array($data['addresses'])) {
                                              //If it's an array, use array_key_exists for safety
                                              if (array_key_exists($i, $data['addresses'])) {
                                                 echo $data['addresses'][$i];
                                              } else {//Key doesn't exisit = empty
                                                 echo '';
                                              }
                                           } else {//Not an array = empty
                                                   echo '';
                                           }
                                           ?>"
                                 >
                              </td>
                              <td class="td-action">
                              <?php
                                 //Fetches the network for use in the address url
                                 $network = $this->get_ether_network();

                                 //Check if $data['addresses'] is an array before accessing it
                                 if (is_array($data['addresses'])) {
                                    // If it's an array, use array_key_exists for safety
                                    if (array_key_exists($i, $data['addresses'])) {
                                       $link = pve_get_wallet_address_transaction_view_link($network, $data['addresses'][$i], 'view');
                                       echo $link;
                                    }
                                 } else { //Handle the case where $data['addresses'] is not an array (empty content)
                                    echo '';
                                 }
                              ?>
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
            </td>
          </tr>
          <?php
          return ob_get_clean();
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
        public function pve_generate_link_html( $key, $data ) {
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
                    <a href="<?php echo pve_get_enther_price_url($args); ?>" target="_blank" class="button button-default btn btn-primary"><?php echo wp_kses_post( $data['title'] ); ?></a>
                <?php echo $this->get_description_html( $data ); ?>
              </fieldset>
            </td>
          </tr>
          <?php
          return ob_get_clean();
        }

        private function pve_apply_markup( $price ) {
          $markup_percent = $this->settings['markup_percent'];
          $markup_percent = ! empty( $markup_percent ) ? $markup_percent : 0;
          $multiplier     = ( $markup_percent / 100 ) + 1;

          return round( $price * $multiplier, 5, PHP_ROUND_HALF_UP );
        }

        public function pve_get_eth_amount() {
            $total    = WC()->cart->total;
            $eth_value = pve_convert_to_eth_amount($total);
            return $eth_value;
        }

        /**
         * You will need it if you want your custom credit card form, Step 4 is about it
         */
        public function pve_payment_fields() {
         
            // ok, let's display some description before the payment form
            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                // display the description with <p> tags etc.
                echo wpautop( wp_kses_post( $this->description ) );
            }
            //<div class="eth-amount-wapper">
             // <div class="eth-amount-title"><span>
                //<?php echo 'Send: ' . $this->get_eth_amount() . ' ETH' >
             // </span></div>
             // <input type="hidden" name="eth-amount" id="eth-amount" class="form-control" required="required" pattern="" title="">
            //</div>
        }

        /*
         * We're processing the payments here, everything about it is in Step 5
         */
        function pve_process_payment($order_id) {           
            $order = new WC_Order($order_id);  
            //we don't redirect default recipt page, we direct to post form page
            return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url( true ));
        }
    }
}
