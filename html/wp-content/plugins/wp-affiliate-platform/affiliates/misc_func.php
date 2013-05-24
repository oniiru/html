<?php
include_once('../../../../wp-load.php');
global $wpdb;
define('WP_AFF_CLICKTHROUGH_TABLE', $wpdb->prefix . "affiliates_clickthroughs_tbl");
define('WP_AFF_AFFILIATES_TABLE', $wpdb->prefix . "affiliates_tbl");
define('WP_AFF_SALES_TABLE', $wpdb->prefix . "affiliates_sales_tbl");
define('WP_AFF_PAYOUTS_TABLE', $wpdb->prefix . "affiliates_payouts_tbl");
define('WP_AFF_BANNERS_TABLE', $wpdb->prefix . "affiliates_banners_tbl");

global $wp_aff_platform_config;

function aff_check_security()
{
	if(!isset($_SESSION)){@session_start();}
	//check for cookies
	if(isset($_COOKIE['user_id'])){
	      $_SESSION['user_id'] = $_COOKIE['user_id'];
	}	
	if (!isset($_SESSION['user_id']))
	{
	   return false;	   
	}
	else
	{
		return true;
	}
}
function page_protect1() {
	if(!isset($_SESSION)){@session_start();}
	//check for cookies
	if(isset($_COOKIE['user_id'])){
	      $_SESSION['user_id'] = $_COOKIE['user_id'];
	   }
	
	if (!isset($_SESSION['user_id']))
	{
	    header("Location: login.php");
	}
}
function aff_redirect($url, $time = 0)
{
  echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"$time;URL=$url\">";
  echo "If you are not redirected within a few seconds then please click <a class=leftLink href=$url>".here.'</a>';
}

//$language = "eng.php";
$aff_language = get_option('wp_aff_language');
if (!empty($aff_language))
	$language_file = "lang/".$aff_language;
else
	$language_file = "lang/eng.php";
include_once($language_file);

$clientdate = (date ("Y-m-d"));
$clienttime	= (date ("H:i:s"));
$clientbrowser = getenv("HTTP_USER_AGENT");
$clientip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
$clienturl = getenv("HTTP_REFERER");
?>