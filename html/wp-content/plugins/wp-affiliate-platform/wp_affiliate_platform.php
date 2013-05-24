<?php
/*
Plugin Name: WP Affiliate Platform
Version: v5.3.3
Plugin URI: http://tipsandtricks-hq.com/wordpress-affiliate/
Author: Ruhul Amin
Author URI: http://www.tipsandtricks-hq.com/
Description: Simple Affiliate Platform for your wordpress blog. Allows your visitors to get an affiliate link for your products and promote it from their blog/site and in return they receive a commission when a sale is made through their link.
*/

//Direct access to this file is not permitted
if (!defined('ABSPATH'))exit;
	
define('WP_AFFILIATE_PLATFORM_VERSION', "5.3.3");
define('WP_AFFILIATE_PLATFORM_DB_VERSION', "4.6");

define('WP_AFF_PLATFORM_FOLDER', dirname(plugin_basename(__FILE__)));
define('WP_AFF_PLATFORM_URL', plugins_url('',__FILE__));
define('WP_AFF_PLATFORM_PATH',plugin_dir_path( __FILE__ ));

include_once('wp_affiliate_config_class.php');
$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
include_once('wp_affiliate_platform1.php');

//Installer
require_once(dirname(__FILE__).'/affiliate_platform_installer.php');
function wp_aff_platform_install ()
{
	wp_affiliate_platform_run_activation();
}
register_activation_hook(__FILE__,'wp_aff_platform_install');
//register_activation_hook( basename(__FILE__), 'wp_aff_platform_install' );

function wp_aff_add_settings_link($links, $file) 
{
	if ($file == plugin_basename(__FILE__)){
		$settings_link = '<a href="admin.php?page=wp_aff_platform_settings">Settings</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links', 'wp_aff_add_settings_link', 10, 2 );

function wp_aff_platform_handle_new_blog_creation($blog_id, $user_id, $domain, $path, $site_id, $meta ){
	global $wpdb; 	
	if (is_plugin_active_for_network(WP_AFF_PLATFORM_FOLDER.'/wp_affiliate_platform.php')) {
		$old_blog = $wpdb->blogid;
		switch_to_blog($blog_id);
    	wp_affiliate_platform_run_installer();	
		switch_to_blog($old_blog);
	}	
}
add_action('wpmu_new_blog', 'wp_aff_platform_handle_new_blog_creation', 10, 6);
?>