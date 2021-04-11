<?php

if ( ! class_exists ( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class C9wep_Ethereum_payments_List extends \WP_List_Table {

    function __construct() {
        parent::__construct( array(
            'singular' => 'Ethereum_payments',
            'plural'   => 'Ethereum_paymentss',
            'ajax'     => false
        ) );
    }

    function get_table_classes() {
        return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
    }

    /**
     * Message to show if no designation found
     *
     * @return void
     */
    function no_items() {
        _e( 'No Ethereum_paymentss Found', 'c9wep' );
    }

    /**
     * Default column values if no callback found
     *
     * @param  object  $item
     * @param  string  $column_name
     *
     * @return string
     */
    function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'id':
                return $item->id;

            case 'payment_status':
                return $item->payment_status;

            case 'store_currency':
                return $item->store_currency;

            case 'transaction_id':
                return $item->transaction_id;

            case 'transaction_status':
                return $item->transaction_status;

            case 'transaction_hash':
                return $item->transaction_hash;

            case 'blockHash':
                return $item->blockHash;

            case 'from_address':
                return $item->from_address;

            case 'my_address':
                return $item->my_address;

            case 'created_at':
                return $item->created_at;

            case 'updated_at':
                return $item->updated_at;

            case 'transaction_init':
                return $item->transaction_init;

            case 'transaction_confirm':
                return $item->transaction_confirm;

            case 'order_id':
                return $item->order_id;

            case 'confirmations':
                return $item->confirmations;

            case 'blockNumber':
                return $item->blockNumber;

            case 'order_total':
                return $item->order_total;

            case 'exchange_rate':
                return $item->exchange_rate;

            case 'amount':
                return $item->amount;

            case 'eth_amount':
                return $item->eth_amount;
            default:
                return isset( $item->$column_name ) ? $item->$column_name : '';
        }
    }

    /**
     * Get the column names
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
//            'id'      => __( 'ID', 'c9wep' ),
            'order_id'      => __( 'Order', 'c9wep' ),
            'payment_mode'      => __( 'Mode', 'c9wep' ),
            'payment_status'      => __( 'Payment Status', 'c9wep' ),
            // 'store_currency'      => __( 'Store Currency', 'c9wep' ),
            // 'transaction_id'      => __( 'Transaction ID', 'c9wep' ),
            'transaction_status'      => __( 'Transaction Status', 'c9wep' ),
            'transaction_hash'      => __( 'Transaction Hash', 'c9wep' ),
            // 'blockHash'      => __( 'Block Hash', 'c9wep' ),
            'from_address'      => __( 'From Address', 'c9wep' ),
            'my_address'      => __( 'My Address', 'c9wep' ),
//            'created_at'      => __( 'Created At', 'c9wep' ),
//            'updated_at'      => __( 'Updated At', 'c9wep' ),
            // 'transaction_init'      => __( 'Transaction Init', 'c9wep' ),
            // 'transaction_confirm'      => __( 'Transaction Confirm', 'c9wep' ),
            'confirmations'      => __( 'Confirmations', 'c9wep' ),
            'blockNumber'      => __( 'Block Number', 'c9wep' ),
            'order_total'      => __( 'Order Total', 'c9wep' ),
            // 'exchange_rate'      => __( 'Exchange Rate', 'c9wep' ),
            // 'amount'      => __( 'Amount', 'c9wep' ),
            'eth_amount'      => __( 'Eth Amount', 'c9wep' ),
            'refer'      => __( 'Refer', 'c9wep' ),
        );

        return $columns;
    }

    /**
     * Render the designation name column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_id( $item ) {

        $actions           = array();
        $actions['edit']   = sprintf( '<a href="%s" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=c9wep-ethereum_payments&action=edit&id=' . $item->id ), $item->id, __( 'Edit this item', 'c9wep' ), __( 'Edit', 'c9wep' ) );
        $actions['delete'] = sprintf( '<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=c9wep-ethereum_payments&action=delete&id=' . $item->id ), $item->id, __( 'Delete this item', 'c9wep' ), __( 'Delete', 'c9wep' ) );

        return sprintf( '<a href="%1$s"><strong>%2$s</strong></a> %3$s', admin_url( 'admin.php?page=c9wep-ethereum_payments&action=view&id=' . $item->id ), $item->id, $this->row_actions( $actions ) );
    }


    function column_payment_mode( $item ) {
        if('test'==$item->payment_mode){
            return '<span style="color:red;">' . $item->payment_mode . '</span>';
        }
        return $item->payment_mode;
    }

    function column_payment_status( $item ) {
        if('failed'==$item->payment_status || 'expired'==$item->payment_status){
            return '<span style="color:red;">' . $item->payment_status .  '</span>';
        }
        return $item->payment_status;
    }

    function column_transaction_status( $item ) {
        if('failed'==$item->transaction_status){
            return '<span style="color:red;">' . $item->transaction_status . '</span>';
        }
        return $item->transaction_status;
    }

    function column_transaction_hash( $item ) {
        return c9wep_get_transaction_view_link($item->transaction_network,$item->transaction_hash);//$item->transaction_network . 'tx/'. $item->transaction_hash;
        // return sprintf( '<a href="%s" target="_blank">%s</a>', $tx_url, $item->transaction_hash);
    }

    function column_from_address( $item ) {
        return c9wep_get_wallet_address_transaction_view_link($item->transaction_network, $item->from_address);
        // $tx_url=$item->transaction_network . 'address/'. $item->from_address;
        // return sprintf( '<a href="%s" target="_blank">%s</a>', $tx_url, $item->from_address);
    }

    function column_my_address( $item ) {
        return c9wep_get_wallet_address_transaction_view_link($item->transaction_network, $item->my_address);
        // $tx_url=$item->transaction_network . 'address/'. $item->my_address;
        // return sprintf( '<a href="%s" target="_blank">%s</a>', $tx_url, $item->my_address);
    }

    function column_order_total( $item ) {
        return $item->store_currency . $item->order_total;
    }

    function column_order_id( $item ) {
        return sprintf( '<a target="_blank" href="%s">%s</a>', admin_url( 'post.php?action=edit&post=' . $item->order_id ), __( '#' . $item->order_id, 'c9wep' ) );
    }

    function column_name( $item ) {
        return sprintf( '<a href="%s" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=c9wep-ethereum_payments&action=edit&id=' . $item->id ), $item->id, __($item->name, 'c9wep' ), __( $item->name, 'c9wep' ) );
    }

    function column_refer( $item ) {
        return sprintf( '<a href="%s" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=c9wep-ethereum_payments&action=view&id=' . $item->id ), $item->id, __( '#' .$item->id, 'c9wep' ), __( '#' . $item->id, 'c9wep' ) );
    }
    /**
     * Get sortable columns
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true ),
        );

        return $sortable_columns;
    }

    /**
     * Set the bulk actions
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'delete'  => __( 'Delete Selected Items', 'c9wep' ),
        );
        return $actions;
    }

    /**
     * Render the checkbox column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="ethereum_payments_id[]" value="%d" />', $item->id
        );
    }

    protected function extra_tablenav( $which ) {
        do_action( 'c9wep_ethereum_payments_tablenav', $which );
        /*
        add_action( 'c9wep_ethereum_payments_tablenav', 'c9wep_c9wep_ethereum_payments_tablenav' );    
        function c9wep_c9wep_ethereum_payments_tablenav( $which ) {
            global $current_screen;
            // ob_start();
            // print_r($which);
            // echo PHP_EOL;
            // print_r($current_screen);
            // echo PHP_EOL;
            // echo PHP_EOL;
            // echo PHP_EOL;
            // echo PHP_EOL;
            // $data1=ob_get_clean();
            // file_put_contents(dirname(__FILE__)  . '/current_screen.log',$data1,FILE_APPEND);
            if( strpos($current_screen->id,'toplevel_page_dropstore-products') !== false && 'top' === $which){
                ?>
                <?php wp_nonce_field( 'c9wep_ethereum_payments_nonce','ethereum_payments_nonce'); ?>
                <div class="alignleft actions custom ethereum_payments-button-wrapper">
                    <a href="javascript:void(0);" class="button" id="ethereum_payments-button><?php
                        echo __( 'Ethereum_payments', 'c9wep' ); ?></a>
                </div>
                <?php
            }
        }
        */
    }
    /**
     * Set the views
     *
     * @return array
     */
    public function get_views_() {
        $status_links   = array();
        $base_link      = admin_url( 'admin.php?page=sample-page' );

        foreach ($this->counts as $key => $value) {
            $class = ( $key == $this->page_status ) ? 'current' : 'status-' . $key;
            $status_links[ $key ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => $key ), $base_link ), $class, $value['label'], $value['count'] );
        }

        return $status_links;
    }

    /**
     * Set the views
     *
     * @return array
     */
    public function get_views() {
        
        $base_url      = admin_url( 'admin.php?page=c9wbs-shipments' );

        $query=[];
        $params=['year'=> 'Years: ','month' => 'Months: ','shipping_status' => 'Shipping Status: '];
        foreach ($params as $param => $label) {
            $urls[$param]=$this->get_urls($param);
            foreach ($urls[$param] as $key => $value) {
                $class = ( $key == $_GET[$param] ) ? 'current' : $param .'-' . $key;
                $query[$param]=$key;
                $links[$param][ $key ] = sprintf( '<li class="%s"><a href="%s" class="%s %s-link">%s</a><span class="count"> (%s)</span></li>', $key, add_query_arg( $query, $base_url ), $class, $param, $value['label'], $value['count'] );
            }
            if( !empty($_GET[$param])){
                $query[$param]=$_GET[$param];
            }
        }

        $row_links=[];
        foreach ($params as $param => $label) {
            $row_links[]='<h4 class="links-title"><b>' . $label . '</b></h4>';
            $row_links[]='<ul class="subsubsub ul-links">' . implode(' | ', $links[$param]) . '</ul>';
        }        
        return implode('', $row_links);
    }

    function get_urls($param) {
        $all_urls=[
            'all'=>'All',
        ];

        $key_vals=$this->get_param_key_vals($param);
        if(!empty($key_vals)){
            foreach ($key_vals as $key=>$val) {
                $all_urls[$key]=$val;
            }
        }

        foreach ($all_urls as $l_key => $l_val) {
            $args=[];
            $args=$this->get_where_from_status($args,$l_key);
            $all_urls[$l_key]=[
                'label'=>$l_val,
                'count'=>c9wbs_get_shipments_count( $args ),
            ];
        }

        return $all_urls;        
    }

    function get_param_key_vals($param) {
        $key_vals=[];
        switch ($param) {
            case 'year':
                $vals=[2020,2021];
                break;
            
            case 'month':
                $vals=[1,2,3,4,5,6,7,8,9,10,11,12];
                foreach ($vals as $val) {
                    //$key_vals[$val]=c9wbs_get_month_name($val);
                }
                break;
            
            case 'shipping_status':
                $vals=['processing','pending'];
                break;
            
        }

        if(empty($key_vals)){
            foreach ($vals as $val) {
                $key_vals[$val]=$val;
            }
        }
        return $key_vals;
    }
    function get_where_from_status($args, $status) {
        switch ($status) {
            case 'active_api_data':
                $args['where']['api_status']='!=deleted';
                $args['where']['woo_product_id']='!=0';
                break;

            
            default:
                break;
        }
        return $args;
    }

    /**
     * Prepare the class items
     *
     * @return void
     */
    function prepare_items() {

        $columns               = $this->get_columns();
        $hidden                = array( );
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page              = 20;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page -1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';
        $search     = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';

        // only ncessary because we have sample data
        $args = array(
            'offset' => $offset,
            'number' => $per_page,
            'search' => '*'.$search .'*',
        );

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
            $args['order']   = $_REQUEST['order'] ;
        }

        if(!empty($search)){
            /*TODO: please check the field name to match*/
            $args['where']   = array('name'=>'*'.$search .'*') ;
        }

        if( !empty($_GET['status'])){
            $args=$this->get_where_from_status($args,$_GET['status']);
        }
        
        $this->items  = c9wep_get_all_ethereum_payments( $args );

        $this->set_pagination_args( array(
            'total_items' => c9wep_get_ethereum_payments_count( $args ),
            'per_page'    => $per_page
        ) );
    }
}