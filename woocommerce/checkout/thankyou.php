<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order">

	<?php if ( $order ) :
        
		do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>
            
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>
            <?php $pickup_option = get_post_meta($order->get_id(), 'Pickup', true); 
            $alternative_address = get_option('pravel_alternative_store_field'); ?>
			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>
                <?php 
                $pay_method = '';
                if($pickup_option == 'yes'){
                    $pay_method = 'Cash on Pickup';
                } else {
                    $pay_method = $order->get_payment_method_title();
                }
                
                ?>
				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo $pay_method; ?></strong>
					</li>
				<?php endif; ?>

			</ul>

		<?php endif; ?>
        
        <?php if($pickup_option == 'yes'){
                    echo '<p>Pay with cash upon Pickup.</p>';
                } else {
                    do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
                } ?>
		
		<?php if($pickup_option == 'yes'){
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
				
			} ?>
		
		<script type="text/javascript">
            jQuery(document).ready(function( $ ) {
                $('#printDiv').click(function(){
            		var printContents = document.getElementById('pravel_store_address').innerHTML;
            		var originalContents = document.body.innerHTML;
            		document.body.innerHTML = printContents;
            		window.print();
            		document.body.innerHTML = originalContents;
            	});
            });
        </script>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>
