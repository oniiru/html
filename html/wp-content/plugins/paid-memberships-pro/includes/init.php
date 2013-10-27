<?php
/*
	Code that runs on the init, set_current_user, or wp hooks to setup PMPro
*/
//init code
function pmpro_init()
{
	require_once(PMPRO_DIR . "/includes/countries.php");
	require_once(PMPRO_DIR . "/includes/states.php");
	require_once(PMPRO_DIR . "/includes/currencies.php");

	wp_enqueue_script('ssmemberships_js', plugins_url('js/paid-memberships-pro.js',dirname(__FILE__) ), array('jquery'));

	if(is_admin())
	{
		if(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/css/admin.css"))
			$admin_css = get_template_directory_uri() . "/paid-memberships-pro/css/admin.css";
		elseif(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/admin.css"))
			$admin_css = get_template_directory_uri() . "/paid-memberships-pro/admin.css";
		else
			$admin_css = plugins_url('css/admin.css',dirname(__FILE__) );		
		wp_enqueue_style('pmpro_admin', $admin_css, array(), PMPRO_VERSION, "screen");
	}
	else
	{		
		if(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/css/frontend.css"))
			$frontend_css = get_template_directory_uri() . "/paid-memberships-pro/css/frontend.css";
		elseif(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/frontend.css"))
			$frontend_css = get_template_directory_uri() . "/paid-memberships-pro/frontend.css";
		else
			$frontend_css = plugins_url('css/frontend.css',dirname(__FILE__) );	
		wp_enqueue_style('pmpro_frontend', $frontend_css, array(), PMPRO_VERSION, "screen");
		
		if(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/css/print.css"))
			$print_css = get_template_directory_uri() . "/paid-memberships-pro/css/print.css";
		elseif(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/print.css"))
			$print_css = get_template_directory_uri() . "/paid-memberships-pro/print.css";
		else
			$print_css = plugins_url('css/print.css',dirname(__FILE__) );
		wp_enqueue_style('pmpro_print', $print_css, array(), PMPRO_VERSION, "print");
	}
	
	global $pmpro_pages, $pmpro_ready, $pmpro_currency, $pmpro_currency_symbol;
	$pmpro_pages = array();
	$pmpro_pages["account"] = pmpro_getOption("account_page_id");
	$pmpro_pages["billing"] = pmpro_getOption("billing_page_id");
	$pmpro_pages["cancel"] = pmpro_getOption("cancel_page_id");
	$pmpro_pages["checkout"] = pmpro_getOption("checkout_page_id");
	$pmpro_pages["confirmation"] = pmpro_getOption("confirmation_page_id");
	$pmpro_pages["invoice"] = pmpro_getOption("invoice_page_id");
	$pmpro_pages["levels"] = pmpro_getOption("levels_page_id");

	$pmpro_ready = pmpro_is_ready();

	//set currency
	$pmpro_currency = pmpro_getOption("currency");
	if(!$pmpro_currency)
	{
		global $pmpro_default_currency;
		$pmpro_currency = $pmpro_default_currency;
	}

	//figure out what symbol to show for currency
	if(in_array($pmpro_currency, array("USD", "AUD", "BRL", "CAD", "HKD", "MXN", "NZD", "SGD")))
		$pmpro_currency_symbol = "&#36;";
	elseif($pmpro_currency == "EUR")
		$pmpro_currency_symbol = "&euro;";
	elseif($pmpro_currency == "GBP")
		$pmpro_currency_symbol = "&pound;";
	elseif($pmpro_currency == "JPY")
		$pmpro_currency_symbol = "&yen;";
	else
		$pmpro_currency_symbol = $pmpro_currency . " ";	//just use the code			
}
add_action("init", "pmpro_init");

//this code runs after $post is set, but before template output
function pmpro_wp()
{
	if(!is_admin())
	{
		global $post, $pmpro_pages, $pmpro_page_name, $pmpro_page_id;		
		
		//run the appropriate preheader function
		foreach($pmpro_pages as $pmpro_page_name => $pmpro_page_id)
		{
			if($pmpro_page_name == "checkout")
			{								
				continue;		//we do the checkout shortcode every time now
			}
				
			if(!empty($post->ID) && $pmpro_page_id == $post->ID)
			{
				require_once(PMPRO_DIR . "/preheaders/" . $pmpro_page_name . ".php");

				function pmpro_pages_shortcode($atts, $content=null, $code="")
				{
					global $pmpro_page_name;
					ob_start();
					if(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/pages/" . $pmpro_page_name . ".php"))
						include(get_stylesheet_directory() . "/paid-memberships-pro/pages/" . $pmpro_page_name . ".php");
					else
						include(PMPRO_DIR . "/pages/" . $pmpro_page_name . ".php");
					
					$temp_content = ob_get_contents();
					ob_end_clean();
					return apply_filters("pmpro_pages_shortcode_" . $pmpro_page_name, $temp_content);
				}
				add_shortcode("pmpro_" . $pmpro_page_name, "pmpro_pages_shortcode");
				break;	//only the first page found gets a shortcode replacement
			}
		}
		
		//make sure you load the preheader for the checkout page. the shortcode for checkout is loaded below		
		if(!empty($post->post_content) && strpos($post->post_content, "[pmpro_checkout]") !== false)
		{
			require_once(PMPRO_DIR . "/preheaders/checkout.php");	
		}
	}
}
add_action("wp", "pmpro_wp", 1);

//add membership level to current user object
function pmpro_set_current_user()
{
	//this code runs at the beginning of the plugin
	global $current_user, $wpdb;
	get_currentuserinfo();
	$id = intval($current_user->ID);
	if($id)
	{
		$current_user->membership_level = pmpro_getMembershipLevelForUser($current_user->ID);
		if(!empty($current_user->membership_level))
		{
			$current_user->membership_level->categories = pmpro_getMembershipCategories($current_user->membership_level->ID);
		}
		$current_user->membership_levels = pmpro_getMembershipLevelsForUser($current_user->ID);
	}

	//hiding ads?
	$hideads = pmpro_getOption("hideads");
	$hideadslevels = pmpro_getOption("hideadslevels");
	if(!is_array($hideadslevels))
		$hideadslevels = explode(",", $hideadslevels);
	if($hideads == 1 && pmpro_hasMembershipLevel() || $hideads == 2 && pmpro_hasMembershipLevel($hideadslevels))
	{
		//disable ads in ezAdsense
		if(class_exists("ezAdSense"))
		{
			global $ezCount, $urCount;
			$ezCount = 100;
			$urCount = 100;
		}
		
		//disable ads in Easy Adsense (newer versions)
		if(class_exists("EzAdSense"))
		{
			global $ezAdSense;
			$ezAdSense->ezCount = 100;
			$ezAdSense->urCount = 100;
		}

		//set a global variable to hide ads
		global $pmpro_display_ads;
		$pmpro_display_ads = false;
	}
	else
	{
		global $pmpro_display_ads;
		$pmpro_display_ads = true;
	}

	do_action("pmpro_after_set_current_user");
}
add_action('set_current_user', 'pmpro_set_current_user');