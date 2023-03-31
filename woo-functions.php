<?php
// function c9wep_plugin_path() {

//   // gets the absolute path to this plugin directory

//   return C9WDS_DIR;//untrailingslashit( plugin_dir_path( __FILE__ ) );
// }

//  add_action('woocommerce_admin_order_item_headers', 'my_woocommerce_admin_order_item_headers');
// function my_woocommerce_admin_order_item_headers() {
//     $column_name = 'TEXT';
//     echo '<th>' . $column_name . '</th>';
// }

// apply_filters( 'woocommerce_get_item_data', $item_data, $cart_item );
// function c9wep_update_appointment_cart_item_data( $item_data, $cart_item ) {
//   foreach ($item_data as $key => $_data) {
//     if('Time'==$_data['name'] || 'Duration'==$_data['name']){
//       unset($item_data[$key]);
//     }
//   }
//   // ob_start();
//   // print_r($item_data);
//   // echo PHP_EOL;
//   // print_r($cart_item);
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // $data1=ob_get_clean();
//   // file_put_contents(dirname(__FILE__)  . '/item_data.log',$data1,FILE_APPEND);
//   return $item_data;
// }
// add_filter( 'woocommerce_get_item_data', 'c9wep_update_appointment_cart_item_data', 90, 2 );

// apply_filters( 'woocommerce_order_items_meta_get_formatted', $formatted_meta, $order )
// function c9wep_update_appointment_formatted_meta( $formatted_meta, $order ) {
//   // ob_start();
//   // print_r($formatted_meta);
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // $data1=ob_get_clean();
//   // file_put_contents(dirname(__FILE__)  . '/formatted_meta.log',$data1,FILE_APPEND);
//   return $formatted_meta;
// }
// add_filter( 'woocommerce_order_items_meta_get_formatted', 'c9wep_update_appointment_formatted_meta', 90, 2 );


// do_action( 'woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
// do_action( 'woocommerce_order_item_line_item_html', $item_id, $item, $order );
// add_action('woocommerce_admin_order_item_values', 'c9wep_woocommerce_admin_order_item_values', 90, 3);
// function c9wep_woocommerce_admin_order_item_values($_product, $item, $item_id = null) {

// This code snippet adds an action for 'woocommerce_order_item_line_item_html' and calls the function 'c9wep_woocommerce_admin_order_item_values' which takes 3 arguments - $item_id, $item, and $order.
add_action('woocommerce_order_item_line_item_html', 'c9wep_woocommerce_admin_order_item_values', 90, 3);
// The function checks if the item type is 'line_item', gets the order ID from $_GET['post'], gets the product and product ID from the item, gets the XKEY value from the order meta, and calls the 'c9wep_admin_order_product_form_items_list' action with product ID and XKEY values if they are not empty.
function c9wep_woocommerce_admin_order_item_values($item_id, $item, $order) {
  if($item['type']=="line_item"){
    $order_id = $_GET['post'];
    $product=$item->get_product();
    $product_id=$product->get_id();
    $xkey=get_post_meta($order_id, XKEY, true);//c9wep_get_xkey_value_from_query();
    if(!empty($product_id) && !empty($xkey)){
      do_action( 'c9wep_admin_order_product_form_items_list', $product_id, $xkey );
    }
  }
}

// The following function is used to display custom fields in the product order items table in WooCommerce's admin panel.
// It takes two parameters: $product_id - the ID of the product being ordered and $xkey - the custom field key.
function c9wep_c9wep_admin_order_product_form_items_list( $product_id, $xkey ) {
  // It begins by starting an output buffer using the PHP ob_start() function.
  ob_start();
  // It then calls the c9wep_get_entry_field_values_with_product_id_and_key() function to retrieve the field values for the product.
  $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
  // If there are values, it iterates through them with a foreach loop and outputs them in a table row with the name and value of the field.
  if(!empty($field_values)):
    foreach ($field_values as $name => $val) {
    ?>
    <tr>
      <td colspan="2">
        <?php echo $name; ?>
      </td>
      // The values are formatted differently depending on whether they are an array or a single value.
      <td colspan="4">
        <?php 
        if(is_array($val)){
          echo implode('<br/>', $val);
        }else{
          echo $val;
        }
        ?>
      </td>
    </tr> 
    <?php
    }
  endif;
  // The resulting HTML is saved to the $html variable and then printed to the screen using the echo statement.
  $html=ob_get_clean();
  echo $html;
}
// Finally, the function is hooked to the 'c9wep_admin_order_product_form_items_list' action with a priority of 90 and two parameters.
add_action( 'c9wep_admin_order_product_form_items_list', 'c9wep_c9wep_admin_order_product_form_items_list', 90, 2 );  

// This code snippet adds an action to the 'c9wep_email_order_product_form_items_list' hook which calls the 'c9wep_c9wep_email_product_form_items_list' function.
add_action( 'c9wep_email_order_product_form_items_list', 'c9wep_c9wep_email_product_form_items_list', 90, 2 );   
// This function generates a list of order items in HTML table rows for the email notifications.
function c9wep_c9wep_email_product_form_items_list( $product_id, $xkey ) {
  // Start the output buffer.
  ob_start();
  // Get the entry field values for the product and key.
  $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
  // Check if the field values exist and loop through them to generate table rows.
  if(!empty($field_values)):
    foreach ($field_values as $name => $val) {
    ?>
    <tr class="cart_item">
      <td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
        <?php echo $name; ?>
      </td>
      <td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
        <?php 
        if(is_array($val)){
          echo implode('<br/>', $val);
        }else{
          echo $val;
        }
        ?>
      </td>
    </tr> 
    <?php
    }
  endif;
  // Get the output buffer contents and clean it.
  $html=ob_get_clean();
  // Echo the cleaned HTML code.
  echo $html;
}

/**
This function is used to generate a table row for each custom field value of a product in the product form.
This function generates a list of entry field values associated with a given product id and key.
It uses an output buffer to capture the HTML output of the list of entry field values.
The list is generated as a table row for each entry field.
If the entry field value is an array, it is imploded with a line break separator.
The resulting HTML is then echoed to the page.

@param int $product_id - The ID of the product being processed.
@param string $xkey - The custom field key to retrieve values from.
@return void
*/
function c9wep_c9wep_product_form_items_list( $product_id, $xkey ) {
  // start buffering output
  ob_start();
  // get custom field values of the product with the given ID and key
  $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
  // check if custom field values exist
  if(!empty($field_values)):
    // loop through each custom field value
    foreach ($field_values as $name => $val) {
    ?>
    <tr class="cart_item">
      <td>
        // output the name of the custom field
        <?php echo $name; ?>
      </td>
      <td>
        <?php
        // check if the custom field value is an array
        if(is_array($val)){
          // if it is an array, output each element on a new line
          echo implode('<br/>', $val);
        }else{
          // if it is not an array, output the value directly
          echo $val;
        }
        ?>
      </td>
    </tr> 
    <?php
    }
  endif;
  //Get the buffered output and clear the buffer
  $html=ob_get_clean();
  // output the HTML
  echo $html;
}

/**
Adds the above function as an action for the following hooks.

This function adds the c9wep_product_form_items_list function as a callback for the 'c9wep_review_order_product_form_items_list' and 'c9wep_order_details_product_form_items_list' actions.
It also sets the priority of the callback to 90 and passes 2 arguments to the callback function.

@return void
*/
add_action( 'c9wep_review_order_product_form_items_list', 'c9wep_c9wep_product_form_items_list', 90, 2 );   
add_action( 'c9wep_order_details_product_form_items_list', 'c9wep_c9wep_product_form_items_list', 90, 2 );   


// add_filter( 'woocommerce_locate_template', 'c9wep_woocommerce_locate_template', 10, 3 );
// function c9wep_woocommerce_locate_template( $template, $template_name, $template_path ) {
//   global $woocommerce;

//   $_template = $template;

//   if ( ! $template_path ) $template_path = $woocommerce->template_url;

//   $plugin_path  = C9WDS_DIR . '/c9wep-templates/woocommerce/';

//   // Look within passed path within the theme - this is priority
//   $template = locate_template(

//     array(
//       $template_path . $template_name,
//       $template_name
//     )
//   );

//   // Modification: Get the template from this plugin, if it exists
//   if ( ! $template && file_exists( $plugin_path . $template_name ) )
//     $template = $plugin_path . $template_name;

//   // Use default template
//   if ( ! $template )
//     $template = $_template;

//   // Return what we found
//   return $template;
// }

// add_filter( 'woocommerce_product_single_add_to_cart_text', 'c9wep_update_add_to_cart_text',90,2 );
// add_filter( 'woocommerce_product_variable_add_to_cart_text', 'c9wep_update_add_to_cart_text',90,2 );
// add_filter( 'woocommerce_product_grouped_add_to_cart_text', 'c9wep_update_add_to_cart_text',90,2 );
// add_filter( 'woocommerce_product_external_add_to_cart_text', 'c9wep_update_add_to_cart_text',90,2 );
// add_filter( 'woocommerce_product_add_to_cart_text', 'c9wep_update_add_to_cart_text',90,2  );
// function c9wep_update_add_to_cart_text($default, $prod) {
//   $page_id=c9wep_get_page_id_with_product_id($prod->get_id());

//   if(!empty($page_id)){
//     return 'Start Booking';
//   }

//   return $default;
// }

// function c9wep_get_entry_field_values_with_form_id_entry_id( $form_id, $entry_id ) {
//   if(empty($form_id) || empty($entry_id)) return false;
//   $fields=c9wep_get_all_fields_arr_by_form_id($form_id);
//   // https://formidableforms.com/knowledgebase/show-details-of-a-single-entry/#kb-php-alternative
//   $entry = FrmProEntriesController::show_entry_shortcode( array( 'id' => $entry_id, 'format' => 'array' ) );

//   // ob_start();
//   // print_r($fields);
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // echo PHP_EOL;
//   // $data1=ob_get_clean();
//   // file_put_contents(dirname(__FILE__)  . '/fields.log',$data1,FILE_APPEND);
//   $field_values=[];
//   foreach ($entry as $f_key => $val) {
//     if(isset($fields[$f_key])){
//       $name=$fields[$f_key]['name'];
//       $type=$fields[$f_key]['type'];
//       if('product'==$type && strpos($name,'edroom') !==false){
//       // [name] => 2 Bedrooms
//       // [description] => 
//       // [type] => product
//       //we change the label of 1 Bedroom, 2 Bedrooms, 3 Bedrooms to Frequency
//         $name='Frequency';
//       }else if('date'==$type){
//         // [field_key] => ship_booking_date
//         // [name] => Datetime Picker
//         // [description] => 
//         // [type] => date
//         $val = c9wep_get_formated_date_time($val);//mysql2date( get_option( 'date_format' ), $val );
//       }else if('datetimer'==$type){
//         // [field_key] => booking_time
//         // [name] => Datetime Picker
//         // [description] => 
//         // [type] => datetimer
//         $val = c9wep_get_formated_date_time($val,'datetime');//mysql2date( get_option( 'date_format' ), $val ) . ' ' . date(get_option( 'time_format' ), strtotime($val));
//       }

//       $field_values[$name]=$val;
//     }
//   }

//   return $field_values;
// }

// function c9wep_get_entry_field_values_with_form_id_entry_id_b0( $form_id, $entry_id ) {
//   if(empty($form_id) || empty($entry_id)) return false;
//   $fields=c9wep_get_all_fields_by_form_id($form_id);
//   // https://formidableforms.com/knowledgebase/show-details-of-a-single-entry/#kb-php-alternative
//   $entry = FrmProEntriesController::show_entry_shortcode( array( 'id' => $entry_id, 'format' => 'array' ) );

//   $field_values=[];
//   foreach ($entry as $f_key => $val) {
//     if(isset($fields[$f_key])){
//       $name=$fields[$f_key];
//       $field_values[$name]=$val;
//     }
//   }

//   return $field_values;
// }

// add_action( 'woocommerce_after_order_notes', 'c9wep_add_xkey_checkout_hidden_field', 10, 1 );
// function c9wep_add_xkey_checkout_hidden_field( $checkout ) {
//     $xkey=$_GET[XKEY];
//     if(empty($xkey)) return;
//     // Output the hidden link
//     echo '<div id="xkey_hidden_checkout_field">
//             <input type="hidden" class="input-hidden" name="xkey" id="xkey" value="' . $xkey . '">
//     </div>';
// }

// add_action( 'woocommerce_checkout_update_order_meta', 'c9wep_save_xkey_checkout_hidden_field', 10, 1 );
// function c9wep_save_xkey_checkout_hidden_field( $order_id ) {

//     if ( ! empty( $_POST['xkey'] ) )
//         update_post_meta( $order_id, 'xkey', sanitize_text_field( $_POST['xkey'] ) );

// }

// function c9wep_get_xkey_value_from_query() {
//   $xkey=$_GET[XKEY];
//   if(empty($xkey)){//it maybe in an ajax updating view
//     if(isset($_POST['post_data'])){
//       parse_str($_POST['post_data'], $post_data);
//       if(isset($post_data[XKEY])){
//         $xkey=$post_data[XKEY];
//       }
//     }
//   }

//   return $xkey;
// }

// function c9wep_get_entry_field_values_with_product_id_and_key( $product_id, $xkey ) {
//   $args['where']=[
//     'product_id'=>$product_id, 
//     'xkey'=>$xkey
//   ];
//   $fields=['entry_id','form_id'];
//   $bookings=c9wep_get_all_booking_xref($args,$fields);
//   if(!empty($bookings)){
//     $entry_id=$bookings[0]->entry_id;
//     $form_id=$bookings[0]->form_id;
//     $field_values=c9wep_get_entry_field_values_with_form_id_entry_id($form_id, $entry_id);
//     return $field_values;
//   }
//   return false;
// }

// define the woocommerce_review_order_after_cart_contents callback
// this function might not be used in the plugin as the add_action section was commented out.
// This fucntion needs testing and documentation.
function action_woocommerce_review_order_after_cart_contents_b0(  ) { 
  ob_start();

  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
        $product_id=$_product->get_id();
        $xkey=$_GET[XKEY];
        if(!empty($product_id) && !empty($xkey)){
          $field_values=c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
          foreach ($field_values as $name => $val) {
          ?>
          <tr>
            <td>
              <?php echo $name; ?>
            </td>
            <td>
              <?php echo $val; ?>
            </td>
          </tr> 
          <?php
          }
        }
    }
  }

  $html=ob_get_clean();
  echo $html;
}     
// add the action 
// add_action( 'woocommerce_review_order_after_cart_contents', 'action_woocommerce_review_order_after_cart_contents', 10, 0 );

// this function might not be used in the plugin as the add_action section was commented out.
// This fucntion needs testing and documentation.
function c9wep_c9wep_checkout_product_row( $product_id, $xkey ) {
  // $field_values=c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
  if(empty($field_values)) return;
  ob_start();
  ?>
  <tr>
    <td>1</td>
    <td>2</td>
  </tr>
  <?php if(false): ?>
  <?php foreach ($field_values as $name => $val): ?>
  <tr>
    <td>
      <?php echo $name; ?>
    </td>
    <td>
      <?php echo $val; ?>
    </td>
  </tr> 
  <?php endforeach ?>
  <?php endif;//end false ?>
  <?php
  $html=ob_get_clean();
  echo $html;
}
// add_action( 'c9wep_checkout_product_row', 'c9wep_c9wep_checkout_product_row', 90, 2 );    

// This function might not be used in the plugin or might be a standalone function not directly linked to woocommerce but some other peice of code.
// This fucntion needs testing and documentation.
function c9wep_get_page_id_with_product_id($product_id) {
  $args['where']=[
    'product_id'=>$product_id
  ];

  $page_id=false;
  $xref=c9wep_get_all_form_xref($args);
  if(!empty($xref[0])){
      $page_id=$xref[0]->page_id;
      if(!empty($page_id)){
        return $page_id;
      }
  }
  return $page_id;
}

// add_filter( 'woocommerce_loop_add_to_cart_link', 'replace_default_button' );
// function replace_default_button(){

//     //list category slugs where button needs to be changed
//     $selected_cats = array('cat-one-slug', 'cat-two-slug', 'cat-three-slug');
//     //get current category object
//     $current_cat = get_queried_object();
//     //get category slug from category object
//     $current_cat_slug = $current_cat->slug;
//     //check if current category slug is in the selected category list
//     if( in_array($current_cat_slug, $selected_cats) ){
//         //replace default button code with custom code
//         return '<button>Text a Dealer</button>';
//     }
// }

// First, remove Add to Cart Button
// remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
// // Second, add View Product Button
// add_action( 'woocommerce_after_shop_loop_item', 'shop_view_product_button', 10);
// function shop_view_product_button() {
//   global $product;
//   $page_id=c9wep_get_page_id_with_product_id($product->get_id());

//   if(!empty($page_id)){
//     echo c9wep_woocommerce_product_add_to_cart_link($default='', $product);
//   }else{//restore original
//     add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 90 );
//   }
// // $link = $product->get_permalink();
// // $default='<a href="' . $link . '" class="button addtocartbutton">Add to Cart</a>';
// }

/*
add_filter( 'add_to_cart_url', 'add_special_payment_link' );

function add_special_payment_link( $link ) {
    global $product;

    // If the stock quantity is less than 50, don't modify the link
    if( $product->get_stock_quantity() <= 50 ) {
        return $link;
    }

    // Write your code here to come up with the special payment link
    $link = 'http://wordpress.org/';  // You know, your link, not WordPress

    return $link;
}
*/

// add_filter( 'woocommerce_payment_complete_order_status', array($this,'update_order_status'), 10, 2 );
// add_action('woocommerce_order_status_pending',array($this,'wc_order_pending'),10);
// add_action('woocommerce_order_status_failed',array($this,'wc_order_failed'),10);
// add_action('woocommerce_order_status_on-hold',array($this,'wc_order_hold'),10);
//add_action('woocommerce_order_status_processing',array($this,'wc_order_processing'),10);
// add_action('woocommerce_order_status_completed',array($this,'wc_order_completed'),10);
// add_action('woocommerce_order_status_refunded',array($this,'wc_order_refunded'),10);
// add_action('woocommerce_order_status_cancelled',array($this,'wc_order_cancelled'),10);

// add_action( 'woocommerce_thankyou', array($this,'woocommerce_thankyou'));
// add_action( 'woocommerce_new_order', array($this,'wc_order_processing'));

/*
Registers a function to be called after the "woocommerce_thankyou" action has been executed, after a customer has placed an order.
The callback function is c9wep_update_order_related_data.
The function updates the order-related data
The priority of the action is set to 90.

@param int $order_id: the ID of the order
@return bool: false if the XKEY is empty
*/
add_action('woocommerce_thankyou','c9wep_update_order_related_data',90);

/**
Updates the order-related data

@param int $order_id: the ID of the order
*/
function c9wep_update_order_related_data($order_id) {
    // Get the XKEY from the order post meta
    $xkey=get_post_meta($order_id, XKEY, true);
    // If XKEY is empty, return false
    if(empty($xkey)) return false;

    // Set the order ID in the $vars array
    $vars['order_id']=$order_id;
    // Get the appointment ID with the order ID
    $appointment_id=c9wep_get_appointment_id_with_order_id($order_id);
    // If the appointment ID is not empty, set it in the $vars array and update the order appointment ID post meta
    if(!empty($appointment_id)){
      $vars['appointment_id']=$appointment_id;
      update_post_meta( $order_id, ORDER_APPOINTMENT_ID, $appointment_id );
    }
    // Set the where condition array
    $where[XKEY]=$xkey;

    // Update the booking cross-reference by vars and where
    c9wep_update_booking_xref_by_vars_where($vars, $where);

    // Set the where condition array for getting all booking cross-references
    $b_args['where']=[
      XKEY=>$xkey
    ];
    // Get all booking cross-reference objects with the where condition array
    $booking_objs=c9wep_get_all_booking_xref($b_args);
    // If the booking cross-reference objects array is not empty
    if(!empty($booking_objs)){
      // Get the booking ID from the first booking cross-reference object and update the order booking ID post meta
      $booking_id=$booking_objs[0]->id;
      update_post_meta( $order_id, ORDER_BOOKING_ID, $booking_id );
    }
}

/**
Replaces the line item subtotal with the custom calculated price based on entry fields and XKEY of the order.
This function is hooked into the woocommerce_order_formatted_line_subtotal filter.
It is responsible for updating the line item subtotal in the order with the total value from the C9WEP form fields.
This function modifies the formatted subtotal of an order line item based on custom product data.
It retrieves the product and order IDs from the item and order objects passed as arguments.
It also retrieves the 'xkey' metadata associated with the order.
If a 'Total' value is found in the custom product data, the line item price is set to that value.
Taxes are recalculated and the line item data is saved.
The modified subtotal is returned.

@param float $subtotal - The original subtotal of the line item
@param object $item - The line item object
@param object $order - The order object

@return float - The new subtotal for the line item
*/
function c9wep_hook_woocommerce_order_formatted_line_subtotal($subtotal,$item,$order)
{
    /* Get the product ID, order ID, and XKEY from order metadata. If any of them are empty, return false. */
    $product       = $item->get_product();
    $product_id=$product->get_id();
    $order_id=$order->get_id();
    $xkey=get_post_meta($order_id, XKEY, true);

     /* If both product ID and XKEY are not empty, fetch the entry field values with product ID and XKEY from C9WEP. If the 'Total' field is set and not empty, update the line item subtotal and total with the 'Total' value, calculate taxes and save the updated line item data. Finally, calculate the order totals and return the new line item price. */
    if(!empty($product_id) && !empty($xkey)){
      $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
      if(isset($field_values['Total']) && !empty($field_values['Total'])){
        $new_line_item_price=$field_values['Total'];
        $item->set_subtotal( $new_line_item_price ); 
        $item->set_total( $new_line_item_price );

        // Make new taxes calculations
        $item->calculate_taxes();

        $item->save(); // Save line item data
        //$cart_item['data']->set_price( $field_values['Total'] );
        // $cart_item['data']->set_price( 25 );
        $order->calculate_totals();

        return $new_line_item_price;
      }
    }
    /* If product ID or XKEY is empty, return the original subtotal. */
    return $subtotal;
}

/* The filter is then added with priority 10 and 3 arguments. */
add_filter('woocommerce_order_formatted_line_subtotal','c9wep_hook_woocommerce_order_formatted_line_subtotal',10,3);

/* The filter is added with priority 9999 and 1 arguments. */
add_action( 'woocommerce_before_calculate_totals', 'add_custom_price', 9999, 1);

/**
Adds custom price to cart items by checking if a custom field with the Total key exists in the entry field values for a product with a given ID and XKEY value.
If the Total field exists, the cart item's price is set to that value.
This function is hooked to the 'woocommerce_before_calculate_totals' action with a priority of 9999 and 1 argument.

@param object $cart The cart object.

@return void
*/
function add_custom_price( $cart ) {

    // This is necessary for WC 3.0+
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    // Avoiding hook repetition (when using price calculations for example)
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

    // Loop through cart items
    foreach ( $cart->get_cart() as $cart_item ) {
      $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

       // If the product exists, quantity is greater than 0, and checkout cart item is visible
      if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
          $product_id=$_product->get_id();
          $xkey=c9wep_get_xkey_value_from_query();
          // If product_id and xkey is not empty, fetch the Total field value from database
          if(!empty($product_id) && !empty($xkey)){
            $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
            // If Total field value is set, update cart item's price with the fetched Total value
            if(isset($field_values['Total']) && !empty($field_values['Total'])){
              $cart_item['data']->set_price( $field_values['Total'] );
              // $cart_item['data']->set_price( 25 );
            }
          }
      }
    }
}

// This function might not be used in the plugin or might be a standalone function not directly linked to woocommerce but some other peice of code.
// This fucntion needs testing and documentation.
/**
Generates a custom button to add a WooCommerce product to cart.

@param void

@return void
*/
function custom_product_button(){
    global $product;
    // HERE your custom button text and link
    // $button_text = __( "Custom text", "woocommerce" );
    // $button_link = '#';
    
    // Display button
    // echo '<a class="button" href="'.$button_link.'">' . $button_text . '</a>';
    $default='';
    echo c9wep_woocommerce_product_add_to_cart_link($default, $product);
}

/**
Replaces the single product button add to cart by a custom button for a specific product category.

@return void
*/
add_action( 'woocommerce_single_product_summary', 'replace_single_add_to_cart_button', 1 );
function replace_single_add_to_cart_button() {
    global $product;
    
    $page_id=c9wep_get_page_id_with_product_id($product->get_id());
    if(empty($page_id)){
      return;//do nothing
    }
    // Only for product category ID 64
    // if( has_term( '64', 'product_cat', $product->get_id() ) ){

        // For variable product types (keeping attribute select fields)
        if( $product->is_type( 'variable' ) ) {
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
            add_action( 'woocommerce_single_variation', 'custom_product_button', 20 );
        }
        // For all other product types
        else {
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            add_action( 'woocommerce_single_product_summary', 'custom_product_button', 30 );
        }
    // }
}

/**
Adds custom link to cart button on shop page.
This function is hooked to the 'woocommerce_loop_add_to_cart_link' action with a priority of 90 and 2 argument.

@param string $default The default button HTML.
@param object $product The product object.

@return string The updated button HTML.
*/
add_filter( 'woocommerce_loop_add_to_cart_link', 'c9wep_woocommerce_product_add_to_cart_link',90,2 );
function c9wep_woocommerce_product_add_to_cart_link($default, $product) {
  // Get the page ID of the product.
  $page_id=c9wep_get_page_id_with_product_id($product->get_id());

  if(!empty($page_id)){
    // Generate button HTML with custom URL and label.
    $simpleURL = get_the_permalink( $page_id );
    $simpleLabel =  "Start Booking";  // BUTTON LABEL HERE
    return sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
        esc_url( $simpleURL ),
        esc_attr( $product->id ),
        esc_attr( $product->get_sku() ),
        esc_attr( isset( $quantity ) ? $quantity : 1 ),
        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
        esc_attr( $product->product_type ),
        esc_html( $simpleLabel )
    );
  }
  return $default;
}

// Single Product
function c9wep_single_add_to_cart_text($default, $prod) {
  return c9wep_update_add_to_cart_text($default, $prod);
  return 'Add to cart'; // Change this to change the text on the Single Product Add to cart button.
}

// Variable Product
function c9wep_variable_add_to_cart_text($default, $prod) {
  return 'Select options'; // Change this to change the text on the Variable Product button.
}

// Grouped Product
function c9wep_grouped_add_to_cart_text($default, $prod) {
  return 'View options'; // Change this to change the text on the Grouped Product button.
}

// External Product
function c9wep_external_add_to_cart_text($default, $prod) {
  return 'Read More'; // Change this to change the text on the External Product button.
}

// Fallback for all other product types, and returns the default text "Add to cart"
function c9wep_add_to_cart_text($default, $prod) {
  return 'Add to cart'; // Change this to change the text on the Default button.
}