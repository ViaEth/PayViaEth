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
* Hooks registered:
*
* Options read: // via get_option()
*   woocommerce_pve_gateway_settings[ title ]
*   woocommerce_pve_gateway_settings[ description ]
*   woocommerce_pve_gateway_settings[ enabled ]
*   woocommerce_pve_gateway_settings[ wallet_addresses ]
*
* Options written: // via update_option() or add_option()
*   woocommerce_pve_gateway_settings
*
* Order meta read: // via get_post_meta()
* Order meta written: // via update_post_meta()
*
* Constants define:
* Files loaded:
*
* @package Payments_Via_Ethereum
* @since 1.420.69
*/

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

if( !class_exists('WC_Payment_Gateway') )  return;

class PVE_Gateway extends WC_Payment_Gateway {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->id = 'pve_gateway'; //Payment gateway ID
		$this->has_fields = true; //This might be false. The customer never enters any payment related information. They just pay.
		$this->method_title = 'Pay Via Eth';
		$this->method_description = 'Ethereum Payments for Wordpress/WooCommerce'; //Displayed on the payments options page
		$this->supports = array(
			'products'
		);

		//Method with all the options fields
		$this->init_form_fields();

		//Load the settings.
		$this->init_settings();
		$this->title = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->enabled = $this->get_option( 'enabled' );
		$this->icon = PVE_URL . 'assets/images/64px-Ethereum-icon-purple.svg.png'; 

		//This action hook saves the settings
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_settings' ) );
	}

	/**
	* Save Settings
	* hooked to admin action, called by WordPress
	*/
	public function save_settings() {
			$this->process_admin_options();
			wp_wc_pve_write_log( 'Plugin Settings Saved', E_USER_NOTICE );
	}

	/**
	* Plugin options.
	* WooCommerce calls this to build the settings page
	*/
	public function init_form_fields(){
		 
			$this->form_fields = array(
				'enabled' => array( // gateway on/off toggle
					'title'		  => 'Enable/Disable',
					'label'		  => 'Enable Ethereum Payment',
					'type'		  => 'checkbox',
					'default'	  => 'no'
				),
				'title' => array( // checkout display title
					'title'		  => 'Title',
					'type'		  => 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'	  => 'Pay via Eth',
					'desc_tip'	  => true,
				),
				'description' => array( // checkout display description
					'title'		  => 'Description',
					'type'		  => 'textarea',
					'description' => 'This controls the description which the user sees during checkout.',
					'default'	  => 'Pay Via Eth.',
				),
				'wallet_addresses' => array( // up to 10 merchant ETH addresses with rotation
				  'title'			  => __( 'Wallet Addresses', 'woocommerce-integration-demo' ),
				  'type'			  => 'ether_addresses',
				  'addresses' => $this->get_option('wallet_addresses'),
				  'description'		  => "Merchant Wallets Address" ,
				  'sanitize_callback'=>array($this, 'sanitize_wallet_address'),//'sanitize_wallet_address',
				  'desc_tip'		  => true,
				),
				'block_explorer_url' => array( // base URL for block explorer links in admin
					'title'		  => 'Block Explorer URL',
					'type'		  => 'text',
					'description' => 'Block Explorer base URL for checking transactions.',
				),
			);
		}

	/**
	 * We're processing the payments here, everything about it is in Step 5
	 */
	public function process_payment($order_id) {}
	
	/**
	* Displays the ETH amount, assigned address, and QR code at checkout.
	* Called by WooCommerce when rendering the payment form.
	* WooCommerce calls this to render the form
	*/
	//public function payment_fields() {}

	/**
	* Validates the checkout form before order is placed.
	* Return true to proceed, false to block with wc_add_notice().
	* WooCommerce calls this before processing
	*/
	//public function validate_fields()

	/**
	* Renders the payment instruction page after order is placed.
	* Called by WooCommerce on the order received/pay page.
	* WooCommerce calls this to render the receipt
	*/
	//public function receipt_page( $order_id )

	/**
	* Selects and assigns one of the 10 merchant addresses via rotation.
	* Stores result in _pve_merchant_address order meta.
	* called only by process_payment()
	*/
	//private function get_assigned_address( $order_id )

	/**
	* Constructs the ethereum:<address>?value=<wei> URI string.
	* Passed to qr generator for client-side QR code rendering.
	* called only by payment_fields()
	*/
	//private function build_eip681_uri( $address, $wei_amount )

	/**
	* Returns the current address rotation position.
	* Increments and wraps.
	* called only by get_assigned_address()
	*/
	//private function get_rotation_index()
}
