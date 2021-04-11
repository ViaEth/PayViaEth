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
add_action('woocommerce_order_item_line_item_html', 'c9wep_woocommerce_admin_order_item_values', 90, 3);
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

function c9wep_c9wep_admin_order_product_form_items_list( $product_id, $xkey ) {
  ob_start();
  $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
  if(!empty($field_values)):
    foreach ($field_values as $name => $val) {
    ?>
    <tr>
      <td colspan="2">
        <?php echo $name; ?>
      </td>
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
  $html=ob_get_clean();
  echo $html;
}
add_action( 'c9wep_admin_order_product_form_items_list', 'c9wep_c9wep_admin_order_product_form_items_list', 90, 2 );  


add_action( 'c9wep_email_order_product_form_items_list', 'c9wep_c9wep_email_product_form_items_list', 90, 2 );   
function c9wep_c9wep_email_product_form_items_list( $product_id, $xkey ) {
  ob_start();
  $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
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
  $html=ob_get_clean();
  echo $html;
}

function c9wep_c9wep_product_form_items_list( $product_id, $xkey ) {
  ob_start();
  $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
  if(!empty($field_values)):
    foreach ($field_values as $name => $val) {
    ?>
    <tr class="cart_item">
      <td>
        <?php echo $name; ?>
      </td>
      <td>
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
  $html=ob_get_clean();
  echo $html;
}
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
add_action('woocommerce_thankyou','c9wep_update_order_related_data',90);
function c9wep_update_order_related_data($order_id) {
    $xkey=get_post_meta($order_id, XKEY, true);
    if(empty($xkey)) return false;

    $vars['order_id']=$order_id;
    $appointment_id=c9wep_get_appointment_id_with_order_id($order_id);
    if(!empty($appointment_id)){
      $vars['appointment_id']=$appointment_id;
      update_post_meta( $order_id, ORDER_APPOINTMENT_ID, $appointment_id );
    }
    $where[XKEY]=$xkey;

    c9wep_update_booking_xref_by_vars_where($vars, $where);

    $b_args['where']=[
      XKEY=>$xkey
    ];
    $booking_objs=c9wep_get_all_booking_xref($b_args);
    if(!empty($booking_objs)){
      $booking_id=$booking_objs[0]->id;
      update_post_meta( $order_id, ORDER_BOOKING_ID, $booking_id );
    }
}

function c9wep_hook_woocommerce_order_formatted_line_subtotal($subtotal,$item,$order)
{
    $product       = $item->get_product();
    $product_id=$product->get_id();
    $order_id=$order->get_id();
    $xkey=get_post_meta($order_id, XKEY, true);

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
    return $subtotal;
}
add_filter('woocommerce_order_formatted_line_subtotal','c9wep_hook_woocommerce_order_formatted_line_subtotal',10,3);

add_action( 'woocommerce_before_calculate_totals', 'add_custom_price', 9999, 1);
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

      if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
          $product_id=$_product->get_id();
          $xkey=c9wep_get_xkey_value_from_query();
          if(!empty($product_id) && !empty($xkey)){
            $field_values = c9wep_get_entry_field_values_with_product_id_and_key($product_id, $xkey);
            if(isset($field_values['Total']) && !empty($field_values['Total'])){
              $cart_item['data']->set_price( $field_values['Total'] );
              // $cart_item['data']->set_price( 25 );
            }
          }
      }
    }
}

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

// Replacing the single product button add to cart by a custom button for a specific product category
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

add_filter( 'woocommerce_loop_add_to_cart_link', 'c9wep_woocommerce_product_add_to_cart_link',90,2 );
function c9wep_woocommerce_product_add_to_cart_link($default, $product) {
  $page_id=c9wep_get_page_id_with_product_id($product->get_id());

  if(!empty($page_id)){
    // return get_the_permalink( $page_id );
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

// Default
function c9wep_add_to_cart_text($default, $prod) {
  return 'Add to cart'; // Change this to change the text on the Default button.
}