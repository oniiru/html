<?php
function aff_login_widget_onpage_version($affiliate_page_url)
{
	if(wp_aff_view_is_logged_in())
	{
		$output = show_aff_widget_logged_in_details($affiliate_page_url);
    }
    else
    {
        $output = show_aff_widget_login_form($affiliate_page_url);
    }
    return $output;
}

function aff_login_widget()
{
    if(aff_main_check_security())
    {
        $output = show_aff_widget_logged_in_details();
    }
    else
    {
        $output = show_aff_widget_login_form();
    }
    return $output;
}

function aff_main_check_security()
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

function show_aff_widget_logged_in_details($affiliate_page_url='')
{    
    if(!empty($affiliate_page_url))
    {
    	$dashboard_url = wp_aff_view_get_url_with_separator("members_only",$affiliate_page_url);
    }
    else
    {
    	$dashboard_url = WP_AFF_PLATFORM_URL.'/affiliates/members_only.php';
    }    

    $username = $_SESSION['user_id'];
    $output = "";
    $output .= '<div class="aff_logged_widget">';
    $output .= '<ul>' . AFF_WIDGET_LOGGED_IN_AS;
    $output .= '<label class="aff_highlight">'.$username.'</label>';
    $output .= '<li><a href="'.$dashboard_url.'">'.AFF_WIDGET_ACCESS_DASHBOARD.'</a></li>';

    $output .= '</ul>';
    $output .= '</div>';
    
    return $output;
}

function show_aff_widget_login_form($affiliate_page_url='')
{
	if(!empty($affiliate_page_url))
	{
		$post_url = wp_aff_view_get_url_with_separator("login",$affiliate_page_url);
		$forgot_pass_url =  wp_aff_view_get_url_with_separator("forgot_pass",$affiliate_page_url);
		$sign_up_url = wp_aff_view_get_url_with_separator("signup",$affiliate_page_url);		
	}
	else
	{
		$post_url = WP_AFF_PLATFORM_URL.'/affiliates/login.php';
		$forgot_pass_url =  WP_AFF_PLATFORM_URL.'/affiliates/forgot.php';
		$sign_up_url = WP_AFF_PLATFORM_URL.'/affiliates/index.php';		
	}

	if(!isset($_POST['userid'])){$_POST['userid']='';}
	if(!isset($_POST['password'])){$_POST['password']='';}
	
   $output = "";
   $output .= '<form action="'.$post_url.'" method="post" class="affLoginForm" name="affLoginForm" id="affLoginForm" >';
   
   if(!empty($affiliate_page_url)){//on page version so add the following hidden input to detect that
   		$output .= '<input type="hidden" name="wpAffDoLogin" value="1">';
   }
	$output .= '<table width="95%" border="0" cellpadding="3" cellspacing="5" class="forms">
	    <tr>
	    	<td colspan="2"><label for="userid" class="aff_label">'.AFF_USERNAME.'</label></td>
	    </tr>
	    <tr>
	        <td colspan="2"><input class="aff_text_input" type="text" id="userid" name="userid" size="15" value="'.$_POST['userid'].'" ></td>
	    </tr>
	    <tr>
	    	<td colspan="2"><label for="password" class="aff_label">'.AFF_PASSWORD.'</label></td></tr>
	    <tr>
	        <td colspan="2"><input class="aff_text_input" type="password" id="password" name="password" size="15" value="'.$_POST['password'].'" ></td>
	    </tr>
	    <tr>
	        <td colspan="2"><input name="wpAffSadoLogin" type="submit" id="wpAffSadoLogin" class="aff_button" value="'.AFF_LOGIN_BUTTON_LABEL.'"></td>
	    </tr>
	    <tr> 
	        <td colspan="2"><a id="aff_forgot_pass" href="'.$forgot_pass_url.'">'.AFF_FORGOT_PASSWORD_LABEL.'</a></td>
	    </tr>
	    <tr> 
	        <td colspan="2"><a id="aff_register" href="'.$sign_up_url.'">'.AFF_SIGNUP_PAGE_TITLE.'</a></td>
	    </tr>
	</table>
	</form>
';
    return $output;
}
?>