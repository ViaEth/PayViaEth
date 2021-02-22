<?php
function c9wep_get_all_ethereum_payments( $args = array(), $fields=array() ) {
    global $wpdb;

    $defaults = array(
        'offset'     => 0,
        'number'     => 20,
        'where'      => array(),
        'orderby'    => 'id',
        'order'      => 'DESC',
        'groupby'    => '',
    );

    $args      = wp_parse_args( $args, $defaults );
    $_cache_key=c9wep_cache_key($args);
    $cache_key = 'ethereum_payments-all' . $_cache_key . $args['offset'] . '-' . $args['number'];
    $items     = wp_cache_get( $cache_key, 'c9wep' );
    $where = c9wep_get_where($args['where']);
    $fields_line=c9wep_get_fields_line($fields);

    if ( false === $items ) {
        $sql='SELECT ' . $fields_line . ' FROM ' .   $wpdb->prefix . "c9wep_ethereum_payments" . $where .' ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number'];
        if(!empty($args['groupby'])){
            $sql='SELECT ' . $fields_line . ' FROM ' .   $wpdb->prefix . "c9wep_ethereum_payments" . $where 
                . ' GROUP BY '. $args['groupby'] .' ORDER BY ' . $args['orderby'] .' ' . $args['order']  .' LIMIT ' . $args['offset'] . ', ' . $args['number'];
        }
        $items = $wpdb->get_results($sql);

        wp_cache_set( $cache_key, $items, 'c9wep' );
    }

    return $items;
}

function c9wep_get_ethereum_payments_count($args=array()) {
    global $wpdb;
    $where = c9wep_get_where($args['where']);
    return (int) $wpdb->get_var( "SELECT COUNT(id) FROM " .   $wpdb->prefix . "c9wep_ethereum_payments" . $where);
}

function c9wep_get_ethereum_payments_by_id( $id = 0, $fields=array()  ) {
    global $wpdb;

    $fields_line=c9wep_get_fields_line($fields);
    return $wpdb->get_row( $wpdb->prepare( "SELECT " . $fields_line . " FROM " .   $wpdb->prefix . "c9wep_ethereum_payments" . " WHERE id = %d", $id ) );
}

function c9wep_update_ethereum_payments_by_vars_where($vars=array(),$where=array()) {
    if(empty($vars)){
        return false;
    }

    global $wpdb;
    $table_prefix=$wpdb->prefix;

    $set_vars=array();
    foreach ($vars as $key => $value) {
        $set_vars[]=sprintf("`%s`='%s'",$key,esc_sql($value));
    }
    $set_vars_line=' SET ' . implode(',',$set_vars) . ' ';

    $where = c9wep_get_where($where);
    if(!empty($where)){
        $sql="UPDATE  " .  $wpdb->prefix . "c9wep_ethereum_payments" . $set_vars_line . $where;
        $result = $wpdb->query($sql);
        return $result;
    }
    return false;
}

function c9wep_delete_ethereum_payments($args=array()) {
    global $wpdb;
    $where = c9wep_get_where($args['where']);
    $result=false;
    if(!empty($where)){
        $result = $wpdb->query("DELETE FROM " .  $wpdb->prefix . "c9wep_ethereum_payments" . $where);
    }
    return $result;
}

function c9wep_delete_ethereum_payments_by_id($id = 0) {
    global $wpdb;
    $table_prefix=$wpdb->prefix;
    $result = $wpdb->query($wpdb->prepare("DELETE FROM " .  $wpdb->prefix . "c9wep_ethereum_payments" . " WHERE  id = %d", $id ));
    return $result;
}

function c9wep_insert_ethereum_payments( $args = array() ) {
    global $wpdb;

    $defaults = array(
        'id' => '',
//        'created_at' => '',
//        'updated_at' => '',
        'payment_status' => '',
        'store_currency' => '',
        'transaction_id' => '',
//        'created_at' => '',
//        'updated_at' => '',
        'order_id' => '',
        'order_total' => '',
        'exchange_rate' => '',
        'amount' => '',
    );

    $args       = wp_parse_args( $args, $defaults );
    $table_name =   $wpdb->prefix . "c9wep_ethereum_payments";
    // if(empty($args['id'])){
    //     $saved_args['where']=[
    //         //condition here
    //     ];
    //     $saved_obj=c9wep_get_all_ethereum_payments($saved_args);

    //     if(isset($saved_obj[0])){
    //         $args['id']=$saved_obj[0]->id;
    //     }
    // }
    // some basic validation
    // if ( empty( $args['ethereum_payments_name'] ) ) {
    //     return new WP_Error( 'no-ethereum_payments_name', __( 'No Campaign Name provided.', 'c9wep' ) );
    // }

    // if ( empty( $args['cp_url'] ) ) {
    //     return new WP_Error( 'no-cp_url', __( 'No Campaign Url provided.', 'c9wep' ) );
    // }
    // remove row id to determine if new or update
    $row_id = (int) $args['id'];
    unset( $args['id'] );

    if ( ! $row_id ) {

        // insert a new
        if ( $wpdb->insert( $table_name, $args ) ) {
            return $wpdb->insert_id;
        }

    } else {

        // do update method here
        $wpdb->update( $table_name, $args, array( 'id' => $row_id ) );
        return $row_id;
    }

    return false;
}