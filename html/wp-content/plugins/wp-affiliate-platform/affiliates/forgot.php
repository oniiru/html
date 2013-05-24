<?php
include_once ('misc_func.php');
/******************* ACTIVATION BY FORM**************************/
if (isset($_POST['doReset']) && $_POST['doReset']==AFF_RESET_BUTTON_LABEL)
{
    $user_email = mysql_real_escape_string($_POST['user_email']);

    //check if activ code and user is valid as precaution
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TABLE;
    $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where email='$user_email'", OBJECT);

    // Match row found with more than 1 results  - the user is authenticated. 
    if (!$result) {
	   $msg = urlencode("Error - Sorry no such account exists or registered.");
	   header("Location: forgot.php?msg=$msg");
	   exit();
	}
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
        $msg = urlencode("Your account password has been reset and a new password has been sent to your email address.");
        header("Location: forgot.php?msg=$msg");						 
        exit();
}
include "header.php"; ?>

<!-- Load jQuery Validation -->
<script language="JavaScript" type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="js/jquery.validate.js"></script>
  <script>
  $(document).ready(function(){
    $("#logForm").validate();
  });
  </script>

<!-- Start Main Page -->
<div id="main">

    <h3 class="title"><?php echo AFF_FORGOT_PASS_PAGE_TITLE; ?></h3>

	  <p>
	  <?php // This code is to show error messages
      if (isset($_GET['msg'])) {
	  $msg = mysql_real_escape_string($_GET['msg']);
	  echo "<div class='error message'>$msg</div>";
	  } ?>
      </p>

      <p><?php echo AFF_FORGOT_PASS_MESSAGE; ?></p>

      <!-- Start Forgot Pwd Form -->
      <form action="forgot.php" method="post" name="actForm" id="actForm" >

        <table width="65%" border="0" cellpadding="4" cellspacing="4" class="loginform">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="36%"><?php echo AFF_FORGOT_PASS_EMAIL; ?></td>
            <td width="64%"><input name="user_email" type="text" class="required email" id="txtboxn" size="48"></td>
          </tr>
          <tr> 
            <td colspan="2"> <div align="center"> 
                <p> 
                  <input name="doReset" type="submit" class="button" id="doLogin3" value="<?php echo AFF_RESET_BUTTON_LABEL; ?>">
                </p>
                <p><img src="images/register.png" /> <a style="color:#CC0000;" href="register.php"><?php echo AFF_AFFILIATE_SIGN_UP_LABEL; ?></a><font color="#EEE">
                  |</font> <img src="images/login.png" /> <a href="login.php"><?php echo AFF_LOGIN_PAGE_LINK_TEXT; ?></a></p>
              </div></td>
          </tr>
        </table>

      </form>
      <!-- Start Forgot Pwd Form -->

<div class="clear"></div>
</div>
<!-- End Main Page -->

<?php include "footer.php"; ?>