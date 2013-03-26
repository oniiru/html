<?php
include_once('wp_aff_includes1.php');
include_once('wp_aff_utility_functions.php');

function show_aff_platform_settings_page()
{
	if(isset($_GET['wpap_hide_sc_msg'])){//Turn off the super cache warning display
		$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
		$wp_aff_platform_config->setValue('wp_aff_do_not_show_sc_warning', '1');
		$wp_aff_platform_config->saveConfig();		
	}
		
	echo '<div class="wrap">';
	echo '<div id="poststuff"><div id="post-body">'; 
	 
	echo wp_aff_admin_submenu_css();
	echo wp_aff_misc_admin_css();
   ?>
   <h2>WP Affiliate Platform Settings v <?php echo WP_AFFILIATE_PLATFORM_VERSION; ?></h2>
   <ul class="affiliateSubMenu">
   <li><a href="admin.php?page=wp_aff_platform_settings">General Settings</a></li>
   <li><a href="admin.php?page=wp_aff_platform_settings&settings_action=email">Email Settings</a></li>
   <li><a href="admin.php?page=wp_aff_platform_settings&settings_action=autoresponder">Autoresponder Settings</a></li>
   <li><a href="admin.php?page=wp_aff_platform_settings&settings_action=wp_user_settings">WP User Settings</a></li>
   </ul>
   <?php

	$action = isset($_GET['settings_action'])?$_GET['settings_action']:'';  
	switch ($action)
	{
		case 'email':
			wp_aff_email_settings();
			break;
		case 'autoresponder':
			include_once('wp_aff_autoresponder_settings.php');
       		wp_affiliate_auto_responder_settings();
       		break;    
		case 'wp_user_settings':
			include_once('wp_aff_wp_user_settings_menu.php');
			wp_aff_wp_user_settings_menu_page();
			break;       
		default:
			show_aff_platform_general_settings_page();
			break;
   }
   	 
     echo '</div></div>';
     echo '</div>';
}
function wp_aff_email_settings()
{
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if (isset($_POST['info_update']))
    {
        update_option('wp_aff_senders_email_address', stripslashes((string)$_POST["wp_aff_senders_email_address"]));
        update_option('wp_aff_signup_email_subject', stripslashes((string)$_POST["wp_aff_signup_email_subject"]));
        update_option('wp_aff_signup_email_body', stripslashes((string)$_POST["wp_aff_signup_email_body"]));

        $wp_aff_platform_config->setValue('wp_aff_comm_notif_senders_address', stripslashes((string)$_POST["wp_aff_comm_notif_senders_address"]));
        $wp_aff_platform_config->setValue('wp_aff_comm_notif_email_subject', stripslashes((string)$_POST["wp_aff_comm_notif_email_subject"]));
        $wp_aff_platform_config->setValue('wp_aff_comm_notif_email_body', stripslashes((string)$_POST["wp_aff_comm_notif_email_body"]));        
        
        $wp_aff_platform_config->saveConfig();
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }
    $wp_aff_senders_address = get_option('wp_aff_senders_email_address');
    if (empty($wp_aff_senders_address))
    {
    	$wp_aff_senders_address = get_bloginfo('name')." <".get_option('admin_email').">";
    	update_option('wp_aff_senders_email_address',$wp_aff_senders_address);
    }
    $wp_aff_signup_email_subject = get_option('wp_aff_signup_email_subject');
    if (empty($wp_aff_signup_email_subject))
    {
    	$wp_aff_signup_email_subject = "Affiliate Login Details";
    	update_option('wp_aff_signup_email_subject',$wp_aff_signup_email_subject);
    }

    $wp_aff_signup_email_body = get_option('wp_aff_signup_email_body');
    if (empty($wp_aff_signup_email_body))
    {
		$wp_aff_signup_email_body = "Thank you for registering with us. Here are your login details...\n".        
        "\nAffiliate ID: {user_name}".
        "\nEmail: {email} \n".
        "\nPasswd: {password} \n".
        "\nYou can Log into the system at the following URL:\n{login_url}\n".           
        "\nPlease log into your account to get banners and view your real-time statistics.\n".        
        "\nThank You".
        "\nAdministrator".
        "\n______________________________________________________".
        "\nTHIS IS AN AUTOMATED RESPONSE. ".
        "\n***DO NOT RESPOND TO THIS EMAIL****";

		update_option('wp_aff_signup_email_body',$wp_aff_signup_email_body);
    }  
    
    $notif_email_from_address = $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address');
	if(empty($notif_email_from_address)){
		$wp_aff_platform_config->setValue('wp_aff_comm_notif_senders_address',$wp_aff_senders_address);
		$wp_aff_platform_config->saveConfig();
	}	
    ?>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

	<div class="postbox">
	<h3><label for="title">Affiliate Signup Email Settings</label></h3>
	<div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    From Email Address
    </td><td align="left">
    <input name="wp_aff_senders_email_address" type="text" size="60" value="<?php echo get_option('wp_aff_senders_email_address'); ?>"/>
    <br /><i>Sender's address (eg. Your Name &lt;admin@your-domain.com&gt;)</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Email Subject
    </td><td align="left">
    <input name="wp_aff_signup_email_subject" type="text" size="60" value="<?php echo $wp_aff_signup_email_subject; ?>"/>
    <br /><i>The Email Subject</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    The Email Body
    </td><td align="left">
    <textarea name="wp_aff_signup_email_body" cols="60" rows="6"><?php echo $wp_aff_signup_email_body; ?></textarea>
    <br /><i>This is the body of the email that will be sent to the affiliate after they sign up. Do not change the text within the braces {}</i><br /><br />
    </td></tr>
    
    </table>
    </div></div>
    
	<div class="postbox">
	<h3><label for="title">Affiliate Commission Notification Email Settings</label></h3>
	<div class="inside">

<?php 
if (get_option('wp_aff_notify_affiliate_for_commission')){
?>	
    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    From Email Address
    </td><td align="left">
    <input name="wp_aff_comm_notif_senders_address" type="text" size="60" value="<?php echo $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address'); ?>"/>
    <br /><i>Sender's email address that will be used in the commission notification email (example, Your Name &lt;admin@your-domain.com&gt;)</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Commission Notification Email Subject
    </td><td align="left">
    <input name="wp_aff_comm_notif_email_subject" type="text" size="60" value="<?php echo $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_subject'); ?>"/>
    <br /><i>The email subject of the commission notification email</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Commission Notification Email Body
    </td><td align="left">
    <textarea name="wp_aff_comm_notif_email_body" cols="60" rows="6"><?php echo $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_body'); ?></textarea>
    <br /><i>This is the body of the email that will be sent to the affiliates when they receive a commission</i><br /><br />
    </td></tr>
    
    </table>
<?php 
}
else{
	echo '<p>Enable the "Send Commission Notification to Affiliates" feature from the general settings section of the plugin to use this option.</p>';
}
?>    
    </div></div>
        
    <div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
    </div>

    </form>    
    <?php
    
}

function show_aff_platform_general_settings_page ()
{
	global $wp_aff_platform_config;
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	   	
	if (isset($_POST['info_update']))
	{
		//Do some data validation
		$error_msg = "";
		$aff_url_validation_error_msg_ignore = "<p><i>If you know for sure that the URL is correct then ignore this message. You can copy and paste the URL in a browser's address bar to make sure the URL is correct.</i></p>";		
	    if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_default_affiliate_landing_url"]))
        {
        	$error_msg .= "<br /><strong>The URL specified in the \"Default Landing Page\" field does not seem to be a valid URL! Please check this value again:</strong>";
        	$error_msg .= "<br />".$_POST["wp_aff_default_affiliate_landing_url"]."<br />";
        }		
		if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_login_url"]))
        {
        	$error_msg .= "<br /><strong>The URL specified in the \"Affiliate Login URL\" field does not seem to be a valid URL! Please check this value again:</strong>";
        	$error_msg .= "<br />".$_POST["wp_aff_login_url"]."<br />";
        }
		if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_terms_url"]))
        {
        	$error_msg .= "<br /><strong>The URL specified in the \"Terms & Conditions URL\" field does not seem to be a valid URL! Please check this value again:</strong>";
        	$error_msg .= "<br />".$_POST["wp_aff_terms_url"]."<br />";
        }
		if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_comm_post_url"]))
        {
        	$error_msg .= "<br /><strong>The URL specified in the \"POST URL for Commission Awarding\" field does not seem to be a valid URL! Please check this value again:</strong>";
        	$error_msg .= "<br />".$_POST["wp_aff_comm_post_url"]."<br />";
        }
		if(!wp_aff_is_valid_url_if_not_empty($_POST["wp_aff_remote_click_post_url"]))
        {
        	$error_msg .= "<br /><strong>The URL specified in the \"POST URL for Remote Click Tracking\" field does not seem to be a valid URL! Please check this value again:</strong>";
        	$error_msg .= "<br />".$_POST["wp_aff_remote_click_post_url"]."<br />";
        }
		
		if(!empty($error_msg)){
	        echo '<div id="message" class="error"><p><strong>';
	        echo $error_msg;
	        echo $aff_url_validation_error_msg_ignore;
	        echo '</strong></p></div>';		
		}
	}
    if (isset($_POST['info_update']))
    {   	    	    	
    	update_option('wp_aff_platform_version', WP_AFFILIATE_PLATFORM_VERSION);
    	
    	update_option('wp_aff_language', (string)$_POST["wp_aff_language"]);
		update_option('wp_aff_site_title', stripslashes((string)$_POST["wp_aff_site_title"]));
        update_option('wp_aff_cookie_life', (string)$_POST["wp_aff_cookie_life"]);
        update_option('wp_aff_currency_symbol', (string)$_POST["wp_aff_currency_symbol"]);
        update_option('wp_aff_currency', (string)$_POST["wp_aff_currency"]);
        update_option('wp_aff_contact_email', (string)$_POST["wp_aff_contact_email"]);
        update_option('wp_aff_default_affiliate_landing_url', (string)$_POST["wp_aff_default_affiliate_landing_url"]);
        update_option('wp_aff_login_url', (string)$_POST["wp_aff_login_url"]);
        update_option('wp_aff_terms_url', (string)$_POST["wp_aff_terms_url"]);
        $wp_aff_platform_config->setValue('wp_aff_make_paypal_email_required',($_POST['wp_aff_make_paypal_email_required']=='1') ? '1':'');	
        update_option('wp_aff_admin_notification', ($_POST['wp_aff_admin_notification']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_notify_affiliate_for_commission', ($_POST['wp_aff_notify_affiliate_for_commission']!='') ? 'checked="checked"':'' );
        $wp_aff_platform_config->setValue('wp_aff_notify_admin_for_commission', ($_POST['wp_aff_notify_admin_for_commission']=='1') ? '1':'');
        
        update_option('wp_aff_enable_clicks_custom_field', ($_POST['wp_aff_enable_clicks_custom_field']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_disable_visitor_signup', ($_POST['wp_aff_disable_visitor_signup']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_show_buyer_details_to_affiliates', ($_POST['wp_aff_show_buyer_details_to_affiliates']!='') ? 'checked="checked"':'' );
        $wp_aff_platform_config->setValue('wp_aff_show_buyer_details_name_to_affiliates',($_POST['wp_aff_show_buyer_details_name_to_affiliates']=='1') ? '1':'');
        $wp_aff_platform_config->setValue('wp_aff_do_not_show_powered_by_section',($_POST['wp_aff_do_not_show_powered_by_section']=='1') ? '1':'');
        update_option('wp_aff_user_affilate_id', (string)$_POST["wp_aff_user_affilate_id"]);

        update_option('wp_aff_use_fixed_commission', ($_POST['wp_aff_use_fixed_commission']!='') ? 'checked="checked"':'' );
        $curr_symbol = get_option('wp_aff_currency_symbol');
        $commission_level = (string)$_POST["wp_aff_commission_level"];
		$commission_level = str_replace("%","",$commission_level);		
		$commission_level = str_replace($curr_symbol,"",$commission_level);				
		update_option('wp_aff_commission_level', $commission_level);
        update_option('wp_aff_commission_reversal', ($_POST['wp_aff_commission_reversal']!='') ? 'checked="checked"':'' );
        //update_option('wp_aff_fixed_comm_amt', (string)$_POST["wp_aff_fixed_comm_amt"]);

        update_option('wp_aff_use_2tier', ($_POST['wp_aff_use_2tier']!='') ? 'checked="checked"':'' );
        $commission_level = (string)$_POST["wp_aff_2nd_tier_commission_level"];
		$commission_level = str_replace("%","",$commission_level);
		$commission_level = str_replace($curr_symbol,"",$commission_level);		        
        update_option('wp_aff_2nd_tier_commission_level', $commission_level);
        //update_option('wp_aff_2nd_tier_fixed_comm_amt', (string)$_POST["wp_aff_2nd_tier_fixed_comm_amt"]);
        update_option('wp_aff_2nd_tier_duration', (string)$_POST["wp_aff_2nd_tier_duration"]);

        update_option('wp_aff_use_recaptcha', ($_POST['wp_aff_use_recaptcha']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_captcha_public_key', (string)$_POST["wp_aff_captcha_public_key"]);
        update_option('wp_aff_captcha_private_key', (string)$_POST["wp_aff_captcha_private_key"]);
        
        update_option('wp_aff_use_custom_color', ($_POST['wp_aff_use_custom_color']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_header_color', (string)$_POST["wp_aff_header_color"]);
        update_option('wp_aff_header_font_color', (string)$_POST["wp_aff_header_font_color"]);
        update_option('wp_aff_footer_color', (string)$_POST["wp_aff_footer_color"]);
        
        $tmpmsg1 = htmlentities(stripslashes($_POST['wp_aff_index_body']), ENT_COMPAT, "UTF-8");
        $wp_aff_platform_config->setValue('wp_aff_index_title',stripslashes((string)$_POST["wp_aff_index_title"]));
		$wp_aff_platform_config->setValue('wp_aff_index_body',$tmpmsg1);
		$tmpmsg2 = htmlentities(stripslashes($_POST['wp_aff_welcome_page_msg']), ENT_COMPAT, "UTF-8");
		$wp_aff_platform_config->setValue('wp_aff_welcome_page_msg',$tmpmsg2);
	
        
        update_option('wp_aff_enable_3rd_party', ($_POST['wp_aff_enable_3rd_party']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_sandbox_mode', ($_POST['wp_aff_sandbox_mode']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_pdt_identity_token', trim($_POST["wp_aff_pdt_identity_token"]));
        
        update_option('wp_aff_enable_remote_post', ($_POST['wp_aff_enable_remote_post']!='') ? 'checked="checked"':'' );
        update_option('wp_aff_comm_post_url', trim($_POST["wp_aff_comm_post_url"]));
        update_option('wp_aff_remote_click_post_url', trim($_POST["wp_aff_remote_click_post_url"])); 
        $wp_aff_platform_config->setValue('wp_aff_lead_capture_post_url', trim($_POST["wp_aff_lead_capture_post_url"]));       
        update_option('wp_aff_secret_word_for_post', trim($_POST["wp_aff_secret_word_for_post"]));  

        $wp_aff_platform_config->setValue('wp_aff_enable_wpcf7_lead_capture', ($_POST['wp_aff_enable_wpcf7_lead_capture']=='1') ? '1':'' ); 
        $wp_aff_platform_config->setValue('wp_aff_wp_cf7_form_exclusion_list', trim($_POST["wp_aff_wp_cf7_form_exclusion_list"]));  

        $wp_aff_platform_config->setValue('wp_aff_enable_gf_paypal', ($_POST['wp_aff_enable_gf_paypal']=='1') ? '1':'' );
        
        update_option('wp_aff_enable_debug', ($_POST['wp_aff_enable_debug']=='1') ? '1':'' );        
        
        $wp_aff_platform_config->saveConfig();
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }
    $aff_language = get_option('wp_aff_language');
    if (empty($aff_language)) $aff_language = 'eng.php';
    
    $wp_aff_login_url = get_option('wp_aff_login_url');
    if (empty($wp_aff_login_url))
    {
        $wp_aff_login_url = WP_AFF_PLATFORM_URL.'/affiliates/login.php';
    }
           
    if (get_option('wp_aff_commission_reversal'))
        $wp_aff_commission_reversal = 'checked="checked"';
    else
        $wp_aff_commission_reversal = '';
        
    if (get_option('wp_aff_use_fixed_commission'))
        $wp_aff_use_fixed_commission = 'checked="checked"';
    else
        $wp_aff_use_fixed_commission = '';

    if (get_option('wp_aff_admin_notification'))
        $wp_aff_admin_notification = 'checked="checked"';
    else
        $wp_aff_admin_notification = '';

    if (get_option('wp_aff_notify_affiliate_for_commission'))
        $wp_aff_notify_affiliate_for_commission = 'checked="checked"';
    else
        $wp_aff_notify_affiliate_for_commission = '';
                
    if (get_option('wp_aff_show_buyer_details_to_affiliates'))
        $wp_aff_show_buyer_details_to_affiliates = 'checked="checked"';
    else
        $wp_aff_show_buyer_details_to_affiliates = '';
                
    if (get_option('wp_aff_use_2tier'))
        $wp_aff_use_2tier = 'checked="checked"';
    else
        $wp_aff_use_2tier = '';

    if (get_option('wp_aff_use_recaptcha'))
        $wp_aff_use_recaptcha = 'checked="checked"';
    else
        $wp_aff_use_recaptcha = '';
        
    if (get_option('wp_aff_use_custom_color'))
        $wp_aff_use_custom_color = 'checked="checked"';
    else
        $wp_aff_use_custom_color = ''; 

    $wp_aff_index_title = $wp_aff_platform_config->getValue('wp_aff_index_title');
    if(empty($wp_aff_index_title))
    {
        $wp_aff_index_title = "Welcome to Affiliate Center";
    }    
    $wp_aff_index_body_tmp = $wp_aff_platform_config->getValue('wp_aff_index_body');//get_option('wp_aff_index_body');
    if(empty($wp_aff_index_body_tmp))
    {
        $wp_aff_index_body_tmp = wp_aff_default_index_body();
    }
    $wp_aff_index_body = html_entity_decode($wp_aff_index_body_tmp, ENT_COMPAT, "UTF-8");
    
    $wp_aff_welcome_page_msg = $wp_aff_platform_config->getValue('wp_aff_welcome_page_msg');
    $wp_aff_welcome_page_msg = html_entity_decode($wp_aff_welcome_page_msg, ENT_COMPAT, "UTF-8");

    if (get_option('wp_aff_enable_3rd_party'))
        $wp_aff_enable_3rd_party = 'checked="checked"';
    else
        $wp_aff_enable_3rd_party = ''; 
        
    if (get_option('wp_aff_sandbox_mode'))
        $wp_aff_sandbox_mode = 'checked="checked"';
    else
        $wp_aff_sandbox_mode = '';     

    if (get_option('wp_aff_enable_remote_post'))
        $wp_aff_enable_remote_post = 'checked="checked"';
    else
        $wp_aff_enable_remote_post = '';  
    
    $wp_aff_comm_post_url = get_option('wp_aff_comm_post_url');
    if(empty($wp_aff_comm_post_url))
    {
        $wp_aff_comm_post_url = WP_AFF_PLATFORM_URL.'/api/post.php';
    }
    $wp_aff_remote_click_post_url = get_option('wp_aff_remote_click_post_url');
    if(empty($wp_aff_remote_click_post_url))
    {
        $wp_aff_remote_click_post_url = WP_AFF_PLATFORM_URL.'/api/remote-click-track.php';
    }      
    $wp_aff_lead_capture_post_url = $wp_aff_platform_config->getValue('wp_aff_lead_capture_post_url');
    if(empty($wp_aff_lead_capture_post_url)){
    	$wp_aff_lead_capture_post_url = WP_AFF_PLATFORM_URL.'/api/remote-lead-capture.php';
    }  
    $wp_aff_secret_word_for_post = get_option('wp_aff_secret_word_for_post');
    if(empty($wp_aff_secret_word_for_post))
    {
        $wp_aff_secret_word_for_post = uniqid();
    }    
	?>

 	<p class="wp_affiliate_grey_box">
 	For information and detailed documentation please visit the 
    <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate" target="_blank">WordPress Affiliate Platform Documentation Site</a>
	<br /><br />
	Like the plugin? Give us a <a href="http://www.tipsandtricks-hq.com/?p=1474#gfts_share" target="_blank">thumbs up here</a> by clicking on a share button.
	</p>
	
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

	<div class="postbox">
	<h3><label for="title">General Settings</label></h3>
	<div class="inside">

    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Affiliate Site Language:</strong>
    </td><td align="left">
	<select name="wp_aff_language">
	<option value="eng.php" <?php if($aff_language=="eng.php")echo 'selected="selected"';?>><?php echo "English" ?></option>
	<option value="ita.php" <?php if($aff_language=="ita.php")echo 'selected="selected"';?>><?php echo "Italian" ?></option>
	<option value="spa.php" <?php if($aff_language=="spa.php")echo 'selected="selected"';?>><?php echo "Spanish" ?></option>
	<option value="cat.php" <?php if($aff_language=="cat.php")echo 'selected="selected"';?>><?php echo "Catalan" ?></option>
	<option value="ger.php" <?php if($aff_language=="ger.php")echo 'selected="selected"';?>><?php echo "German" ?></option>
	<option value="nld.php" <?php if($aff_language=="nld.php")echo 'selected="selected"';?>><?php echo "Dutch" ?></option>
	<option value="fr.php" <?php if($aff_language=="fr.php")echo 'selected="selected"';?>><?php echo "French" ?></option>
	<option value="heb.php" <?php if($aff_language=="heb.php")echo 'selected="selected"';?>><?php echo "Hebrew" ?></option>
	<option value="ru.php" <?php if($aff_language=="ru.php")echo 'selected="selected"';?>><?php echo "Russian" ?></option>
	</select>
	</td></tr>
	
    <tr valign="top"><td width="25%" align="left">
    <strong>Affiliate Site Title:</strong>
    </td><td align="left">
    <input name="wp_aff_site_title" type="text" size="40" value="<?php echo get_option('wp_aff_site_title'); ?>"/>
    <br /><i>This will be shown in the header of the affiliate site</i><br />
    </td></tr>
   
    <tr valign="top"><td width="25%" align="left">
    <strong>Cookie Life (Days):</strong>
    </td><td align="left">
    <input name="wp_aff_cookie_life" type="text" size="5" value="<?php echo get_option('wp_aff_cookie_life'); ?>"/> Days
    <br /><i>This is the Cookie Life Time. A refferer will be awarded for a sale if the sale is made bofore the cookie life expires</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Currency Symbol:</strong>
    </td><td align="left">
    <input name="wp_aff_currency_symbol" type="text" size="2" value="<?php echo get_option('wp_aff_currency_symbol'); ?>"/>
    <br /><i>eg. $, &#163;, &#8364; etc. This symbol will be shown next to the payment amount</i><br />
    </td></tr>  
        
    <tr valign="top"><td width="25%" align="left">
    <strong>Currency Code:</strong>
    </td><td align="left">
    <input name="wp_aff_currency" type="text" size="3" value="<?php echo get_option('wp_aff_currency'); ?>"/>
    <br /><i>eg. USD, AUD, GBP etc. The affiliates will earn commission in this currency</i><br />
    </td></tr>  


    <tr valign="top"><td width="25%" align="left">
    <strong>Contact Email Address:</strong>
    </td><td align="left">
    <input name="wp_aff_contact_email" type="text" size="50" value="<?php echo get_option('wp_aff_contact_email'); ?>"/>
    <br /><i>The affiliates will be able to contact the admin using this email address</i><br />
    </td></tr>  

    <tr valign="top"><td width="25%" align="left">
    <strong>Default Landing Page:</strong>
    </td><td align="left">
    <input name="wp_aff_default_affiliate_landing_url" type="text" size="100" value="<?php echo get_option('wp_aff_default_affiliate_landing_url'); ?>"/>
    <br /><i>This is the URL where your affiliates will send traffic to by default. You can configure additional text links and banner ads from the <a href="admin.php?page=edit_banners">Add/Edit Ads</a> menu.</i><br />
    </td></tr>  
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Affiliate Login URL:</strong>
    </td><td align="left">
    <input name="wp_aff_login_url" type="text" size="100" value="<?php echo $wp_aff_login_url; ?>"/>
    <br /><i>The affiliates will be able to log in at this URL. You do not need to change it unless you have customized your affiliate login URL following <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=42" target="_blank">this instruction</a>.</i><br />
    </td></tr>  

    <tr valign="top"><td width="25%" align="left">
    <strong>Terms & Conditions URL:</strong>
    </td><td align="left">
    <input name="wp_aff_terms_url" type="text" size="100" value="<?php echo get_option('wp_aff_terms_url'); ?>"/>
    <br /><i>URL of the affiliate Terms and Conditions page. Leave empty if you do not have a Terms and Conditions page.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Enable Custom Field Tracking for Clicks:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_enable_clicks_custom_field" value="1" <?php echo get_option('wp_aff_enable_clicks_custom_field'); ?> />
    <br /><i>Enable this if you want to track a custom field for your clicks. <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=357" target="_blank">Read More Here</a></i><br />
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Do Not Allow Visitors to Signup:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_disable_visitor_signup" value="1" <?php echo get_option('wp_aff_disable_visitor_signup'); ?> />
    <br /><i>Check this box if you don't want to allow your visitors to be able to sign up as an affiliate. If you want to selectively create accounts for your affiliates from the admin dashboard then check this option.</i><br />
    </td></tr>

	<tr valign="top"><td width="25%" align="left">
	<strong>Make PayPal Email Address a Required Field:</strong>
	</td><td align="left">
	<input name="wp_aff_make_paypal_email_required" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_make_paypal_email_required')=='1'){echo 'checked="checked"';} ?> value="1"/><br />
	<i>If checked, the PayPal email address field will be a required field on the affiliate signup page (can be useful if you only want to pay affiliate commission via PayPal).</i><br />
	</td></tr>   
                    
    <tr valign="top"><td width="25%" align="left">
    <strong>Send Signup Notification to Admin:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_admin_notification" value="1" <?php echo $wp_aff_admin_notification; ?> />
    <br /><i>Check this box if you want to get notified via email when a new affiliate signs up.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Send Commission Notification:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_notify_affiliate_for_commission" value="1" <?php echo $wp_aff_notify_affiliate_for_commission; ?> />
    Send Notification to Affiliates
    <br /><i>Check this box if you want your affiliates to get notified via email when they receive a commission.</i><br />
    
    <input type="checkbox" name="wp_aff_notify_admin_for_commission" value="1" <?php if($wp_aff_platform_config->getValue('wp_aff_notify_admin_for_commission')=='1'){echo 'checked="checked"';} ?> />
    Send Notification to Admin
    <br /><i>Check this box if you want the admin of this site to get notified via email when an affiliate receives a commission.</i><br />    
    </td></tr>    

    <tr valign="top"><td width="25%" align="left">
    <strong>Show Buyer Details to Affiliates in the Affiliate Area:</strong>
    </td><td align="left">
    <input name="wp_aff_show_buyer_details_name_to_affiliates" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1'){echo 'checked="checked"';} ?> value="1"/> Show buyer name    
    <br /><input type="checkbox" name="wp_aff_show_buyer_details_to_affiliates" value="1" <?php echo $wp_aff_show_buyer_details_to_affiliates; ?> /> Show buyer email address   
    <br /><i>By default, the buyer details from a sale is only available to the site admin (this is to comply with the privacy policy of most websites). If you want the buyer details to be available to the affiliates then check this option (make sure you inform your buyers that their details will be revealed to 3rd party affiliates otherwise they can get very upset).</i><br />
    </td></tr>   
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Your Tips & Tricks HQ Affiliate ID:</strong>
    </td><td align="left">
    <input name="wp_aff_do_not_show_powered_by_section" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_do_not_show_powered_by_section')=='1'){echo 'checked="checked"';} ?> value="1"/>
    Turn off the affiliate ID display section (this will turn off the powered by section in the affiliate area)
    <br />
    <input name="wp_aff_user_affilate_id" type="text" size="15" value="<?php echo get_option('wp_aff_user_affilate_id'); ?>"/> (optional)
    <br /><i>If you have signed up for an affilate account on <a href="http://www.tipsandtricks-hq.com/affiliate_program" target="_blank">Tips and Tricks HQ</a> then you can specify your affiliate ID here to promote our product and get rewarded for it.</i><br />
    </td></tr>
    
    </table>
    </div></div>


	<div class="postbox">
	<h3><label for="title">Commission Settings</label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use Fixed Commission Amount:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_use_fixed_commission" value="1" <?php echo $wp_aff_use_fixed_commission; ?> />
    <br /><i>Check this box if you want to use fixed commission amount ($) rather than a percentage (%) value. Leave it unchecked to use a percentage value for affiliate commission calculation.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Commission Level:</strong>
    </td><td align="left">
    <input name="wp_aff_commission_level" type="text" size="4" value="<?php echo get_option('wp_aff_commission_level'); ?>"/>
    <br /><i>Only enter the number (do not use "%" or "$" sign). This is the default commission level for a newly joined affiliate. The commission level for individual affiliate can be changed by editing the affiliates details</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Use Automatic Commission Reversal:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_commission_reversal" value="1" <?php echo $wp_aff_commission_reversal; ?> />
    <br /><i>Check this box if you want to automatically reverse the commission for refunded products. Only works when used with <a href="http://www.tipsandtricks-hq.com/?p=1059" target="_blank">WP eStore</a></i><br />
    </td></tr>

    </table>
    </div></div>


	<div class="postbox">
	<h3><label for="title">2nd Tier Affiliate Settings (If you want to use a 2 tier affiliate model then use this section). <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=112" target="_blank"><strong>What is two-tier affiliate model?</strong></a></label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use 2 Tier Affiliate Model:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_use_2tier" value="1" <?php echo $wp_aff_use_2tier; ?> />
    <br /><i>Check this box if you want to use a 2 tier affiliate model (two level of affiliates).</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>2nd Tier Commission Level:</strong>
    </td><td align="left">
    <input name="wp_aff_2nd_tier_commission_level" type="text" size="4" value="<?php echo get_option('wp_aff_2nd_tier_commission_level'); ?>"/>
    <br /><i>Only enter the number (do not use "%" or "$" sign). The commission that the parent affiliate should get (eg. 10%). If you are using a fixed commission structure then enter the fixed amount here.</i><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Duration:</strong>
    </td><td align="left">
    <input name="wp_aff_2nd_tier_duration" type="text" size="5" value="<?php echo get_option('wp_aff_2nd_tier_duration'); ?>"/> Day(s)
    <br /><i>Number of days the parent affiliate receives commission (eg. 365 days). Leave empty for lifetime.</i><br />
    </td></tr>
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">reCAPTCHA Settings (If you want to use <a href="http://recaptcha.net/learnmore.html" target="_blank">reCAPTCHA</a> then you need to get reCAPTCHA API keys from <a href="http://recaptcha.net/whyrecaptcha.html" target="_blank">here</a> and use in the settings below)</label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use reCAPTCHA:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_use_recaptcha" value="1" <?php echo $wp_aff_use_recaptcha; ?> />
    <br /><i>Check this box if you want to use <a href="http://recaptcha.net/learnmore.html" target="_blank">reCAPTCHA</a> on the affiliate signs up form.</i><br />
    </td></tr>
    <tr valign="top"><td width="25%" align="left">
    <strong>Public Key:</strong>
    </td><td align="left">
    <input name="wp_aff_captcha_public_key" type="text" size="50" value="<?php echo get_option('wp_aff_captcha_public_key'); ?>"/>
    <br /><i>The public key for the reCAPTCHA API</i><br />
    </td></tr>  
    <tr valign="top"><td width="25%" align="left">
    <strong>Private Key:</strong>
    </td><td align="left">
    <input name="wp_aff_captcha_private_key" type="text" size="50" value="<?php echo get_option('wp_aff_captcha_private_key'); ?>"/>
    <br /><i>The private key for the reCAPTCHA API</i><br />
    </td></tr>
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">Affiliate Area/Center Related Options</label></h3>
	<div class="inside">
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Index Page Title:</strong>
    </td><td align="left">
    <input name="wp_aff_index_title" type="text" size="80" value="<?php echo $wp_aff_index_title; ?>"/>
    <br /><i>This title will appear on the index page of your affiliate center</i><br />
    </td></tr>  
    <tr valign="top"><td width="25%" align="left">
    <strong>Index Page Message:</strong>
    </td><td align="left">
    <textarea name="wp_aff_index_body" cols="80" rows="7"><?php echo $wp_aff_index_body; ?></textarea>
    <br /><i>This will appear on the index page of your affiliate center</i><br />
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    <strong>Welcome Page Message (optional):</strong>
    </td><td align="left">
    <textarea name="wp_aff_welcome_page_msg" cols="80" rows="3"><?php echo $wp_aff_welcome_page_msg; ?></textarea>
    <br /><i>If you want to add extra message/info for your affiliates then you can specify it here (HTML code is allowed). This message will appear on the welcome page (the page affiliates see right after they log in)</i><br />
    </td></tr>
        
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">3rd Party Shopping Cart Integration (You do not need to use these settings when using with the <a href="http://www.tipsandtricks-hq.com/?p=1059" target="_blank">WP eStore</a> plugin)</label></h3>
	<div class="inside">
	
	<br />
	<strong><i>(Only use this section if you have been instructed to do so from one of the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/" target="_blank">documentation pages</a>)</i></strong>
	<br /><br />
	
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">		
    <tr valign="top"><td width="25%" align="left">
    <strong>Enable 3rd Party Cart Integration:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_enable_3rd_party" value="1" <?php echo $wp_aff_enable_3rd_party; ?> />
    <br /><i>Check this box if you want to use this plugin with a 3rd Party Shopping Cart plugin.</i><br />
    </td></tr>
    <tr valign="top"><td width="25%" align="left"> 
    <strong>Sandbox Mode:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_sandbox_mode" value="1" <?php echo $wp_aff_sandbox_mode; ?> />
    <br /><i>Check this box if you want to test a transaction in Sandbox mode.</i><br />
    </td></tr>
    <tr valign="top"><td width="25%" align="left">
    <strong>PayPal PDT Identity Token:</strong>
    </td><td align="left">
    <input name="wp_aff_pdt_identity_token" type="text" size="100" value="<?php echo get_option('wp_aff_pdt_identity_token'); ?>"/>
    <br /><i>Specify your identity token in the text field above. If you need help finding your token then <a href="https://www.paypaltech.com/PDTGen/PDTtokenhelp.htm" target="_blank">click here</a>.</i><br />
    </td></tr>          
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">Additional Integration Options</label></h3>
	<div class="inside">

	<br />
	<strong><i>(Only use this section if you have been instructed to do so from one of the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/" target="_blank">documentation pages</a>)</i></strong>
	<br /><br />
		
    <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">    
    <tr valign="top"><td width="25%" align="left">
    <strong>Enable Remote POST:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_enable_remote_post" value="1" <?php echo $wp_aff_enable_remote_post; ?> />
    <br /><i>Check this box if you want to be able to award commission by sending a HTTP POST request to a URL or remotely track clicks.</i><br />
    </td></tr>
    
    <tr valign="top"><td width="25%" align="left">
    <strong>POST URL for Remote Click Tracking:</strong>
    </td><td align="left">
    <input name="wp_aff_remote_click_post_url" type="text" size="100" value="<?php echo $wp_aff_remote_click_post_url; ?>"/>
    <br /><i>This is the URL where you will need to POST your request to track clicks remotely.</i><br />
    </td></tr>  
        
    <tr valign="top"><td width="25%" align="left">
    <strong>POST URL for Sale/Commission Awarding:</strong>
    </td><td align="left">
    <input name="wp_aff_comm_post_url" type="text" size="100" value="<?php echo $wp_aff_comm_post_url; ?>"/>
    <br /><i>This is the URL where you will need to POST your request to award commission/sale tracking.</i><br />
    </td></tr>     
        
    <tr valign="top"><td width="25%" align="left">
    <strong>POST URL for Lead Capture:</strong>
    </td><td align="left">
    <input name="wp_aff_lead_capture_post_url" type="text" size="100" value="<?php echo $wp_aff_lead_capture_post_url; ?>"/>
    <br /><i>This is the URL where you will need to POST your request to capture a lead from your script.</i><br />
    </td></tr> 
            
    <tr valign="top"><td width="25%" align="left">
    <strong>Secret Word:</strong>
    </td><td align="left">
    <input name="wp_aff_secret_word_for_post" type="text" size="30" value="<?php echo $wp_aff_secret_word_for_post; ?>"/>
    <br /><i>This secret word will be used to verify any request sent to the POST URL. You can change this code to something random.</i><br />
    </td></tr>  
    </table>
    </div></div>
    
    <div class="postbox">
    <h3><label for="title">Lead Capture Related Settings</label></h3>
	<div class="inside">    	    
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">  
	  
	<tr valign="top"><td width="25%" align="left">
    <strong>Enable Contact Form 7 Lead Capture:</strong>
    </td><td align="left">
    <input name="wp_aff_enable_wpcf7_lead_capture" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_enable_wpcf7_lead_capture')=='1'){echo 'checked="checked"';} ?> value="1"/><br />		    
    <i>Check this option if you want to capture leads with the Contact Form 7 plugin. <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=215" target="_blank">Read More Here</a></i>
    <br />
	</td>
	</tr> 
                       
    <tr valign="top"><td width="25%" align="left">
    <strong>Lead Capture Form Exclusion List (optional)</strong>
    </td><td align="left">
    <input name="wp_aff_wp_cf7_form_exclusion_list" type="text" size="100" value="<?php echo $wp_aff_platform_config->getValue('wp_aff_wp_cf7_form_exclusion_list'); ?>"/>
    <br /><i>If you have multiple contact forms and you want to exclude a contact form (example, your general contact form) from the lead capture pool then specify the ID of that form (example, 2500) in the above field. You can add multiple form IDs separated by comma.</i><br />
    </td></tr>   
                           
	</table>
	</div></div>  
	    
    <div class="postbox">
    <h3><label for="title">Gravity Forms Integration Related Settings</label></h3>
	<div class="inside">    	    
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">  
	  
	<tr valign="top"><td width="25%" align="left">
    <strong>Enable Gravity Forms PayPal Tracking:</strong>
    </td><td align="left">
    <input name="wp_aff_enable_gf_paypal" type="checkbox"  <?php if($wp_aff_platform_config->getValue('wp_aff_enable_gf_paypal')=='1'){echo 'checked="checked"';} ?> value="1"/><br />		    
    <i>Check this option if you want to track and award commission for customers who make a purchase via the Gravity Forms PayPal addon on your site. <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=586" target="_blank">Read More Here</a></i>
    <br />
	</td>
	</tr> 

	<tr valign="top"><td width="25%" align="left">
    <strong>Gravity Forms Lead Capture:</strong>
    </td><td align="left">
    <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=385" target="_blank">How to capture lead with Gravity Forms</a>
	</td>
	</tr> 
                           
	</table>
	</div></div>  
		    
    <div class="postbox">
    <h3><label for="title">Testing and Debugging Settings</label></h3>
	<div class="inside">    	    
		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">    
		    	<tr valign="top"><td width="25%" align="left">
		    	<strong>Enable Debug:</strong>
		    	</td><td align="left">
		    	<input name="wp_aff_enable_debug" type="checkbox"  <?php $wp_aff_enable_debug = get_option('wp_aff_enable_debug');echo ($wp_aff_enable_debug)?'checked="checked"':''?> value="1"/><br />
		    	<i>If checked, debug output will be written to log file. This can come in handy when troubleshooting.</i><br /><br />
				You can check the debug log file by clicking on the link below (The log files can be viewed using any text editor):
		    	<li style="margin-left:15px;"><a href="<?php echo WP_AFF_PLATFORM_URL."/wp_affiliate_debug.log"; ?>" target="_blank">wp_affiliate_debug.log file</a></li>		    	
		    	<li style="margin-left:15px;"><a href="<?php echo WP_AFF_PLATFORM_URL."/api/ipn_handle_debug.log"; ?>" target="_blank">ipn_handle_debug.log file</a> (for plain PayPal button integration)</li>
			    </td>
                </tr>            
		</table>
	</div></div>       
        
    <div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
    </div>
    
    </form>

    <?php
}

?>