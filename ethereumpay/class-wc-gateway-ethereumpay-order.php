<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Gateway_EthereumPay_Order {
	private $order;
	
	public function __construct($order) {
	    $this->order = $order;
	}
	
	public function getOrginOrder() {
		return $this->order;
	}
	
	public function __get($key) {
		if (property_exists($this->order, $key)) {
			return $this->order->$key;
		} else {
			if (!method_exists($this->order, "get_$key")) {
				$order_prefix = 'order_';
				if (substr($key, 0, strlen($order_prefix)) === $order_prefix) {
					$key = substr($key, strlen($order_prefix));
				}
			}

			if (method_exists($this->order, "get_$key")) {
				return $this->order->{"get_$key"}();
			}
		}
	}
	
	public function __call($method, $parameters) {
		return $this->order->$method(...$parameters);
	}
}
?>