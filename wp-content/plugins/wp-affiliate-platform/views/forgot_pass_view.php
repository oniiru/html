<?php
function wp_aff_forgot_pass_view()
{
	$output .= wp_aff_view_get_navbar();
	$output .= '<div id="wp_aff_inside">';
	$output .= wp_aff_show_forgot_pass_page();
	$output .= '</div>';
	$output .= wp_aff_view_get_footer();
	return $output;
}

function wp_aff_show_forgot_pass_page()
{
	if ($_POST['doReset']==AFF_RESET_BUTTON_LABEL)
	{
	    $user_email = mysql_real_escape_string($_POST['user_email']);
	
	    //check if activ code and user is valid as precaution
	    global $wpdb;
	    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	    $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where email='$user_email'", OBJECT);
	
	    // Match row found with more than 1 results  - the user is authenticated. 
	    if (!$result) 
	    {
	    	$msg = AFF_FORGOT_PASS_NO_ACCOUNT_EXISTS;
		}
		else
		{
			//generate 6 digit random number
			$new_pass = rand(100000,999999);

			//Hash the password
			include_once(ABSPATH.WPINC.'/class-phpass.php');
			$wp_hasher = new PasswordHash(8, TRUE);
			$password = $wp_hasher->HashPassword($new_pass);				
			
			//Set the new password here
			$user_id = $result->refid;
			$updatedb = "UPDATE $affiliates_table_name SET pass = '".$password."' WHERE refid = '".$user_id."'";
			$results = $wpdb->query($updatedb);
		
			//send email
	        $aemailbody =
	        "Here is your new password details ...\n
	        User Email: $user_email \n
	        User ID: $user_id \n
	        Password: $new_pass \n
	        
	        Thank You
	        
	        Administrator
	        ______________________________________________________
	        THIS IS AN AUTOMATED RESPONSE.
	        ***DO NOT RESPOND TO THIS EMAIL****
	        ";
	        $email_subj = "New Affiliate Password";
	        $from_email_address = get_option('wp_aff_senders_email_address');
	        $attachment = '';
	        
	        $headers = 'From: '.$from_email_address . "\r\n";
	        wp_mail($user_email, $email_subj, $aemailbody, $headers);
	        $msg = AFF_FORGOT_PASS_PASSWORD_HAS_BEEN_RESET;
		}		      	       
	}	
	?>
	
<!-- Load jQuery Validation -->
<script language="JavaScript" type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/js/jquery.validate.min.js'; ?>"></script>
<script type="text/javascript"> 
/* <![CDATA[ */
  jQuery(document).ready(function($){
	    $("#forgotPassForm").validate();
	  });
/*]]>*/  
</script>
	  
    <h3 class="wp_aff_title"><?php echo AFF_FORGOT_PASS_PAGE_TITLE; ?></h3>
    
    <?php 
    if(!empty($msg))
    {
    	$output .= "<p><div class='error message'>".$msg."</div></p>";
    }
    ob_start();
    ?>
      <p><?php echo AFF_FORGOT_PASS_MESSAGE; ?></p>

      <!-- Start Forgot Pwd Form -->
      <form action="" method="post" name="actForm" id="forgotPassForm" >

        <table width="65%" border="0" cellpadding="4" cellspacing="4" class="loginform">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="36%"><?php echo AFF_FORGOT_PASS_EMAIL; ?></td>
            <td width="64%"><input name="user_email" type="text" class="required email" id="wp_aff_txtbox" size="30"></td>
          </tr>
          <tr> 
            <td colspan="2"> <div align="center"> 
                <p> 
                  <input name="doReset" type="submit" class="button" id="doLogin3" value="<?php echo AFF_RESET_BUTTON_LABEL; ?>">
                </p>
                <p><img src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/register.png'; ?>" /> <a style="color:#CC0000;" href="<?php echo wp_aff_view_get_url_with_separator("signup"); ?>"><?php echo AFF_AFFILIATE_SIGN_UP_LABEL; ?></a><font color="#EEE">
                  |</font> <img src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/login.png'; ?>" /> <a href="<?php echo wp_aff_view_get_url_with_separator("login"); ?>"><?php echo AFF_LOGIN_PAGE_LINK_TEXT; ?></a></p>
              </div></td>
          </tr>
        </table>

      </form>	
      <?php	 
	$output .= ob_get_contents();
	ob_end_clean();
	
	return $output;       
}
?>