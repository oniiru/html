<?php
/*
Template Name: Payment Callback
*/
if( $_SERVER['REQUEST_METHOD'] != 'POST' )
{
	// This is not a post…ignore the request
	header("HTTP/1.0 404 Not Found");
	exit;
}

require_once STRIPE_PAYMENTS_PLUGIN_DIR.'/stripe-php-1.6.1/lib/Stripe.php';
require_once dirname(__FILE__).'/int.processor.php';
require_once dirname(__FILE__).'/class.event.processor.php';
require_once dirname(__FILE__).'/class.legacy.processor.php';

// The processor. TBD by the post data.
$processor = null;

$json = $_POST['json'];
if(is_null($json)) {
	$body = @file_get_contents('php://input');
	$json = json_decode($body);
	
	$processor = new EventProcessor();
} else {
	$processor = new LegacyProcessor();
}
$processor->process($json);
 
?>