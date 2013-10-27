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

wp_aff_api_debug('Start processing remote click tracking request...',true);

if(isset($_REQUEST['secret']))
{  	
	$secret = $_REQUEST['secret'];
	$ap_id = $_REQUEST['ap_id'];
	$clientbrowser = $_REQUEST['clientbrowser'];
	$clientip = $_REQUEST['clientip'];
	$clienturl = $_REQUEST['clienturl'];
	wp_aff_api_debug('POST data: '.$secret."|".$ap_id."|".$clientbrowser."|".$clientip."|".$clienturl,true);		
}
else
{
	wp_aff_api_debug('Request does not have secret key present.. cannot process request',false);	
	exit;
}


wp_aff_api_debug('Validating Request Data',true);
$true_secret = get_option('wp_aff_secret_word_for_post');
$valid = true;
if(empty($secret) || empty($ap_id) || empty($clientbrowser) || empty($clientip))
{
	wp_aff_api_debug('One of the mandatory parameter is missing... cannot process request',false);
	$valid = false;
	exit;
}
else if($secret != $true_secret)
{
	wp_aff_api_debug('Secret word do not match... cannot process request',false);
	$valid = false;
	exit;	
}

if($valid)
{
	wp_aff_record_remote_click($ap_id, $clientbrowser, $clientip, $clienturl);
	wp_aff_api_debug('Remote click recorded',true);	
}
?>