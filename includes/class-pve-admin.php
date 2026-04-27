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
*   add_action( 'admin_menu', array( $this, 'pve_register_ethereum_payments_page' ) )
*   add_action( 'admin_post_pve_approve_order', array( $this, 'pve_handle_approve_order' ) )
*   add_action( 'admin_notices', array( $this, 'pve_maybe_show_notice' ) )
*
* Options read: // via get_option()
*   none
*
* Options written: // via update_option() or add_option()
*   none
*
* Order meta read: // via get_post_meta()
*   none
*
* Order meta written: // via update_post_meta()
*   none
*
* Constants defined:
*   none
*
* Files loaded:
*   none
*
* @package Payments_Via_Ethereum
* @since 1.420.69
*/

//ABSPATH guard, must be first executable line, no exceptions
defined( 'ABSPATH' ) || exit;

class PVE_Admin {

	public function init() {
		add_action( 'admin_menu', array( $this, 'pve_register_ethereum_payments_page' ) );
		add_action( 'admin_post_pve_approve_order', array( $this, 'pve_handle_approve_order' ) );
		add_action( 'admin_notices', array( $this, 'pve_maybe_show_notice' ) );
	}

	/**
	* Registers the Ethereum Payments submenu page under WooCommerce.
	*
	* Hooked to admin_menu. Adds a submenu under the WooCommerce parent
	* so the page appears in the WooCommerce section of the sidebar.
	* Rendering is handled by pve_render_ethereum_payments_page().
	*
	* @since 1.420.69
	* @return void
	*/
	public function pve_register_ethereum_payments_page() {
		add_menu_page(
			__( 'Ethereum Payments', 'pay-via-eth' ),
			__( 'Ethereum ', 'pay-via-eth' ),
			'manage_woocommerce',
			'pve-eth-payments',
			array( $this, 'pve_render_ethereum_payments_page' ),
			'dashicons-money',
			56
		);
	}


	/**
	* Renders the Pending ETH Payments admin page.
	*
	* Outputs the page wrapper, title, and pending payments table.
	* Capability check is enforced here in addition to registration.
	* Table columns: Order ID, Date, USD Amount, Quoted ETH,
	* Assigned Address, Explorer Link, Action.
	*
	* @since 1.420.69
	* @return void
	*/
	public function pve_render_ethereum_payments_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'pay-via-eth' ) );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Pending ETH Payments', 'pay-via-eth' ); ?></h1>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Order ID', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'USD Amount', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Quoted ETH', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Assigned Address', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Explorer Link', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Action', 'pay-via-eth' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="7"><?php esc_html_e( 'No pending ETH payments to verify.', 'pay-via-eth' ); ?></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th scope="col"><?php esc_html_e( 'Order ID', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'USD Amount', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Quoted ETH', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Assigned Address', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Explorer Link', 'pay-via-eth' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Action', 'pay-via-eth' ); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php
	}

	public function pve_handle_approve_order() {}
	public function pve_maybe_show_notice() {}
}
