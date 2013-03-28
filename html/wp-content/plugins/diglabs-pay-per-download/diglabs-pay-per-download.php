<?php
/*
Plugin Name: Dig Labs - Pay Per Download
Plugin URI: http://diglabs.com/stripe/pay-per-download/
Description: This plugin integrates an easy to use 'pay per download' system into Wordpress.
Author: Dig Labs
Version: 1.2.3
Author URI: http://diglabs.com/
*/

session_start();

// Define variables
define( 'DL_PAY_PER_DOWNLOAD', '1.0.0' );

if ( ! defined( 'DL_PAY_PER_DOWNLOAD_PLUGIN_BASENAME' ) )
	define( 'DL_PAY_PER_DOWNLOAD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'DL_PAY_PER_DOWNLOAD_PLUGIN_NAME' ) )
	define( 'DL_PAY_PER_DOWNLOAD_PLUGIN_NAME', trim( dirname( DL_PAY_PER_DOWNLOAD_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'DL_PAY_PER_DOWNLOAD_PLUGIN_DIR' ) )
	define( 'DL_PAY_PER_DOWNLOAD_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . DL_PAY_PER_DOWNLOAD_PLUGIN_NAME );

if ( ! defined( 'DL_PAY_PER_DOWNLOAD_PLUGIN_URL' ) )
	define( 'DL_PAY_PER_DOWNLOAD_PLUGIN_URL', WP_PLUGIN_URL . '/' . DL_PAY_PER_DOWNLOAD_PLUGIN_NAME );

// Bootstrap this plugin
require_once DL_PAY_PER_DOWNLOAD_PLUGIN_DIR . '/includes/shortcodes.php';
require_once DL_PAY_PER_DOWNLOAD_PLUGIN_DIR . '/includes/class-payperdownload.php';
require_once DL_PAY_PER_DOWNLOAD_PLUGIN_DIR . '/includes/common/alt-api.php';

if( is_admin() ) {
	require_once DL_PAY_PER_DOWNLOAD_PLUGIN_DIR . '/includes/admin/admin.php';
}

// Register hook to initialize the plugin
//
register_activation_hook( __FILE__, 'dlppd_init_plugin' );
function dlppd_init_plugin() {

	// This plugin requires the stripe plugin
	//
	if( !is_plugin_active("diglabs-stripe-payments/diglabs-stripe-payments.php") ) {

		// Deactivate this plugin
		//
		deactivate_plugins( basename( __FILE__ ) );

		$message = "<p>This plugin requires the <strong>Dig Labs - Stripe Payments</strong> plugin to be active.</p>";
		die( $message );
	}
	// create the table if necessary
	//
	$ppd = new PayPerDownload();
	$ppd->ensure_db_tables_exists();	
}

// Hook into the template filter to inject our custom
//	templates for the payment and webhook URLs that
//	are configured on the admin page.
add_filter('template_include', 'dlppd_custom_templates');
function dlppd_custom_templates() {
	global $template;

	$proto = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) ? 'https://' : 'http://';
	$the_url = $proto . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
	$the_url_lc = untrailingslashit( strtolower($the_url) );
	$url_parts = parse_url( $the_url_lc );
	$this_route = $url_parts[ "path" ];
	$length_this_route = strlen( $this_route );
	
	$ppd = new PayPerDownload();
	$download_root_url = untrailingslashit( strtolower( $ppd->get_product_download_url( '' ) ) );
	$download_root_url_parts = parse_url( $download_root_url );
	$download_route_root = $download_root_url_parts[ "path" ];
	$length_root = strlen( $download_route_root );
		
	if($length_this_route >= $length_root) {

		$substr = substr( $this_route, 0, $length_root );
		if($substr ===  $download_route_root ) {

			// parse out the id
			$payment_id = substr( $this_route, $length_root + 1 );
			
			// fetch the file path			
			$ppd = new PayPerDownload();
			$path = $ppd->get_product_download_path( $payment_id );
			
			if( !is_null( $path ) && file_exists( $path ) ) {

				// found a valid download request.
				// add file path to session and redirect to download page.
				$_SESSION[ 'dlppd_product_path' ] = $path;
				$url = DL_PAY_PER_DOWNLOAD_PLUGIN_URL . "/download.php";
				header( "Location: $url" );
				return;
			} else {

				header( 'HTTP/1.0 404 Not Found' );
			}
		}
	}
	
	return $template;
}

add_action( 'plugins_loaded', 'dlppd_callback_registration' );
function dlppd_callback_registration() {

	if( function_exists(stripe_register_payment_begin_callback)) {
		stripe_register_payment_begin_callback('dlppd_stripe_payment_begin_callback');
		function dlppd_stripe_payment_begin_callback($response) {
		    if(!is_null($response['dlppd_product'])){
		    	// This is a pay per download file
		    	
		    	// Ensure we have a valid product
		    	$prod_id = $response['dlppd_product'];
		    	
		    	// Fetch info from the database.
		    	$ppd = new PayPerDownload();
				$product = $ppd->get_product($prod_id);
				if(is_null($product)) {
					$response['cancel'] = true;
					$response['error'] = "Invalid product id (id=$prod_id)";
					return;
				}
				
				// Ensure we are charging the correct amount
				//	Note: amount is in cents on this call
				$amount = floatval($response['amount']/100);
				$cost = floatval($product['cost']);		
				if( isset( $response[ 'discount' ] ) ) {

					$cost = floatval( intval( 100 * $cost * (1 - $response['discount'] / 100.0) ) /100.0);
				}
				if($amount != $cost) {
					$response['cancel'] = true;
					$response['error'] = "Expected payment amount is " . $product['cost'] . ".";
					return;
				}
				
				// Everything is valid. Let the payment processing proceed.
			}
		}
	} else {
		echo "<p>Stripe Plugin callback registrations function not found!</p>";
	}

	if( function_exists(stripe_register_payment_end_callback)){
		stripe_register_payment_end_callback('dlppd_stripe_payment_end_callback');
		function dlppd_stripe_payment_end_callback($response) {

		    // alter the body of the email
		    if(!is_null($response['dlppd_product'])){
		    	// This is a pay per download file
		 
		    	// Get the product id
		    	$prod_id = $response['dlppd_product'];
		    	
		    	// Ensure we have a valid product.
		    	$ppd = new PayPerDownload();
				$product = $ppd->get_product($prod_id);
				if(is_null($product)) {
					// The begin payment callback should have caught this.
					echo "PRODUCT ID NOT FOUND<br /><br />";
					return;
				}
				
				// Ensure the amount paid is correct.
				//	Note: amount is in dollars here.
				$amount = floatval($response['amount']);
				$cost = floatval($product['cost']);
				if($amount != $cost) {
					// The begin payment callback should have caught this.
					echo "AMOUNTS ARE NOT EQUAL<br /><br />";
					return;
				}
				
				$payment_id = $response['id'];
				$file = $product['file'];
				$email = $response['email'];
				if($ppd->add_payment($payment_id, 5, $file, $prod_id, $email)) {
					$url = $ppd->get_product_download_url($payment_id);
					
					// Email the link to download the file
					$to = $email;
					$subject = 'File Download';
					$title = "File Download";
					$prod_name = $product['name'];
					$body = "<p>Use the following link to download your file. This link can be used only 5 times.</p><p><a href='$url'>$prod_name</a></p>";
				    
				    // Note: This class is part of the WordPress plugin.
					$email = new StripeEmailHelper();
					$email->sendReceipt($to, $subject, $title, $body, null);
				}
			}
		} 
	} else {
		echo "<p>Stripe Plugin callback registrations function not found!</p>";
	}
}


// Add the ability for the plugin to detect available updates.
//
$api_url = 'http://diglabs.com/api/plugin/';
$plugin_folder = 'diglabs-pay-per-download';
$plugin_file = 'diglabs-pay-per-download.php';

$dlppd_alt_api = new Dl_Plugin_Alt_Api( $api_url, $plugin_folder, $plugin_file );
add_filter( 'pre_set_site_transient_update_plugins', array( &$dlppd_alt_api, 'Check' ) );
add_filter( 'plugins_api', array( &$dlppd_alt_api, 'Info' ), 10, 3);

?>