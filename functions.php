<?php 

// Remove WP Version From Styles	
add_filter( 'style_loader_src', 'sdt_remove_ver_css_js', 9999 );
// Remove WP Version From Scripts
add_filter( 'script_loader_src', 'sdt_remove_ver_css_js', 9999 );

// Function to remove version numbers
function sdt_remove_ver_css_js( $src ) 
{
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
        if ( strpos( $src, 'version=' ) )
		$src = remove_query_arg( 'version', $src ); return $src; 
}

add_filter( 'woocommerce_account_menu_items', 'wc_custom_account_tabs', 99, 1 );
function wc_custom_account_tabs( $items ) {
	$items['orders'] = "Quotes";
	unset($items['downloads']);
	return $items;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function ywraq_get_updated_item_quantity( $product_id ) {
    // Check if the quote content exists in the session
    if ( isset($_SESSION['raq_content']) && is_array($_SESSION['raq_content']) ) {
        // Return the updated quantity for the specific product ID, or 0 if not found
        return isset($_SESSION['raq_content'][$product_id]) ? intval($_SESSION['raq_content'][$product_id]) : 0;
    }
    
    // Return 0 if session or product ID is invalid
    return 0;
}
function woo_custom_change_cart_string($translated_text, $text, $domain) {
	$translated_text = str_replace("cart", "basket", $translated_text);
	$translated_text = str_replace("Cart", "Basket", $translated_text);
	$translated_text = str_replace("View Cart", "View Basket", $translated_text);
return $translated_text;
}
add_filter('gettext', 'woo_custom_change_cart_string', 100, 3);
add_filter('ngettext', 'woo_custom_change_cart_string', 100, 3);
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_single_add_to_cart_text' );                // < 2.1
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_single_add_to_cart_text' );  // 2.1 +
function woo_custom_single_add_to_cart_text() {
	return __( 'Add to Basket', 'woocommerce' );
}

//add_filter( 'woocommerce_quantity_input_args', 'ywraq_set_quantity_step_value_specific_products', 10, 2 );
function ywraq_set_quantity_step_value_specific_products( $args, $product ) {
			$qty = $product->get_attribute('pa_boxed-per');
			$updated_quantity = ywraq_get_updated_item_quantity( $product->get_id());
			print_r($updated_quantity);
			if(! empty( $qty ) ){
				$args['input_value'] = $qty;
	            $args['step'] = $qty;  // Set quantity step to your desired value, e.g., 5
			} else {
			$args['input_value'] = 1;
            $args['step'] = 1;  // Set quantity step to your desired value, e.g., 5
			}	
    return $args;
}

add_filter(  'gettext',  'register_text'  );
add_filter(  'ngettext',  'register_text'  );
function register_text( $translated ) {
     $translated = str_ireplace(  'Login',  'Login/Register',  $translated );
     return $translated;
}

function custom_check_user(){
	if (!is_user_logged_in()) {
		wp_clear_auth_cookie();
		wp_set_current_user(6);
		wp_set_auth_cookie(6, true, is_ssl());
	}
	if (get_the_ID()==374 && get_current_user_id()==6) {
		wp_redirect('https://www.bentleybrown.co.uk/quote-overview/');
	}
}
//add_action( 'wp_head', 'custom_check_user' );

function add_custom_login_message(){
	if (!is_user_logged_in()) {
?>
<p>Please <a href="<?php echo get_site_url(); ?>/my-account" data-open="#login-form-popup">log in or register</a> to be able to add items to the basket.</p>
<?php
	}
}
//add_action('woocommerce_archive_description','add_custom_login_message');
//add_action('woocommerce_single_product_summary','add_custom_login_message');

/**
 * Pre-populate Woocommerce checkout fields
 */
add_filter('woocommerce_checkout_get_value', function($input, $key ) {
	global $current_user;
	switch ($key) :
		case 'billing_first_name':
		case 'shipping_first_name':
			return $current_user->first_name;
		break;
		
		case 'billing_last_name':
		case 'shipping_last_name':
			return $current_user->last_name;
		break;
		case 'billing_email':
			return $current_user->user_email;
		break;
		case 'billing_phone':
			return $current_user->phone;
		break;
	endswitch;
}, 10, 2);

add_action( 'wp_footer', function() {
	?>
	<style type="text/css">
		#dgwt_wcas_ajax_search-4 { display: none; }
/* 		input[type=search].dgwt-wcas-search-input { 
			display: none; 
		} */
	</style>
	<?php 
});
add_action( 'wp_head', function() {
	?>
	<script>
		jQuery( document ).ready(function() {
			jQuery('#yith-ywraq-form .product-quantity input.qty').prop('readonly','readonly');
			jQuery('#collection_date, #delivery_date').datepicker({
				minDate : 0,
				dateFormat: jQuery("#collection_date, #delivery_date").data("format") || "dd-mm-yy",
				beforeShow: function() {
                    setTimeout(function() {
                        jQuery("#ui-datepicker-div").wrap('<div class="yith_datepicker"></div>').css({
                            "z-index": 99999999999999,
                            top: jQuery("#collection_date, #delivery_date").offset().top + 45,
                            left: jQuery("#collection_date, #delivery_date").offset().left
                        }),
                        jQuery("#ui-datepicker-div").show()
                    }, 0)
                },
				onSelect: function(dateString, instance) {
					//let date = jQuery("#collection_date").datepicker('getDate');
					let date = instance.input.datepicker('getDate');

					// increment day
					date.setDate(date.getDate() + 1)
					jQuery("#return_date").datepicker('option', 'minDate', date);
					jQuery("#collection_date2").datepicker('option', 'minDate', date);
				}
			});
			/*jQuery('#return_date').datepicker({
				minDate : 1,
				dateFormat: jQuery("#return_date").data("format") || "dd-mm-yy",
				beforeShow: function() {
                    setTimeout(function() {
                        jQuery("#ui-datepicker-div").wrap('<div class="yith_datepicker"></div>').css({
                            "z-index": 99999999999999,
                            top: jQuery("#return_date").offset().top + 45,
                            left: jQuery("#return_date").offset().left
                        }),
                        jQuery("#ui-datepicker-div").show()
                    }, 0)
                },
			});*/

			jQuery(document).on('change', '#delivery_options_delivery-collection', function() {
				if(jQuery('#delivery_address').prop('checked')) {
					jQuery("[name='shipping_contact_name']").val(jQuery("[name='billing_contact_name']").val());
					jQuery("[name='shipping_customer_name_company']").val(jQuery("[name='customer_name_company']").val());
					jQuery("[name='shipping_address_1']").val(jQuery("[name='address_1']").val());
					jQuery("[name='shipping_address_2']").val(jQuery("[name='address_2']").val());
					jQuery("[name='shipping_city']").val(jQuery("[name='city']").val());
					jQuery("[name='shipping_telephone']").val(jQuery("[name='telephone']").val());
					jQuery("[name='shipping_mobile']").val(jQuery("[name='billing_mobile']").val());
					jQuery("[name='shipping_county']").val(jQuery("[name='county']").val());
					jQuery("[name='shipping_postcode']").val(jQuery("[name='postcode']").val());
					jQuery("[name='shipping_country']").val(jQuery("[name='country']").val());
				}
			});
			jQuery(document).on('change', '#delivery_address', function() {
				if(this.checked) {
					jQuery("[name='shipping_contact_name']").val(jQuery("[name='billing_contact_name']").val());
					jQuery("[name='shipping_customer_name_company']").val(jQuery("[name='customer_name_company']").val());
					jQuery("[name='shipping_address_1']").val(jQuery("[name='address_1']").val());
					jQuery("[name='shipping_address_2']").val(jQuery("[name='address_2']").val());
					jQuery("[name='shipping_city']").val(jQuery("[name='city']").val());
					jQuery("[name='shipping_telephone']").val(jQuery("[name='telephone']").val());
					jQuery("[name='shipping_mobile']").val(jQuery("[name='billing_mobile']").val());
					jQuery("[name='shipping_county']").val(jQuery("[name='county']").val());
					jQuery("[name='shipping_postcode']").val(jQuery("[name='postcode']").val());
					jQuery("[name='shipping_country']").val(jQuery("[name='country']").val());
				}
			});				
			jQuery( "#yith-ywraq-default-form" ).on( "submit", function( event ) {
				if(jQuery('#delivery_address').prop('checked')) {
					jQuery("[name='shipping_contact_name']").val(jQuery("[name='billing_contact_name']").val());
					jQuery("[name='shipping_customer_name_company']").val(jQuery("[name='customer_name_company']").val());
					jQuery("[name='shipping_address_1']").val(jQuery("[name='address_1']").val());
					jQuery("[name='shipping_address_2']").val(jQuery("[name='address_2']").val());
					jQuery("[name='shipping_city']").val(jQuery("[name='city']").val());
					jQuery("[name='shipping_telephone']").val(jQuery("[name='telephone']").val());
					jQuery("[name='shipping_mobile']").val(jQuery("[name='billing_mobile']").val());
					jQuery("[name='shipping_county']").val(jQuery("[name='county']").val());
					jQuery("[name='shipping_postcode']").val(jQuery("[name='postcode']").val());
					jQuery("[name='shipping_country']").val(jQuery("[name='country']").val());
				}
			  event.preventDefault();
			});
			jQuery(document).on("click, change", ".product-quantity input", function(e) {
				e.preventDefault();
			});
			jQuery('#yith-ywraq-form').on('submit', function(event) {
			    // Prevent the form from submitting
			    event.preventDefault();

			    // Optionally, add your custom logic here
			    alert('Form submission prevented!');
			});	
			jQuery(document).on('click','.minus.button, .plus.button',function(){
				jQuery(this).attr('disabled',true);
				jQuery(this).css("opacity", "0");
				//setTimeout(function() { 
				//	jQuery(this).attr('disabled', false);
				//}, 500);
			});
			jQuery(document).on('click, change', '.product-quantity input', function (e) {
				jQuery( this ).siblings('.minus.button, .plus.button').attr('disabled',true);				
			});
			
		});
	</script>
	<?php 
});

function my_text_strings( $translated_text, $text, $domain ) {
	switch ( $translated_text ) {
		case 'SKU:':
			$translated_text = __( 'TSS:', 'woocommerce' );
			break;
		case ' SKU:':
			$translated_text = __( ' TSS:', 'yith-woocommerce-request-a-quote' );
			break;
	}

	return $translated_text;
}
add_filter( 'gettext', 'my_text_strings', 20, 3 );

// Set quantity increment based on a product attribute value
add_filter( 'woocommerce_quantity_input_args', 'set_quantity_increment_based_on_attribute', 10, 2 );
function set_quantity_increment_based_on_attribute( $args, $product ) {
    // Check if the product has the desired attribute
    if ( $product->is_type( 'variable' ) || $product->is_type( 'simple' ) ) {
        $attribute_value = $product->get_attribute( 'pa_pack_size' ); // Replace 'pa_pack_size' with your attribute slug

        // Define increments based on the attribute value
        if ( $attribute_value == 'Small Pack' ) {
            $args['min_value'] = 1; // Set minimum quantity
            $args['step'] = 1; // Set increment step
        } elseif ( $attribute_value == 'Medium Pack' ) {
            $args['min_value'] = 2; // Set minimum quantity
            $args['step'] = 2; // Set increment step
        } elseif ( $attribute_value == 'Large Pack' ) {
            $args['min_value'] = 5; // Set minimum quantity
            $args['step'] = 5; // Set increment step
        }
    }
    return $args;
}

/*No paging Product Gallery */
//added Oct 8 2018
add_action('woocommerce_product_query', 'all_products_query' );

function all_products_query( $q ){
    $q->set( 'posts_per_page', -1 );
}
//added Oct 9 2018
add_filter( 'facetwp_result_count', function( $output, $params ) {
    $output = $params['total'];
    return $output;
}, 10, 2 );

// FTP EXPORT START
// ------------------------------------
add_action('ywraq_after_create_order','sp_ftp_xml_export',10, 3);
function sp_ftp_xml_export( $oid, $postdata, $raq ){
	
	// GENERATE ARRAY DATA
	$xmldata = $postdata;
	$order   = wc_get_order( $oid );
	
	// UPDATE ORDER DETAILS		
	if(isset($xmldata['delivery_options']) && $xmldata['delivery_options'] == 'delivery-collection'){
		$xmldata['delivery_options'] = 0;
		$xmldata['delivery_collection_date'] = $xmldata['delivery_date'];
		$xmldata['collect_return_date'] = $xmldata['collection_date2'];
	}
	else {
		$xmldata['delivery_options'] = 1;
		$xmldata['delivery_collection_date'] = $xmldata['collection_date'];
		$xmldata['collect_return_date'] = $xmldata['return_date'];
	}
	unset($xmldata['delivery_date']);
	unset($xmldata['collection_date2']);
	unset($xmldata['collection_date']);
	unset($xmldata['return_date']);
	
	// GET ORDER	
	if ( count( $order->get_items() ) > 0 ) {
		$ctr=1;
		$xmldata['order']['order_id'] = $oid;
		foreach ( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();
			$xmldata['order']['products']["product[del]{$ctr}"] = [
				'product_id'   => $product->get_sku(),
				'product_name' => $item->get_name(),
				'quantity'     => $item->get_quantity()
			];
			$ctr++;
		}
	}
	
	// FTP UPLOAD
// 	$prot = get_field( "sp-ftp-protocol", "option", false );
// 	$host = get_field( "sp-ftp-host", "option", false );
// 	$port = get_field( "sp-ftp-port", "option", false );
// 	$user = get_field( "sp-ftp-username", "option", false );
// 	$pass = get_field( "sp-ftp-password", "option", false );
// 	if( $prot && $host && $port && $user && $pass ){
// 		$ftp_conn = ($prot && $prot == "SFTP") ? ftp_ssl_connect($host) : ftp_connect($host);
// 		if($ftp_conn){
// 			$login = ftp_login($ftp_conn, $user, $pass);
// 			if($login){
// 				$dest = "{$oid}.xml";
// 				$srcf = wp_get_upload_dir()['basedir'] . "/yith_ywraq/{$oid}.xml";
// 				$xml_data = sp_arr_to_xml($xmldata, new SimpleXMLElement('<root/>'))->asXML($srcf);
// 				if(ftp_put($ftp_conn, $dest, $srcf, FTP_ASCII)){
// 					//log success
// 					// delete file
// 				}else{
// 					//log fail
// 				}
// 			}
// 			ftp_close($ftp_conn);
// 		}else{
// 			//log fail
// 		}
// 	}
	
	// CREATE XML FILE DIRECTORY
	$xmldir = wp_get_upload_dir()['basedir'] . "/yith_ywraq/xml_logs";
	if (!file_exists($xmldir)) {
		mkdir($xmldir, 0777, true);
	}
	
	// CREATE XML FILE
	$xml_data = sp_arr_to_xml($xmldata, new SimpleXMLElement('<quote_request/>'))->asXML("{$xmldir}/{$oid}.xml");
}

function sp_arr_to_xml(array $data, SimpleXMLElement $xml){
	foreach ($data as $name => $value) {
		$name = explode("[del]",$name)[0];
		is_array($value)
			? sp_arr_to_xml($value, $xml->addChild($name))
			: $xml->addChild($name, $value);
	}
	return $xml;
}
// ------------------------------------
// FTP EXPORT END

add_action('wp', function() {
        remove_action('yith_ywraq_action', 'update_item_quantity', 10); 
});