<?php

if ( ! class_exists( 'Pravel_Settings_Tab_Notify' ) ) :
class Pravel_Settings_Tab_Notify {
     
    //call all action
	public function __construct() {
	    
        add_action( 'woocommerce_product_meta_start',  array($this, 'pravel_email_notification_subscribe'), 20 );

        add_action( 'wp_ajax_nopriv_pravel_stock_notify',  array($this, 'pravel_stock_notify') );
        add_action( 'wp_ajax_pravel_stock_notify',  array($this, 'pravel_stock_notify') );

        add_action( 'wp_ajax_nopriv_pravel_rent_stock_notify',  array($this, 'pravel_rent_stock_notify') );
        add_action( 'wp_ajax_pravel_rent_stock_notify',  array($this, 'pravel_rent_stock_notify') );
        
        add_filter( 'page_template', array($this,'pravel_page_template') );

        add_action( 'save_post_product',  array($this, 'pravel_stock_update') );
        
    } 
    
    //Function to display Email Notification Form.
    public function pravel_email_notification_subscribe(){
        
        global $product;       
        $rentableProduct = get_post_meta($product->id, 'pravel_admin_booking_option', true);
        $notify = get_option('woocommerce_notify_email');
        $manage = get_post_meta($product->id, '_manage_stock', true);
        $rentaloption = get_post_meta($product->id, 'pravel_product_option',true);
        $onlyRentemailids = get_post_meta($product->id, '_pravel_stock_rent_notify_email',true);
		$pravel_disable_dates_ser = get_post_meta($product->id, 'pravel_disable_dates'); 
        $pravel_disable_dates_ser = $pravel_disable_dates_ser[0];
        $sDate = sanitize_text_field($_POST['start_unavaiable_hidden_date']);
        $eDate = sanitize_text_field($_POST['start_unavaiable_hidden_date']);
        
        $userid = '';
        
        if($notify == 'no') {
            return;
        }
        
        if($rentableProduct == 1) {
            if($rentaloption == 'pravel_only_rent'){
                
                $userid = '';
                if ( is_user_logged_in() ) {
                    $current_user = wp_get_current_user();
                    $userid = $current_user->ID;
                    $value = '';
                    $text = '';
                    $enabled = '';
                    $usermails = get_user_meta( $userid, '_pravel_stock_rent_notify_email', true );
                    if (!empty($onlyRentemailids)) {
                                
                        foreach ($onlyRentemailids as $key => $eid){
                            if($eid['userid'] == $userid ) {
                                $value = $eid['email'];
                                $text = "You Have Already Enrolled for Notification of Product Re-Stock.";
                                $enabled = 'disabled';
                            } elseif( $eid['userid'] == $userid && $eid['start_date'] == $sDate ) {
                                $value = $eid['email'];
                                $text = "You Have Already Enrolled for Notification of Product Re-Stock.";
                                $enabled = 'disabled';
                            } else {
                                $value = $current_user->user_email;
                                $text = 'Enter your email to know when this product is Re-Stocked.';
                                $enabled = '';
                            }
                        }
                    } else {
                    	$text = 'Enter your email to know when this product is Re-Stocked.';
                        $value = $current_user->user_email;
                    }
                } else {
                	$text = 'Enter your email to know when this product is Re-Stocked.';
                    $value = isset( $_POST['_custom_option'] ) ? sanitize_text_field( $_POST['_custom_option'] ) : '';
                    $userid = '';
                                    
                }
                
                if(!empty($pravel_disable_dates_ser[0]['from_date']) || $product->get_stock_quantity() == 0) {
                    _e('<div class="full_rent_form">');
                        _e('<p> To Get Notify When Product Is Available! <a id="Notiyformlink">Click Here</a></p>');
					    _e('<div class="row buy_product notify_form" style="display:none;">');
						echo $text;
						_e('<input name="_custom_rent_option" id="_custom_rent_option" data-id="'.$product->id.'" data-user="'.$userid.'" placeholder="Enter your custom text" value="'.$value.'"  style="width:80%;" '.$enabled.' />');
						_e('<input type="hidden" name="product_type" id="product_type" value="pravel_rental"/>');
						_e('<button type="button" class="email_rent_subscribe" name="email_rent_subscribe" '.$enabled.'>Notify Me</button>');
						_e('<p>When the Product become available We will notify you with the dates for which the Product is available.</p>');
					    _e('</div><div id="loader" style="display:none;"><img src="'.plugin_dir_url( _DIR__ ) . 'images/loader.gif'.'" style="height: 30px;"></div>');
					 _e('</div>');
                }
                
                _e('<div class="full_rent_form2" >');
                    _e('<input type="hidden" name="start_unavaiable_hidden_date" id="start_unavaiable_hidden_date" value="" />');
                    _e('<input type="hidden" name="end_unavaiable_hidden_date" id="end_unavaiable_hidden_date" value="" />');
                    _e('<p> To Get Notify When Product Is Available! <a id="Notiyformlink">Click Here</a></p>');
					_e('<div class="row buy_product notify_form" style="display:none;">');
					echo $text;
					_e('<input name="_custom_rent_option" id="_custom_rent_option" data-id="'.$product->id.'" data-user="'.$userid.'" placeholder="Enter your custom text" value="'.$value.'"  style="width:80%;" '.$enabled.' />');
					_e('<input type="hidden" name="product_type" id="product_type" value="pravel_rental"/>');
					_e('<button type="button" class="email_rent_subscribe" name="email_rent_subscribe" '.$enabled.'>Notify Me</button>');
					_e('<p>When the Product become available We will notify you with the dates for which the Product is available.</p>');
					_e('</div><div id="loader" style="display:none;"><img src="'.plugin_dir_url( _DIR__ ) . 'images/loader.gif'.'" style="height: 30px;"></div>');
				_e('</div>');
            }
        } 
        if($rentableProduct == 0) {
			$emailids = get_post_meta($product->id, '_pravel_stock_notify_email',true);		
            if ($manage == 'yes'){
                if($product->get_stock_quantity() == 0) {
                    _e('<form>');
                        if ( is_user_logged_in() ) {
                            $current_user = wp_get_current_user();
                            $userid = $current_user->ID;
                            $value = '';
                            $text = '';
                            $enabled = '';
                            if (!empty($emailids)) {
                                    
                                foreach ($emailids  as $key => $eid){
                                    if($eid['userid'] == $userid && empty($eid['start-date'])) {
                                        $value = $eid['email'];
                                        $text = __("You Have Already Enrolled for Notification of Product Re-Stock.");
                                        $enabled = 'disabled';
                                    } else {
                                        $value = $current_user->user_email;
                                        $text = __('Enter your email to know when this product is Re-Stocked.');
                                        $enabled = '';
                                    }
                                }
                            } else {
                                $text = __('Enter your email to know when this product is Re-Stocked.');
                                $value = $current_user->user_email;
                            }
                        } else {
                            $text = __('Enter your email to know when this product is Re-Stocked.');
                            $value = isset( $_POST['_custom_option'] ) ? sanitize_text_field( $_POST['_custom_option'] ) : '';
                            $userid = '';
                                
                        }
                            
                        _e('<div class="row buy_product buy_notify_form">');
                            _e($text);
                            _e('<input name="_custom_option" id="_custom_option" data-id="'.$product->id.'" data-user="'.$userid.'" placeholder="Enter your custom text" value="'.$value.'"  style="width:80%;" '.$enabled.'/>');
                            _e('<input type="hidden" name="product_buy_type" id="product_buy_type" value="buy"/>');
                            _e('<button type="button" class="email_subscribe" name="email_subscribe" '.$enabled.'>Notify Me</button>');
                            _e('<p>When the Product be back in stock we will notify you</p>');
                            _e('</div><div id="loader" style="display:none;"><img src="'.plugin_dir_url( _DIR__ ) . 'images/loader.gif'.'" style="height: 30px;"></div>');
                    _e('</form>');
                }
            }
        }
        
        
    }
    
    public function pravel_page_template() {
        
        $unsubscribe_page = get_option('pravel_unsubscribe_page');
        $manage_notify_option = get_option('woocommerce_notify_email');
        $page_template = '';
        if($manage_notify_option == 'yes') {
            if ( is_page( $unsubscribe_page ) ) {
                $page_template = PRAVEL_PLUGIN_DIR . 'templates/unsubscribe.php';
            }
        }
        
        return $page_template;
    }
    
    //Function to save data of subscriber
    public function pravel_stock_notify() {
    
    	$id = sanitize_text_field($_POST['id']);
    	$value = sanitize_text_field($_POST['email']);
    	$user = sanitize_text_field($_POST['userid']);
    	$Type = sanitize_text_field($_POST['producttype']);

       $emailids = get_post_meta($id, '_pravel_stock_notify_email');
       
       $new = array('userid'=>$user, 'email' => $value, 'productType' => $Type);
       
        if(empty($emailids)){ 
            $emailids[] = $new;
        } else {
			array_push($emailids, $new);
        }
		
    	update_post_meta( $id, '_pravel_stock_notify_email', $emailids );
    	
    	die();
    }

    public function pravel_rent_stock_notify(){
        $id = sanitize_text_field($_POST['id']);
        $value = sanitize_text_field($_POST['email']);
        $user = sanitize_text_field($_POST['userid']);
        $type = sanitize_text_field($_POST['producttype']);
        $start = sanitize_text_field($_POST['start_date']);
        $end = sanitize_text_field($_POST['end_date']);
        $subscribe = 1;
        $randKey = mt_rand(pow(10,(10-1)),pow(10,10)-1);
        
         
        $emailids = get_post_meta($id, '_pravel_stock_rent_notify_email',true);
            
        $new = array('userid'=>$user, 'email' => $value, 'start_date' => $start, 'end_date' => $end, 'productType' => $type, 'subscribe' => $subscribe, 'key' => $randKey);
       
        if(empty($emailids)):
            $emailids[] = $new;
        else :
            array_push($emailids, $new);
        endif;
            
        update_post_meta( $id, '_pravel_stock_rent_notify_email', $emailids );
        

        
        
        die();
    }
    
    //Function to send Email Notification
    public function pravel_stock_update() {   
	
		if(isset($_POST['_original_stock'])):
			
			$original_stock = sanitize_text_field($_POST['_original_stock']);
			$new_stock = sanitize_text_field($_POST['_stock']);
			$product_id = sanitize_text_field($_POST['post_ID']);
			$product_title = sanitize_text_field($_POST['post_title']);			
			$old_rent_stock = get_post_meta($product_id, 'pravel_rent_product_qty', true);			
			$pravel_disable_dates_database = get_post_meta($product_id, 'pravel_disable_dates', true);
			$pravel_product_option = sanitize_text_field($_POST['pravel_product_option']);
			$pravel_admin_booking_option = sanitize_text_field($_POST['pravel_admin_booking_option']);
			$unsubscribe_page = get_option('pravel_unsubscribe_page');
	        $unsubscribe_page_url = get_permalink($unsubscribe_page);

			$pravel_disable_product_date = array(); 
			if(isset($_POST['pravel_from_date']) && !empty($_POST['pravel_from_date'])) {           
				
				$pravel_from_date = array_map( 'sanitize_text_field', $_POST['pravel_from_date']);
				$pravel_to_date = array_map( 'sanitize_text_field', $_POST['pravel_to_date']);                 
				$i=0;
				foreach($pravel_from_date as $key => $val) {            
					$pravel_from_date_sub = $pravel_from_date[$key];
					$pravel_to_date_sub = $pravel_to_date[$key];
					if(empty($pravel_from_date_sub) && !empty($pravel_to_date_sub)) :               
						$pravel_from_date_sub = date('F d, Y', strtotime($pravel_to_date_sub .' -1 day'));
					endif;
					if(empty($pravel_to_date_sub) && !empty($pravel_from_date_sub) ) :              
						$pravel_to_date_sub = date('F d, Y', strtotime($pravel_from_date_sub .' +1 day'));
					endif;
					$pravel_disable_product_date[$i]['from_date'] = $pravel_from_date_sub;
					$pravel_disable_product_date[$i]['to_date'] = $pravel_to_date_sub;
					
					$i++;
				}
			}

			$j=0;
			$val_text_date = '';
			if(!empty($pravel_disable_dates_database)) {
				foreach ($pravel_disable_dates_database as $key => $value) {
					if (!in_array($value, $pravel_disable_product_date)){
						$val_text_date .= $value['from_date'];
						$val_text_date .=' To ';
						$val_text_date .= $value['to_date'];
						$val_text_date .="\n";
						$j++;
					} 
					
				}
			}
			
			if($pravel_product_option == 'pravel_only_rent' && $pravel_admin_booking_option == 'on' ){
				if( ($j > 0 && $original_stock > 0 ) || ($new_stock > $original_stock && $original_stock == 0)) {
				
					$subject = get_option('pravel_notify_email_rent_subject') . ' ' .  $product_title;
					$message = wp_strip_all_tags(get_option('pravel_notify_email_rent_message'));
					$header_main = get_option('pravel_notify_email_rent_recepient');
					$header='';
					if($header_main == '') {
						$header = 'From: wordpress@pravelsolutions.co.in'. "\r\n";
					} else {
						$header = 'From: '.$header_main.'\r\n';
					}
					$header  .= 'MIME-Version: 1.0' . "\r\n";
                    $header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				
				
					
					$toid = array();
					
					$emailidsrentold = get_post_meta($product_id, '_pravel_stock_rent_notify_email',true);
				    
					foreach ($emailidsrentold as $key => $eid){
					    
						$type = $eid['productType'];
						$text = '';
						$id = $eid['userid'];
						$key = $eid['key'];
						$url = $unsubscribe_page_url . '?productid=' . $product_id . '&useremail=' . $eid['email'] . '&key=' . $eid['key'] . '&producttype=' .$eid['productType'];
        				if($new_stock > $original_stock) {
        				    $text = '<html><body>';
        					$text .= $message . '<br/>';
        					$text .= 'The Product is Available Now <br/>';
        					$text .= '<a href = "'. $url .'">Click Here</a> To Un Subscribe.';
        					$text .= '</body></html>';
        				}
        
        				if($j > 0) {
        				    $text = '<html><body>'; 
        					$text .= $message . '<br/>';
        					$text .='The Product is Available For Following Dates : <br/>' ;
        					$text .= $val_text_date . '<br/>';
        					$text .='<a href = "'. $url .'">Click Here</a> To Un Subscribe.';
        					$text .= '</body></html>';
        				}
						if($type == 'pravel_rental') {
						    
							$sent = mail($eid['email'], $subject, $text, $header);
							
        					if($sent) {
        						_e('Successfull');
        					} else {
        						_e('Error');
        					}
						}
					}
					
					
				}
			} 
			
			$emailidsrent = get_post_meta($product_id, '_pravel_stock_rent_notify_email',true);
	        
			$today = date('m/d/Y');
			foreach ($emailidsrent as $key => $eid){
				$type = $eid['productType'];
				$sDate = date($eid['start_date']);
				$url = $unsubscribe_page_url . '?productid=' . $product_id . '&useremail=' . $eid['email'] . '&key=' . $eid['key'] . '&producttype=' .$eid['productType'];
				if( $today < $sDate ) {
				    if($pravel_product_option == 'pravel_only_rent' && $new_stock > $original_stock) {
        			    $subject = get_option('pravel_notify_email_rent_subject') . ' ' .  $product_title;
        				$message = wp_strip_all_tags(get_option('pravel_notify_email_rent_message'));
        				$header_main = get_option('pravel_notify_email_rent_recepient');
        				$header='';
        				if($header_main == '') {
        					$header = 'From: wordpress@pravelsolutions.co.in'. "\r\n";
        				} else {
        					$header = 'From: '.$header_main.'\r\n';
        				}
        				$header  .= 'MIME-Version: 1.0' . "\r\n";
                        $header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        				
        				$text = '<html><body>'; 
        				$text .= $message . '<br/>';
        				$text .='The Product is Available For Following Dates : <br/>' ;
        				$text .= $eid['start_date'] . ' To ' . $eid['end_date'] . '<br/>';
        				$text .='<a href = "'. $url .'">Click Here</a> To Un Subscribe.';
        				$text .= '</body></html>';
        			
        				$sent = mail($eid['email'], $subject, $text, $header);
        					   
        				if($sent) {
        					_e('Successfull');
        				} else {
        					_e('Error');
        				}
        			}
				}
			}
		    
			
			$buyemailids = get_post_meta($product_id, '_pravel_stock_notify_email',true);
			if($pravel_admin_booking_option == '' && $original_stock == 0){
				
				if ($new_stock > $original_stock) {
					
					$subject = get_option('pravel_notify_email_buy_subject') . ' ' .  $product_title;
					$message = wp_strip_all_tags(get_option('pravel_notify_email_buy_message'));
					$header_main = get_option('pravel_notify_email_buy_recepient');
					$header='';
					if($header_main == '') {
						$header = 'From: wordpress@pravelsolutions.co.in'. "\r\n";
					} else {
						$header = 'From: '.$header_main.'\r\n';
					}
					$header  .= 'MIME-Version: 1.0' . "\r\n";
                    $header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$text = '';
					foreach ($buyemailids as $key => $eid){
					    $url = $unsubscribe_page_url . '?productid=' . $product_id . '&useremail=' . $eid['email'] . '&key=' . $eid['key'] . '&producttype=' .$eid['productType'];
					    
					    $text = '<html><body>'; 
        				$text .= $message . '<br/>';
        				$text .='<a href = "'. $url .'">Click Here</a> To Un Subscribe.';
        				$text .= '</body></html>';
        				
						if ($eid['productType'] == 'pravel_buy') {
						    $sent = mail($eid['email'], $subject, $text, $header);
						
    						if($sent) {
    						    _e('Successfull');
    						} else {
    							_e('Error');
    						}
						}
						if ($eid['productType'] == 'buy') {
							$sent = mail($eid['email'], $subject, $text, $header);
						
    						if($sent) {
    						    _e('Successfull');
    						} else {
    							_e('Error');
    						}
						}
						
					}
					
					if(!empty($toid)) {
						
					}
				   
				   if(!empty($tonormailbuyid)) {
						$to = implode(',', $tonormailbuyid);
						
					}
				}
			}
		endif;
    }
}
return new Pravel_Settings_Tab_Notify();
endif;