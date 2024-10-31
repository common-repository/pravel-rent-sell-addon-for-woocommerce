<?php

if ( ! class_exists( 'Pravel_Gateway_Pickup' ) ) :
    
	class Pravel_Gateway_Pickup extends WC_Payment_Gateway {

		public function __construct() {			
			
			$this->id = 'pickup';
			$this->has_fields = false;
			$this->method_title = __( 'Cash on Pickup', 'pravel-gateway-pickup' );
			$this->method_description = __('Have your customers pay with cash (or by other means) upon Pickup.');
			
			$this->pravel_init_form_fields();			
			
			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );
			$this->order_status = $this->get_option( 'order_status', 'completed' );
			
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'pravel_thankyou_page' ) );
			
			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'pravel_email_instructions' ), 10, 3 );
			
		}
		
		
		//Initialize Gateway Settings Form Fields		
		public function pravel_init_form_fields() {
		  
			$this->form_fields = apply_filters( 'wc_offline_form_fields', array(
				  
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'pravel-gateway-pickup' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Pickup Payment', 'pravel-gateway-pickup' ),
					'default' => 'yes'
				),
		
				'title' => array(
					'title'       => __( 'Title', 'pravel-gateway-pickup' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'pravel-gateway-pickup' ),
					'default'     => _x( 'Cash on Pickup', 'pravel-gateway-pickup' ),
					'desc_tip'    => true,
				),
		
				'description' => array(
					'title'       => __( 'Description', 'pravel-gateway-pickup' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'pravel-gateway-pickup' ),
					'default'     => __( 'Please remit payment to Store Name upon pickup.', 'pravel-gateway-pickup' ),
					'desc_tip'    => true,
				),
		
				'instructions' => array(
					'title'       => __( 'Instructions', 'pravel-gateway-pickup' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'pravel-gateway-pickup' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			) );
		}
		
		/**
		 * Output for the order received page.
		 */
		public function pravel_thankyou_page() {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) );
			}
		}
	
		//Add content to the WC emails.		 
		public function pravel_email_instructions( $order, $sent_to_admin, $plain_text = false ) {
				
			if ( $this->instructions && ! $sent_to_admin && 'offline' === $order->payment_method && $order->has_status( 'processing' ) ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}
		
		public function process_payment( $order_id ) {
		
			$order = wc_get_order( $order_id );
					
			// Mark as on-hold (we're awaiting the payment)
			$order->update_status( 'processing', __( 'Awaiting offline payment', 'pravel-gateway-pickup' ) );
					
			// Remove cart
			WC()->cart->empty_cart();
					
			// Return thankyou redirect
			return array(
				'result'    => 'success',
				'redirect'  => $this->get_return_url( $order )
			);
		}
	} 
	return new Pravel_Gateway_Pickup();
endif;
