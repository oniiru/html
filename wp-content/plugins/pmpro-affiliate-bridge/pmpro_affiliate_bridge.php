<?php
/*
Plugin Name: PMPRO Affiliate bridge
Depends: Paid Memberships Pro, WP Affiliate Platform
Plugin URI: 
Description: Provides a simple interconnect between two advanced plugins: Paid Memberships Pro amd WP Affiliate Platform. As a result, all the transactions made through PMPRO are captured in affiliate program if necessary.
Author: Anton Matiyenko
Author URI: https://www.odesk.com/users/~01536b7882ffb1daea
Version: 1.0
Author URI: 
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( ABSPATH . DIRECTORY_SEPARATOR . 'wp-includes' . DIRECTORY_SEPARATOR . 'pluggable.php');

/**
 * Verifies dependencies of plugin
 * @return boolean
 */
function pmproa_dependencies_check(){
	//List of all plugins required for PMPROAB
	$requiredPlugins = array(
		'Paid Memberships Pro' => 'paid-memberships-pro/paid-memberships-pro.php',
		'WP Affiliate Platform' => 'wp-affiliate-platform/wp_affiliate_platform.php',
	);
	//Defalt value for dependencies check result
	$dependenciesOK = true;
	//Notification class init
	$PMPRONotice = new PMPROA_notice();
	//Seek through requirements list
	foreach($requiredPlugins as $pluginName => $pluginPath){
		//Plugin not found/inactive
		if(!is_plugin_active($pluginPath)) {
			//Add associated dependency error message
			$PMPRONotice->error_broken_dependency($pluginName);
			$dependenciesOK = false;
		}
	}
	//Set up all possible error messages
	add_action('admin_notices', array($PMPRONotice, 'showMessages'));
	return $dependenciesOK;
}

/**
 * Initializes required paths as constants
 */
function pmproa_paths_init(){
	define('PMPROA_PATH', dirname(__FILE__));
}

/**
 * Calculates affiliate payment an stores the transaction
 * for WP Affiliate platform
 * @param object $morder
 */
function pmproa_process_affiliate_transaction($morder) {
	//Get user associated with current order
	$user = get_user_by('id', $morder->user_id);
	//Get refferer ID from database
	$referrer = pmproa_get_referrer($morder->user_id);
	if($referrer) {
		//If referrer ID is present, generate a sale
		do_action('wp_affiliate_process_cart_commission', array("referrer" => $referrer, "sale_amt" => $morder->subtotal, "txn_id" => $morder->code, "buyer_email" => $user->user_email));
	}
}

/**
 * Initializes plugin
 */
function pmproa_init(){
	pmproa_paths_init();
	include_once( PMPROA_PATH . DIRECTORY_SEPARATOR . 'pmproa_notice.class.php' );
	if(is_admin() && is_user_logged_in()) {
		if(isset($_GET['pmproa_ignore'])) {
			PMPROA_notice::ignore_error($_GET['pmproa_ignore']);
		}
	}
	//1. Check dependencies
	if(pmproa_dependencies_check()) {
		//2. Hook the processing to both:
		//New PMPRO order
		add_action('pmpro_added_order', 'pmproa_process_affiliate_transaction');
		//Order update (probably might be executed on webhook??)
		add_action('pmpro_updated_order', 'pmproa_process_affiliate_transaction');		
	}
}
add_action( 'init', 'pmproa_init' );

/**
 * Handles affiliate ID.
 * When registered take $_COOKIE data and save in database.
 * @param integer $user_id new arriving user ID
 */
function pmproa_handle_registration($user_id){
	if(isset($_COOKIE['ap_id'])) {
		add_user_meta($user_id, 'ap_id', $_COOKIE['ap_id']);
	}
}
add_action('user_register', 'pmproa_handle_registration');

/**
 * Saves additional user meta field for affiliate ID
 * @param integer $user_id
 * @return mixed
 */
function pmproa_get_referrer($user_id){
	$meta = get_user_meta($user_id, 'ap_id');
	if(isset($meta[0]))
		return $meta[0];
	return false;
}
?>
