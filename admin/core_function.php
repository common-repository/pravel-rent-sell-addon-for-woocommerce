<?php
/* All core admin side product function */

if ( ! class_exists( 'Pravel_Product_Settings' ) ) :
class Pravel_Product_Settings {	

	//call all action
	public function __construct() {
		
		add_filter( 'woocommerce_locate_template', array( $this,'pravel_woocommerce_locate_template'), 10, 3 );
		
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'pravel_rental_product_option' ),10, 0 );
		
		add_action( 'woocommerce_product_data_panels', array( $this, 'pravel_rental_option_fields' ));
		
		add_action( 'woocommerce_product_data_panels', array( $this, 'pravel_booked_all_data' ));
		
		add_action( 'product_type_options', array( $this, 'pravel_enable_disable_option' ) );
		
		add_action( 'save_post_product', array( $this, 'pravel_save_product_option' ),40, 1 );
		
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'pravel_rental_price_option' ) );
		
		add_filter( 'woocommerce_product_data_tabs', array( $this,'pravel_add_rental_option_tab' ), 0 );
		
		add_action( 'woocommerce_thankyou', array( $this,'pravel_manage_quantity_after_order') );

		add_action( 'plugins_loaded', array($this,'pravel_add_pickup_payment_gateway' ) );
		
		add_action( 'admin_footer', array($this,'pravel_my_admin_footer_function' ) );

		add_filter( 'woocommerce_product_data_tabs',  array( $this,'pravel_reorder_tabs'), 98 );

		add_action( 'woocommerce_after_shop_loop_item', array( $this,'pravel_add_to_cart_text'), 5 );
	}

	//Add to Cart Text
	public function pravel_add_to_cart_text() {
		global $product;
		$id = $product->get_id();
		$ProductRentable = get_post_meta($id, '_product_attributes', true);
		$link = $product->get_permalink();
		
		if (is_shop() && !empty($ProductRentable) ) {

			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			
			echo '<a href="'.$link.'" data-quantity="1" class="button product_type_variable add_to_cart_button" data-product_id="'.$id.'" data-product_sku="" rel="nofollow">View Product</a>';
		} else {
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		}
	}
	
	//Paid Plugin Popup	
	public function pravel_my_admin_footer_function() {
		echo '<div class="pravel_parent_popup" id="mypravelpopup">
		<div class="pravel_popup_bg" >
				<div class="pravel_popup_main">
					<div class="Pravel_popup_close_btn">
						<a href="javascript:void(0)" class="pravel_close"></a>
					</div>

					<div class="pravel_popup_left">

					</div>
					<div class="pravel_popup_right">
						<div class="pravel_buy_logo">
							<img src="'.PRAVEL_PLUGIN_URL .'images/Doneplugin02_round.png">
						</div>
						<h2>Get Powerful Plugin for Dropshipping Business</h2>
						<div class="pravel_buy_btn">
							<a href="#"><img src="'.PRAVEL_PLUGIN_URL .'images/buy-now-button.png"></a>
						</div>
						<a href="#" style="padding: 0px 0px 0px 95px;">Watch Video To see How it Works</a>
						
					</div>
				</div>                  
			</div>';
	}
	
	//Reorder Left side menu product edit page
	public function pravel_reorder_tabs( $tabs ) {		
		$tabs['general']['priority'] = 5;			
		$tabs['pravel_rental_option']['priority'] = 10;		
		$tabs['pravel_booked_data']['priority'] = 15;	
		return $tabs;
	}
	
	//Pickup Payment gateway
	public function pravel_add_pickup_payment_gateway() {
	    $pickup = get_option('pravel_pickup_enable_field');	   
	    if($pickup == 'yes') {
	        include_once( PRAVEL_PLUGIN_DIR . 'admin/add_payment_method.php');
	    }
	}

	
	public function pravel_woocommerce_locate_template( $template, $template_name, $template_path ) {
        global $woocommerce;

        $_template = $template;

        if ( ! $template_path ) $template_path = $woocommerce->template_url;        
        $plugin_path  = PRAVEL_PLUGIN_DIR . '/woocommerce/';

        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        // Modification: Get the template from this plugin, if it exists
        if ( ! $template && file_exists( $plugin_path . $template_name ) )
           $template = $plugin_path . $template_name;

        // Use default template
        if ( ! $template )
            $template = $_template;

        // Return what we found
        return $template;
    }
	
	
	public function pravel_manage_quantity_after_order( $order_id ){
		$user_id = get_current_user_id();
		$order = wc_get_order( $order_id );		
		$items = $order->get_items();
		$i = 0;
		foreach ( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();
			$product_id = $product->get_id();
			$order_qty = $item->get_quantity();
	
			$booking_check = wc_get_order_item_meta($item_id, 'Start Date', true);
			$end_booking_check = wc_get_order_item_meta($item_id, 'End Date', true);
			$check_product_qty_type = get_post_meta( $product_id, '_manage_stock', true );
			$old_simple_pro_qty = get_post_meta( $product_id, '_stock', true );
			$old_rent_pro_qty = get_post_meta( $product_id, 'pravel_rent_product_qty', true );
			$new_simple_pro_qty = $old_simple_pro_qty + $order_qty;
		
			$previous_booking_arr = get_post_meta( $product_id, 'pravel_order_booking', true );
			
			
			$order_booking_array = array();		
			if($booking_check != '' && !empty($booking_check))
			{
				
				$order_booking_array[$i]['from_date'] = $booking_check;
				$order_booking_array[$i]['to_date'] = $end_booking_check;
				$order_booking_array[$i]['qty'] = $order_qty;
				$order_booking_array[$i]['user_id'] = $user_id;
				if(empty($previous_booking_arr)):
					$order_booking_array_mrg = $order_booking_array;
				else :
					$order_booking_array_mrg = array_merge($previous_booking_arr, $order_booking_array);
				endif;
				
				if($check_product_qty_type == 'yes')
				{					
					update_post_meta($product_id, '_stock', $new_simple_pro_qty);
					update_post_meta($product_id, '_stock_status', 'instock');
				}				
				update_post_meta($product_id, 'pravel_order_booking', $order_booking_array_mrg);
				$i++;
			}
			$new_qty_left_simple_product = $new_simple_pro_qty - $order_qty;
			if(($new_qty_left_simple_product <  $old_rent_pro_qty) && $check_product_qty_type == 'yes')
			{
				update_post_meta($product_id, '_backorders', 'yes');
			}
		}
		
		exit;
		
	}
	
	
	//Enable Disable Rental Option
	public function pravel_enable_disable_option($product_options){
		
		global $post;
		if(!empty($post)){
			$post_id = $post->ID;
		}
		else{
			$post_id = 0;
		}
		$selected_booking_option = get_post_meta( $post_id, 'pravel_admin_booking_option', true );
		$product_options['pravel_admin_booking_option'] = array(
            'id'            => 'pravel_admin_booking_option',
			'value'         => 'yes',
            'label'         => __( 'Rentable', 'woocommerce-easy-booking-system' ),         
            'default'       => $selected_booking_option === '1' ? 'yes' : 'no',
			
        );
		return $product_options;
	}
	
	
	public function pravel_rental_product_option() {
		global $post;
		if(!empty($post)){
			$post_id = $post->ID;
		}
		else{
			$post_id = 0;
		}
		$product = get_product( $post_id );	
		
		$vis = 'none';
		$selected_booking_option = get_post_meta( $post_id, 'pravel_admin_booking_option', true );
		$pravel_product_option = get_post_meta( $post_id, 'pravel_product_option', true );
		
		if($selected_booking_option == 1 && $product->is_type( 'simple' ) && !($product->is_downloadable('yes')) && !($product-> is_virtual('yes'))):		
			$vis = 'block';
		endif;
		if($selected_booking_option == 1 && $product->is_type( 'simple' ) && !($product->is_downloadable('yes')) && !($product-> is_virtual('yes'))  && $pravel_product_option == 'pravel_only_rent') :
		
			echo '<style>
				._regular_price_field, ._sale_price_field
				{
					display:none;
				}
				
			</style>';
		else :

			$pravel_product_option = 'pravel_only_rent';

		endif;
		if(!empty($product)):
			if(!($product->is_type( 'simple' )) || $product->is_downloadable('yes') || $product-> is_virtual('yes')) :
				echo '<style>
					label[for="pravel_admin_booking_option"]
					{
						display:none;
					}
					
				</style>';
			
			endif;
		endif;
		
		
		_e('<div class="prave_prduct_option_set_css" id="prave_prduct_option_set" style="display: '.$vis.';border-bottom: 1px solid #eee;">');
			
		_e('<fieldset class="form-field _field "><legend><b>Select Product Option</b></legend><ul class="wc-radios"><li><label><input name="pravel_product_option" value="pravel_sell_and_rent" type="radio" data-text="Rent and Sell Feature to allow Sell as well as Rent a Single Product." data-video="https://www.youtube.com/watch?v=6UhWv1_B8dU" class="" style=""> Rent &amp; Sell</label>
		</li><li><label><input name="pravel_product_option" value="pravel_only_rent" type="radio" class="" style="" checked="checked"> Only Rent</label>
		</li></ul></fieldset>');
		_e('</div>');
	}
	
	//Rental Price extra field
	public function pravel_rental_price_option() {
		global $post;
		if(!empty($post)){
			$post_id = $post->ID;
		}
		else{
			$post_id = 0;
		}
		$product = get_product( $post_id );	
		$vis = 'none';
		$vis2 = 'none';
		$vis3 = 'none';
		$selected = '';
		$selected_booking_option = 0;
		$selected_booking_option = get_post_meta( $post_id, 'pravel_admin_booking_option', true );		
		$pravel_booking_reg_price = get_post_meta( $post_id, 'pravel_booking_reg_price', true );
		$pravel_booking_sale_price = get_post_meta( $post_id, 'pravel_booking_sale_price', true );	

		$pravel_booking_extra_price_option = get_post_meta( $post_id, 'pravel_booking_extra_price_option', true );	

		$pravel_extra_price_date = get_post_meta($post_id, 'pravel_extra_price_date', true);
			
		$pravel_extra_price_days = get_post_meta($post_id, 'pravel_extra_price_days', true);
		
		$pravel_custom_price_dates_eve_month = get_post_meta($post_id, 'pravel_custom_price_dates_eve_month', true);
		
		if($pravel_booking_extra_price_option == 1) {
			$vis2 = 'block';
			$selected = 'checked';
		}
		
		
		
		if($selected_booking_option == 1 && $product->is_type( 'simple' ) && !($product->is_downloadable('yes')) && !($product-> is_virtual('yes'))){
			$vis = 'block';
		}
		
		$pravel_extra_price_same_option = get_post_meta( $post_id, 'pravel_extra_price_same_option', true );
		if($pravel_extra_price_same_option == '' )
		{
			$pravel_extra_price_same_option = 'pravel_max_price';
		}
	
		
		_e('<div id="pravel_rental_price_option" style="display: '.$vis.';border-bottom: 1px solid #eee;">
			<h4 style="padding: 0px 10px;">Rent Product</h4>');
		$args = array(
			'label' => 'Regular Price ('.get_woocommerce_currency_symbol().')',
			'placeholder' => '',
			'class' => 'wc_input_price short',
			'style' => '',
			'wrapper_class' => '',
			'value' => $pravel_booking_reg_price,
			'id' => 'pravel_booking_reg_price',			
			'name' => 'pravel_booking_reg_price', 
			'type' => 'text',
			
			
		);
		if($selected_booking_option == 1 && $product->is_type( 'simple' ) && !($product->is_downloadable('yes')) && !($product-> is_virtual('yes'))) {
			$required = array(
				'custom_attributes' => array(
					'required' 	=> true,				
				) 
			);
			$args = array_merge($required, $args);
		}		
		woocommerce_wp_text_input( $args );
		
		$args = array(
			'label' => 'Offer Price ('.get_woocommerce_currency_symbol().')',
			'placeholder' => '',
			'class' => 'wc_input_price short',
			'style' => '',
			'wrapper_class' => '',
			'value' => $pravel_booking_sale_price,
			'id' => 'pravel_booking_sale_price',
			'name' => 'pravel_booking_sale_price', 
			'type' => 'text',		  
		);
		woocommerce_wp_text_input( $args );
		
		
		_e('<div style="display:none;color: red;padding: 0px 12px;" class="error_pravel" id="pravel_price_error">Please Enter in a value less than regular price value </div>
			<p class="form-field pravel_booking_extra_price_option ">
				<label for="pravel_booking_extra_price_option">Set Special Prices</label>
				<input type="checkbox" class="checkbox" style="" name="pravel_booking_extra_price_option" id="pravel_booking_extra_price_option" data-text="Special Price Feature to allow Special Price for Different Days." data-video ="https://www.youtube.com/watch?v=SR9UNXpfvxM" placeholder="" '.$selected.'> 			
			</p>
		</div>');
		
	}

	//Save all extra product option
	public function pravel_save_product_option(){
		global $post;
		if(!empty($post)):
			
			global $wpdb;
			$id = $post->ID;
			$p_opt_check = 0;
			
			if(isset($_POST['pravel_admin_booking_option'])) :
				$p_opt_check = 1;			
			endif;
			update_post_meta( $id, 'pravel_admin_booking_option', $p_opt_check );	
			
			if(isset($_POST['pravel_product_option'])) :
				$value = sanitize_meta( 'pravel_product_option', $_POST['pravel_product_option'], 'post' );
				update_post_meta( $id, 'pravel_product_option', $value );	
			endif;
			
			if(isset($_POST['pravel_booking_reg_price'])) :
				$Regprice = sanitize_text_field( $_POST['pravel_booking_reg_price'] );
				update_post_meta( $id, 'pravel_booking_reg_price', $Regprice );	
			endif;
			
			if(isset($_POST['pravel_booking_sale_price'])) :
				$SalePrice = sanitize_text_field( $_POST['pravel_booking_sale_price'] );
				update_post_meta( $id, 'pravel_booking_sale_price', $SalePrice );	
			endif;
			
			//Days Block 
			$pravel_manage_add_block = 0;
			if(isset($_POST['pravel_manage_add_block'])) :
				$pravel_manage_add_block = 1;			
			endif;
			update_post_meta( $id, 'pravel_manage_add_block', $pravel_manage_add_block );

			$pravel_blocks_array = array();
			
			if(isset($_POST['pravel_block_name']) && !empty($_POST['pravel_block_name'])) {

				$pravel_block_name = array_map( 'sanitize_text_field', $_POST['pravel_block_name']);
				$pravel_block_days = array_map( 'absint', $_POST['pravel_block_days']);
				$pravel_block_price = array_map( 'sanitize_text_field', $_POST['pravel_block_price']);

				$i = 0;
				foreach ($pravel_block_name as $key => $value) { 

					$pravel_block_name_sub = $pravel_block_name[$key];
					$pravel_block_days_sub = $pravel_block_days[$key];
					$pravel_block_price_sub = $pravel_block_price[$key];

					$pravel_blocks_array[$i]['block_name'] = $pravel_block_name_sub;
					$pravel_blocks_array[$i]['block_days'] = $pravel_block_days_sub;
					$pravel_blocks_array[$i]['block_price'] = $pravel_block_price_sub;
					$i++;
				}

			}
			update_post_meta($id, 'pravel_blocks_ser', $pravel_blocks_array);

			$pravel_disable_product_date = array();	
			if(isset($_POST['pravel_from_date']) && !empty($_POST['pravel_from_date'])) {			
				
				$pravel_from_date = array_map( 'sanitize_text_field', $_POST['pravel_from_date']);
				$pravel_to_date = array_map( 'sanitize_text_field', $_POST['pravel_to_date']);					
				$i=0;
				foreach($pravel_from_date as $key => $val) {			
					$pravel_from_date_sub = $pravel_from_date[$key];
					$pravel_to_date_sub = $pravel_to_date[$key];
				    if(!empty($pravel_from_date_sub) || !empty($pravel_to_date_sub)) :
    					if(empty($pravel_from_date_sub) && !empty($pravel_to_date_sub)) :				
    						$pravel_from_date_sub = date('F d, Y', strtotime($pravel_to_date_sub .' -1 day'));
    					endif;
    					if(empty($pravel_to_date_sub) && !empty($pravel_from_date_sub) ) :				
    						$pravel_to_date_sub = date('F d, Y', strtotime($pravel_from_date_sub .' +1 day'));
    					endif;
    					$pravel_disable_product_date[$i]['from_date'] = $pravel_from_date_sub;
    					$pravel_disable_product_date[$i]['to_date'] = $pravel_to_date_sub;
    					
    					$i++;
    				endif;
				}
			}			
			update_post_meta($id, 'pravel_disable_dates', $pravel_disable_product_date);
					
			if(isset($_POST['pravel_min_booking_req'])) :
				$minBook = sanitize_text_field( $_POST['pravel_min_booking_req'] );
				update_post_meta($id, 'pravel_min_booking_req', $minBook );			
			endif;
			
			$regular_product_price = sanitize_text_field($_POST['_regular_price']);
			if($regular_product_price == '' && $p_opt_check == 1 && $_POST['pravel_booking_reg_price'] > 0)
			{
				$_POST['_regular_price'] = 0;
			}
		endif;
	}
	
	//Add custom rental Tab
	public function pravel_add_rental_option_tab( $product_data_tabs ) {	
		global $post;
		$selected_booking_option = get_post_meta( $post->ID, 'pravel_admin_booking_option', true );
		$show_class = 'pravel_rental_tab_hide';
		if($selected_booking_option == 1) :
			$show_class = 'pravel_rental_tab_show';
		endif;
			$product_data_tabs['pravel_rental_option'] = array(
				'label' => __( 'Rental Option', 'pravel-rental' ),
				'target' => 'pravel_rental_option',
				'class'     => array( 'show_if_simple', $show_class ),				
			);
			$product_data_tabs['pravel_booked_data'] = array(
				'label' => __( 'User Booking', 'pravel-rental' ),
				'target' => 'pravel_booked_data',
				'class'     => array( 'show_if_simple', $show_class ),				
			);
		return $product_data_tabs;
	}
	
	//Add All fields in Rental Tab section
	public function pravel_rental_option_fields(){
		global $post;
		if(!empty($post)){
			$post_id = $post->ID;
		}
		else{
			$post_id = 0;
		}		
		$pravel_min_booking_req = get_post_meta( $post_id, 'pravel_min_booking_req', true );		
		
		$pravel_manage_add_block = get_post_meta( $post_id, 'pravel_manage_add_block', true );
		$pr_block = '';
		$qty_required_block = '';
		if($pravel_manage_add_block == 1){
			$pr_block = 'checked="checked"';
			$qty_required_block = 'required';
		}
		$pravel_rent_product_qty = get_post_meta( $post_id, 'pravel_rent_product_qty', true );		
		$pravel_disable_dates_ser = get_post_meta($post_id, 'pravel_disable_dates'); 
		if(!empty($pravel_disable_dates_ser)) :
			$pravel_disable_dates_ser = $pravel_disable_dates_ser[0];
		endif;
		$data = '';
		$data .= '	
		<div id = "pravel_rental_option" class = "panel woocommerce_options_panel" >
			<div class = "options_group" >				
				<h4 style="margin-bottom:0px;">Disable product</h4>
				<span class="description">Add dates on which user want be able to book</span>
				<div class="">
					<div class="table_grid" >
						<table class="widefat" id="pravel_repeater">
							<thead >
								<tr>						
									<th>From</th>
									<th>To</th>							
									<th>Delete</th>
								</tr>
							</thead>
							<tbody>';
								if(!empty($pravel_disable_dates_ser)) :
									foreach($pravel_disable_dates_ser as $val) :
										$data .= '<tr class="items" data-group="date">
											<td>
												<input type="text" data-name="pravel_from_date" date-type = "pravel_start-date" value="'.$val['from_date'].'"  class="pravel_start-date" readonly="true"  />
											</td>
											<td>
												<input type="text" data-name="pravel_to_date" date-type = "pravel_end-date" value="'.$val['to_date'].'" class="pravel_end-date" readonly="true" />				
											</td>
											<td>
												<button type="button"  class="button button-danger remove-btn">Remove</button>
											</td>
										</tr>';
										
									endforeach;								
								else :								
									$data .= '<tr class="items" data-group="date">
										<td>
											<input type="text" data-name="pravel_from_date" date-type = "pravel_start-date" value=""  class="pravel_start-date" readonly="true" />
										</td>
										<td>
											<input type="text" data-name="pravel_to_date" date-type = "pravel_end-date" value="" class="pravel_end-date"   readonly="true" />				
										</td>
										<td>
											<button type="button"  class="button button-danger remove-btn">Remove</button>
										</td>
									</tr>';
								endif;
							$data .= '</tbody>
							<tfoot>
								<tr>
									<th colspan="6">
										<button type="button" class="button button-primary repeater-add-btn">Add Dates</button>
										<span class="description">Please select the date range to be disabled for the product.</span>
									</th>
								</tr>
							</tfoot>
							
						</table>
					</div>
				</div>
					<h4>Minimum Required Booking in Days</h4>
					<p class="form-field_min pravel_min_booking_req_field ">
						<label for="pravel_min_booking_req">Minimum Days Booking</label>
						<input type="text" class="" style="" name="pravel_min_booking_req" id="pravel_min_booking_req" value="'.$pravel_min_booking_req.'" placeholder=""> 
					</p>
					<div style="color: red; padding: 0px 12px;" class="error_pravel" id="pravel_min_book_error"></div>
					<h4>Add Blocks</h4>
					<p class="form-field_min pravel_rent_product_block ">
						<label for="pravel_manage_add_block">Enable Adding Blocks?</label>
						<input type="checkbox" class="checkbox" style="" name="pravel_manage_add_block" id="pravel_manage_add_block"  placeholder="" '.$pr_block.'> 
						<span class="description">Add Blocks In days</span>
					</p>
					<div class="pravel_block_table">
						<div class="table_grid" >
							<table class="widefat" id="pravel_block_repeater">
								<thead>
									<tr>						
										<th>Block Name</th>
										<th>No. of Days</th>			
										<th>Price</th>		
										<th>Delete</th>
									</tr>
								</thead>
								<tbody>
								<tr>					
									<td colspan="3"><p id="pravel_custom_price_date_error_block" style="color:red;padding: 0;margin: 0;display:none;"></p></td>								
								</tr>';
								$pravel_blocks_ser = get_post_meta($post_id, 'pravel_blocks_ser');
								$pravel_min_days = '';
								if($pravel_manage_add_block == 1){
								    $pravel_min_days_db = get_post_meta($post_id, 'pravel_min_booking_req', true);
									$pravel_min_days = 'min="'.$pravel_min_days_db.'"';
								}
								
								$err_class = "'pravel_custom_price_date_error_block'";
								if(!empty($pravel_blocks_ser)) :
									$pravel_blocks_ser = $pravel_blocks_ser[0];
								endif;
									if(!empty($pravel_blocks_ser)) :
										foreach($pravel_blocks_ser as $key => $val) :
											$data .= '<tr class="items" data-group="block">
												<td>
													<input type="text" data-name="pravel_block_name" value="'.$val['block_name'].'"  class="pravel_block-name" placeholder="" '.$qty_required_block.'/>
												</td>
												<td>
													<input type="number" data-name="pravel_block_days" '.$pravel_min_days.' value="'.$val['block_days'].'" class="pravel_block-days" placeholder="" '.$qty_required_block.' />
							
												</td>
												<td>
													<input type="text" data-name="pravel_block_price" value="'.$val['block_price'].'" class="pravel_block-price" placeholder="" '.$qty_required_block.' onkeyup="check_price_val(this, '.$err_class.')" />
													
												</td>
												<td>
													<button type="button"  class="button button-danger remove-btn remove-row">Remove</button>
												</td>
											</tr>';
										
										endforeach;								
									else :								
										$data .= '<tr class="items empty-row" data-group="block">
												<td>
													<input type="text" data-name="pravel_block_name" value=""  class="pravel_block-name" placeholder="" '.$qty_required_block.'/>
												</td>
												<td>
													<input type="number" data-name="pravel_block_days" '.$pravel_min_days.' value="" class="pravel_block-days" placeholder="" '.$qty_required_block.' />				
												</td>
												<td>
													<input type="text" data-name="pravel_block_price" data-type="pravel_block-price" value="" class="pravel_block-price" placeholder="" '.$qty_required_block.' onkeyup="check_price_val(this, '.$err_class.')" />				
												</td>
												<td>
													<button type="button"  class="button button-danger remove-btn remove-row">Remove</button>
												</td>
											</tr>';
									endif;
								$data .= '</tbody>
								<tfoot>
									<tr>
										<th colspan="6">
											<button type="button" class="button button-primary repeater-add-block-btn">Add Blocks</button>
											<span class="description">Please Add Blocks with Price.</span>
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="error-msg" style="display:none;">
							<p>Please Enter Block Days More than Minimum Booking Days.</p>
						</div>
					</div>
					<h4>Quantity For rent product only</h4>
					<p class="form-field_min pravel_rent_product_qty ">
						<label for="pravel_rent_product_qty">Stock Management</label>
						<input type="checkbox" class="checkbox" style="" name="pravel_manage_rent_stock" id="pravel_manage_rent_stock" data-text = "Manage Stock Feature to allow to Manage multiple Stocks" data-video = "https://www.youtube.com/watch?v=xpQedrlgP6w&t=23s"> 
						<span class="description">Manage rental Product Quantity only</span>
					</p>
					
			</div>			
		</div>';
		echo $data;
	}

	public function pravel_booked_all_data(){
		global $post;
		if(!empty($post)){
			$post_id = $post->ID;
		}
		else{
			$post_id = 0;
		}
		$pravel_min_booking_req = get_post_meta( $post_id, 'pravel_min_booking_req', true );		
		$pravel_rent_product_qty = get_post_meta( $post_id, 'pravel_rent_product_qty', true );		
		$pravel_booked_by_user = get_post_meta($post_id, 'pravel_order_booking'); 
		if(!empty($pravel_booked_by_user)):
			$pravel_booked_by_user = $pravel_booked_by_user[0];
		endif;
		$data = '';
		$data .= '	
		<div id = "pravel_booked_data" class = "panel woocommerce_options_panel" >
			<div class = "options_group" >				
				<h4>User Booking</h4>
				<div class="prave_prduct_option_set_css">
					<fieldset class="form-field _field ">
						
						<ul class="wc-radios">
							<li>
								<label>
									<input name="pravel_booking_sorting" value="pravel_all_booking" type="radio" class="" style="" checked="checked"> All Order
								</label>
							</li>
							<li>
								<label>
									<input name="pravel_booking_sorting" value="pravel_future_booking" type="radio" class="" style=""> Future Order
								</label>
							</li>
							<li>
								<label>
									<input name="pravel_booking_sorting" value="pravel_past_booking" type="radio" class="" style=""> Past Order
								</label>
							</li>
						</ul>
					</fieldset>
				</div>
				<div class="">
					<div class="table_grid pravel_datatable_div" id="pravel_data_table_div" >
						<table class="display" id="pravel_data_table">
							<thead>
								<tr>
									<th>No</th>
									<th>User</th>
									<th>From</th>
									<th>To</th>							
									<th>Quantity</th>
								</tr>
							</thead>
							<tbody>';
								
									$no = 1;
									if(!empty($pravel_booked_by_user)):
										foreach($pravel_booked_by_user as $val) :
										$user_id = $val['user_id'];									
										$user_edit_link = admin_url( sprintf( 
				   'user-edit.php?user_id=%d',$user_id) );										
											$data .= '<tr class="items" data-group="date">
												<td>'.$no.'</td>
												<td><a href="'.$user_edit_link.'" target="_blank">'.$val['user_id'].'</a></td>
												<td>'.$val['from_date'].'</td>
												<td>'.$val['to_date'].'</td>							
												<td>'.$val['qty'].'</td>											
											</tr>';
											$no++;
											
										endforeach;	
									endif;
								
							$data .= '</tbody>						
							
						</table>
					</div>
					
					<div class="table_grid pravel_datatable_div" id="pravel_data_table_future_div" style="display:none;">
						<table class="display" id="pravel_data_table_future">
							<thead >
								<tr>
									<th>No</th>
									<th>User</th>
									<th>From</th>
									<th>To</th>							
									<th>Quantity</th>
								</tr>
							</thead>
							<tbody>';
								
									$no = 1;
									if(!empty($pravel_booked_by_user)):
										foreach($pravel_booked_by_user as $val) :
										$user_id = $val['user_id'];
										$order_start_date = new DateTime( $val['from_date'] ); 
										$today = new DateTime();
										$user_edit_link = admin_url( sprintf( 
				   'user-edit.php?user_id=%d',$user_id) );
										if($order_start_date >= $today ){
											$data .= '<tr class="items" data-group="date">
												<td>'.$no.'</td>
												<td><a href="'.$user_edit_link.'" target="_blank">'.$val['user_id'].'</a></td>
												<td>'.$val['from_date'].'</td>
												<td>'.$val['to_date'].'</td>							
												<td>'.$val['qty'].'</td>											
											</tr>';
											$no++;
										}
										endforeach;	
									endif;
								
							$data .= '</tbody>						
							
						</table>
					</div>
					
					<div class="table_grid pravel_datatable_div" id="pravel_data_table_past_div" style="display:none;">
						<table class="display" id="pravel_data_table_past" >
							<thead >
								<tr>
									<th>No</th>
									<th>User</th>
									<th>From</th>
									<th>To</th>							
									<th>Quantity</th>
								</tr>
							</thead>
							<tbody>';
							
									$no = 1;
									if(!empty($pravel_booked_by_user)):
										foreach($pravel_booked_by_user as $val) :
										$user_id = $val['user_id'];
										$order_start_date = new DateTime( $val['from_date'] ); 
										$today = new DateTime();
										$user_edit_link = admin_url( sprintf( 
				   'user-edit.php?user_id=%d',$user_id) );
											if($order_start_date < $today ){
												$data .= '<tr class="items" data-group="date">
													<td>'.$no.'</td>
													<td><a href="'.$user_edit_link.'" target="_blank">'.$val['user_id'].'</a></td>
													<td>'.$val['from_date'].'</td>
													<td>'.$val['to_date'].'</td>							
													<td>'.$val['qty'].'</td>											
												</tr>';
												$no++;
											}
										endforeach;		
									endif;
								
							$data .= '</tbody>						
							
						</table>
					</div>
				</div>					
			</div>			
		</div>';
		echo $data;
	}

}
return new Pravel_Product_Settings();
endif;
