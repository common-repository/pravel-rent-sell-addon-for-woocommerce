<?php
/**
 * Add rental option in cart
 *
 * @param array $cart_item_data
 * @param array $item_data
 * @param int   $product_id
 * @param int   $variation_id
 *
 * @return array
 */
 
add_filter( 'woocommerce_add_cart_item_data', 'pravel_booking_data_add_in_cart', 10, 3 );

add_filter( 'woocommerce_add_cart_item_data', 'pravel_attribute_add_in_cart', 10, 3 );

add_filter( 'woocommerce_get_item_data', 'pravel_booking_data_view_in_cart', 10, 2 );

add_action( 'woocommerce_add_order_item_meta', 'pravel_booking_order_meta' , 10, 2);

add_filter( 'woocommerce_get_item_data', 'pravel_attribute_view_in_cart', 10, 2 );

add_action('woocommerce_before_calculate_totals', 'pravel_booking_product_price_update', 20, 1);

add_action( 'woocommerce_email_customer_details', 'pravel_pickup_store_address', 10 , 4);


add_action( 'woocommerce_after_cart_table', 'pravel_normal_product_qty', 10, 1 );


//Booking Extra Data add in Cart
function pravel_booking_data_add_in_cart( $cart_item_data, $product_id, $variation_id ) {	

	$from_date = filter_input( INPUT_POST, 'pravel_front_start_date');
	$to_date = filter_input( INPUT_POST, 'pravel_front_end_date'); 
	$block_price = filter_input( INPUT_POST, 'block_price2');

	$pravel_booking_option = filter_input( INPUT_POST, 'pravel_choose_product_option');
	$pravel_booking_price='';
	if( !empty($block_price) ){
		$pravel_booking_price = filter_input( INPUT_POST, 'block_price2');
	} else {
		$pravel_booking_price = filter_input( INPUT_POST, 'pravel_booking_price');
	}

	$pravel_book_product_qty = filter_input( INPUT_POST, 'pravel_book_product_qty');
	if( empty( $from_date ) || empty( $to_date ) || $pravel_booking_option == 'pravel_buy'):
		return $cart_item_data;
	endif;
	
 
	$cart_item_data['pravel_front_start_date'] = $from_date;
	$cart_item_data['pravel_front_end_date'] = $to_date;
	$cart_item_data['pravel_booking_price'] = $pravel_booking_price;
	$cart_item_data['pravel_book_product_qty'] = $pravel_book_product_qty;
 
	return $cart_item_data;
}
 
//Extra attribute add in cart 
function pravel_attribute_add_in_cart( $cart_item_data, $product_id, $variation_id ) {
	
	$product = wc_get_product( $product_id );
	$attributes = $product->get_attributes();
	foreach ($attributes as $taxonomy => $attribute_obj ) {		
		$attribute_label_name = wc_attribute_label($taxonomy);	
		$field_name = 'pravel_attribute_'.$attribute_label_name;
		$cart_item_data[$field_name] = filter_input( INPUT_POST, $field_name);
	}
	return $cart_item_data;
}

//Extra data view in cart
function pravel_booking_data_view_in_cart( $item_data, $cart_item ) {
	if ( empty( $cart_item['pravel_front_start_date'] ) ) :
		return $item_data;
	endif;
 
	$item_data[] = array(
		'key'     => __( 'Start Date', 'pravel-rental' ),
		'value'   =>  $cart_item['pravel_front_start_date'] ,
		'display' => '',
	);
	
	$item_data[] = array(
		'key'     => __( 'End Date', 'pravel-rental' ),
		'value'   =>  $cart_item['pravel_front_end_date'] ,
		'display' => '',
	);
	
	$item_data[] = array(
		'key'     => 'pravel_rental_product_qty',
		'value'   =>  $cart_item['pravel_book_product_qty'] ,
		'display' => '',
	);
 
	return $item_data;
}

//Extra attribute view in cart 
function pravel_attribute_view_in_cart( $item_data, $cart_item ) {
	$product_id = $cart_item['product_id'];
	$product = wc_get_product( $product_id );
	$attributes = $product->get_attributes();
	foreach ($attributes as $taxonomy => $attribute_obj ) {	
		$attribute_label_name = wc_attribute_label($taxonomy);	
		$field_name = 'pravel_attribute_'.$attribute_label_name;
		$item_data[] = array(
				'key'     => __( $attribute_label_name, 'pravel-rental' ),
				'value'   =>  $cart_item[$field_name] ,
				'display' => '',
			);
	}
	
	return $item_data;
}


//Extra booking order meta
function pravel_booking_order_meta ( $item_id, $values ) {
	
	if ( isset( $values [ 'pravel_front_start_date' ] )  && isset( $values [ 'pravel_front_end_date' ] )) :		
		wc_add_order_item_meta( $item_id, 'Start Date', $values['pravel_front_start_date'] );
		wc_add_order_item_meta( $item_id, 'End Date', $values['pravel_front_end_date'] );		
	endif;
	
	$product_id = $values['product_id'];
	$product = wc_get_product( $product_id );
	$attributes = $product->get_attributes();	
	foreach ($attributes as $taxonomy => $attribute_obj ) {	
		$attribute_label_name = wc_attribute_label($taxonomy);	
		$field_name = 'pravel_attribute_'.$attribute_label_name;		
		wc_add_order_item_meta( $item_id, $attribute_label_name, $values[$field_name] );
	}
}


//Change rent price
function pravel_booking_product_price_update( $cart ) {
	
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;
    foreach (  $cart->get_cart() as $cart_item_key => $cart_item ) :
        if ( isset( $cart_item['pravel_booking_price'] ) ) :
            $cart_item['data']->set_price( $cart_item['pravel_booking_price'] );		
		endif;
    endforeach;
	
}

//Get Buy product stock
function pravel_get_normal_product_stock(){
    
    foreach ( WC()->cart->get_cart() as $cart_item ) {        
        $product = $cart_item['data'];
        $stock_qty = $product->get_stock_quantity();
    }
    return $stock_qty;
}

//Get Buy product QTY
function pravel_normal_product_qty(){
    
	if( pravel_get_normal_product_stock() ){
        $qty = pravel_get_normal_product_stock();
        echo '<input type="hidden" name="pravel_product_qty" id="pravel_product_qty" value="'.$qty.'"/>';
    }
}

//Store Pickup Address 
function pravel_pickup_store_address($order, $sent_to_admin, $plain_text, $email){
	$pickup_option = get_post_meta($order->get_id(), 'Pickup', true);
	$alternative_address = get_option('pravel_alternative_store_field');
	
	if($pickup_option == 'yes'){
	    if($alternative_address == 'yes') {
        	$store_name = get_option('pravel_store_name');
			$store_address = get_option( 'pravel_store_address_1' );
			$store_address_2 = get_option( 'pravel_store_address_2' );
			$store_city = get_option( 'pravel_store_city' );
			$store_country = WC()->countries->get_base_country();
			$country = WC()->countries->countries[ $store_country ];
			$store_state = WC()->countries->get_base_state();
			$state = WC()->countries->states[$store_country][ $store_state ];
			$store_postcode = get_option( 'pravel_store_zip' );

			_e('<input type="image" id="printDiv" src="'.plugins_url( "pravel-rent-and-sell-woocommerce-addon-pro/assets/css/images/print-icon.png" ).'" alt="Submit" width="50" height="50">');
				
			_e('<div id="pravel_store_address">');

			_e('<h3>Please Collect Your Order From Following Address</h3> <br>');

			echo $store_name . ',<br>' . $store_address . ',<br>' . $store_address_2 . ',<br>' . $store_city . ',<br>' . $state . ',<br>' .$country . ',<br>' . $store_postcode .'.';
				
			_e('</div>');
        } else {
			$store_address = get_option( 'woocommerce_store_address' );
			$store_address_2 = get_option( 'woocommerce_store_address_2' );
			$store_city = get_option( 'woocommerce_store_city' );
			$store_country = WC()->countries->get_base_country();
			$country = WC()->countries->countries[ $store_country ];
			$store_state = WC()->countries->get_base_state();
			$state = WC()->countries->states[$store_country][ $store_state ];
			$store_postcode = get_option( 'woocommerce_store_postcode' );
				
			_e('<input type="image" id="printDiv" src="'.plugins_url( "pravel-rent-and-sell-woocommerce-addon-pro/assets/css/images/print-icon.png" ).'" alt="Submit" width="50" height="50">');
				
			_e('<div id="pravel_store_address">');

			_e('<h3>Please Collect Your Order From Following Address</h3> <br>');

			echo $store_name . ',<br>' . $store_address . ',<br>' . $store_address_2 . ',<br>' . $store_city . ',<br>' . $state . ',<br>' .$country . ',<br>' . $store_postcode .'.';
				
			_e('</div>');
        }
	}
}

