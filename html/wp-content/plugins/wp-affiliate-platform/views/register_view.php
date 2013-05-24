<?php
function wp_aff_register_view()
{
	if(!isset($_POST['afirstname'])){$_POST['afirstname']='';}
	if(!isset($_POST['alastname'])){$_POST['alastname']='';}
	if(!isset($_POST['acompany'])){$_POST['acompany']='';}
	if(!isset($_POST['awebsite'])){$_POST['awebsite']='';}
	if(!isset($_POST['aemail'])){$_POST['aemail']='';}
	if(!isset($_POST['paypal_email'])){$_POST['paypal_email']='';}
	if(!isset($_POST['tax_id'])){$_POST['tax_id']='';}
	if(!isset($_POST['astreet'])){$_POST['astreet']='';}
	if(!isset($_POST['atown'])){$_POST['atown']='';}
	if(!isset($_POST['astate'])){$_POST['astate']='';}
	if(!isset($_POST['apostcode'])){$_POST['apostcode']='';}
	if(!isset($_POST['aphone'])){$_POST['aphone']='';}
	if(!isset($_POST['user_name'])){$_POST['user_name']='';}
	if(!isset($_POST['apayable'])){$_POST['apayable']='';}
	
	$output = "";
	ob_start();	
	echo wp_aff_view_get_navbar();
	echo '<div id="wp_aff_inside">';
	$retval = "";
	if(get_option('wp_aff_disable_visitor_signup')){//Affiliate self signup is disabled
		echo '<p style="color:red;" align="center"><strong>'.AFF_ACCOUNT_SIGNUP_DISABLED.'</strong></p>';
    }else{	
		$retval = wp_aff_signup_form_processing_code();
		if($retval != "processed"){
			wp_aff_show_signup_form();
		}
    }
	echo '</div>';
	echo wp_aff_view_get_footer();	
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;	
}

function wp_aff_show_signup_form($recaptcha_error='')
{	
	global $wp_aff_platform_config;
	include_once('countries.php');		
	$login_url = wp_aff_view_get_url_with_separator("login");		
?>

<script language="JavaScript" type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/js/jquery.validate.min.js'; ?>"></script>
<script type="text/javascript"> 
/* <![CDATA[ */
  jQuery(document).ready(function($){	  
    $.validator.addMethod("username", function(value, element) {
        return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
    }, "Username must contain only letters, numbers, or underscore.");

    $("#regForm").validate();
  });
/*]]>*/  
</script>
  
    <h3 class="wp_aff_title"><?php echo AFF_SIGNUP_PAGE_TITLE; ?></h3>
    <p><?php echo AFF_SIGNUP_PAGE_MESSAGE;?></p>

	 <?php
      if (isset($_GET['msg'])) {
	  $msg = mysql_real_escape_string($_GET['msg']);
	  echo "<div class=\"wp_aff_error_msg\">$msg</div>";
	  }
	  ?>

    <!-- Start Registration Form -->
      <form action="" method="post" name="regForm" id="regForm" >
        <table width="95%" border="0" cellpadding="3" cellspacing="3" class="forms">
        
          <tr> 
            <td><b><?php echo AFF_FIRST_NAME; ?>: *</b></td>
            <td><b> 
              <input type="text" name="afirstname" size="20" value="<?php echo $_POST['afirstname']; ?>" class="required">
              </b></td>
          </tr>
          <tr> 
            <td><b><?php echo AFF_LAST_NAME; ?>: *</b></td>
            <td><b> 
              <input type="text" name="alastname" size="20" value="<?php echo $_POST['alastname']; ?>" class="required">
              </b></td>
          </tr>
          <tr> 
            <td><b><?php echo AFF_COMPANY; ?>:</b></td>
            <td><b> 
              <input type="text" name="acompany" size="20" value="<?php echo $_POST['acompany']; ?>">
              </b></td>
          </tr>
           <tr> 
            <td><b><?php echo AFF_WEBSITE; ?>:</b></td>
            <td><b> 
              <input type="text" name="awebsite" size="20" value="<?php echo $_POST['awebsite']; ?>">
              </b></td>
          </tr>
          <tr> 
            <td><b><?php echo AFF_EMAIL; ?>: *</b></td>
            <td><b> 
              <input type="text" name="aemail" size="20" value="<?php echo $_POST['aemail']; ?>" class="required email">
              </b></td>
          </tr>
          <tr>
          	<?php 
          	if($wp_aff_platform_config->getValue('wp_aff_make_paypal_email_required')=='1')
          	{
            	echo '<td><b>'.AFF_PAYPAL_EMAIL.': *</b></td>';
            	echo '<td><b><input type="text" name="paypal_email" size="20" value="'.$_POST['paypal_email'].'" class="required email"></b></td>';             		
          	}
          	else
          	{
            	echo '<td><b>'.AFF_PAYPAL_EMAIL.': </b></td>';
            	echo '<td><b><input type="text" name="paypal_email" size="20" value="'.$_POST['paypal_email'].'"></b></td>';          		
          	}
          	?>
          </tr>
          <tr> 
            <td><b><?php echo AFF_TAX_ID; ?>:</b></td>
            <td><b> 
              <input type="text" name="tax_id" size="20" value="<?php echo $_POST['tax_id']; ?>">
              </b></td>
          </tr>          
          <tr> 
            <td><b><?php echo AFF_ADDRESS; ?>:</b></td>
            <td><b> 
              <input type="text" name="astreet" size="20" value="<?php echo $_POST['astreet']; ?>">
              </b></td>
          </tr>
          <tr> 
            <td><b><?php echo AFF_TOWN; ?>:</b></td>
            <td><b> 
              <input type="text" name="atown" size="20" value="<?php echo $_POST['atown']; ?>">
              </b></td>
          </tr>
          <tr> 
            <td><b><?php echo AFF_STATE; ?>:</b></td>
            <td><b> 
              <input type="text" name="astate" size="20" value="<?php echo $_POST['astate']; ?>">
              </b></td>
          </tr>
          <tr> 
            <td><b><?php echo AFF_ZIP; ?>:</b></td>
            <td><b> 
              <input type="text" name="apostcode" size="20" value="<?php echo $_POST['apostcode']; ?>">
              </b></td>
          </tr>
          <tr> 
            <td><b><?php echo AFF_COUNTRY; ?>:</b></td>
            <td><b> 
             <select name="acountry" class="user-select">
<?php
            foreach($GLOBALS['countries'] as $key => $country)
                print '<option value="'.$key.'" '.($key == "US" ? 'selected' : '').'>'.$country.'</option>'."\n";
?>
              </select>
              </b></td>
          </tr>

          <tr> 
            <td><b><?php echo AFF_PHONE; ?>:</b></td>
            <td><b> 
              <input type="text" name="aphone" size="20" value="<?php echo $_POST['aphone']; ?>">
              </b></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>


          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2"><h4><strong><?php echo AFF_LOGIN_DETAILS; ?></strong></h4></td>
          </tr>
          <tr> 
            <td class="randomadjust"><?php echo AFF_USERNAME; ?><span class="required"><font color="#CC0000">*</font></span></td>
            <td><input name="user_name" type="text" id="user_name" class="required username" minlength="5" value="<?php echo $_POST['user_name']; ?>" > 
            	<br /><span style="color:red; font: bold 12px verdana; " id="checkid" ></span>
            </td>
            
          <tr>
          	<td></td>
          	<td>
              <input name="btnAvailable" type="button" class="button" id="btnAvailable" onclick='jQuery(document).ready(function($){$("#checkid").html("<?php echo AFF_SI_PLEASE_WAIT; ?>"); $.get("<?php echo WP_AFF_PLATFORM_URL.'/affiliates/checkuser.php'; ?>",{ cmd: "check", user: $("#user_name").val() } ,function(data){  $("#checkid").html(data); }); });' value="<?php echo AFF_AVAILABILITY_BUTTON_LABEL; ?>">                        	
          	</td>
          </tr>  			  	     
            
          </tr>
          <tr>
            <td><?php echo AFF_PASSWORD; ?><span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="wp_aff_pwd" type="password" class="required password user-edit" minlength="5" id="wp_aff_pwd"></td>
          </tr>
          <tr> 
            <td><?php echo AFF_RETYPE_PASSWORD; ?><span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="wp_aff_pwd2"  id="wp_aff_pwd2" class="required password user-edit" type="password" minlength="5" equalto="#wp_aff_pwd"></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <?php
          if (get_option('wp_aff_use_recaptcha'))
          {
              	echo '<tr>
                  <td width="22%"><strong>'.AFF_IMAGE_VERIFICATION.' </strong></td>
                  <td width="78%">';
		        if (!function_exists('_recaptcha_qsencode'))
		        {
		            require_once('recaptchalib.php');
		        }
		        $publickey = get_option('wp_aff_captcha_public_key');
		        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){  
              		echo recaptcha_get_html($publickey,$recaptcha_error,true);
		        }
		        else{
		        	echo recaptcha_get_html($publickey,$recaptcha_error);
		        }
              	echo '</td></tr>';
          }
          ?>

        </table>
        <p align="center">
         <?php
        if(get_option('wp_aff_disable_visitor_signup'))
        {
        	echo '<p style="color:red;" align="center"><strong>'.AFF_ACCOUNT_SIGNUP_DISABLED.'</strong></p>';
        }
        else
        {
            $terms_url = get_option('wp_aff_terms_url');
	        if (!empty($terms_url))
	        {
    			$terms = "<a href=\"$terms_url\" target=\"_blank\"><u>".AFF_TERMS_AND_COND."</u></a>";    			        	
	        	echo '<label for="affiliate-t-and-c">'.AFF_TERMS_AGREE.$terms.'</label><input type="checkbox" name="affiliate-t-and-c" class="affiliate-t-and-c required" value="" /><br />';
	            //echo AFF_YOU_AGREE_TO.' <strong><a href="'.$terms_url.'" target="_blank">'.AFF_TERMS_AND_COND.'</a></strong><br /><br />';
	        }          	
        	echo '<input name="doRegister" type="submit" id="doRegister" class="button" value="'.AFF_SIGN_UP_BUTTON_LABEL.'">';      	
        }
        ?>
        </p>
      </form>

      <p>&nbsp;</p>
      <p><?php echo AFF_ALREADY_MEMBER; ?>? <img src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/login.png'; ?>" /> <a style="color:#CC0000" href="<?php echo $login_url; ?>"><?php echo AFF_LOGIN_HERE; ?></a></p>
<?php
}

function wp_aff_signup_form_processing_code()
{
	$login_url = wp_aff_view_get_url_with_separator("login");
	if(isset($_POST['doRegister']))
	{ 

		$_POST = wp_aff_singup_details_filter($_POST);
	
	    if (get_option('wp_aff_use_recaptcha'))
	    {
	        if (!function_exists('_recaptcha_qsencode'))
	        {
	            require_once('recaptchalib.php');
	        }
	        $privatekey = get_option('wp_aff_captcha_private_key');
	        $resp = recaptcha_check_answer ($privatekey,
	                                          $_SERVER["REMOTE_ADDR"],
	                                          $_POST["recaptcha_challenge_field"],
	                                          $_POST["recaptcha_response_field"]);
	    
	        if (!$resp->is_valid) {
	        	$recaptcha_error = AFF_IMAGE_VERIFICATION_FAILED;
	        	$_GET['msg'] = AFF_IMAGE_VERIFICATION_FAILED;
	        	return;
	        }
	    }
	
		//=================
		include_once(ABSPATH.WPINC.'/class-phpass.php');
		$wp_hasher = new PasswordHash(8, TRUE);
		$password = $wp_hasher->HashPassword($_POST['wp_aff_pwd']);
				
		$user_ip = $_SERVER['REMOTE_ADDR'];
		$host  = $_SERVER['HTTP_HOST'];
		$host_upper = strtoupper($host);
		$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$activ_code = rand(1000,9999);
		$aemail = mysql_real_escape_string($_POST['aemail']);
		$user_name = mysql_real_escape_string($_POST['user_name']);
		//============
	
		$userid = mysql_real_escape_string($_POST['user_name']);
	    global $wpdb;
	    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	    $result = $wpdb->get_results("SELECT refid FROM $affiliates_table_name where refid='$userid'", OBJECT);
	
	    if($result)
		{
			$_GET['msg'] = AFF_SI_USEREXISTS;
			return;				
		}
        // save and send notification email
        // check if referred by another affiliate
        $referrer = "";
        if (!empty($_SESSION['ap_id']))
        {
            $referrer = $_SESSION['ap_id'];
        }
        else if (isset($_COOKIE['ap_id']))
        {
            $referrer = $_COOKIE['ap_id'];
        }

		$commission_level = get_option('wp_aff_commission_level');
		$date = (date ("Y-m-d"));

        global $wpdb;
        $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
        $updatedb = "INSERT INTO $affiliates_table_name (refid,pass,company,firstname,lastname,website,email,payableto,street,town,state,postcode,country,phone,date,paypalemail,commissionlevel,referrer,tax_id) VALUES ('".$_POST['user_name']."', '".$password."', '".$_POST['acompany']."', '".$_POST['afirstname']."', '".$_POST['alastname']."', '".$_POST['awebsite']."', '".$_POST['aemail']."', '".$_POST['apayable']."', '".$_POST['astreet']."', '".$_POST['atown']."', '".$_POST['astate']."', '".$_POST['apostcode']."', '".$_POST['acountry']."', '".$_POST['aphone']."', '$date','".$_POST['paypal_email']."','".$commission_level."','".$referrer."', '".$_POST['tax_id']."')";
        $results = $wpdb->query($updatedb);

        $affiliate_login_url = get_option('wp_aff_login_url');

        $email_subj = get_option('wp_aff_signup_email_subject');			
        $body_sign_up = get_option('wp_aff_signup_email_body');	
        $from_email_address = get_option('wp_aff_senders_email_address');
        $headers = 'From: '.$from_email_address . "\r\n";	       		
        
        $tags1 = array("{user_name}","{email}","{password}","{login_url}");			
        $vals1 = array($user_name,$aemail,$_POST['wp_aff_pwd'],$affiliate_login_url);	
        $vals2 = array($user_name,$aemail,"********",$affiliate_login_url);		        
        $aemailbody = str_replace($tags1,$vals1,$body_sign_up);	
        $admin_email_body = str_replace($tags1,$vals2,$body_sign_up);		

        if (get_option('wp_aff_admin_notification'))
        {
             $admin_email_subj = "New affiliate sign up notification";
             $admin_contact_email = get_option('wp_aff_contact_email');
             if(empty($admin_contact_email)){
             	$admin_contact_email = $from_email_address;
             }
             wp_mail($admin_contact_email, $admin_email_subj, $admin_email_body);
             wp_affiliate_log_debug("Affiliate signup notification email successfully sent to the admin: ".$admin_contact_email,true);
        }
        wp_mail($_POST['aemail'], $email_subj, $aemailbody, $headers);
        wp_affiliate_log_debug("Welcome email successfully sent to the affiliate: ".$_POST['aemail'],true);
        
        //Check and do autoresponder signup
		wp_aff_global_autoresponder_signup($_POST['afirstname'],$_POST['alastname'],$_POST['aemail']);
        
		//$redirect_page = wp_aff_view_get_url_with_separator("thankyou");
		//echo '<meta http-equiv="refresh" content="0;url='.$redirect_page.'" />';
		//exit();		
		
  	    echo "<h2 class='wp_aff_title'>".AFF_THANK_YOU."</h2> <p class='message'>".AFF_REGO_COMPLETE."</p>";
  	    echo '<a style="color:#CC0000" href="'.$login_url.'">'.AFF_LOGIN_HERE.'</a>';
	  	return "processed";
	}	
}

function wp_aff_singup_details_filter($arr) 
{
	return array_map('mysql_real_escape_string', $arr);
}
?>