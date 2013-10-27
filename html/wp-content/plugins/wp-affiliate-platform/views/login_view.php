<?php
function wp_aff_login_view()
{
	$output = "";
	$output .= wp_aff_view_get_navbar();
	$output .= '<div id="wp_aff_inside">';
	$output .= wp_aff_show_login_page();
	$output .= '</div>';
	$output .= wp_aff_view_get_footer();
	return $output;
}
function wp_aff_show_login_page()
{	
	$output = "";
	ob_start();
	?>
<!-- Load jQuery Validation -->
<script language="JavaScript" type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/js/jquery.validate.min.js'; ?>"></script>
<script type="text/javascript"> 
/* <![CDATA[ */
  jQuery(document).ready(function($){
    $("#logForm").validate();
  });
/*]]>*/  
</script>
  
    <h3 class="wp_aff_title"><?php echo AFF_LOGIN_PAGE_TITLE; ?></h3>

	  <?php // This code is to show error messages
      if (isset($_GET['msg'])) {
	  $msg = mysql_real_escape_string($_GET['msg']);
	  echo "<p class='error'>$msg</p>";
	  } ?>
      
      <!-- Start Login Form -->
      <form action="" method="post" name="logForm" id="logForm" >
		
        <table width="60%" border="0" cellpadding="4" cellspacing="4" class="loginform">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%"><?php echo AFF_USERNAME; ?></td>
            <td width="72%"><input name="userid" type="text" class="required" id="txtbox" size="21"></td>
          </tr>
          <tr>
            <td> <?php echo AFF_PASSWORD; ?></td>
            <td><input name="password" type="password" class="required password" id="txtbox" size="21"></td>
          </tr>
          <tr> 
            <td colspan="2">
                <p style="margin-left: 78px;display: inline;
"> 
                  <input name="wpAffDoLogin" class="btn btn-success" type="submit" id="wpAffDoLogin" value="<?php echo AFF_LOGIN_BUTTON_LABEL; ?>">
                </p>
                <input style="vertical-align: top;margin-top: 0px;margin-left: 10px;" name="remember" type="checkbox" id="remember" value="1">
                <div style="vertical-align:top;display:inline"><?php echo AFF_REMEMBER_ME; ?></div></td>
          </tr>
          <tr> 
            <td colspan="2"> <div style="padding-top: 10px;text-align: right;margin-right: 47px;
}" align="center"> 
               
                <p> <a style="color:#CC0000;" href="<?php echo wp_aff_view_get_url_with_separator("signup"); ?>"><?php echo AFF_AFFILIATE_SIGN_UP_LABEL; ?></a><font color="#EEE">
                  |</font>  <a href="<?php echo wp_aff_view_get_url_with_separator("forgot_pass"); ?>"><?php echo AFF_FORGOT_PASSWORD_LABEL; ?></a></p>
              </div></td>
          </tr>
        </table>

      </form>
      <!-- End Login Form -->  
      
	<?php
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;  
}
?>