<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once C9WEP_DIR . '/ethereumpay/class-wc-gateway-ethereumpay-order.php';

/**
 * Generates requests to send to EthereumPay
 */
class WC_Gateway_EthereumPay_Request {

	/**
	 * Stores line items to send to EthereumPay
	 * @var array
	 */
	protected $line_items = array();

	/**
	 * Pointer to gateway making the request
	 * @var WC_Gateway_EthereumPay
	 */
	protected $gateway;

	/**
	 * Endpoint for requests from EthereumPay
	 * @var string
	 */
	// protected $notify_url;

	/**
	 * Constructor
	 * @param WC_Gateway_EthereumPay $gateway
	 */
	public function __construct( $gateway ) {
		$this->gateway    = $gateway;
		// $this->notify_url = WC()->api_request_url( 'WC_Gateway_EthereumPay' );
	}

	/**
	 * Get the EthereumPay request URL for an order
	 * @param  WC_Order  $order
	 * @param  boolean $sandbox
	 * @return string
	 */
	public function get_request_url( $order ) {
		$EthereumPay_args = http_build_query( $this->get_ethereumpay_args( $order ), '', '&' );

		return c9hpp_get_ethereumpay_processer_url() .'/?' . $EthereumPay_args;
	}

	/**
	 * Get EthereumPay Args for passing to PP
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	protected function get_ethereumpay_args( $order ) {
		$order = new WC_Gateway_EthereumPay_Order($order);

		
		$args=array(
            'SYS_TRUST_CODE'=>$this->gateway->get_SYS_TRUST_CODE(),
            'SHOP_TRUST_CODE'=>$this->gateway->get_SHOP_TRUST_CODE(),
            'SHOP_ID'=>$this->gateway->get_SHOP_ID(),
            'PROD_ID'=>$this->gateway->get_PROD_ID(),
            'CURRENCY' =>$this->gateway->get_currency(), //get_woocommerce_currency(),
            'simulator_mode'=>$this->gateway->get_simulator_mode(),
		);

		$api=new EthereumPay_API($args);

		$item_args=$this->get_line_item_args($order);
		/* sample
			Array
			(
			    [item_name_1] => delivery product
			    [quantity_1] => 1
			    [amount_1] => 100.00
			    [item_number_1] => 
			    [item_name_2] => trial subscription test
			    [quantity_2] => 3
			    [amount_2] => 5.00
			    [item_number_2] => 
			)
		*/
		$ORDER_ITEM='';
		foreach ($item_args as $key => $value) {
			if(strpos($key,'item_name_') !== false){//find item name item
				$ORDER_ITEM .= ';' . $value;
			}else{
				continue;
			}
		}
		$ORDER_ITEM=trim($ORDER_ITEM,';');

		$pay_amount=c9hpp_get_order_pay_amount($order);
		$order_args=array(
            'ORDER_ID'=>$order->id,
            'AMOUNT'=>$pay_amount,
            'ORDER_ITEM'=>$ORDER_ITEM,
            'SHOP_PARA'=>$order->order_key,
		);

		$data=$api->get_post_fields($order_args);
		return apply_filters( 'woocommerce_EthereumPay_args', $data, $order->getOrginOrder());
	}

	// private function generateSignature(&$req, $secretKey) {
	// 	$arr = array($req['api_key'], $req['pm_id'], $req['amount'], $req['currency'],
	// 			$req['track_id'], $req['sub_track_id'], $secretKey);
	// 	$msg = implode('|', $arr);
	// 	return md5($msg);
	// }

	/**
	 * Get phone number args for EthereumPay request
	 * @param  WC_Order $order
	 * @return array
	 */
	// protected function get_phone_number_args( $order ) {
	// 	$order = new WC_Gateway_EthereumPay_Order($order);
	// 	if ( in_array( $order->billing_country, array( 'US','CA' ) ) ) {
	// 		$phone_number = str_replace( array( '(', '-', ' ', ')', '.' ), '', $order->billing_phone );
	// 		$phone_args   = array(
	// 			'night_phone_a' => substr( $phone_number, 0, 3 ),
	// 			'night_phone_b' => substr( $phone_number, 3, 3 ),
	// 			'night_phone_c' => substr( $phone_number, 6, 4 ),
	// 			'day_phone_a' 	=> substr( $phone_number, 0, 3 ),
	// 			'day_phone_b' 	=> substr( $phone_number, 3, 3 ),
	// 			'day_phone_c' 	=> substr( $phone_number, 6, 4 )
	// 		);
	// 	} else {
	// 		$phone_args = array(
	// 			'night_phone_b' => $order->billing_phone,
	// 			'day_phone_b' 	=> $order->billing_phone
	// 		);
	// 	}
	// 	return $phone_args;
	// }

	/**
	 * Get shipping args for EthereumPay request
	 * @param  WC_Order $order
	 * @return array
	 */
	// protected function get_shipping_args( $order ) {
	// 	$order = new WC_Gateway_EthereumPay_Order($order);
	// 	$shipping_args = array();

	// 	if ( 'yes' == $this->gateway->get_option( 'send_shipping' ) ) {
	// 		$shipping_args['address_override'] = $this->gateway->get_option( 'address_override' ) === 'yes' ? 1 : 0;
	// 		$shipping_args['no_shipping']      = 0;

	// 		// If we are sending shipping, send shipping address instead of billing
	// 		$shipping_args['first_name']       = $order->shipping_first_name;
	// 		$shipping_args['last_name']        = $order->shipping_last_name;
	// 		$shipping_args['company']          = $order->shipping_company;
	// 		$shipping_args['address1']         = $order->shipping_address_1;
	// 		$shipping_args['address2']         = $order->shipping_address_2;
	// 		$shipping_args['city']             = $order->shipping_city;
	// 		$shipping_args['state']            = $this->get_EthereumPay_state( $order->shipping_country, $order->shipping_state );
	// 		$shipping_args['country']          = $order->shipping_country;
	// 		$shipping_args['zip']              = $order->shipping_postcode;
	// 	} else {
	// 		$shipping_args['no_shipping']      = 1;
	// 	}

	// 	return $shipping_args;
	// }

	/**
	 * Get line item args for EthereumPay request
	 * @param  WC_Order $order
	 * @return array
	 */
	protected function get_line_item_args( $order ) {
		/**
		 * Try passing a line item per product if supported
		 */
		if ( ( ! wc_tax_enabled() || ! wc_prices_include_tax() ) && $this->prepare_line_items( $order ) ) {

			$line_item_args             = $this->get_line_items();
			$line_item_args['tax_cart'] = $order->get_total_tax();

			if ( $order->get_total_discount() > 0 ) {
				$line_item_args['discount_amount_cart'] = round( $order->get_total_discount(), 2 );
			}

		/**
		 * Send order as a single item
		 *
		 * For shipping, we longer use shipping_1 because EthereumPay ignores it if *any* shipping rules are within EthereumPay, and EthereumPay ignores anything over 5 digits (999.99 is the max)
		 */
		} else {

			$this->delete_line_items();

			$this->add_line_item( $this->get_order_item_names( $order ), 1, number_format( $order->get_total() - round( $order->get_total_shipping() + $order->get_shipping_tax(), 2 ), 2, '.', '' ), $order->get_order_number() );
			$this->add_line_item( sprintf( __( 'Shipping via %s', 'woocommerce' ), ucwords( $order->get_shipping_method() ) ), 1, number_format( $order->get_total_shipping() + $order->get_shipping_tax(), 2, '.', '' ) );

			$line_item_args = $this->get_line_items();
		}

		return $line_item_args;
	}

	/**
	 * Get order item names as a string
	 * @param  WC_Order $order
	 * @return string
	 */
	protected function get_order_item_names( $order ) {
		$item_names = array();

		foreach ( $order->get_items() as $item ) {
			$item_names[] = $item['name'] . ' x ' . $item['qty'];
		}

		return implode( ', ', $item_names );
	}

	/**
	 * Get order item names as a string
	 * @param  WC_Order $order
	 * @param  array $item
	 * @return string
	 */
	protected function get_order_item_name( $order, $item ) {
		$item_name = $item['name'];
		$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );

		if ( $meta = $item_meta->display( true, true ) ) {
			$item_name .= ' ( ' . $meta . ' )';
		}

		return $item_name;
	}

	/**
	 * Return all line items
	 */
	protected function get_line_items() {
		return $this->line_items;
	}

	/**
	 * Remove all line items
	 */
	protected function delete_line_items() {
		$this->line_items = array();
	}

	/**
	 * Get line items to send to EthereumPay
	 *
	 * @param  WC_Order $order
	 * @return bool
	 */
	protected function prepare_line_items( $order ) {
		$order = new WC_Gateway_EthereumPay_Order($order);
		$this->delete_line_items();
		$calculated_total = 0;

		// Products
		foreach ( $order->get_items( array( 'line_item', 'fee' ) ) as $item ) {
			if ( 'fee' === $item['type'] ) {
				$line_item        = $this->add_line_item( $item['name'], 1, $item['line_total'] );
				$calculated_total += $item['line_total'];
			} else {
				$product          = $order->get_product_from_item( $item );
				$line_item        = $this->add_line_item( $this->get_order_item_name( $order, $item ), $item['qty'], $order->get_item_subtotal( $item, false ), $product->get_sku() );
				$calculated_total += $order->get_item_subtotal( $item, false ) * $item['qty'];
			}

			if ( ! $line_item ) {
				return false;
			}
		}

		// Shipping Cost item - EthereumPay only allows shipping per item, we want to send shipping for the order
		if ( $order->get_total_shipping() > 0 && ! $this->add_line_item( sprintf( __( 'Shipping via %s', 'woocommerce' ), $order->get_shipping_method() ), 1, round( $order->get_total_shipping(), 2 ) ) ) {
			return false;
		}

		// Check for mismatched totals
		if ( wc_format_decimal( $calculated_total + $order->get_total_tax() + round( $order->get_total_shipping(), 2 ) - round( $order->get_total_discount(), 2 ), 2 ) != wc_format_decimal( $order->get_total(), 2 ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add EthereumPay Line Item
	 * @param string  $item_name
	 * @param integer $quantity
	 * @param integer $amount
	 * @param string  $item_number
	 * @return bool successfully added or not
	 */
	protected function add_line_item( $item_name, $quantity = 1, $amount = 0, $item_number = '' ) {
		$index = ( sizeof( $this->line_items ) / 4 ) + 1;

		if ( ! $item_name || $amount < 0 || $index > 9 ) {
			return false;
		}

		$this->line_items[ 'item_name_' . $index ]   = html_entity_decode( wc_trim_string( $item_name, 127 ), ENT_NOQUOTES, 'UTF-8' );
		$this->line_items[ 'quantity_' . $index ]    = $quantity;
		$this->line_items[ 'amount_' . $index ]      = $amount;
		$this->line_items[ 'item_number_' . $index ] = $item_number;

		return true;
	}

	/**
	 * Get the state to send to EthereumPay
	 * @param  string $cc
	 * @param  string $state
	 * @return string
	 */
	// protected function get_EthereumPay_state( $cc, $state ) {
	// 	if ( 'US' === $cc ) {
	// 		return $state;
	// 	}

	// 	$states = WC()->countries->get_states( $cc );

	// 	if ( isset( $states[ $state ] ) ) {
	// 		return $states[ $state ];
	// 	}

	// 	return $state;
	// }
}
?>