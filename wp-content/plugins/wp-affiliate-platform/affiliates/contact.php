<?php include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}
//include "./lang/$language";

if(!aff_check_security())
{
    aff_redirect('index.php');
    exit;
}
  
include "header.php"; 


if (isset($_POST['send_msg']))
{
    $subj = AFF_C_MSG_FROM_AFFILIATE;
    $affiliate_details = AFF_C_AFFILIATE_NAME.$_POST['name']."\n".AFF_C_AFFILIATE_EMAIL.$_POST['email'];
    $body = "\n-------------------\n".$affiliate_details.
			"\n-------------------\n\n".$_POST['msg'];
            

    $admin_email = get_option('wp_aff_contact_email');
    $headers = 'From: '.$_POST['email'] . "\r\n";

    wp_mail($admin_email, $subj, $body, $headers);
    echo "<br /><strong>".AFF_C_MSG_SENT."</strong><br />";
}

global $wpdb;
$affiliates_table_name = WP_AFF_AFFILIATES_TABLE;
$editingaff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '".$_SESSION['user_id']."'", OBJECT);
?>
<link rel="stylesheet" type="text/css" href="contact_form_css.css" />
<img src="images/contact_icon.jpg" alt="contact icon" />

<form id="payment" action="contact.php" method="post">
<input type="hidden" name="send_msg" id="send_msg" value="true" />
	<fieldset>
		<legend><?php echo AFF_C_USE_THE_FORM_BELOW; ?></legend>
		<ol>
			<li>
				<label for=name><?php echo AFF_C_NAME; ?></label>
				<input id=name name=name type=text value=<?php echo $editingaff->firstname; ?> placeholder="First and last name" required autofocus>
			</li>
			<li>
				<label for=email><?php echo AFF_C_EMAIL; ?></label>
				<input id=email name=email type=email value=<?php echo $editingaff->email; ?> placeholder="example@domain.com" required>
			</li>
			<li>
				<label for=phone><?php echo AFF_C_MSG; ?></label>
				<textarea id=msg name=msg rows=5 required></textarea>
			</li>
		</ol>
	</fieldset>
	<fieldset>
		<button type=submit name=sendMsg><?php echo AFF_C_SEND_MSG_BUTTON; ?></button>
	</fieldset>
</form>

<?php

include "footer.php";

?>