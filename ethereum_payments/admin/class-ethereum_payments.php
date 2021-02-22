<?php

class C9wep_Ethereum_payments {

    /**
     * Kick-in the class
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /**
     * Add menu items
     *
     * @return void
     */
    public function admin_menu() {

        /** Top Menu **/
        // add_menu_page( __( 'Ethereum Payments', 'c9wep' ), __( 'Ethereum Payments', 'c9wep' ), 'manage_options', 'c9wep-ethereum_payments', array( $this, 'plugin_page' ), 'dashicons-groups', null );

        add_submenu_page( 'woocommerce', __( 'Ethereum Payments', 'c9wep' ), __( 'Ethereum Payments', 'c9wep' ), 'manage_options', 'c9wep-ethereum_payments', array( $this, 'plugin_page' ) );
    }

    /**
     * Handles the plugin page
     *
     * @return void
     */
    public function plugin_page() {
        $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'list';
        $id     = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
        //https://stackoverflow.com/questions/23541269/how-to-add-custom-bulk-actions-in-wordpress-list-tables
        //since wordpress 4.7, we need take the value from action2
        if('-1'==$action){
            $action = isset( $_REQUEST['action2'] ) ? $_REQUEST['action2'] : 'list';
        }
        $template = dirname( __FILE__ ) . '/views/ethereum_payments-list.php';
        switch ($action) {
            case 'view':

                $template = dirname( __FILE__ ) . '/views/ethereum_payments-view.php';
                break;

            case 'edit':
                $template = dirname( __FILE__ ) . '/views/ethereum_payments-edit.php';
                break;

            case 'new':
                $template = dirname( __FILE__ ) . '/views/ethereum_payments-new.php';
                break;

            case 'delete':
                $ids=isset( $_REQUEST['ethereum_payments_id'] ) ? $_REQUEST['ethereum_payments_id'] : null;
                if(!empty($ids)){
                    foreach ($ids as $key => $id) {
                        c9wep_delete_ethereum_payments_by_id($id);
                    }
                }else if(!empty($id)){
                    c9wep_delete_ethereum_payments_by_id($id);
                }
                break;
            default:
                $template = dirname( __FILE__ ) . '/views/ethereum_payments-list.php';
                break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }
    }
}

new C9wep_Ethereum_payments();