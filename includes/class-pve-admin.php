<?php

defined( 'ABSPATH' ) || exit;

class PVE_Admin {

    public function init() {
        add_action( 'admin_menu', array( $this, 'register_pending_payments_page' ) );
        add_action( 'admin_post_pve_approve_order', array( $this, 'handle_approve_order' ) );
        add_action( 'admin_notices', array( $this, 'maybe_show_notice' ) );
    }

    public function register_pending_payments_page() {}
    public function handle_approve_order() {}
    public function maybe_show_notice() {}
}
