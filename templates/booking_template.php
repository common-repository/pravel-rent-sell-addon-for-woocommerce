<?php
/*  Front site Booking template option */

if ( ! class_exists( 'Pravel_rental_front_option' ) ) :
class Pravel_rental_front_option {	

	public function __construct(){
		
		add_action( 'woocommerce_single_product_summary', array($this, 'pravel_select_product_option' ));
		
		add_action( 'woocommerce_product_meta_start', array($this, 'pravel_rental_datepicker_option' ));
		
		add_action( 'woocommerce_before_shop_loop_item_title', array($this, 'pravel_product_option_tag' ));
		
		add_action( 'woocommerce_before_add_to_cart_button',  array($this, 'pravel_attribute_option_dropdown' )); 			
	}
	
	//Rental and Attribute management product detail page
	public function pravel_rental_datepicker_option(){		
		global $post;
		$id = $post->ID;
		$selected_booking_option = get_post_meta( $id, 'pravel_admin_booking_option', true );
		$product = get_product( $id );		
		if($selected_booking_option == 0 || !($product->is_type( 'simple' )) || $product->is_downloadable('yes') || $product-> is_virtual('yes'))
		{
			return;
		}	
		global $product;
		$attributes = $product->get_attributes();
		global $woocommerce;
		$cart_items = $woocommerce->cart->get_cart();				
		$cart_my_data = array();
		$i = 0;
		foreach($cart_items as $item => $values) : 
			$cart_pro_id = $values['data']->get_id();
			if($id == $cart_pro_id) :			
			
			$cart_my_data[$i]['from_date'] = $cart_items[$item]['pravel_front_start_date'];
			$cart_my_data[$i]['to_date'] = $cart_items[$item]['pravel_front_end_date'];
			$cart_my_data[$i]['qty'] = $cart_items[$item]['quantity'];			
			$i++;
			
			endif;
		endforeach;
		
		$data = '';
		
		$pravel_booking_price = 0;
		$pravel_choose_product_option = 'pravel_buy';
		$selected_booking_option = get_post_meta( $id, 'pravel_admin_booking_option', true );
		if($selected_booking_option == 0)
		{
			return;
		}	
		$product_check_mangestock = get_post_meta( $id, 'pravel_manage_rent_stock', true );			
		$pravel_product_option = get_post_meta( $id, 'pravel_product_option', true );
		$pravel_booking_reg_price = get_post_meta( $post->ID, 'pravel_booking_reg_price', true );
		$pravel_booking_sale_price = get_post_meta( $post->ID, 'pravel_booking_sale_price', true );
		$pravel_book_product_qty = get_post_meta( $post->ID, 'pravel_rent_product_qty', true );
		$pravel_buy_product_qty = get_post_meta( $post->ID, '_stock', true );
		$pravel_manage_stock = get_post_meta( $post->ID, 'pravel_manage_rent_stock', true );
		$simple_manage_stock = get_post_meta( $post->ID, '_manage_stock', true );
		$stock_availability = get_post_meta( $post->ID, '_stock_status', true );
		$backorders = get_post_meta( $post->ID, '_backorders', true );
		if(!empty($pravel_booking_sale_price)) : 
			$pravel_booking_price = $pravel_booking_sale_price;
		else :
			$pravel_booking_price = $pravel_booking_reg_price;
		endif;
		
		$pravel_min_book_required = get_post_meta( $post->ID, 'pravel_min_booking_req', true );
		
		$pravel_disable_dates_ser = get_post_meta($id, 'pravel_disable_dates'); 
		$pravel_disable_dates_ser = $pravel_disable_dates_ser[0];	
		$pravel_disable_dates_ser_json = htmlspecialchars(json_encode($pravel_disable_dates_ser));
		
		
		$pravel_booked_by_user = get_post_meta($id, 'pravel_order_booking'); 
		$pravel_booked_by_user = $pravel_booked_by_user[0];
		
		if($pravel_buy_product_qty == 0 && $simple_manage_stock == 'yes'){
			return;
		}		
		if($stock_availability == 'outofstock'){
			return;
		}
		if(!empty($cart_my_data) && !empty($pravel_booked_by_user))
		{
			$pravel_booked_by_user = array_merge($pravel_booked_by_user, $cart_my_data);
		}	
		$pravel_booked_by_user_json = htmlspecialchars(json_encode($pravel_booked_by_user));
		
		if($pravel_product_option == 'pravel_only_rent') :		
			
			$pravel_choose_product_option = 'pravel_rent';
			
			$data .= '<style>
					form.cart:not(.pravel_rent_date_field_form)
					{
						display:none !important;
					}
				</style>';
		endif;
		
		
		
		
		$data .= '
		
		<input type="hidden" id="pravel_min_book_required" name="pravel_min_book_required" value="'.$pravel_min_book_required.'" />
		<input type="hidden" id="pravel_buy_product_qty_check" name="pravel_buy_product_qty_check" value="'.$simple_manage_stock.'" />';
		
		if($selected_booking_option) :
			
			global $product;
			
			$data .= '<div class="pravel_datepicker_product_front" id="pravel_date_choose_section">
					<form autocomplete="off" class="cart pravel_rent_date_field_form" action="'.get_permalink( $id ).'" method="POST" enctype="multipart/form-data">
						<input type="hidden" id="pravel_book_product_qty" name="pravel_book_product_qty" value="'.$pravel_buy_product_qty.'" pravel_buy_qty = "'.$pravel_buy_product_qty.'" />
						
						<input type="hidden" id="pravel_book_product_qty_nochange" name="pravel_book_product_qty_nochange" value="'.$pravel_buy_product_qty.'" pravel_buy_qty = "'.$pravel_buy_product_qty.'" />';
						
					if(!empty($attributes)){
					$data .= '<h4 class="pravel_title">Select Attribute</h4>';
					foreach ($attributes as $taxonomy => $attribute_obj ) {			
						$attribute_label_name = wc_attribute_label($taxonomy);	
						$all_attr_value = get_terms($taxonomy, array(
							'hide_empty' => false,
						));
						$data .= '<select required name="pravel_attribute_'.$attribute_label_name.'">
							<option value="">Select '.$attribute_label_name.'</option>';
							if(isset($all_attr_value->errors))
							{
								$option_data = $attribute_obj->get_data();							
								$attr_option = $option_data['options'];
								$total_attr = count($attr_option);
								for($i = 0; $i<$total_attr; $i++){			
									$data .= '<option value="'.$attr_option[$i].'">
										'.$attr_option[$i].'
									</option>';					
								}
							}
							else
							{
								foreach ($all_attr_value as $val):				
									$data .= '<option value="'.$val->name.'">
										'.$val->name.'
									</option>';					
								endforeach;
							}
							$data .= '</select>';			
					}
				}
				
				
				$data .= '<h4 class="pravel_title">Select Booking Date</h4>
					<div class="pravel_booking_guide">
						<ul>
							<li><span class="selected_pravel"></span> Selected Date</li>
							<li><span class="disabled_pravel"></span> Disabled Date</li>
							<li><span class="current_pravel"></span> Current Date</li>
						</ul>';
						if($pravel_min_book_required != '' || $pravel_min_book_required != 0)
						{
							$data .= '<div class="pravel_min_book_ins">
								<p>'.$pravel_min_book_required.' days minimum booking required</p>
							</div>';
						}
					$data .= '</div>
					<div class="pravel_rent_date_field_div">
						<input type="text" name="pravel_front_start_date" date-type = "pravel_front_start_date" placeholder="From Date" class="pravel_front_start_date" required />
						<input type="text" name="pravel_front_end_date" placeholder="To Date" class="pravel_front_end_date" value="" required />
						<div class="pravel_date_error" style="display:none;"><p>Product Not Available on this duration</p></div>
						<p id="has_disabled_value" style="color:#FF0000;"></p>
						<input type="hidden" id="pravel_disable_dates_ser_json" value="'.$pravel_disable_dates_ser_json.'" />					
						<input type="hidden" id="pravel_booked_by_user" value="'.$pravel_booked_by_user_json.'" />						

						<input type="hidden" name="block_price2" id="block_price2" />

						<input type="hidden" id="pravel_booking_price" name="pravel_booking_price" value="0" />
						<input type="hidden" id="pravel_booking_base_price" name="pravel_booking_base_price" value="'.$pravel_booking_price.'" />
						
						<input type="hidden" id="pravel_choose_product_option" name="pravel_choose_product_option" value="'.$pravel_choose_product_option.'" />		
						<input type="hidden" id="inner_form_qty_rent" name="pravel_book_product_qty" value="'.$pravel_book_product_qty.'" pravel_buy_qty = "'.$pravel_buy_product_qty.'" />
						<input type="hidden" name="action" value="submitform" />
						  '. wp_nonce_field( 'submitform', 'submitform_nonce' ).'
					</div>
					<div class="quantity">
						<label class="screen-reader-text" for="quantity_5d4560f705515">Demo Product 2 quantity</label>
						<input type="number" id="quantity_5d4560f705515" class="input-text qty text" step="1" min="1" max="'.$pravel_buy_product_qty.'" name="quantity" value="1" title="Qty" size="4" inputmode="numeric">
					</div>					
					<button type="submit" name="add-to-cart" value="'.$id.'" class="single_add_to_cart_button button alt">Book Now</button>				
				</form>';
				
				$data .= '</div>';			
				
			
		endif;
		echo $data;
	}
	
	//Choose Product Booking option
	public function pravel_select_product_option(){
		global $post;
		$id = $post -> ID;
		$data = '';
		$selected_booking_option = get_post_meta( $id, 'pravel_admin_booking_option', true );
		$product = get_product( $id );		
		if($selected_booking_option == 0 || !($product->is_type( 'simple' )) || $product->is_downloadable('yes') || $product-> is_virtual('yes'))
		{
			return;
		}			
		$pravel_product_option = get_post_meta( $id, 'pravel_product_option', true );	
		$pravel_booking_reg_price = get_post_meta( $post->ID, 'pravel_booking_reg_price', true );
		$pravel_booking_sale_price = get_post_meta( $post->ID, 'pravel_booking_sale_price', true );	
		$data .= '<input type="hidden" name="product_rent_type" id="product_rent_type" value="'.$pravel_product_option.'">';
		$vis = 'none';
		if($pravel_product_option == 'pravel_only_rent') :
			$vis = 'block';
			echo '<style>
					.price:not(.pravel_rent_price){display:none;}
				</style>';
		endif;
		
		//Stock Add to cart hide
		$simple_stock = get_post_meta( $post->ID, '_stock', true );
		$rental_stock = get_post_meta( $post->ID, 'pravel_rent_product_qty', true );
		$mange_stock = get_post_meta( $post->ID, '_manage_stock', true );
		$backorders = get_post_meta( $post->ID, '_backorders', true );
		if($simple_stock <= 0 && $rental_stock > 0 && $mange_stock == 'yes' && $backorders == "yes") :
			echo '<style>
				form.cart:not(.pravel_rent_date_field_form)
				{
					display:none !important;
				}
			</style>';
		endif;
		
		if($selected_booking_option == 1) :
			$data .= '
			<div class="pravel_datepicker_product_front" style="margin-bottom:20px;">';
			
				if($pravel_product_option == 'pravel_sell_and_rent') :
					$data .= '<h4 class="pravel_title">What you want?</h4>
					<input id="pravel_buy" type="radio" name="pravel_choose_option" value="pravel_buy" checked />
					<label for="pravel_buy"><span></span>Buy</label>
					<input id="pravel_rental" type="radio" name="pravel_choose_option" value="pravel_rental">
					<label for="pravel_rental"><span></span>Rentout</label>';

				endif;
				
				$enable_blocks = get_post_meta( $post->ID, 'pravel_manage_add_block', true );
				$pravel_block_array = get_post_meta( $post->ID, 'pravel_blocks_ser', true );

				if($enable_blocks == 1 && !empty($pravel_block_array[0]['block_name']) ) :
					$data .= '<div class="block_options"><h4 class="pravel_title">How You Want To Rent This Product?</h4>
					<input id="pravel_days" type="radio" name="pravel_choose_rent_option" value="pravel_days" checked />
					<label for="pravel_days"><span></span>Customized Days</label>
					<input id="pravel_block" type="radio" name="pravel_choose_rent_option" value="pravel_block">
					<label for="pravel_block"><span></span>Fixed Days Blocks</label></div>
					<input type="hidden" id="pravel_chosen_rent_option" />';
				endif;

				$pravel_block_array = get_post_meta( $post->ID, 'pravel_blocks_ser', true );
				
				if( empty( $pravel_block_array ) ){
				    return;
				}
				
				$data .= '<div class="block-select-option">
				<label for="block_select_option">Select Block : </label>
					<select class="block_select_option" name="block_select_option" id="block_select_option" data-id="'.$post->ID.'">
						<option vlaue="selet_option" selected disabled> Select Days Block </option>';
				foreach ($pravel_block_array as $key => $value) {
					$data .= '<option value="'.$value['block_price'].'" data-block="'.$value['block_name'].'" data-days="'.$value['block_days'].'"> '.$value['block_name'].'
					</option>';
				}
				$data .= '</select></div>';
				$data .= '<input type="hidden" name="block_selected" id="block_selected" />
				<input type="hidden" name="block_price" id="block_price" />
				<input type="hidden" name="block_days" id="block_days" />';
				
				$data .= '<p class="price pravel_block_price dont-display "style="display:none;">
							
					<ins>
						<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol" data-sym="'.get_woocommerce_currency_symbol().'"></span></span>
					</ins>
				</p>';

				if($pravel_booking_reg_price != '') :
					if($pravel_booking_sale_price != '') :					
						$data .= '<p class="price pravel_rent_price "style="display:'.$vis.'">
							<del>
								<span class="woocommerce-Price-amount amount">
									<span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span>'.$pravel_booking_reg_price.'
								</span>
							</del> 
							<ins>
								<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span>'.$pravel_booking_sale_price.'</span>
							</ins> / Day
						</p>';
					else :
						$data .= '<p class="price pravel_rent_price "style="display:'.$vis.'">
							
							<ins>
								<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span>'.$pravel_booking_reg_price.'</span>
							</ins> / Day
						</p>';
					endif;
				endif;

			$data .= '</div>';
			echo $data;
		endif;
	}
	
	//Attribute dropdown show in product page
	public function pravel_attribute_option_dropdown(){
		global $post;
		$id = $post->ID;
		$selected_booking_option = get_post_meta( $id, 'pravel_admin_booking_option', true );
		$product = get_product( $id );		
		if(!($product->is_type( 'simple' )) || $product->is_downloadable('yes') || $product-> is_virtual('yes'))
		{
			return;
		}	
		$data = '';
		
		global $product;
		$attributes = $product->get_attributes();
		if(!empty($attributes)){
			$data .= '<div class = "pravel_attribute_buy_div"><h4 class="pravel_title">Select Attribute</h4>';
			foreach ($attributes as $taxonomy => $attribute_obj ) {			
				$attribute_label_name = wc_attribute_label($taxonomy);	
				$all_attr_value = get_terms($taxonomy, array(
					'hide_empty' => false,
				));
				
				$data .= '<select required name="pravel_attribute_'.$attribute_label_name.'">
					<option value="">Select '.$attribute_label_name.'</option>';
					if(isset($all_attr_value->errors))
					{
						$option_data = $attribute_obj->get_data();							
						$attr_option = $option_data['options'];
						$total_attr = count($attr_option);
						for($i = 0; $i<$total_attr; $i++){			
							$data .= '<option value="'.$attr_option[$i].'">
								'.$attr_option[$i].'
							</option>';					
						}
					}
					else
					{
						foreach ($all_attr_value as $val):				
							$data .= '<option value="'.$val->name.'">
								'.$val->name.'
							</option>';					
						endforeach;
					}
					
				$data .= '</select>';			
			}
			$data .= '</div>';
			echo $data;
		}
	}
	
	//all option show
	public function pravel_product_option_tag(){
		global $post;
		$id = $post -> ID;
		
		$selected_booking_option = get_post_meta( $id, 'pravel_admin_booking_option', true );
		$product = get_product( $id );		
		if($selected_booking_option == 0 || !($product->is_type( 'simple' )) || $product->is_downloadable('yes') || $product-> is_virtual('yes'))
		{
			return;
		}	
		
		$pravel_product_option = get_post_meta($id, 'pravel_product_option', true );	
		$pravel_selected_option = '';
		if($selected_booking_option == 1) :		
			if($pravel_product_option == 'pravel_sell_and_rent') :			
				$pravel_selected_option = 'Rent & Buy';			
			elseif($pravel_product_option == 'pravel_only_rent') :			
				$pravel_selected_option = 'Only Rent';			
			else :
				$pravel_selected_option = 'Only Buy';
			endif;
		else :
			$pravel_selected_option = 'Only Buy';
		endif;
		$data = '';
		$data .= '
			<div class="pravel_product_option_tag">
				' .$pravel_selected_option. '
			</div>';
		echo $data;
		
	}

}

return new Pravel_rental_front_option();
endif;