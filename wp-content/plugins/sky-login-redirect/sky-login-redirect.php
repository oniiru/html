<?php
/*
Plugin Name: Sky Login Redirect
Plugin URI: http://www.skyminds.net/wordpress-plugins/sky-login-redirect/
Description: Redirects users to the page they were reading just before logging in.
Version: 1.1
Author: Matt
Author URI: http://www.skyminds.net/
License: GPLv2 or later
*/
function sky_login_redirect() {
	$sky_referer  = $_SERVER['HTTP_REFERER'];
	$sky_site_url = home_url();
	$redirect_to  = $_REQUEST['redirect_to'];

	/* check if $redirect_to is set, not empty and belongs to our domain  */
	if( isset($redirect_to) && !empty($redirect_to) && strpos($redirect_to, $_SERVER['HTTP_HOST']) )
	{
		return $redirect_to;
	}

	/* else check if referer belongs to our domain */
	elseif( strpos($sky_referer, $_SERVER['HTTP_HOST']) ){
		/* Smooth transparent redirects to the previous page */
		return $sky_referer;
	}

	/* it doesn't, let's redirect users to our homepage */
	else{
		return $sky_site_url;
	}
}
add_filter('login_redirect', 'sky_login_redirect');
?>
