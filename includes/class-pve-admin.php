<?php

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
