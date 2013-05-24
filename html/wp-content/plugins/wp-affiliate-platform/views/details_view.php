<?php
function wp_aff_details_view()
{
	$output = "";
	$output .= wp_aff_view_get_navbar();
	$output .= '<div id="wp_aff_inside">';		
	$output .= wp_aff_show_edit_details();
	$output .= '</div>';
	$output .= wp_aff_view_get_footer();	
	return $output;
}

function wp_aff_show_edit_details()
{
	include_once('countries.php');
	$output = "";
	
	global $wpdb;
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	$errorMsg = '';
	  
	if(isset($_POST['commited']) && $_POST['commited'] == 'yes')
	{
	    // check
	    //if($_POST['password'] == '')
	    //  $errorMsg .= AFF_SI_PWDMISSING.'<br>';
	      
	    if($_POST['clientemail'] == '')
	      $errorMsg .= AFF_SI_EMAILMISSING.'<br>';
	           
	    if($errorMsg == '')
	    {       
	    	if(!empty($_POST['password'])){
		    	$password = $_POST['password'];
				include_once(ABSPATH.WPINC.'/class-phpass.php');
				$wp_hasher = new PasswordHash(8, TRUE);
				$password = $wp_hasher->HashPassword($password);
	    	}
	    	else{
	    		$password = $_POST['encrypted-pass'];
	    	}	
			$payableto = "";//$_POST['clientpayableto']    	
	        $updatedb = "UPDATE $affiliates_table_name SET pass = '".$password."', company = '".$_POST['clientcompany']."', payableto = '".$payableto."', title = '".$_POST['clienttitle']."', firstname = '".$_POST['clientfirstname']."', lastname = '".$_POST['clientlastname']."', email = '".$_POST['clientemail']."', street = '".$_POST['clientstreet']."', town = '".$_POST['clienttown']."', state = '".$_POST['clientstate']."', country = '".$_POST['clientcountry']."', postcode = '".$_POST['clientpostcode']."', website = '".$_POST['webpage']."', phone = '".$_POST['clientphone']."', fax = '".$_POST['clientfax']."', paypalemail = '".$_POST['clientpaypalemail']."', tax_id = '".$_POST['tax_id']."', account_details = '".$_POST['account_details']."' WHERE refid = '".$_SESSION['user_id']."'";
	        $results = $wpdb->query($updatedb);
			
	        do_action('wp_aff_profile_update',$_SESSION['user_id'],$_POST);
	        
	        $output .= "<div class='wp_aff_success'>".AFF_D_CHANGED."</div>";
	    }
	}
	
	$editingaff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '".$_SESSION['user_id']."'", OBJECT);
	
	if($errorMsg != '')
	     $output .= "<p class='error'>$errorMsg</p>";
	
	if ($editingaff)
	{
		ob_start();
		?>	
		<img src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/user_signup.png'; ?>" alt="affiliate details icon" />
	      <form action="" method="post" ENCTYPE=multipart/form-data>
	        <div id="update_user">
	          <p><label><?php echo AFF_AFFILIATE_ID; ?>:</label>
	          <?php echo '<strong>'.$_SESSION['user_id'].'</strong>'; ?>
	          </p>
	          <label><?php echo AFF_PASSWORD; ?>: </label>
	          <span style="font-size:10px;"><?php echo AFF_LEAVE_EMPTY_TO_KEEP_PASSWORD; ?></span>

	            <input class="user-edit" type=password name=password value="">
	            <input name="encrypted-pass" type="hidden" value="<?php echo $editingaff->pass; ?>" size="20" />            	            
	        
	          <label><?php echo AFF_COMPANY; ?>: </label>
	            <input class="user-edit" type=text name=clientcompany value="<?php echo $editingaff->company; ?>">
	          
	          
	          <label><?php echo AFF_FIRST_NAME; ?>: </label>
	            <input class="user-edit" type=text name=clientfirstname value="<?php echo $editingaff->firstname; ?>">
	          
	          <label><?php echo AFF_LAST_NAME; ?>: </label>
	            <input class="user-edit" type=text name=clientlastname value="<?php echo $editingaff->lastname; ?>">
	          
	          <label><?php echo AFF_EMAIL; ?>: </label>
	            <input class="user-edit" type=text name=clientemail value="<?php echo $editingaff->email; ?>">
	          
	          <label><?php echo AFF_ADDRESS; ?>: </label>
	            <input class="user-edit" type=text name=clientstreet value="<?php echo $editingaff->street; ?>">
	          
	          <label><?php echo AFF_TOWN; ?>: </label>
	            <input class="user-edit" type=text name=clienttown value="<?php echo $editingaff->town; ?>">
	          
	          <label><?php echo AFF_STATE; ?>: </label>
	            <input class="user-edit" type=text name=clientstate value="<?php echo $editingaff->state; ?>">
	          
	          <label><?php echo AFF_COUNTRY; ?>: </label>
	            <select class="user-select" name=clientcountry class=dropdown>
	                <?php foreach($GLOBALS['countries'] as $key => $country)
	                    print '<option value="'.$key.'" '.($editingaff->country == $key ? 'selected' : '').'>'.$country.'</option>'."\n";
	                ?>
	            </select>
	          
	          <label><?php echo AFF_ZIP; ?>: </label>
	            <input class="user-edit" type=text name=clientpostcode value="<?php echo $editingaff->postcode; ?>">
	          
	          <label><?php echo AFF_WEBSITE; ?>: </label>
	            <input class="user-edit" type=text name=webpage value="<?php echo $editingaff->website; ?>">
	          
	          <label><?php echo AFF_PHONE; ?>: </label>
	            <input class="user-edit" type=text name=clientphone value="<?php echo $editingaff->phone; ?>">
	          
	          <label><?php echo AFF_FAX; ?>: </label>
	            <input class="user-edit" type=text name=clientfax value="<?php echo $editingaff->fax; ?>">
	          
	          <label><?php echo AFF_PAYPAL_EMAIL; ?>: </label>
	            <input class="user-edit" type=text name=clientpaypalemail value="<?php echo $editingaff->paypalemail; ?>">
	          
	          <label><?php echo AFF_BANK_ACCOUNT_DETAILS; ?>: </label>
	          	<textarea name="account_details" cols="23" rows="2"><?php echo $editingaff->account_details; ?></textarea>	           
	          	          
	          <label><?php echo AFF_TAX_ID; ?>: </label>
	            <input class="user-edit" type=text name=tax_id value="<?php echo $editingaff->tax_id; ?>">	            

			  
	          <input type=hidden name=commited value=yes>
	          <input class="button" type=submit name=Submit value="<?php echo AFF_UPDATE_BUTTON_TEXT; ?>">
	          
	        </div>
	      </form>
	
		<?php 
		$output .= ob_get_contents();
		ob_end_clean();		
	}
	return $output;
}
?>