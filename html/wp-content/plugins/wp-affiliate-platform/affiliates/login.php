<?php
include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}
if(aff_check_security())
{
    aff_redirect('members_only.php');
    exit;
}
if (isset($_POST['wpAffSadoLogin']))
{
//$user_email = mysql_real_escape_string($_POST['userid']);
//$md5pass = md5(mysql_real_escape_string($_POST['password']));
    if ($_POST['userid']!='' && $_POST['password']!='')
    {
        // protection against script injection
        $userid = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['userid']);
        $password = $_POST['password'];
		include_once(ABSPATH.WPINC.'/class-phpass.php');
		$wp_hasher = new PasswordHash(8, TRUE);        

        global $wpdb;
        $affiliates_table_name = WP_AFF_AFFILIATES_TABLE;        
	    $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$userid'", OBJECT);
	    
	    if($wp_hasher->CheckPassword($password, $result->pass))
        {
            // this sets session and logs user in
        	if(!isset($_SESSION)){@session_start();}
    	    // this sets variables in the session
    		$_SESSION['user_id']= $userid;
    		setcookie("user_id", $userid, time()+60*60*6, "/",COOKIE_DOMAIN); //set cookie for 6 hours

    		//set a cookie witout expiry until 60 days
    	    if(isset($_POST['remember']))
            {
    		    setcookie("user_id", $_SESSION['user_id'], time()+60*60*24*60, "/",COOKIE_DOMAIN);
    		}
    		header("Location: members_only.php");
        }
    	else
    	{
    		$msg = urlencode("Invalid Login. Please try again with correct user name and password. ");
    		header("Location: login.php?msg=$msg");
    	}
    }
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

    <h3 class="title"><?php echo AFF_LOGIN_PAGE_TITLE; ?></h3>

	  <?php // This code is to show error messages
      if (isset($_GET['msg'])) {
	  $msg = mysql_real_escape_string($_GET['msg']);
	  echo "<p class='error'>$msg</p>";
	  } ?>

      <img style="float:right;" src="images/wp_aff_login.jpg" alt="Login screen icon" />

      <!-- Start Login Form -->
      <form action="login.php" method="post" name="logForm" id="logForm" >

        <table width="60%" border="0" cellpadding="4" cellspacing="4" class="loginform">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><img src="images/user_icon.png" /> <?php echo AFF_USERNAME; ?></td>
            <td width="72%"><input name="userid" type="text" class="required" id="txtbox" size="25"></td>
          </tr>
          <tr>
            <td><img src="images/password_icon.png" /> <?php echo AFF_PASSWORD; ?></td>
            <td><input name="password" type="password" class="required password" id="txtbox" size="25"></td>
          </tr>
          <tr> 
            <td colspan="2"><div align="center">
                <input name="remember" type="checkbox" id="remember" value="1">
                <?php echo AFF_REMEMBER_ME; ?></div></td>
          </tr>
          <tr> 
            <td colspan="2"> <div align="center"> 
                <p> 
                  <input name="wpAffSadoLogin" class="button" type="submit" id="doLogin3" value="<?php echo AFF_LOGIN_BUTTON_LABEL; ?>">
                </p>
                <p><img src="images/register.png" /> <a style="color:#CC0000;" href="register.php"><?php echo AFF_AFFILIATE_SIGN_UP_LABEL; ?></a><font color="#EEE">
                  |</font> <img src="images/forgot_pass.png" /> <a href="forgot.php"><?php echo AFF_FORGOT_PASSWORD_LABEL; ?></a></p>
              </div></td>
          </tr>
        </table>

      </form>
      <!-- End Login Form -->

<div class="clear"></div>
</div>
<!-- End Main Page -->

<?php include "footer.php"; ?>