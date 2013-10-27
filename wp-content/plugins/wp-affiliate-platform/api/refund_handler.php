<?php
include_once('../../../../wp-load.php');
include_once('../wp_aff_includes.php');
include_once('../wp_aff_debug_handler.php');

$debug_enabled = true;
$debug_log_file_name = 'aff_commission_post_debug.log';

$allow_remote_post = get_option('wp_aff_enable_remote_post');
if(!$allow_remote_post)
{
	echo "Remote POST is disabled";
	wp_aff_api_debug('Remote POST is disabled in the settings.',false);
	exit;
}

wp_aff_api_debug('Start Processing remote refund handling request...',true);

if($_REQUEST["data"])
{
	$data = $_REQUEST["data"];
	list($secret,$ap_id,$sale_amt,$txn_id,$item_id,$buyer_email) = explode('|',$data);
	wp_aff_api_debug('GET joined data: '.$secret."|".$ap_id."|".$sale_amt."|".$txn_id."|".$item_id."|".$buyer_email,true);
}
else if($_REQUEST["secret"])
{
	$secret = $_REQUEST["secret"];
	$parent_txn_id = $_REQUEST["parent_txn_id"];
	wp_aff_api_debug('Request individual data: '.$secret."|".$parent_txn_id,true);
}
else
{
	wp_aff_api_debug('Request does not have any GET or POST data.. cannot process request',true);	
	exit;
}


wp_aff_api_debug('Validating Request Data',true);
$true_secret = get_option('wp_aff_secret_word_for_post');
$valid = true;
if(empty($secret))
{
	wp_aff_api_debug('Secret word is missing... cannot process request',true);
	$valid = false;
	exit;
}
else if($secret != $true_secret)
{
	wp_aff_api_debug('Secret word do not match... cannot process request',true);
	$valid = false;
	exit;	
}
if($valid)
{
	wp_aff_handle_refund($parent_txn_id);		
	wp_aff_api_debug('Commission reversed',true);
}
?>