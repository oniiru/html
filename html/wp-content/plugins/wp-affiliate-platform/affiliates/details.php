<?php

include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}
//include "./lang/$language";
include "./countries.php";
  
if(!aff_check_security())
{
    aff_redirect('index.php');
    exit;
}
    	
include "header.php";
      
global $wpdb;
$affiliates_table_name = WP_AFF_AFFILIATES_TABLE;
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
		
        echo "<p class='ok'>".AFF_D_CHANGED."</p>";
    }
}

$editingaff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '".$_SESSION['user_id']."'", OBJECT);

if($errorMsg != '')
     echo "<p class='error'>$errorMsg</p>";

if ($editingaff)
{ ?>
	<img src="images/user_signup.png" alt="user details icon" />

      <form action=details.php method=post ENCTYPE=multipart/form-data>
        <div id="update_user">
          <p><label><?php echo AFF_AFFILIATE_ID; ?>:</label>
          <?php echo '<strong>'.$_SESSION['user_id'].'</strong>'; ?>
          </p>
          <p><label><?php echo AFF_PASSWORD; ?>: </label>
            <input class="user-edit" type="password" name="password" value="">
            <input type="hidden" name="encrypted-pass" value="<?php echo $editingaff->pass; ?>">
            <br /><span style="font-size:10px;"><?php echo AFF_LEAVE_EMPTY_TO_KEEP_PASSWORD; ?></span>
          <br /><br />
          <label><?php echo AFF_COMPANY; ?>: </label>
            <input class="user-edit" type=text name=clientcompany value="<?php echo $editingaff->company; ?>">
          <br />
          <label><?php echo AFF_TITLE; ?>: </label>
                <select class="user-select" name=clienttitle>
                  <option value=Mr <?php if($editingaff->title=="Mr")echo 'selected="selected"';?>><?php echo AFF_MR; ?></option>
                  <option value=Mrs <?php if($editingaff->title=="Mrs")echo 'selected="selected"';?>><?php echo AFF_MRS; ?></option>
                  <option value=Miss <?php if($editingaff->title=="Miss")echo 'selected="selected"';?>><?php echo AFF_MISS; ?></option>
                  <option value=Ms <?php if($editingaff->title=="Ms")echo 'selected="selected"';?>><?php echo AFF_MS; ?></option>
                  <option value=Dr <?php if($editingaff->title=="Dr")echo 'selected="selected"';?>><?php echo AFF_DR; ?></option>
                </select>
          <br />
          <label><?php echo AFF_FIRST_NAME; ?>: </label>
            <input class="user-edit" type=text name=clientfirstname value="<?php echo $editingaff->firstname; ?>">
          <br />
          <label><?php echo AFF_LAST_NAME; ?>: </label>
            <input class="user-edit" type=text name=clientlastname value="<?php echo $editingaff->lastname; ?>">
          <br />
          <label><?php echo AFF_EMAIL; ?>: </label>
            <input class="user-edit" type=text name=clientemail value="<?php echo $editingaff->email; ?>">
          <br />
          <label><?php echo AFF_ADDRESS; ?>: </label>
            <input class="user-edit" type=text name=clientstreet value="<?php echo $editingaff->street; ?>">
          <br />
          <label><?php echo AFF_TOWN; ?>: </label>
            <input class="user-edit" type=text name=clienttown value="<?php echo $editingaff->town; ?>">
          <br />
          <label><?php echo AFF_STATE; ?>: </label>
            <input class="user-edit" type=text name=clientstate value="<?php echo $editingaff->state; ?>">
          <br />
          <label><?php echo AFF_COUNTRY; ?>: </label>
            <select class="user-select" name=clientcountry class=dropdown>
                <?php foreach($GLOBALS['countries'] as $key => $country)
                    print '<option value="'.$key.'" '.($editingaff->country == $key ? 'selected' : '').'>'.$country.'</option>'."\n";
                ?>
            </select>
          <br />
          <label><?php echo AFF_ZIP; ?>: </label>
            <input class="user-edit" type=text name=clientpostcode value="<?php echo $editingaff->postcode; ?>">
          <br />
          <label><?php echo AFF_WEBSITE; ?>: </label>
            <input class="user-edit" type=text name=webpage value="<?php echo $editingaff->website; ?>">
          <br />
          <label><?php echo AFF_PHONE; ?>: </label>
            <input class="user-edit" type=text name=clientphone value="<?php echo $editingaff->phone; ?>">
          <br />
          <label><?php echo AFF_FAX; ?>: </label>
            <input class="user-edit" type=text name=clientfax value="<?php echo $editingaff->fax; ?>">
          <br />
          <label><?php echo AFF_PAYPAL_EMAIL; ?>: </label>
            <input class="user-edit" type=text name=clientpaypalemail value="<?php echo $editingaff->paypalemail; ?>">
          <br />
	      <label><?php echo AFF_BANK_ACCOUNT_DETAILS; ?>: </label>
	      	<textarea name="account_details" cols="23" rows="2"><?php echo $editingaff->account_details; ?></textarea>	           
	      <br />          
          <label><?php echo AFF_TAX_ID; ?>: </label>
            <input class="user-edit" type=text name=tax_id value="<?php echo $editingaff->tax_id; ?>">            
          </p>
          <p>
            <input type=hidden name=commited value=yes>
            <input class="button" type=submit name=Submit value="<?php echo AFF_UPDATE_BUTTON_TEXT; ?>">
          </p>
        </div>
      </form>

<?php } ?>

<?php include "footer.php"; ?>