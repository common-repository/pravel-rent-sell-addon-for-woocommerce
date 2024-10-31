<?php
get_header();
$email = sanitize_text_field($_GET['useremail']);
$userkey = sanitize_text_field($_GET['key']);
$product = sanitize_text_field($_GET['productid']);
$type = sanitize_text_field($_GET['producttype']);

$emailsRent = get_post_meta($product, '_pravel_stock_rent_notify_email',true);

$emailsbuy = get_post_meta($product, '_pravel_stock_notify_email',true);

if($type == 'pravel_rental') {
    foreach($emailsRent as $k => $rent) {
   
        if($rent['email'] = $email) {
            unset($emailsRent[$k]);
        }
        update_post_meta($product, '_pravel_stock_rent_notify_email',$emailsRent);
    }
} else {
    foreach($emailsbuy as $k => $buy) {
        if($buy['email'] = $email) {
            unset($emailsbuy[$k]);
        }
        update_post_meta($product, '_pravel_stock_notify_email',$emailsbuy);
    }
}          

            

?>
<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		    <h1>We Will Miss You....</h1>
		    <p>You Have Successfully Unsubscribed yourself.</p>
		</main>
</div>
		    
<?php
get_footer();

    
   
