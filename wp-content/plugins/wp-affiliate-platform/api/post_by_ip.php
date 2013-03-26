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

/* Example HTML code for pixel tracking type technique
//<img src="http://www.your-domain.com/wp-content/plugins/wp-affiliate-platform/api/post_by_ip.php?secret=XXXX&&sale_amt=[%total%]&txn_id=[%txn_id%]&buyer_email=[%payer_email%]" width="1" height="1"/>
//Replace "XXXX" with the actual secret code from the settings menu of this plugin
*/

wp_aff_api_debug('Start Processing commission by ip address ...',true);

if($_GET["data"])
{
	$data = $_GET["data"];
	list($secret,$ip_address,$sale_amt,$txn_id,$item_id,$buyer_email) = explode('|',$data);
	//Try to read the IP address if it is empty
	if(empty($ip_address)){
		$ip_address = $_SERVER['REMOTE_ADDR'];
	}	
	wp_aff_api_debug('GET joined data: '.$secret."|".$ip_address."|".$sale_amt."|".$txn_id."|".$item_id."|".$buyer_email,true);
}
else if(isset($_REQUEST['secret']))
{
	$secret = $_REQUEST['secret'];
	$ip_address = $_REQUEST['ip_address'];
	$sale_amt = $_REQUEST['sale_amt'];
	$txn_id = $_REQUEST['txn_id'];
	$item_id = $_REQUEST['item_id'];
	$buyer_email = $_REQUEST['buyer_email'];
	//Try to read the IP address if it is empty
	if(empty($ip_address)){
		$ip_address = $_SERVER['REMOTE_ADDR'];
	}
	wp_aff_api_debug('POST data: '.$secret."|".$ip_address."|".$sale_amt."|".$txn_id."|".$item_id."|".$buyer_email,true);	
}
else
{
	wp_aff_api_debug('Request does not have any GET or POST data.. cannot process request',false);	
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
if(empty($ip_address))
{
	wp_aff_api_debug('IP Address is missing... cannot process request',true);
	$valid = false;
	exit;
}
if(empty($sale_amt))
{
	wp_aff_api_debug('Sale amount is missing... cannot process request',true);
	$valid = false;
	exit;
}

if($valid)
{
	$ap_id = wp_aff_get_referrer_id_from_ip_address($ip_address);
	if(!empty($ap_id)){
		wp_aff_api_debug('Found a referrer ID for given client IP address: '.$ip_address,true);
		if(wp_aff_true_sale($ip_address))
		{
			if(!wp_aff_check_commission_awarded_for_txn_id($txn_id)){
				wp_aff_award_commission($ap_id,$sale_amt,$txn_id,$item_id,$buyer_email,$ip_address);
				wp_aff_api_debug('Commission awarded to: '.$ap_id.' Client IP address: '.$ip_address,true);
			}
			else{
				wp_aff_api_debug('Commission for this transaction ID has already been awarded.',true);
			}
		}	
	}
	else
	{
		wp_aff_api_debug('No commission awarded. Could not find a referrer for this client\'s IP Address: '.$ip_address,true);
	}	
}
?>