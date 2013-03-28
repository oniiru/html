<?php
/*
Plugin Name: Dig Labs - Premium Subscribers
Plugin URI: http://diglabs.com
Description: Creates premium subscriptions features in your blog
Author: Dig Labs
Version: 2.0.0
Author URI: http://diglabs.com
*/
session_start();


// Constants
//
define( 'DLPS_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'DLPS_PLUGIN_URL' , plugins_url( '', __FILE__ ) );

// Includes
//
require_once DLPS_PLUGIN_PATH . '/includes/models/options.php';
require_once DLPS_PLUGIN_PATH . '/includes/models/level.php';
require_once DLPS_PLUGIN_PATH . '/includes/models/user.php';
require_once DLPS_PLUGIN_PATH . '/includes/models/plan.php';
require_once DLPS_PLUGIN_PATH . '/includes/models/package.php';
require_once DLPS_PLUGIN_PATH . '/includes/models/subscribers.php';

require_once DLPS_PLUGIN_PATH . '/includes/handlers.php';
require_once DLPS_PLUGIN_PATH . '/includes/filter.php';
require_once DLPS_PLUGIN_PATH . '/includes/widget.php';
require_once DLPS_PLUGIN_PATH . '/includes/shortcodes.php';
require_once DLPS_PLUGIN_PATH . '/includes/common/alt-api.php';

if( is_admin() ) {
	require_once DLPS_PLUGIN_PATH . '/includes/admin/admin.php';
	require_once DLPS_PLUGIN_PATH . '/includes/dashboard1.php';
	require_once DLPS_PLUGIN_PATH . '/includes/metabox.php';
}


// WP Header Hook (add javascript and css to the page)
add_action('wp_head', 'dl_ps_header', 0);
function dl_ps_header() {	
    if (function_exists('wp_enqueue_script')) {
        	    	        
		wp_register_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
		wp_enqueue_style( 'jquery-style' );
 		wp_register_style( 'dlps_premium',  DLPS_PLUGIN_URL . '/css/premium.css' );
		wp_enqueue_style( 'dlps_premium' );

	   	wp_enqueue_script('jquery');
		wp_register_script( 'google-jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'google-jquery-ui' );	
	 	wp_register_script( 'dlps-plans-js', DLPS_PLUGIN_URL.'/js/plans.js', array( 'jquery' ) );
		wp_enqueue_script( 'dlps-plans-js' );	
				    	
    }
}

// Plugin installation
//
register_activation_hook( __FILE__, 'dl_ps_install' );
function dl_ps_install() {

	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

	if ( !is_plugin_active( 'diglabs-stripe-payments/diglabs-stripe-payments.php' ) ) {
	    
		// Deactivate this plugin.
		//
		deactivate_plugins( __FILE__);

		exit ('Requires the <a href="http://diglabs.com/stripe/">Dig Labs Stripe</a> plugin to be active.!' );
	}
}

// Register widgets
//
add_action( 'widgets_init', 'dl_ps_register_widgets' );
function dl_ps_register_widgets() {
	register_widget( 'Dl_ps_login_widget' );
}

// Delete user hook
//
add_action( 'delete_user', 'dl_ps_delete_user' );
function dl_ps_delete_user( $user_id ) {

	// Delete the user's meta data
	//
	DlPs_User::Delete( $user_id );
}


// Stripe callback
//
add_action( 'plugins_loaded', 'dl_ps_stripe_callback_registration', 0 );
function dl_ps_stripe_callback_registration() {

	if( function_exists( 'stripe_register_payment_begin_callback')) {
		
		stripe_register_payment_begin_callback('dl_ps_stripe_payment_begin');
	}

	if( function_exists( 'stripe_register_payment_end_callback' ) ) {

		stripe_register_payment_end_callback('dl_ps_stripe_payment_end');
	}
}
function dl_ps_stripe_payment_begin( $response ) {

    if( !is_null( $response[ 'dlps_subscriber' ] ) ) {

    	// This is a premium subscriber payment
    	
    	// Ensure we have a valid plan.
    	//
    	$plan_id = $response['plan_id'];
    	$plan = DlPs_Plan::Get( $plan_id );
    	if( is_null( $plan ) ) {
			$response['cancel'] = true;
			$response['error'] = "Invalid plan id (id=$plan_id)";
			return;
    	}
    			
		// Ensure we are charging the correct amount
		//	Note: amount is in cents on this call
		//
		$amount = floatval( $response['amount'] / 100 );
		$cost = floatval( $plan->amount );		
		if($amount != $cost) {
			$response['cancel'] = true;
			$response['error'] = "Expected payment amount is " . $plan->amount . ".";
			return;
		}
		
		// Everything is valid. Let the payment processing proceed.
	}
}
function dl_ps_stripe_payment_end( $response ) {

    if( !is_null( $response[ 'dlps_subscriber' ] ) ) {

    	// This is a premium subscriber payment
 
    	// Get the plan.
    	//
    	$plan_id = $response['plan_id'];
    	$plan = DlPs_Plan::Get( $plan_id );
    	if( is_null( $plan ) ) {

			// The begin payment callback should have caught this.
			//
			echo "PLAN ID NOT FOUND<br /><br />";
			return;
		}
		
		// Ensure the amount paid is correct.
		//	Note: amount is in dollars here.
		//
		$amount = floatval( $response[ 'amount' ] );
		$cost = floatval( $plan->amount );
		if( isset( $response[ 'discount' ] ) ) {

			$cost = floatval( intval( 100 * $cost * (1 - $response['discount'] / 100.0) ) /100.0);
		}
		if($amount != $cost) {

			// The begin payment callback should have caught this.
			//
			echo "AMOUNTS ARE NOT EQUAL<br /><br />Received: $cost, Expected: $amount";
			return;
		}
		
		// We have a successful payment.
		//
		$wp_user = wp_get_current_user();
		$user = new DlPs_User( $wp_user );
	
		// Need to save save this user's customer stripe ID.
		//	The payment notification callback doesn't have
		//	access to the Wordpress user. The payment notification
		//	will reverse lookup the Wordpress user using the
		//	customer stripe ID which is available.
		//
		$user->stripe_id 	= $response[ 'cust_id' ];

	
		// Update the user's info and save.
		//
		$user->Update();
	}
}

add_action( 'stripe_payment_notification', 'dlps_stripe_payment_notification', 1 );
function dlps_stripe_payment_notification( $event ) {
/* An example event that comes out of the Stripe payment plugin

{
   "id":"evt_ipqjktWIORFP4Z",
   "created":1337139352,
   "livemode":false,
   "object":"event",
   "pending_webhooks":2,
   "type":"charge.succeeded",
   "data":{
      "object":{
         "id":"ch_4mFhBRmoy2gLed",
         "amount":1500,
         "created":1337139350,
         "currency":"usd",
         "customer":"cus_b1VltORqckTXiY",
         "description":"{\"dlps_subscriber\":\"1\",\"level_id\":\"1\",\"plan_id\":\"1\",\"email\":\"bob.cravens@diglabs.com\"}",
         "disputed":false,
         "fee":74,
         "livemode":false,
         "object":"charge",
         "paid":true,
         "refunded":false,
         "card":{
            "id":"cc_QCNGDFDpL38Cdc",
            "country":"US",
            "cvc_check":"pass",
            "exp_month":5,
            "exp_year":2012,
            "fingerprint":"4Ug9tlEJoCLnTxEu",
            "last4":"4242",
            "name":"Bob Cravens",
            "object":"card",
            "type":"Visa"
         }
      }
   },
   "customer":{
      "id":"cus_b1VltORqckTXiY",
      "account_balance":0,
      "created":1337139351,
      "description":"{\"dlps_subscriber\":\"1\",\"level_id\":\"1\",\"plan_id\":\"1\",\"email\":\"bob.cravens@diglabs.com\"}",
      "email":"bob.cravens@diglabs.com",
      "livemode":false,
      "object":"customer",
      "active_card":{
         "country":"US",
         "cvc_check":"pass",
         "exp_month":5,
         "exp_year":2012,
         "fingerprint":"4Ug9tlEJoCLnTxEu",
         "last4":"4242",
         "name":"Bob Cravens",
         "object":"card",
         "type":"Visa"
      }
   },
   "diglabs":{
      "email":{
         "Amount":"$15.00",
         "Name":"Bob Cravens",
         "Card Type":"Visa",
         "Card Last 4":"4242",
         "Invoice Id":"ch_4mFhBRmoy2gLed",
         "Email":"bob.cravens@gmail.com"
      },
      "plan_ids":[

      ]
   }
}
*/
	
	$description = null;
	if( !is_null( $event->data->object->description ) ) {
	
		$description = json_decode($event->data->object->description);
		
	} else if( !is_null($event->customer->description)) {
	
		$description = json_decode($event->customer->description);
	}
	if( is_null( $description ) ) {
	
		// Couldn't find the description
		return;
	}
	
	if( is_null( $description->dlps_subscriber ) ) {
		
		// This is not a subscription form.
		return;
	}
	
	$level_id = $description->level_id;
	$level = DlPs_Level::Get( $level_id );
	if( is_null( $level ) ) {
		
		// This is not a valid level.
		return;
	}
	
	$plan_id = $description->plan_id;
	$plan = DlPs_Plan::Get( $plan_id );
	if( is_null( $plan ) ) {
	
		// This is not a valid plan
		return;
	}
	
	$customer = $event->customer;
	if( is_null( $customer ) ) {
		
		// No customer object.
		return;
	}
	$cust_stripe_id = $customer->id;
	$user = DlPs_User::GetByStripeId( $cust_stripe_id );
	if( is_null( $user ) ) {
		
		// This stripe id was not found.
		return;
	}
	$prev_plan_id = $user->plan_id;
	
	$type = $event->type;
	if( $type == 'charge.succeeded' ) {
	
		// Charge succeeded. Extend this users expiration.
		//
				
		// Set the new variables.
		//
		$user->level_id		= $level_id;
		$user->level 		= $level;
		$user->plan_id 		= $plan_id;
		$user->plan 		= $plan;
		$user->stripe_id 	= $customer->id;
		$user->card_type 	= $customer->active_card->type;
		$user->card_last4 	= $customer->active_card->last4;

		// Extend the user's expiration date.
		//
		$user->expiration 	= $plan->ExtendDate( $user->expiration );
	
		// If the subscription is changing....update it.
		//
		if( $plan_id != $prev_plan_id ) {
	
			// Either create a new or update a current subscription.
			//
			if( !DlPs_Subscribers::UpdateSubscription( $user, $plan ) ) {
	
				echo 'Failed to update the subscription.';
				return;
			}
		}
	
		// Update the user's info and save.
		//
		$user->Update();
		
	} else if( $type == 'invoice.payment_failed' ){
	
		// Charge failed. Do not extend.
	}
	/*
	$message = "EVENT:\r\n".json_encode($event)."\r\n\r\n";
	$message .= "EVENT:\r\n".$event."\r\n\r\n";
	$message .= "TYPE:\r\n".$type."\r\n\r\n";
	$message .= "STRIPE_ID:\r\n".$cust_stripe_id."\r\n\r\n";
			
	$to = 'bob.cravens@diglabs.com';
	$subject = 'stripe test';
	mail($to, $subject, $message);*/
}


// Add the ability for the plugin to detect available updates.
//
$api_url = 'http://diglabs.com/api/plugin/';
$plugin_folder = 'diglabs-premium-subscribers';
$plugin_file = 'diglabs-premium-subscribers.php';

$dlps_alt_api = new Dl_Plugin_Alt_Api( $api_url, $plugin_folder, $plugin_file );
add_filter( 'pre_set_site_transient_update_plugins', array( &$dlps_alt_api, 'Check' ) );
add_filter( 'plugins_api', array( &$dlps_alt_api, 'Info' ), 10, 3);


?>