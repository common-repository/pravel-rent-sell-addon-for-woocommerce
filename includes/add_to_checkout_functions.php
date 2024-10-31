<?php


add_action( 'woocommerce_after_order_notes', 'pravel_pickup_order_option', 10, 1 );

add_action( 'wp_ajax_pravel_get_pickup_data', 'pravel_get_pickup_data' );

add_action( 'wp_ajax_nopriv_pravel_get_pickup_data', 'pravel_get_pickup_data' );

add_filter( 'woocommerce_package_rates','pravel_conditional_shipping_cost', 90, 2 );

add_action( 'woocommerce_checkout_update_order_review', 'pravel_new_shipping_methods', 10, 1 );

add_action('woocommerce_checkout_update_order_meta', 'pravel_save_delivery_option');

add_filter( 'woocommerce_payment_gateways', 'pravel_pickup_add_to_gateways' );

add_filter('woocommerce_available_payment_gateways', 'pravel_hide_payment_method', 10, 1 );



// Add a Custom checkbox field for shipping options
function pravel_pickup_order_option( $checkout ) {
    $pickOrder = get_option('pravel_pickup_enable_field');

    if($pickOrder == 'yes') {
        $field_id = 'pickup_order';
   
        woocommerce_form_field( $field_id, array(
            'type' => 'checkbox',
            'class' => array( 'form-row-wide' ),
            'label' => __('Pickup Your Order From Store'),
        ), '' );
    
        echo '<input type="hidden" id="pravel_delivery_selected" name="pravel_delivery_selected" value=""/>';
    }
}

// function that gets the Ajax data
function pravel_get_pickup_data() {
	
    if ( $_POST['pickup_order'] == '1' ){
        WC()->session->set('pickup_order', '1' );
    } else {
        WC()->session->set('pickup_order', '0' );
    }
    echo json_encode( WC()->session->get('pickup_order' ) );
    die(); 
	
}

// Conditionally changing the shipping methods costs
function pravel_conditional_shipping_cost( $rates, $package ) {

    if ( WC()->session->get('pickup_order' ) == '1' ){
        foreach ( $rates as $rate_key => $rate_values ) {            
            if ( 'free_shipping' !== $rate_values->method_id ) {                
                $rates[$rate_key]->cost = 0;              
                $taxes = array();
                foreach ($rates[$rate_key]->taxes as $key => $tax)
                    if( $rates[$rate_key]->taxes[$key] > 0 ) // set the new tax cost
                        $taxes[$key] = 0;
                $rates[$rate_key]->taxes = $taxes;
            }
        }
    }
    return $rates;
}

// Enabling, disabling and refreshing session shipping methods data
function pravel_new_shipping_methods( $post_data ){
    $bool = true;
    if ( WC()->session->get('pickup_order' ) == '1' ){
        $bool = false;
    } 

    foreach ( WC()->cart->get_shipping_packages() as $package_key => $package ){
        WC()->session->set( 'shipping_for_package_' . $package_key, $bool );
    }
    WC()->cart->calculate_shipping();
}

function pravel_save_delivery_option($order_id) {
	
	if (!empty($_POST['pravel_delivery_selected'])) {
		update_post_meta($order_id, 'Pickup',sanitize_text_field($_POST['pravel_delivery_selected']));
	}
}

function pravel_pickup_add_to_gateways( $gateways ) {
    $gateways[] = 'Pravel_Gateway_Pickup';
    return $gateways;
}

function pravel_hide_payment_method( $available_gateways ){
	if(!is_admin()) {
        if ( WC()->session->get('pickup_order' ) == '1' ){
            echo '<style>.payment_method_pickup{display: block;}</style>';
            foreach( $available_gateways as $gateways_id => $gways ){
                if( $gateways_id !== 'pickup' ) {
                    unset($available_gateways[$gateways_id]);
                }
            }
        } 
    	else {
            WC()->session->__unset( 'chosen_payment_method' );
            WC()->session->set( 'chosen_payment_method', 'bacs' );
            
        }
        
        $session = WC()->session->get('pickup_order' );
        
        if (empty($session)) {
            echo '<style>.payment_method_pickup{display: none;}</style>';
        }
        
        
        if( WC()->session->get('pickup_order' ) == '0' ) {
            unset($available_gateways['pickup']);
        }
       
        WC()->session->__unset( 'pickup_order' );
        return $available_gateways;
    }
}

function pravel_payment_method_text( $content ) {
    
    $content = "You Have selected Pickup From Shop Option.";
    return $content;
}