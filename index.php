<?php
/**
 * Plugin Name: Pravel Rent & Sell Addon for WooCommerce	
 * Plugin URI: https://pravelsolutions.co.in/Pravel-Rent-and-Sell/
 * Description: Pravel - This is a woocommerce addon that user can buy or rent product option and admin can manage sell, rent or both product option. 
 * Version: 1.0.1
 * Author: Pravel Solutions
 * Author URI: http://pravelsolutions.com
 * License: https://pravelsolutions.co.in/Pravel-Rent-and-Sell/
*/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	include(ABSPATH . "wp-includes/pluggable.php"); 

	define( 'PRAVEL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'PRAVEL_PLUGIN_VERSION', '1.0.0' );
	define( 'PRAVEL_PLUGIN_URL', plugin_dir_url(__FILE__) );
	define( 'PRAVEL_PLUGIN_BASENAME', plugin_basename(__FILE__) );
	
	//Include Files
	include_once( PRAVEL_PLUGIN_DIR . 'admin/core_function.php' );	
	include_once( PRAVEL_PLUGIN_DIR . 'admin/notification_settings.php');
	include_once( PRAVEL_PLUGIN_DIR . 'admin/pravel_settings_tab.php');
	include_once( PRAVEL_PLUGIN_DIR . 'templates/booking_template.php' );
	
		
	include_once( PRAVEL_PLUGIN_DIR . 'includes/add_to_cart_function.php' );
	include_once( PRAVEL_PLUGIN_DIR . 'includes/add_to_checkout_functions.php' );
	
	
	//admin JS CSS Include
	function pravel_load_scripts_admin() {
		//Include JS
		wp_enqueue_script( 'pravel_repeater', PRAVEL_PLUGIN_URL . 'assets/js/pravel_repeater.js' , array(),  PRAVEL_PLUGIN_VERSION, true);	

		wp_enqueue_script("jquery-ui-datepicker");
		
		wp_enqueue_script( 'pravel_jquery', PRAVEL_PLUGIN_URL . 'assets/js/main.js' , array(),  PRAVEL_PLUGIN_VERSION, true);
		
		wp_enqueue_script( 'pravel_jquery_datatable', PRAVEL_PLUGIN_URL . 'assets/js/jquery.dataTables.js' , array(),  PRAVEL_PLUGIN_VERSION, true);
		
		//Include CSS
		wp_enqueue_style( 'pravel_style', PRAVEL_PLUGIN_URL . 'assets/css/main_style.css' , array(), PRAVEL_PLUGIN_VERSION);
		
		wp_enqueue_style( 'pravel_style_ui', PRAVEL_PLUGIN_URL . 'assets/css/pravel_jquery-ui.css' , array(), PRAVEL_PLUGIN_VERSION);
		
		wp_enqueue_style( 'pravel_style_datatable', PRAVEL_PLUGIN_URL . 'assets/css/jquery.dataTables.css' , array(), PRAVEL_PLUGIN_VERSION);
	}
	add_action('admin_enqueue_scripts', 'pravel_load_scripts_admin');
	
	
	function pravel_load_scripts_front($hook) {
		
		//Include CSS
		
		wp_enqueue_style( 'pravel_style_ui', PRAVEL_PLUGIN_URL . 'assets/css/pravel_jquery-ui.css' , array(), PRAVEL_PLUGIN_VERSION);
		
		wp_enqueue_style( 'pravel_stylesheet', PRAVEL_PLUGIN_URL . 'assets/css/style_front.css', array(), PRAVEL_PLUGIN_VERSION);
		
		
		//Include JS
		
		wp_enqueue_script("jquery-ui-datepicker");
		
		wp_enqueue_script( 'pravel_jquery_front', PRAVEL_PLUGIN_URL . 'assets/js/jquery_front.js' , array(),  PRAVEL_PLUGIN_VERSION, true);
		
		wp_localize_script('pravel_jquery_front', 'ajax_custom', array('ajaxurl' => admin_url('admin-ajax.php')));
	}
	add_action('wp_enqueue_scripts', 'pravel_load_scripts_front');
	
	function pravel_my_acf_admin_notice() {	
		if(isset($_SESSION['pravel_admin_notice']))
		{
			echo $_SESSION['pravel_admin_notice'];		
			unset ($_SESSION['pravel_admin_notice']);
		}	
	}
	add_action( 'admin_notices', 'pravel_my_acf_admin_notice' );	
}
else
{
    function pravel_check_woo_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'Please Install WooCommerce First before activating this Plugin. You can download WooCommerce from <a href="http://wordpress.org/plugins/woocommerce/">here</a>.', 'pravel-addon' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pravel_check_woo_admin_notice' );
}
