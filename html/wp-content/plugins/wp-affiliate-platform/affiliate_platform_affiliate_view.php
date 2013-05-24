<?php
function wp_affiliate_view_handler($atts)
{
	return affiliate_platform_affiliate_view_main();
}

function affiliate_platform_affiliate_view_main()
{
	wp_aff_load_affiliate_view_css();
	$output = "";	 
	if(wp_aff_view_is_logged_in())
	{		
		$action = isset($_GET['wp_affiliate_view'])?$_GET['wp_affiliate_view']:'';
		switch ($action)
	    {
	    	case 'members_only':
				include_once('views/members_only_view.php');
				$output .= wp_aff_members_only_view();
	           	break;
	       	case 'details':
				include_once('views/details_view.php');
				$output .= wp_aff_details_view();	
	           	break;	
	       	case 'clicks':
				include_once('views/referrals_view.php');
				$output .= wp_aff_referrals_view();	
	           	break;	
	       	case 'sub-affiliates':
				include_once('views/sub_affiliates_view.php');
				$output .= wp_aff_sub_affiliates_view();	
	           	break;		           	
	       	case 'sales':
				include_once('views/sales_view.php');
				$output .= wp_aff_sales_view();	
	           	break;	
	       	case 'payments':
				include_once('views/payments_view.php');
				$output .= wp_aff_payment_history_view();	
	           	break;	           	
	       	case 'ads':
				include_once('views/ads_view.php');
				$output .= wp_aff_ads_view();	
	           	break;  
	       	case 'creatives':
				include_once('views/creatives_view.php');
				$output .= wp_aff_creatives_view();	
	           	break; 	           		           	
	       	case 'contact':
				include_once('views/contact_view.php');
				$output .= wp_aff_contact_view();	
	           	break;   	           	 
	       	case 'logout':	           	   	           	
				//see the code in "wp_affiliate_platform1.php" file
				break;        				    	      	           		           	
	       	default:
				include_once('views/members_only_view.php');
				$output .= wp_aff_members_only_view();
	          	break;
	   	}	
	}
	else
	{
		$action = isset($_GET['wp_affiliate_view'])?$_GET['wp_affiliate_view']:'';
		switch ($action)
	    {
	       	case 'login':	           	   	           	
				include_once('views/login_view.php');
				$output .= wp_aff_login_view();	
	           	break; 
	       	case 'signup':	           	   	           	
				include_once('views/register_view.php');
				include_once('wp_aff_auto_responder_handler.php');
				$output .= wp_aff_register_view();	
	           	break; 	
	       	case 'forgot_pass':	           	   	           	
				include_once('views/forgot_pass_view.php');
				$output .= wp_aff_forgot_pass_view();	
	           	break; 	
	       	case 'thankyou':	           	   	           	
				include_once('views/thankyou_view.php');
				$output .= wp_aff_thankyou_view();	
	           	break; 	           		           		           		
	       	default:
				$output .= wp_aff_view_main_index();
	          	break;	           	    	
	    }	   
	}
	return $output;
}

function wp_aff_load_affiliate_view_css()
{
    echo '<link type="text/css" rel="stylesheet" href="'.WP_AFF_PLATFORM_URL.'/views/affiliate_view.css" />'."\n";
}

function wp_aff_view_main_index()
{
	global $wp_aff_platform_config;
    $login_url = wp_aff_view_get_url_with_separator("login"); 
	$signup_url = wp_aff_view_get_url_with_separator("signup");
			
	$output = "";
	$output .= wp_aff_view_get_navbar();
	$output .= '<div id="wp_aff_inside">';
	
    $wp_aff_index_title = $wp_aff_platform_config->getValue('wp_aff_index_title');

    $output .= '<h3 class="wp_aff_title">'.$wp_aff_index_title.'</h3>';

    $output .= '<div id="aff-box-content">';
    $output .= '<div class="wp-aff-box"><img src="'.WP_AFF_PLATFORM_URL.'/affiliates/images/user_signup.png" class="center" alt="Affiliate Sign up icon" />
    <div id="aff-box-action">
    <div style="float: left;">
        <a href="'.$signup_url.'"><img src="'.WP_AFF_PLATFORM_URL.'/affiliates/images/signup_round_40.png" /></a>
    </div>';
    
    $output .= '<div class="action-head"><a href="'.$signup_url.'">'.AFF_SIGN_UP.'</a></div>    
    </div></div>';

    $output .= '<div class="wp-aff-box"><img src="'.WP_AFF_PLATFORM_URL.'/affiliates/images/login_icon_128.png" class="center" alt="Affiliate Login icon" />
    <div id="aff-box-action">
    <div style="float: left;">
        <a href="'.$login_url.'"><img src="'.WP_AFF_PLATFORM_URL.'/affiliates/images/login_icon_round_48.png" /></a>
    </div>
        <div class="action-head"><a href="'.$login_url.'">'.AFF_LOGIN.'</a></div>        
    </div></div><div class="wp_aff_clear"></div>
    </div>';
    
    $wp_aff_index_body_tmp = $wp_aff_platform_config->getValue('wp_aff_index_body');
    $wp_aff_index_body = html_entity_decode($wp_aff_index_body_tmp, ENT_COMPAT, "UTF-8");
    $output .= '<div id="wp_aff-index-body">'.$wp_aff_index_body.'</div>';
	$output .= '<div class="wp_aff_clear"></div>';
	
	$output .= '</div>';
	$output .= wp_aff_view_get_footer();
	return $output;
}

function wp_aff_view_get_navbar()
{
	$output = "";
	if(wp_aff_view_is_logged_in()) 
	{
    	$separator='?';
		$url=get_permalink();
		if(strpos($url,'?wp_affiliate_view='))
		{
			$separator='?';
		}
		else if(strpos($url,'?')!==false)
		{
		    $separator='&';
		}   		
		$output .= '<div id="wp_aff_nav"><ul>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=members_only">'.AFF_NAV_HOME.'</a></li>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=details">'.AFF_NAV_EDIT_PROFILE.'</a></li>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=clicks">'.AFF_NAV_REFERRALS.'</a></li>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=sales">'.AFF_NAV_SALES.'</a></li>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=payments">'.AFF_NAV_PAYMENT_HISTORY.'</a></li>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=ads">'.AFF_NAV_ADS.'</a></li>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=contact">'.AFF_NAV_CONTACT.'</a></li>';
		$output .= '<li><a href="'.$url.$separator.'wp_affiliate_view=logout">'.AFF_NAV_LOGOUT.'</a></li>';
		$output .= '</ul></div>';
		$output .= '<div class="wp_aff_clear"></div>';
		return $output;
	}
	return $output;
}
function wp_aff_view_get_url_with_separator($name_value_data,$url='')
{
    $separator='?';
    if(empty($url))
    {
		//$url=wp_aff_current_page_url();
	    $url=get_permalink();  
	    if(empty($url))
	    {
	    	$current_url = wp_aff_current_page_url(); 
	    	$position = strpos($current_url,'wp_affiliate_view=');	   
	    	$url = substr_replace($current_url, '', $position-1);		
	    } 
    }
	if(strpos($url,'?wp_affiliate_view='))
	{
		$separator='?';
	}
	else if(strpos($url,'?')!==false)
	{
	    $separator='&';
	} 	
	$full_url = $url.$separator.'wp_affiliate_view='.$name_value_data;
	return $full_url;
}

function wp_aff_view_get_footer()
{	
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	$output = "";
	if($wp_aff_platform_config->getValue('wp_aff_do_not_show_powered_by_section')!='1'){
		$output .= '<div id="wp_aff_footer">';   
	    $aff_id = get_option('wp_aff_user_affilate_id');
		if(!empty($aff_id))
		{
			$output .= '<div style="float:right;">Powered by&nbsp;&nbsp;<a target="_blank" href="http://tipsandtricks-hq.com/?p=1474&ap_id='.$aff_id.'">WP Affiliate Platform</a></div>';
		}
		else
		{
			$output .= '<div style="float:right;">Powered by&nbsp;&nbsp;<a target="_blank" href="http://tipsandtricks-hq.com/?p=1474">WP Affiliate Platform</a></div>';
		}
	    $output .= '<div class="wp_aff_clear"></div>';
		$output .= '</div>';
	}
	return $output;
}
function wp_aff_view_is_logged_in()
{
	//TODO - remove this function and use "wp_aff_is_logged_in" function from utility functions set (or simply call that other function from here).
	if(isset($_COOKIE['user_id'])){
	      $_SESSION['user_id'] = $_COOKIE['user_id'];
	}	
	if (!isset($_SESSION['user_id'])){
	   return false;	   
	}else{
		return true;
	}
}
function wp_aff_platform_redirect($url, $time = 0)
{
  	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"$time;URL=$url\">";
  	echo "If you are not redirected within a few seconds then please click <a class=leftLink href=$url>".here.'</a>';
}
?>