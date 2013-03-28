<?php
include_once('wp_aff_includes1.php');

$affiliates_table_name = $wpdb->prefix . "affiliates_tbl";

function aff_top_affiliates_menu() {
	echo '<div class="wrap"><h2>WP Affiliate Platform - Manage Affiliates</h2>';
	echo '<div id="poststuff"><div id="post-body">';

	echo wp_aff_admin_submenu_css();
	echo wp_aff_misc_admin_css();
	?>
	<ul class="affiliateSubMenu">
		<li><a href="admin.php?page=affiliates">Affiliate Details</a></li>
		<li><a href="admin.php?page=affiliates&action=affiliate_email">Affiliate Emails</a></li>
		<li><a href="admin.php?page=affiliates&action=import_wp_user">Import WP Users</a></li>
	</ul>
	<?php
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	switch ($action) {
		case 'affiliate_email':
			affiliates_email_menu();
			break;
		case 'import_wp_user':
			affiliates_import_wp_users_menu();
			break;
		default:
			affiliates_menu();
			break;
	}

	echo '</div></div>';
	echo '</div>';
}

function affiliates_import_wp_users_menu() {
	echo "<h2>Import Existing WP Users as Affiliates</h2>";
	global $wpdb;
	$error_msg = "";
	$commission_level = get_option('wp_aff_commission_level');

	if (isset($_POST['import_wp_users_as_affiliate'])) {
		$validated = true;
//		if(empty($_POST['default_password'])){
//			$validated = false;
//			$error_msg .= 'Please specify a default password that will be used when creating the affiliate accounts!<br />';
//		}

		if ($validated) {
			$query = "SELECT ID,user_login FROM $wpdb->users";
			$result = $wpdb->get_results($query, ARRAY_A);
			foreach ($result as $row) {
				$user_info = get_userdata($row['ID']);
				$fields = array();
				$fields['refid'] = $user_info->user_login;
				$fields['firstname'] = $user_info->user_firstname;
				$fields['lastname'] = $user_info->user_lastname;
				$fields['pass'] = $user_info->user_pass; //$_POST['default_password'];
				$fields['date'] = current_time('mysql');
				$fields['email'] = $user_info->user_email;
				$fields['commissionlevel'] = $commission_level;

				if (!wp_aff_check_if_account_exists($fields['email'])) {
					if (wp_aff_create_affilate_using_array_data($fields)) {
						//Account created successfully
					} else {
						$error_msg .= 'Error! Failed to create affiliate account for user: ' . $fields['refid'] . '<br />';
					}
				} else {
					$error_msg .= 'Affiliate account already exists for email address: ' . $fields['email'] . '. No account was created for this user.<br />';
				}
				//var_dump($fields);
			}
			if (empty($error_msg)) {
				echo '<p class="wp_affiliate_grey_box">Mass user import completed!</p>';
			} else {
				echo '<p class="wp_affiliate_yellow_box"><strong>Mass user import completed with some errors!</strong><br />' . $error_msg . '</p>';
			}
		} else {
			echo '<p class="wp_affiliate_yellow_box">' . $error_msg . '</p>';
		}
	}
	?>
	<div class="postbox">
		<h3><label for="title">Import WP Users</label></h3>
		<div class="inside">

			<p style="color:blue">You can use this option to automatically create an affiliate account for all of your existing WordPress users (the WordPress username will be used as the Affiliate ID)</p>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >
				<div class="submit">
					<input type="submit" name="import_wp_users_as_affiliate" value="Import WP Users" />
				</div>
			</form>

		</div></div>
	<?php
}

function affiliates_email_menu() {
	echo "<h2>Affiliate Emails</h2>";
	?>
	<div class="postbox">
		<h3><label for="title">Affiliate Email Options</label></h3>
		<div class="inside">

			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<input type="hidden" name="wp_aff_display_all_affiliate_email" id="wp_aff_display_all_affiliate_email" value="true" />
				<table width="100%" border="0" cellspacing="0" cellpadding="6">
					<tr valign="top">
						<td width="25%" align="left">
							Display Email List of All Affiliates:
						</td>
						<td align="left">
							<input type="submit" name="wp_aff_display_all_affiliate_email" value="<?php _e('Display All Affiliates Email List'); ?> &raquo;" />
							<br /><i>Use this to display a list of emails (comma separated) of all the affiliates for bulk emailing purpose. After you generate the list you can copy and paste it into your email management software.</i><br />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<?php
	global $wpdb;
	global $affiliates_table_name;
	if (isset($_POST['wp_aff_display_all_affiliate_email'])) {
		$ret_aff_db = $wpdb->get_results("SELECT * FROM $affiliates_table_name ORDER BY refid DESC", OBJECT);
		echo wp_aff_display_affiliate_email_list($ret_aff_db);
	}
}

function wp_aff_display_affiliate_email_list($ret_aff_db) {
	$output = "";
	if ($ret_aff_db) {
		foreach ($ret_aff_db as $ret_aff_db) {
			$output .= $ret_aff_db->email;
			$output .= ', ';
		}
	} else {
		$output .= 'No Affiliates found.';
	}
	return $output;
}

function affiliates_menu() {
	echo "<h2>Affiliate Details</h2>";
	?>
	<div class="postbox">
		<h3><label for="title">Affiliate Search</label></h3>
		<div class="inside">
			<strong>Search for an Affiliate by Entering the Affiliate ID or First Name or Email address</strong> (Full or Partial)
			<br /><br />
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<input type="hidden" name="info_update" id="info_update" value="true" />

				<input name="wp_aff_referrer_search" type="text" size="35" value=""/>
				<div class="submit">
					<input type="submit" name="info_update" value="<?php _e('Search'); ?> &raquo;" />
				</div>
			</form>
		</div></div>
	<?php
	if (isset($_REQUEST['Delete'])) {
		if (wp_affiliate_delete_aff_data($_REQUEST['delete_ref_id'])) {
			$message = "Record successfully deleted";
		} else {
			$message = "An error occurded while trying to delete the entry";
		}
		echo '<div id="message" class="updated fade"><p><strong>';
		echo $message;
		echo '</strong></p></div>';
	}

	if (isset($_POST['info_update'])) {
		$search_term = (string) $_POST["wp_aff_referrer_search"];
		update_option('wp_aff_referrer_search', (string) $_POST["wp_aff_referrer_search"]);
		display_affiliate_search($search_term);
	} else {
		display_recent_affiliates();
	}
}

/* * * This method is only used by the search option. It will later be replaced with the new list table method ** */

function aff_display_affiliates($wp_aff_affiliates_db) {
	echo '
	<table class="widefat">
	<thead><tr>
	<th scope="col">' . __('Affiliate ID', 'wp_affiliate') . '</th>
	<th scope="col">' . __('First Name', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Last Name', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Email Address', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Date Joined', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Country', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Commission Level (% or $)', 'wp_affiliate') . '</th>
	<th scope="col"></th>
	</tr></thead>
	<tbody>';

	if ($wp_aff_affiliates_db) {
		foreach ($wp_aff_affiliates_db as $wp_aff_affiliates_db) {
			echo '<tr>';
			echo '<td>' . $wp_aff_affiliates_db->refid . '</td>';
			echo '<td><strong>' . $wp_aff_affiliates_db->firstname . '</strong></td>';
			echo '<td><strong>' . $wp_aff_affiliates_db->lastname . '</strong></td>';
			echo '<td><strong>' . $wp_aff_affiliates_db->email . '</strong></td>';
			echo '<td><strong>' . $wp_aff_affiliates_db->date . '</strong></td>';
			echo '<td><strong>' . $wp_aff_affiliates_db->country . '</strong></td>';
			echo '<td><strong>' . $wp_aff_affiliates_db->commissionlevel . '</strong></td>';
			echo '<td style="text-align: center;"><a href="admin.php?page=affiliates_addedit&editaff=' . $wp_aff_affiliates_db->refid . '">Edit</a>';
			echo "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";
			echo "<input type=\"hidden\" name=\"delete_ref_id\" value=" . $wp_aff_affiliates_db->refid . " />";
			echo '<input style="border: none; background-color: transparent; padding: 0; cursor:pointer;" type="submit" name="Delete" value="Delete">';
			echo "</form>";
			echo '</td>';

			echo '</tr>';
		}
	} else {
		echo '<tr> <td colspan="8">' . __('No affiliates found.', 'wp_affiliate') . '</td> </tr>';
	}

	echo '</tbody>
	</table>';
}

function wp_affiliate_delete_aff_data($aff_id) {
	global $wpdb;
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	$updatedb = "DELETE FROM $affiliates_table_name WHERE refid='$aff_id'";
	$results = $wpdb->query($updatedb);
	if ($results > 0) {
		return true;
	} else {
		return false;
	}
}

function display_affiliate_search($search_term) {
	echo '<div id="message" class="updated fade"><p><strong>';
	echo 'Displaying Affiliate Search Result';
	echo '</strong></p></div>';

	global $wpdb;
	global $affiliates_table_name;
	$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table_name WHERE refid like '%" . $search_term . "%' OR firstname like '%" . $search_term . "%' OR email like '%" . $search_term . "%' OR paypalemail like '%" . $search_term . "%'", OBJECT);
	aff_display_affiliates($wp_aff_affiliates_db);
}

function display_recent_affiliates() {
	include_once('wp_aff_list_affiliates_table.php');
	//Create an instance of our package class...
	$affiliates_list_table = new WP_AFF_List_Affiliates_Table();
	//Fetch, prepare, sort, and filter our data...
	$affiliates_list_table->prepare_items();
	?>
	<div class="wrap">

		<div id="icon-users" class="icon32"><br/></div>
		<h2>Your Affiliates</h2>

		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="affiliates-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $affiliates_list_table->display() ?>
		</form>

	</div>
	<?php
	return;

// This method of displaying the affiliate list has been deprecated	(to be deleted in a future version)
//	global $wpdb;
//	global $affiliates_table_name;
//	$limit = 50;
//    if (isset($_GET['product_page'])){
//        $page = $_GET['product_page'];
//    }
//    else{
//        $page = 1;
//    }
//    $start = ($page - 1) * $limit;
//	$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table_name ORDER BY date DESC LIMIT $start, $limit", OBJECT);
//    //get total rows
//    $totalrows = $wpdb->get_var("SELECT COUNT(*) FROM $affiliates_table_name;");
//    aff_display_affiliates($wp_aff_affiliates_db);
//    //Number of pages setup
//	$pages = ceil($totalrows / $limit)+1;
//    for($r = 1;$r<$pages;$r++){
//        echo "<a href='admin.php?page=affiliates&product_page=".$r."' class=\"button rbutton\">".$r."</a>&nbsp;";
//    }
}

function edit_affiliates_menu() {
	echo '<div class="wrap">';
	echo "<h2>Add/Edit Affiliates</h2>";
	echo '<div id="poststuff"><div id="post-body">';
	global $wpdb;
	$affiliates_table_name = $wpdb->prefix . "affiliates_tbl";

	//If affiliate is being edited, grab current affiliate info
	if (isset($_GET['editaff'])) {
		$theid = $_GET['editaff'];
		$editingaff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$theid'", OBJECT);
	}
	if (isset($_POST['Submit'])) {
		$form_fields_validated = true;
		$validation_msg = "";
		if (!isset($_POST['editedaffiliate'])) {
			$_POST['editedaffiliate'] = "";
		}
		$post_editedaffiliate = $wpdb->escape($_POST['editedaffiliate']);
		$post_refid = $wpdb->escape($_POST['refid']);

		if (!empty($_POST['pass'])) {
			$post_pass = $wpdb->escape($_POST['pass']);
			include_once(ABSPATH . WPINC . '/class-phpass.php');
			$wp_hasher = new PasswordHash(8, TRUE);
			$post_pass = $wp_hasher->HashPassword($post_pass);
		} else {
			$post_pass = $_POST['encrypted-pass'];
		}

		$post_title = $wpdb->escape($_POST['title']);
		$post_firstname = $wpdb->escape($_POST['firstname']);
		$post_lastname = $wpdb->escape($_POST['lastname']);
		$post_company = $wpdb->escape($_POST['company']);
		$post_website = $wpdb->escape($_POST['website']);
		$post_email = $wpdb->escape($_POST['email']);
		$post_payableto = $wpdb->escape($_POST['payableto']);
		$post_street = $wpdb->escape($_POST['street']);
		$post_town = $wpdb->escape($_POST['town']);
		$post_state = $wpdb->escape($_POST['state']);
		$post_postcode = $wpdb->escape($_POST['postcode']);
		$post_country = $wpdb->escape($_POST['country']);
		$post_phone = $wpdb->escape($_POST['phone']);
		$post_fax = $wpdb->escape($_POST['fax']);
		$post_date = $wpdb->escape($_POST['date']);
		if (empty($post_date)) {
			$post_date = current_time('mysql');
		}
		$post_paypalemail = $wpdb->escape($_POST['paypalemail']);

		$curr_symbol = get_option('wp_aff_currency_symbol');
		$commission_level = $wpdb->escape($_POST['commissionlevel']);
		$commission_level = str_replace("%", "", $commission_level);
		$commission_level = str_replace($curr_symbol, "", $commission_level);
		$post_commissionlevel = $commission_level;
		if (empty($post_commissionlevel)) {
			$post_commissionlevel = get_option('wp_aff_commission_level');
		}
		$post_referrer = $wpdb->escape($_POST['referrer']);
		$post_tax_id = $wpdb->escape($_POST['tax_id']);
		$post_account_details = $wpdb->escape($_POST['account_details']);
		$post_sec_tier_commissionlevel = $wpdb->escape($_POST['sec_tier_commissionlevel']);

		//Some basic validation
		if (preg_match('/[^a-zA-Z0-9_]/', $post_refid)) {
			$validation_msg = "Error! Permitted characters for Affiliate ID field are letters, numbers and underscore";
			$form_fields_validated = false;
		}

		if (!$form_fields_validated) {

			echo '<div id="message" class="error fade"><p>';
			echo $validation_msg;
			echo '</p></div>';
		} else {//Fields validated
			if ($post_editedaffiliate == '') {
				// Add the affiliate to the DB
				if (!empty($post_refid)) {
					$updatedb = "INSERT INTO $affiliates_table_name (refid,pass,company,title,firstname,lastname,website,email,payableto,street,town,state,postcode,country,phone,fax,date,paypalemail,commissionlevel,referrer,tax_id,account_details,sec_tier_commissionlevel) VALUES ('$post_refid','$post_pass','$post_company','$post_title','$post_firstname','$post_lastname','$post_website','$post_email','$post_payableto','$post_street','$post_town','$post_state','$post_postcode','$post_country','$post_phone','$post_fax','$post_date','$post_paypalemail','$post_commissionlevel','$post_referrer','$post_tax_id','$post_account_details','$post_sec_tier_commissionlevel')";
					$results = $wpdb->query($updatedb);

					include_once('wp_aff_auto_responder_handler.php');
					wp_aff_global_autoresponder_signup($post_firstname, $post_lastname, $post_email);

					echo '<div id="message" class="updated fade"><p>Affiliate &quot;' . $post_firstname . '&quot; created.</p></div>';
				} else {
					echo '<div id="message" class="updated fade"><p>Affiliate ID cannot be empty.</p></div>';
				}
			} else {
				// Update the affiliate info
				$updatedb = "UPDATE $affiliates_table_name SET pass = '$post_pass',company = '$post_company',title = '$post_title',firstname = '$post_firstname',lastname = '$post_lastname',website = '$post_website',email = '$post_email',payableto = '$post_payableto',street = '$post_street',town = '$post_town',state = '$post_state',postcode = '$post_postcode',country = '$post_country',phone = '$post_phone',fax = '$post_fax',date = '$post_date',paypalemail = '$post_paypalemail',commissionlevel = '$post_commissionlevel',referrer='$post_referrer',tax_id='$post_tax_id',account_details='$post_account_details',sec_tier_commissionlevel='$post_sec_tier_commissionlevel' WHERE refid='$post_editedaffiliate'";
				$results = $wpdb->query($updatedb);

				// Get the editing affiliate again
				$theid = $post_editedaffiliate;
				$_GET['editaff'] = $post_editedaffiliate;
				$editingaff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$theid'", OBJECT);

				echo '<div id="message" class="updated fade"><p>' . __('Affiliate', 'wp_affiliate') . ' &quot;' . $post_firstname . '&quot; ' . __('updated.', 'wp_affiliate') . '</p></div>';
			}
		}
	}
	// Delete Affiliate
	if (isset($_POST['deleteaffiliate'])) {
		$post_editedaffiliate = $wpdb->escape($_POST['editedaffiliate']);
		echo '<div id="message" class="updated fade"><p>' . __('Do you really want to delete this Affiliate? This action cannot be undone.', 'wp_affiliate') . ' <a href="admin.php?page=affiliates_addedit&deleteaffiliate=' . $post_editedaffiliate . '">' . __('Yes', 'wp_affiliate') . '</a> &nbsp; <a href="admin.php?page=affiliates_addedit&editaff=' . $post_editedaffiliate . '">' . __('No!', 'wp_affiliate') . '</a></p></div>';
	}
	if (isset($_GET['deleteaffiliate'])) {
		$theaff = $_GET['deleteaffiliate'];
		$updatedb = "DELETE FROM $affiliates_table_name WHERE refid='$theaff'";
		$results = $wpdb->query($updatedb);
		echo '<div id="message" class="updated fade"><p>' . __('Affiliate deleted.', 'wp_affiliate') . '</p></div>';
	}
	?>

	<form method="post" action="admin.php?page=affiliates_addedit">

		<div class="postbox">
			<h3><label for="title">Affiliate Details</label></h3>
			<div class="inside">

				<table class="form-table">
					<?php
					if (isset($_GET['editaff'])) {//An existing record is being edited
						echo '<input name="editedaffiliate" type="hidden" value="' . $_GET['editaff'] . '" />';
						$read_only_value = 'readonly="readonly"';
					} else {
						$read_only_value = '';
					}
					?>

					<tr valign="top">
						<th scope="row"><?php _e('Affiliate ID', 'wp_affiliate'); ?></th>
						<td><input name="refid" type="text" id="refid" value="<?php
				if (isset($editingaff->refid)) {
					echo $editingaff->refid;
				}
					?>" size="20" <?php echo $read_only_value; ?> /><br/><?php _e('Affiliate ID of the Affiliate (permitted characters are letters, numbers and underscore)', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Login Password', 'wp_affiliate'); ?></th>
						<td><input name="pass" type="password" id="pass" value="" size="20" />
							<input name="encrypted-pass" type="hidden" value="<?php
							   if (isset($editingaff->pass)) {
								   echo $editingaff->pass;
							   }
					?>" size="20" />
							<br/>
							<?php
							if (isset($_GET['editaff'])) {
								echo 'Leave empty to keep the current password. You can only change it but cannot view the password.';
							} else {
								echo 'Login password of the affiliate.';
							}
							?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Title', 'wp_affiliate'); ?></th>
						<td><input name="title" type="text" id="title" value="<?php
						if (isset($editingaff->title)) {
							echo $editingaff->title;
						}
							?>" size="10" /><br/><?php _e('Title of the Affiliate', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('First Name', 'wp_affiliate'); ?></th>
						<td><input name="firstname" type="text" id="firstname" value="<?php
							   if (isset($editingaff->firstname)) {
								   echo $editingaff->firstname;
							   }
							?>" size="40" /><br/><?php _e('First Name of the Affiliate', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Last Name', 'wp_affiliate'); ?></th>
						<td><input name="lastname" type="text" id="lastname" value="<?php
							   if (isset($editingaff->lastname)) {
								   echo $editingaff->lastname;
							   }
							?>" size="40" /><br/><?php _e('Last Name of the Affiliate', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Company', 'wp_affiliate'); ?></th>
						<td><input name="company" type="text" id="company" value="<?php
							   if (isset($editingaff->company)) {
								   echo $editingaff->company;
							   }
							?>" size="40" /><br/></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Website', 'wp_affiliate'); ?></th>
						<td><input name="website" type="text" id="website" value="<?php
							   if (isset($editingaff->website)) {
								   echo $editingaff->website;
							   }
							?>" size="40" /><br/><?php _e('Website Address of the Affiliate', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Email Address', 'wp_affiliate'); ?></th>
						<td><input name="email" type="text" id="email" value="<?php
							   if (isset($editingaff->email)) {
								   echo $editingaff->email;
							   }
							?>" size="40" /><br/><?php _e('Email Address of the Affiliate', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Payable To', 'wp_affiliate'); ?></th>
						<td><input name="payableto" type="text" id="payableto" value="<?php
							   if (isset($editingaff->payableto)) {
								   echo $editingaff->payableto;
							   }
							?>" size="40" /><br/><?php _e('This can be used when paying the affiliate using a Cheque', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Street Address', 'wp_affiliate'); ?></th>
						<td><input name="street" type="text" id="street" value="<?php
							   if (isset($editingaff->street)) {
								   echo $editingaff->street;
							   }
							?>" size="40" /><br/><?php _e('Street Address of the Affiliate', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('City', 'wp_affiliate'); ?></th>
						<td><input name="town" type="text" id="town" value="<?php
							   if (isset($editingaff->town)) {
								   echo $editingaff->town;
							   }
							?>" size="20" /><br/></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('State', 'wp_affiliate'); ?></th>
						<td><input name="state" type="text" id="state" value="<?php
							   if (isset($editingaff->state)) {
								   echo $editingaff->state;
							   }
							?>" size="20" /><br/></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Post Code', 'wp_affiliate'); ?></th>
						<td><input name="postcode" type="text" id="postcode" value="<?php
							   if (isset($editingaff->postcode)) {
								   echo $editingaff->postcode;
							   }
							?>" size="10" /><br/></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Country', 'wp_affiliate'); ?></th>
						<td><input name="country" type="text" id="country" value="<?php
							   if (isset($editingaff->country)) {
								   echo $editingaff->country;
							   }
							?>" size="20" /><br/></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Phone Number', 'wp_affiliate'); ?></th>
						<td><input name="phone" type="text" id="phone" value="<?php
							   if (isset($editingaff->phone)) {
								   echo $editingaff->phone;
							   }
							?>" size="20" /><br/></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Fax Number', 'wp_affiliate'); ?></th>
						<td><input name="fax" type="text" id="fax" value="<?php
							   if (isset($editingaff->fax)) {
								   echo $editingaff->fax;
							   }
							?>" size="20" /><br/></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Date Joined (yyyy-mm-dd)', 'wp_affiliate'); ?></th>
						<td><input name="date" type="text" id="date" value="<?php
							   if (isset($editingaff->date)) {
								   echo $editingaff->date;
							   }
							?>" size="20" /><br/><?php _e('This value must be entered in the format yyyy-mm-dd. Leave empty to use todays date.', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('PayPal Email Address', 'wp_affiliate'); ?></th>
						<td><input name="paypalemail" type="text" id="paypalemail" value="<?php
							   if (isset($editingaff->paypalemail)) {
								   echo $editingaff->paypalemail;
							   }
							?>" size="40" /><br/><?php _e('This address is used to pay the Affiliate through PayPal', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Bank Account Details', 'wp_affiliate'); ?></th>
						<td><textarea name="account_details" cols="50" rows="3"><?php
							   if (isset($editingaff->account_details)) {
								   echo $editingaff->account_details;
							   }
							?></textarea>
							<br/><?php _e('Bank account details of this affiliate if applicable', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Tax ID / SSN', 'wp_affiliate'); ?></th>
						<td><input name="tax_id" type="text" id="tax_id" value="<?php
							if (isset($editingaff->tax_id)) {
								echo $editingaff->tax_id;
							}
							?>" size="40" /><br/><?php _e('Tax ID of this affiliate if applicable', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Commission Level (% or $)', 'wp_affiliate'); ?></th>
						<td><input name="commissionlevel" type="text" id="commissionlevel" value="<?php
							   if (isset($editingaff->commissionlevel)) {
								   echo $editingaff->commissionlevel;
							   }
							?>" size="4" /><br/><?php _e('Only enter the number (do not use "%" or "$" sign).', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('2nd Tier Commission Level (% or $)', 'wp_affiliate'); ?></th>
						<td><input name="sec_tier_commissionlevel" type="text" id="sec_tier_commissionlevel" value="<?php
							   if (isset($editingaff->sec_tier_commissionlevel)) {
								   echo $editingaff->sec_tier_commissionlevel;
							   }
							?>" size="4" /><br/><?php _e('Only enter the number (do not use "%" or "$" sign). The value you specify here will override the default 2nd tier commission level for this affiliate.', 'wp_affiliate'); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Referrer ID', 'wp_affiliate'); ?></th>
						<td><input name="referrer" type="text" id="referrer" value="<?php
							   if (isset($editingaff->referrer)) {
								   echo $editingaff->referrer;
							   }
							?>" size="20" /><br/><?php _e('Affiliate ID of the affiliate who referred this affiliate (This value is used when tier affiliate model is used)', 'wp_affiliate'); ?></td>
					</tr>

				</table>
			</div></div>

		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Affiliate', 'wp_affiliate'); ?>" /> &nbsp; <?php if (isset($_GET['editaff'])) { ?><input type="submit" name="deleteaffiliate" value="<?php _e('Delete Affiliate', 'wp_affiliate'); ?>" /><?php } ?></p>
	</form>

	<?php
	echo '<br /><a href="admin.php?page=affiliates" class="button rbutton">' . __('Manage Affiliates', 'wp_affiliate') . '</a>';
	echo '</div></div>';
	echo '</div>';
}
?>
