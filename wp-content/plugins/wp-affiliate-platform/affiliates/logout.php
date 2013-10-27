<?php 
include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}

$aff_id = $_SESSION['user_id'];
unset($_SESSION['user_id']);
/* Delete the cookies*******************/
setcookie("user_id", '', time()-60*60*24*60, "/",COOKIE_DOMAIN);

do_action('wp_aff_logout',$aff_id);

//Logout eMember account if using auto affiliate log-in option
if(function_exists('wp_eMember_install'))
{
	global $emember_config;
	$emember_config = Emember_Config::getInstance();
	$eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
    if($eMember_auto_affiliate_account_login){	
    	wp_emem_logout();
    }
}
		
header("Location: login.php"); 
?>