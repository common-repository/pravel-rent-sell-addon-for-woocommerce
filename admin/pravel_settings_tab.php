<?php

if ( ! class_exists( 'Pravel_Settings_Tabs' ) ) :
class Pravel_Settings_Tabs {
	//call all action
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array',array($this, 'pravel_add_notification_settings_tab'), 50 );

		add_filter( 'woocommerce_sections_rns_tab' , array($this, 'pravel_add_setting_section' ), 10, 2 );

		add_action( 'woocommerce_settings_tabs_rns_tab', array($this, 'pravel_settings_tab') );
		
		add_action( 'woocommerce_update_options_rns_tab', array($this, 'pravel_update_settings') );
	}

	//Add a new settings tab to the WooCommerce settings tabs array.
    public static function pravel_add_notification_settings_tab( $settings_tabs ) {
        $settings_tabs['rns_tab'] = __( 'Pravel R&S', 'woocommerce-rns-tab' );
        return $settings_tabs;
    }

    public static function pravel_get_sections() {

        $sections = array(
            '' => __( 'Pick Up', 'woocommerce-rns-tab' ),
            'notify_me' => __( 'Notify Me', 'woocommerce-rns-tab' )
        );
        return apply_filters( 'woocommerce_get_sections_rns_tab', $sections );
    }

    public static function pravel_add_setting_section( $settings ) {
        global $current_section;
        $sections = self::pravel_get_sections();
        if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
            return;
        }
        echo '<ul class="subsubsub">';
        $array_keys = array_keys( $sections );
        foreach ( $sections as $id => $label ) {
            echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=rns_tab&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
        }
        echo '</ul><br class="clear" />';
    }

    //Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
    public static function pravel_settings_tab() {
        woocommerce_admin_fields( self::pravel_get_settings() );
    }
    
    //Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
    public static function pravel_update_settings() {
        woocommerce_update_options( self::pravel_get_settings() );
    }

    //Get all the settings for this plugin for @see woocommerce_admin_fields() function.
    public static function pravel_get_settings( ) {
    	global $current_section;
    	$admin_email = get_option('admin_email');
        
        if( $current_section == 'notify_me' ) {
        	$view2 = get_option('pravel_notify_email');
        	$display2 = '';
        	if($view2 == 'no' || $view2 == ''){
        		$display2 = 'disabled';
        	}
        	$settings = array(
	            'section_title' => array(
	                'name'     => __( 'Enable Notification', 'woocommerce-rns-tab' ),
	                'type'     => 'title',
	                'desc'     => '',
	                'id'       => 'wc_settings_tab_demo_section_title'
	            ),
	            array(
	                    'title'    => __( 'Enable Notification Mail', 'woocommerce-rns-tab' ),
	                    'desc'     => __( 'User will be notified via email', 'woocommerce-rns-tab' ),
	                    'id'       => 'pravel_notify_email',
	                    'default'  => 'no',
	                    'type'     => 'checkbox',
	                    'desc_tip' => __( 'Notification email will be sent to customer when product is back in Stock.', 'woocommerce-rns-tab' ),
	                ),
	            'section_end' => array(
	                 'type' => 'sectionend',
	                 'id' => 'wc_settings_tab_demo_section_title'
	            ),
	            array(
	                    'title' => __( 'Buy Product Email Notification', 'woocommerce-rns-tab' ),
	                    'type'  => 'title', 
	                    'desc'  => '',
	                    'id'    => 'pravel_buy_product_email_options',
	                ),
	            array(
	                    'title'       => __( 'Recipient(s) *', 'woocommerce-rns-tab' ),
	                    'desc'        =>  __( 'Enter recipients (comma separated) for this email. Defaults to admin email id.', 'woocommerce-rns-tab' ),
	                    'id'          => 'pravel_notify_email_buy_recepient',
	                    'default'     => $admin_email,
	                    'custom_attributes' => array( 
	                    	'required' => 'required', 
	                    	'disabled' => $display2 
	                    ),
	                    'type'        => 'text',
	                    'desc_tip'    =>  __( 'Enter the Recepient Email Id.', 'woocommerce-rns-tab' ),
	                ),
	            array(
	                    'title'       => __( 'Subject *', 'woocommerce-rns-tab' ),
	                    'desc_tip'    =>  __( 'Enter Subject of Email', 'woocommerce-rns-tab' ),
	                    'id'          => 'pravel_notify_email_buy_subject',
	                    'custom_attributes' => array( 'required' => 'required', 'disabled' => $display ),
	                    'default'     => 'Product is Back in Stock',
	                    'type'        => 'text',
	                ),
	            array(
	                    'title'       => __( 'Message *', 'woocommerce-rns-tab' ),
	                    'id'          => 'pravel_notify_email_buy_message',
	                    'type'        => 'textarea',
	                    'custom_attributes' => array( 'disabled' => $display ),
	                    'desc_tip'    => true,
	                    'description' =>  __( 'Message', 'woocommerce-rns-tab' ),
	                    'placeholder' => '',
	                    'default'     => 'The Product You Wanted to Purchase is Back In Stock.',
	                ),
	            
	            array(
	                 'type' => 'sectionend',
	                 'id' => 'pravel_buy_product_email_options'
	            ),
	            array(
	                    'title' => __( 'Rent Product Email Notification', 'woocommerce-rns-tab' ),
	                    'type'  => 'title',
	                    'desc'  => '',
	                    'id'    => 'pravel_rent_product_email_options',
	                ),
	            array(
	                    'title'       => __( 'Recipient(s) *', 'woocommerce-rns-tab' ),
	                    'desc'        =>  __( 'Enter recipients (comma separated) for this email. Defaults to admin email id.', 'woocommerce-rns-tab' ),
	                    'id'          => 'pravel_notify_email_rent_recepient',
	                    'default'     => $admin_email,
	                    'custom_attributes' => array( 'required' => 'required', 'disabled' => $display ),
	                    'type'        => 'text',
	                    'desc_tip'    =>  __( 'Enter the Recepient Email Id.', 'woocommerce-rns-tab' ),
	                ),
	            array(
	                    'title'       => __( 'Subject *', 'woocommerce-rns-tab' ),
	                    'id'          => 'pravel_notify_email_rent_subject',
	                    'type'        => 'text',
	                    'desc_tip'    => true,
	                    'custom_attributes' => array( 'required' => 'required', 'disabled' => $display ),
	                    'desc'        =>  __( 'Enter Subject of Email', 'woocommerce-rns-tab' ),
	                    'placeholder' => '',
	                    'default'     => 'Product is Back in Stock',
	                ),
	            array(
	                    'title'       => __( 'Message *', 'woocommerce-rns-tab' ),
	                    'id'          => 'pravel_notify_email_rent_message',
	                    'type'        => 'textarea',
	                    'desc_tip'    => true,
	                    'custom_attributes' => array( 'disabled' => $display ),
	                    'description' =>  __( 'Message', 'woocommerce-rns-tab' ),
	                    'placeholder' => '',
	                    'default'     => 'The Product You Wanted to Rent is Back In Stock.',
	                ),
	                
	            array(
	                 'type' => 'sectionend',
	                 'id' => 'pravel_rent_product_email_options'
	            ),
	            array(
	                    'title' => __( 'Unsubscribe Option', 'woocommerce-rns-tab' ),
	                    'type'  => 'title',
	                    'desc'  => '',
	                    'id'    => 'pravel_unsubscribe_options',
	                ),
	            array(
	                    'title'       => __( 'Unsubscribe Page', 'woocommerce-rns-tab' ),
	                    'id'          => 'pravel_unsubscribe_page',
	                    'type'        => 'single_select_page',
	                    'desc_tip'    => true,
	                    'description' =>  __( 'The base page can also be used for unsubscription', 'woocommerce-rns-tab' ),
	                    'custom_attributes' => array( 'required' => 'required' ),
	                    'css'         => 'min-width:300px;',
	                    'class'    => 'wc-enhanced-select-nostd pravel-unsubscribe-page',
	                    'default'     => ''
	                ),
	            array(
	                 'type' => 'sectionend',
	                 'id' => 'pravel_unsubscribe_options'
	            )
	        );
       		return apply_filters( 'woocommerce_notify_me_settings', $settings );
        } else {
        	$view = get_option('pravel_pickup_enable_field');
        	$display = '';
        	if($view == 'no' || $view == ''){
        		$display = 'disabled';
        	}  
        	$settings = array(
	            array(
	                'title' => __( 'Pickup Order Settings', 'woocommerce-rns-tab' ),
	                'type'  => 'title',
	                'desc'  => '',
	                'id'    => 'pravel_pickup_settings',
	            ),
	            array(
	                'title'     => __( 'Enable Pickup', 'woocommerce-rns-tab' ),
	                'desc'      => '',
	                'id'        => 'pravel_pickup_enable_field',
	                'desc'      => __( 'Enable Pickup facility from store' ),
	                'type'      => 'checkbox',
	                'default'   => 'no',
	                'desc_tip'  => false,
	            ),
	            array(
	                 'type' => 'sectionend',
	                 'id' => 'pravel_pickup_settings'
	            ),
	            array(
	                'title' => __( 'Alternative Store', 'woocommerce-rns-tab' ),
	                'type'  => 'title',
	                'desc'  => '',
	                'id'    => 'pravel_alternative_store',
	            ),
	            array(
	                'title'     => __( 'Enable Alternative Store', 'woocommerce-rns-tab' ),
	                'desc'      => __( 'Aternative Store Details for Pickup', 'woocommerce-rns-tab' ),
	                'id'        => 'pravel_alternative_store_field',
	                'type'      => 'checkbox',
	                'default'   => 'no',
	                'desc_tip'  => __( 'Enable if you want customer to pickup their order from other store', 'woocommerce-rns-tab' ),
	            ),
	            array(
	                 'type' => 'sectionend',
	                 'id' => 'pravel_alternative_store'
	            ),
	            array(
	                'title' => __( 'Alternative Store Address', 'woocommerce-rns-tab' ),
	                'type'  => 'title',
	                'desc'  => '',
	                'id'    => 'pravel_alternative_store_address',
	            ),
	            array(
	                'title'     => __( 'Store Name', 'woocommerce-rns-tab' ),
	                'desc'      => 'Optional Store Name',
	                'id'        => 'pravel_store_name',
	                'desc'      => '',
	                'type'      => 'text',
	                'custom_attributes' => array( 'disabled' => $display ),
	                'default'   => '',
	                'desc_tip'  => true,
	            ),
	            array(
	                'title'     => __( 'Address 1', 'woocommerce-rns-tab' ),
	                'desc'      => '',
	                'id'        => 'pravel_store_address_1',
	                'desc'      => '',
	                'type'      => 'text',
	                'custom_attributes' => array( 'disabled' => $display ),
	                'default'   => '',
	                'desc_tip'  => 'Street Address',
	            ),
	            array(
	                'title'     => __( 'Address 2', 'woocommerce-rns-tab' ),
	                'desc'      => '',
	                'id'        => 'pravel_store_address_2',
	                'desc'      => '',
	                'type'      => 'text',
	                'custom_attributes' => array( 'disabled' => $display ),
	                'default'   => '',
	                'desc_tip'  => 'Optional Addresss Line',
	            ),
	            array(
	                'title'     => __( 'City', 'woocommerce-rns-tab' ),
	                'desc'      => '',
	                'id'        => 'pravel_store_city',
	                'desc'      => '',
	                'type'      => 'text',
	                'custom_attributes' => array( 'disabled' => $display ),
	                'default'   => '',
	                'desc_tip'  => 'The City of your Alternative Store',
	            ),
	            array(
	                'title'     => __( 'Country/State', 'woocommerce-rns-tab' ),
	                'desc'      => '',
	                'id'        => 'pravel_store_country_state',
	                'desc'      => '',
	                'type'      => 'single_select_country',
	                'custom_attributes' => array( 'disabled' => $display ),
	                'default'   => '',
	                'desc_tip'  => 'The Country and State of Alternative Store',
	            ),
	            array(
	                'title'     => __( 'Postcode/ZIP', 'woocommerce-rns-tab' ),
	                'desc'      => '',
	                'id'        => 'pravel_store_zip',
	                'desc'      => '',
	                'type'      => 'text',
	                'custom_attributes' => array( 'disabled' => $display ),
	                'default'   => '',
	                'desc_tip'  => 'The Postal Code, If any',
	            ),
	            array(
	                 'type' => 'sectionend',
	                 'id' => 'pravel_alternative_store_address'
	            )
	        );
	        return apply_filters( 'woocommerce_rns_tab_settings', $settings );
        }
    }
    
}
return new Pravel_Settings_Tabs();
endif;