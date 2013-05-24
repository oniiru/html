<?php
include_once('../../../../wp-load.php');
include_once('../wp_aff_includes.php');
include_once('../wp_aff_debug_handler.php');

$allow_remote_post = get_option('wp_aff_enable_remote_post');
if(!$allow_remote_post)
{
	echo "Remote POST is disabled";
	wp_aff_api_debug('Remote POST is disabled in the settings.',false);
	exit;
}

wp_aff_api_debug('Start processing remote commission tracking request...',true);

if($_REQUEST["data"])
{
	$data = $_REQUEST["data"];
	list($secret,$ap_id,$sale_amt,$txn_id,$item_id,$buyer_email) = explode('|',$data);
	wp_aff_api_debug('GET joined data: '.$secret."|".$ap_id."|".$sale_amt."|".$txn_id."|".$item_id."|".$buyer_email,true);
}
else if(isset($_REQUEST['secret']))
{
	$secret = $_REQUEST['secret'];
	$ap_id = $_REQUEST['ap_id'];
	$sale_amt = $_REQUEST['sale_amt'];
	$txn_id = $_REQUEST['txn_id'];
	$item_id = $_REQUEST['item_id'];
	$buyer_email = $_REQUEST['buyer_email'];
	$buyer_name = $_REQUEST['buyer_name'];
	$commission_amt = $_REQUEST['commission_amt'];
	wp_aff_api_debug('POST data: '.$secret."|".$ap_id."|".$sale_amt."|".$txn_id."|".$item_id."|".$buyer_email."|".$buyer_name."|".$commission_amt,true);	
}
else
{
	wp_aff_api_debug('Request does not have any GET or POST data... cannot process request',false);	
	exit;
}


wp_aff_api_debug('Validating Request Data',true);
$true_secret = get_option('wp_aff_secret_word_for_post');
$valid = true;
if(empty($secret))
{
	wp_aff_api_debug('Secret word is missing... cannot process request',false);
	$valid = false;
	exit;
}
else if($secret != $true_secret)
{
	wp_aff_api_debug('Secret word do not match... cannot process request',false);
	$valid = false;
	exit;	
}
if(empty($ap_id))
{
	wp_aff_api_debug('Referrer ID is missing... cannot process request',false);
	$valid = false;
	exit;
}
if(empty($sale_amt))
{
	wp_aff_api_debug('Sale amount is missing... cannot process request',false);
	$valid = false;
	exit;
}

if($valid)
{
	if(isset($_REQUEST['commission_amt'])){//add direct commission without calculation
		wp_aff_api_debug('Adding commission amount directly without any calculation. Commission amount: '.$_REQUEST['commission_amt'],true);
		$fields = array();
		$fields['refid'] = $ap_id;
		$fields['payment'] = $_REQUEST['commission_amt'];
		$fields['sale_amount'] = $sale_amt;
		$fields['txn_id'] = $txn_id;
		$fields['item_id'] = $item_id;
		$fields['buyer_email'] = $buyer_email;
		$fields['buyer_name'] = $buyer_name;
		wp_aff_add_unique_commission_amt_directly($fields);
	}
	else{
		wp_aff_award_commission_unique($ap_id,$sale_amt,$txn_id,$item_id,$buyer_email);
		wp_aff_api_debug('Commission awarded!',true);
	}
}
?>