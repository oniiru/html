<?php
function wp_affiliate_auto_responder_settings()
{
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if (isset($_POST['info_update']))
    {
        $errors = "";
		if (isset($_POST['aweber_make_connection']))
		{
			if($wp_aff_platform_config->getValue('wp_aff_aweber_authorize_status') != 'authorized'){
		        $authorization_code = trim($_POST['aweber_auth_code']);	        
		        if (!class_exists('AWeberAPI')){
					include_once('lib/auto-responder/aweber_api/aweber_api.php');
		        }        
				$auth = AWeberAPI::getDataFromAweberID($authorization_code);
				list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $auth;
		        $wp_aff_aweber_access_keys = array(
		            'consumer_key'    => $consumerKey,
		            'consumer_secret' => $consumerSecret,
		            'access_key'      => $accessKey,
		            'access_secret'   => $accessSecret,
		        );	
		        $wp_aff_platform_config->setValue('wp_aff_aweber_access_keys', $wp_aff_aweber_access_keys);		        	
				//var_dump($wp_aff_aweber_access_keys);
				
				if ($wp_aff_aweber_access_keys['access_key']){
					try {
		            	$aweber = new AWeberAPI($consumerKey, $consumerSecret);
		            	$account = $aweber->getAccount($accessKey, $accessSecret);
		        	} catch (AWeberException $e) {
		            	$account = null;
		        	}
		        	if (!$account){
		            	//$this->deauthorize();//TODO - remove the keys
						$errors = 'AWeber authentication failed! Please try connecting again.';            	
		        	}
		        	else{
		        		$wp_aff_platform_config->setValue('wp_aff_aweber_authorize_status', 'authorized');
		        		$_POST['wp_aff_use_new_aweber_integration'] = '1';//Set the wp_aff_use_new_aweber_integration flag to enabled
				        echo '<div id="message" class="updated fade"><p><strong>';
				        echo 'AWeber authorization success!';
				        echo '</strong></p></div>';      	        		
		        	}			
				}
				else{
					$errors = 'You need to specify a valid authorization code to establish an AWeber API connection';
				}
			}
			else{//Remove existing connection
		        $wp_aff_aweber_access_keys = array(
		            'consumer_key' => '',
		            'consumer_secret' => '',
		            'access_key' => '',
		            'access_secret' => '',
		        );		
		        $wp_aff_platform_config->setValue('wp_aff_aweber_access_keys', $wp_aff_aweber_access_keys);
		        $wp_aff_platform_config->setValue('wp_aff_aweber_authorize_status', '');
		        $_POST['wp_aff_use_new_aweber_integration'] = '';//Set the wp_aff_use_new_aweber_integration flag to disabled
				echo '<div id="message" class="updated fade"><p><strong>';
				echo 'AWeber connection removed!';
				echo '</strong></p></div>';  		        	
			}			
		}
		    	
        update_option('wp_aff_enable_aweber_int', ($_POST['wp_aff_enable_aweber_int']=='1') ? '1':'' );
        update_option('wp_aff_aweber_list_name', trim($_POST["wp_aff_aweber_list_name"]));
        $wp_aff_platform_config->setValue('wp_aff_use_new_aweber_integration', ($_POST['wp_aff_use_new_aweber_integration']=='1') ? '1':'' );
        
        update_option('wp_aff_use_mailchimp', ($_POST['wp_aff_use_mailchimp']=='1') ? '1':'' );
        //update_option('wp_aff_enable_global_chimp_int', ($_POST['wp_aff_enable_global_chimp_int']=='1') ? '1':'' );
        update_option('wp_aff_chimp_list_name', trim($_POST["wp_aff_chimp_list_name"]));
        update_option('wp_aff_chimp_api_key', trim($_POST["wp_aff_chimp_api_key"]));
        //update_option('wp_aff_chimp_user_name', trim($_POST["wp_aff_chimp_user_name"]));
       // update_option('wp_aff_chimp_pass', trim($_POST["wp_aff_chimp_pass"]));
        update_option('wp_aff_mailchimp_disable_double_optin', ($_POST['wp_aff_mailchimp_disable_double_optin']=='1') ? '1':'' );
        update_option('wp_aff_signup_date_field_name', trim($_POST["wp_aff_signup_date_field_name"]));

        update_option('wp_aff_use_getResponse', ($_POST['wp_aff_use_getResponse']=='1') ? '1':'' );
        //update_option('wp_aff_enable_global_getResponse_int', ($_POST['wp_aff_enable_global_getResponse_int']=='1') ? '1':'' );
        update_option('wp_aff_getResponse_campaign_name', trim($_POST["wp_aff_getResponse_campaign_name"]));
        update_option('wp_aff_getResponse_api_key', trim($_POST["wp_aff_getResponse_api_key"]));

        $wp_aff_platform_config->saveConfig();
        
        if(!empty($errors)){
	        echo '<div id="message" class="error"><p>';
	        echo $errors;
	        echo '</p></div>';         	
        }else{    
	        echo '<div id="message" class="updated fade"><p><strong>';
	        echo 'Autoresponder Options Updated!';
	        echo '</strong></p></div>';
        }
    }

	?>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

	<div class="postbox">
	<h3><label for="title">AWeber Settings (<a href="http://www.tipsandtricks-hq.com/wordpress-affiliate//?p=453" target="_blank">AWeber Integration Instructions</a>)</label></h3>
	<div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Enable AWeber Signup:</strong>
    </td><td align="left">
    <input name="wp_aff_enable_aweber_int" type="checkbox"<?php if(get_option('wp_aff_enable_aweber_int')!='') echo ' checked="checked"'; ?> value="1"/>
    <br /><i>When checked the plugin will automatically sign up the affiliates to your AWeber List specified below.</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>AWeber List Name:</strong>
    </td><td align="left">
    <input name="wp_aff_aweber_list_name" type="text" size="40" value="<?php echo get_option('wp_aff_aweber_list_name'); ?>"/>
    <br /><i>The name of the AWeber list where the affiliates will be signed up to (eg. listname@aweber.com)</i><br /><br />
    </td></tr>
    </table>
    
    <div style="border-bottom: 1px solid #dedede; height: 10px"></div>
    <table class="form-table">
    
    <tr valign="top"><td width="25%" align="left">
    Use the New AWeber Integration Option:
    </td><td align="left">    
    <input name="wp_aff_use_new_aweber_integration" type="checkbox"<?php if($wp_aff_platform_config->getValue('wp_aff_use_new_aweber_integration')!='') echo ' checked="checked"'; ?> value="1"/>
    <br /><p class="description">When checked the plugin will use the new AWeber integration method which uses the AWeber API (this method is recommended over the old method that uses the email parser).</p>
    </td></tr>
        
    <tr valign="top"><td width="25%" align="left">
    Step 1: Get Your AWeber Authorization Code:
    </td><td align="left">    
    <a href="https://auth.aweber.com/1.0/oauth/authorize_app/999d6172" target="_blank">Click here to get your authorization code</a>
    <br /><p class="description">Clicking on the above link will take you to the AWeber site where you will need to log in using your AWeber username and password. Then give access to the Tips and Tricks HQ AWeber app.</p>
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Step 2: Paste in Your Authorization Code:
    </td><td align="left">
    <input name="aweber_auth_code" type="text" size="140" value=""/>
    <br /><p class="description">Paste the long authorization code that you got from AWeber in the above field.</p>
    </td></tr>    

	<tr valign="top"><td colspan="2" align="left">
	<?php 
	if($wp_aff_platform_config->getValue('wp_aff_aweber_authorize_status') == 'authorized'){
		echo '<input type="submit" name="aweber_make_connection" value="Remove Connection" class= "button button" />';
	}else{
		echo '<input type="submit" name="aweber_make_connection" value="Make Connection" class= "button-primary" />';
	}
	?>
	</td></tr>	

    </table>    
    </div></div>

	<div class="postbox">
	<h3><label for="title">MailChimp Settings (<a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=455" target="_blank">MailChimp Integration Instructions</a>)</label></h3>
	<div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use MailChimp AutoResponder:</strong>
    </td><td align="left">
    <input name="wp_aff_use_mailchimp" type="checkbox"<?php if(get_option('wp_aff_use_mailchimp')!='') echo ' checked="checked"'; ?> value="1"/>
    <br /><i>Check this if you want to use MailChimp Autoresponder service.</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>MailChimp List Name:</strong>
    </td><td align="left">
    <input name="wp_aff_chimp_list_name" type="text" size="30" value="<?php echo get_option('wp_aff_chimp_list_name'); ?>"/>
    <br /><i>The name of the MailChimp list where the affiliates will be signed up to (e.g. Affiliate List)</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>MailChimp API Key:</strong>
    </td><td align="left">
    <input name="wp_aff_chimp_api_key" type="text" size="50" value="<?php echo get_option('wp_aff_chimp_api_key'); ?>"/>
    <br /><i>The API Key of your MailChimp account (can be found under the "Account" tab). If you do not have the API Key then you can use the username and password option below.</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Disable Double Opt-In:</strong>
    </td><td align="left">
    <input name="wp_aff_mailchimp_disable_double_optin" type="checkbox"<?php if(get_option('wp_aff_mailchimp_disable_double_optin')!='') echo ' checked="checked"'; ?> value="1"/>
    Do not send double opt-in confirmation email  
    <br /><i>Use this checkbox if you do not wish to use the double opt-in option. Please note that abusing this option may cause your MailChimp account to be suspended.</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>Signup Date Field Name (optional):</strong>
    </td><td align="left">
    <input name="wp_aff_signup_date_field_name" type="text" size="30" value="<?php echo get_option('wp_aff_signup_date_field_name'); ?>"/>
    <br /><i>If you have configured a signup date field for your mailchimp list then specify the name of the field here (example: SIGNUPDATE). <a href="http://kb.mailchimp.com/article/how-do-i-create-a-date-field-in-my-signup-form" target="_blank">More Info</a></i><br /><br />
    </td></tr>
            
    </table>
    </div></div>

	<div class="postbox">
	<h3><label for="title">GetResponse Settings (<a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=457" target="_blank">GetResponse Integration Instructions</a>)</label></h3>
	<div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Use GetResponse AutoResponder:</strong>
    </td><td align="left">
    <input name="wp_aff_use_getResponse" type="checkbox"<?php if(get_option('wp_aff_use_getResponse')!='') echo ' checked="checked"'; ?> value="1"/>
    <br /><i>Check this if you want to use GetResponse.</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>GetResponse Campaign Name:</strong>
    </td><td align="left">
    <input name="wp_aff_getResponse_campaign_name" type="text" size="30" value="<?php echo get_option('wp_aff_getResponse_campaign_name'); ?>"/>
    <br /><i>The name of the GetResponse campaign where the affiliates will be signed up to (e.g. marketing)</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    <strong>GetResponse API Key:</strong>
    </td><td align="left">
    <input name="wp_aff_getResponse_api_key" type="text" size="50" value="<?php echo get_option('wp_aff_getResponse_api_key'); ?>"/>
    <br /><i>The API Key of your GetResponse account (can be found inside your GetResponse Account). When you use the API key option make sure to enable the API key in your GetResponse account (by default it is off).</i><br /><br />
    </td></tr>

    </table>
    </div></div>
    
    <div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Update'); ?> &raquo;" />
    </div>
    </form>
    <?php
}
?>