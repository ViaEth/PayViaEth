<?php
/**
* PVE_Admin — Pending Payments Interface and Other Admin Only Features.
*
* Handles all WordPress admin functionality specific to this plugin.
* Responsible for registering the pending payments menu page under
* WooCommerce, rendering the payments table, and processing the
* approve order action.
*
* What this class should handles:
* - Registering the admin menu page under WooCommerce
* - Rendering the pending payments table
* - Processing the approve order POST action
* - Constructing block explorer links from gateway settings
*
* What this class should NOT handle:
* - Gateway registration or payment processing
* - ETH price fetching or arithmetic
* - Front-end checkout output
*
* Hooks registered by this class:
*   add_action( 'admin_menu', array( $this, 'pve_register_pending_payments_page' ) )
*   add_action( 'admin_post_pve_approve_order', array( $this, 'pve_handle_approve_order' ) )
*   add_action( 'admin_notices', array( $this, 'pve_maybe_show_notice' ) )
*
* Options read: // via get_option()
* Options written: // via update_option() or add_option()
*
* Order meta read: // via get_post_meta()
* Order meta written: // via update_post_meta()
*
* Constants defined:
* Files loaded:
*
* @package Payments_Via_Ethereum
* @since 1.420.69
*/

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

class PVE_Admin {

	public function init() {
		add_action( 'admin_menu', array( $this, 'pve_register_pending_payments_page' ) );
		add_action( 'admin_post_pve_approve_order', array( $this, 'pve_handle_approve_order' ) );
		add_action( 'admin_notices', array( $this, 'pve_maybe_show_notice' ) );
	}
	public function pve_register_pending_payments_page() {}
	public function pve_handle_approve_order() {}
	public function pve_maybe_show_notice() {}
}
