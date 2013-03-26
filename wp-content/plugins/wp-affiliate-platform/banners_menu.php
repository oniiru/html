<?php
include_once('wp_aff_includes1.php');
include_once('wp_aff_utility_functions.php');

function manage_banners_menu()
{
	echo '<div class="wrap">';
	echo '<div class="wrap">';	
	echo '<div id="poststuff"><div id="post-body">';
  	
	echo wp_aff_admin_submenu_css();
   ?>
   <h2>WP Affiliate Platform - Manage Ads</h2>
   <ul class="affiliateSubMenu">
   <li><a href="admin.php?page=manage_banners">Banners/Links</a></li>
   <li><a href="admin.php?page=manage_banners&action=article">Creatives</a></li>   
   </ul>
   <?php

    if(isset($_POST['Delete']))
    {
        if(wp_affiliate_delete_ad_data($_POST['delete_ad_id']))
        {
            $message = "Record successfully deleted";
        }
        else
        {
            $message = "An error occurded while trying to delete the entry";
        }
        echo '<div id="message" class="updated fade"><p><strong>';
	    echo $message;
	    echo '</strong></p></div>';
    }   
   
   $action = isset($_GET['action'])?$_GET['action']:'';
   switch ($action)
   {
       case 'article':
           wp_aff_manage_creatives_menu();
           break;
       default:
           wp_aff_manage_banners_menu();
           break;
   }	
	
	echo '</div></div>';
	echo '</div>';
}

function wp_aff_edit_ads_menu()
{
	echo '<div class="wrap">';	
	echo '<div id="poststuff"><div id="post-body">';
  	
	 echo wp_aff_admin_submenu_css();
   ?>
   <h2>WP Affiliate Platform - Add/Edit Ads</h2>
   <ul class="affiliateSubMenu">
   <li><a href="admin.php?page=edit_banners">Banners/Links</a></li>
   <li><a href="admin.php?page=edit_banners&action=article">Creatives</a></li>   
   </ul>
   <?php
   $action = isset($_GET['action'])?$_GET['action']:'';
   switch ($action)
   {
       case 'article':
           wp_aff_edit_articles_menu();
           break;
       default:
           wp_aff_edit_banners_menu();
           break;
   }	
	
	echo '</div></div>';
	echo '</div>';
}

function wp_aff_manage_banners_menu()
{
	echo "<h2>Manage Text Links or Image Banners</h2>";
	echo '
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('ID', 'wp_affiliate').'</th>
	<th scope="col">'.__('Name', 'wp_affiliate').'</th>
	<th scope="col">'.__('Description', 'wp_affiliate').'</th>
	<th scope="col">'.__('Link/Alt Text', 'wp_affiliate').'</th>
	<th scope="col">'.__('Target URL', 'wp_affiliate').'</th>
	<th scope="col">'.__('Banner/Link Preview', 'wp_affiliate').'</th>
	<th scope="col"></th>
	</tr></thead>
	<tbody>';
	
	global $wpdb;
	$banners_table_name = $wpdb->prefix . "affiliates_banners_tbl";
	$wp_banners_db = $wpdb->get_results("SELECT * FROM $banners_table_name ORDER BY number DESC", OBJECT);

	if ($wp_banners_db)
	{
		foreach ($wp_banners_db as $wp_banners_db)
		{
			if($wp_banners_db->creative_type == "0")
			{
				echo '<tr>';
				echo '<td>'.$wp_banners_db->number.'</td>';
				echo '<td><strong>'.$wp_banners_db->name.'</strong></td>';
				echo '<td><strong>'.$wp_banners_db->description.'</strong></td>';
				echo '<td><strong>'.$wp_banners_db->link_text.'</strong></td>';
				echo '<td><strong>'.$wp_banners_db->ref_url.'</strong></td>';
				if (!empty($wp_banners_db->image))
				{
					echo '<td><a href="'.$wp_banners_db->ref_url.'"><img src="'.$wp_banners_db->image.'" /></a></td>';
				}
				else
				{				
					echo '<td><a href="'.$wp_banners_db->ref_url.'">'.$wp_banners_db->link_text.'</a></td>';
				}
				echo '<td style="text-align: center;"><a href="admin.php?page=edit_banners&editrecord='.$wp_banners_db->number.'">Edit</a>';
				echo "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";				
				echo "<input type=\"hidden\" name=\"delete_ad_id\" value=".$wp_banners_db->number." />";
	            echo '<input style="border: none; background-color: transparent; padding: 0; cursor:pointer;" type="submit" name="Delete" value="Delete">';
	            echo "</form>";						
				echo '</td>';
				echo '</tr>';
			}
		}
	}
	else
	{
		echo '<tr> <td colspan="8">'.__('No Banners/Links found.', 'wp_affiliate').'</td> </tr>';
	}

	echo '</tbody>
	</table>';
	
	echo '<br /><a href="admin.php?page=edit_banners" class="button rbutton">'.__('Add Banner/Link', 'wp_affiliate').'</a>';	
}
function wp_aff_manage_creatives_menu()
{
	echo "<h2>Manage Creatives</h2>";
	echo 'You can add pre written and optimized text copy of several hundred words with links built into it so your affiliates can use it to promote your products.<br /><br />';
	echo '
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('ID', 'wp_affiliate').'</th>
	<th scope="col">'.__('Name', 'wp_affiliate').'</th>
	<th scope="col">'.__('Creative Code', 'wp_affiliate').'</th>
	<th scope="col"></th>
	</tr></thead>
	<tbody>';
	
	global $wpdb;
	$banners_table_name = $wpdb->prefix . "affiliates_banners_tbl";
	$wp_banners_db = $wpdb->get_results("SELECT * FROM $banners_table_name ORDER BY number DESC", OBJECT);
	$ads_found = false;
	if ($wp_banners_db)
	{
		foreach ($wp_banners_db as $wp_banners_db)
		{
			if($wp_banners_db->creative_type == "3")
			{
				$ads_found = true;
				echo '<tr>';
				echo '<td>'.$wp_banners_db->number.'</td>';
				echo '<td><strong>'.$wp_banners_db->name.'</strong></td>';
				echo '<td><textarea name="creative" cols="70" rows="5">'.$wp_banners_db->description.'</textarea>';
				//echo '<td><strong>'.$wp_banners_db->description.'</strong></td>';

				echo '<td style="text-align: center;"><a href="admin.php?page=edit_banners&action=article&editrecord='.$wp_banners_db->number.'">Edit</a>';
				echo "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";				
				echo "<input type=\"hidden\" name=\"delete_ad_id\" value=".$wp_banners_db->number." />";
	            echo '<input style="border: none; background-color: transparent; padding: 0; cursor:pointer;" type="submit" name="Delete" value="Delete">';
	            echo "</form>";						
				echo '</td>';
				echo '</tr>';
			}
		}
		if(!$ads_found)
		{
			echo '<tr><td colspan="5">'.__('No creatives found.', 'wp_affiliate').'</td></tr>';
		}
	}
	else
	{
		echo '<tr><td colspan="5">'.__('No creatives found.', 'wp_affiliate').'</td> </tr>';
	}

	echo '</tbody>
	</table>';	
	echo '<br /><a href="admin.php?page=edit_banners&action=article" class="button rbutton">'.__('Add Creative', 'wp_affiliate').'</a>';
}

function wp_aff_edit_banners_menu()
{
	echo "<h2>Configure Banners/Text Links</h2>";
	echo "<p>Not sure how to configure a text link or banner for your affiliates to use? <a href=\"http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=153\" target=\"_blank\">Check this tutorial</a></p>";
	
	global $wpdb;
	$banners_table_name = $wpdb->prefix . "affiliates_banners_tbl";
	
	//If being edited, grab current info
	if (isset($_GET['editrecord']))
	{
		$theid = $_GET['editrecord'];
		$editingrecord = $wpdb->get_row("SELECT * FROM $banners_table_name WHERE number = '$theid'", OBJECT);
	}	
	if (isset($_POST['Submit']))
	{
		if(!isset($_POST['editedrecord']))$_POST['editedrecord']="";
		$post_editedrecord = $wpdb->escape($_POST['editedrecord']);
		$tmp_name = strip_tags(stripslashes($_POST['name']));
		$post_name = $wpdb->escape($tmp_name);
		$post_ref_url = $wpdb->escape(strip_tags(stripslashes($_POST['refurl'])));
		$post_linktext = $wpdb->escape(strip_tags(stripslashes($_POST['linktext'])));
		$post_imageurl = $wpdb->escape(strip_tags(stripslashes($_POST['imageurl'])));
		$tmp_desc = strip_tags(stripslashes($_POST['description']));
		$post_description = $wpdb->escape($tmp_desc);
		//$tmpdescription = htmlentities(stripslashes($_POST['description']) , ENT_COMPAT, "UTF-8");
		//$post_description = $wpdb->escape($tmpdescription);		
		
		//Do some data validation
		$validation_error_msg = "";
	    if(!wp_aff_is_valid_url_if_not_empty($post_ref_url))
        {
        	$validation_error_msg .= wp_aff_url_validation_error_message("Target URL",$post_ref_url);
        }		
		if(!wp_aff_is_valid_url_if_not_empty($post_imageurl))
        {
        	$validation_error_msg .= wp_aff_url_validation_error_message("Image URL",$post_imageurl);
        }		
		if(!empty($validation_error_msg)){
	        echo '<div id="message" class="error"><p><strong>';
	        echo $validation_error_msg;
	        echo '</strong></p></div>';		
		}
		//End of data validation		

		if ($post_editedrecord=='')
		{
			if(empty($post_name)){echo '<div id="message" class="updated fade"><p>Error! Ad Name cannot be empty!</p></div>';
			}else{// Add the record to the DB				
				$updatedb = "INSERT INTO $banners_table_name (name, ref_url, link_text, image, description) VALUES ('$post_name', '$post_ref_url','$post_linktext','$post_imageurl','$post_description')";
				$results = $wpdb->query($updatedb);
				echo '<div id="message" class="updated fade"><p>Banner &quot;'.$post_name.'&quot; created.</p></div>';
			}
		}
		else
		{
			// Update the info
			$updatedb = "UPDATE $banners_table_name SET name = '$post_name', ref_url = '$post_ref_url', link_text = '$post_linktext', image = '$post_imageurl', description = '$post_description' WHERE number='$post_editedrecord'";
			$results = $wpdb->query($updatedb);
			
			$_GET['editrecord'] = $post_editedrecord;
			$editingrecord = $wpdb->get_row("SELECT * FROM $banners_table_name WHERE number = '$post_editedrecord'", OBJECT);
			echo '<div id="message" class="updated fade"><p>'.__('Banner', 'wp_affiliate').' &quot;'.$post_name.'&quot; '.__('updated.', 'wp_affiliate').'</p></div>';
		}
	}
	// Delete
	if (isset($_POST['deleterecord']))
	{
		$post_editedrecord = $wpdb->escape($_POST['editedrecord']);
		echo '<div id="message" class="updated fade"><p>'.__('Do you really want to delete this Banner? This action cannot be undone.', 'wp_affiliate').' <a href="admin.php?page=edit_banners&deleterecord='.$post_editedrecord.'">'.__('Yes', 'wp_affiliate').'</a> &nbsp; <a href="admin.php?page=edit_banners&editrecord='.$post_editedrecord.'">'.__('No!', 'wp_affiliate').'</a></p></div>';
	}
	if (isset($_GET['deleterecord']))
	{
		$therecord=$_GET['deleterecord'];
		$updatedb = "DELETE FROM $banners_table_name WHERE number='$therecord'";
		$results = $wpdb->query($updatedb);
		echo '<div id="message" class="updated fade"><p>'.__('Banner deleted.', 'wp_affiliate').'</p></div>';
	}
?>

<form method="post" action="">

<div class="postbox">
<h3><label for="title">Add/Edit Text Links or Image Banners for Your Affiliates to Use</label></h3>
<div class="inside">
	
<table class="form-table">
<?php if (isset($_GET['editrecord'])) { echo '<input name="editedrecord" type="hidden" value="'.$_GET['editrecord'].'" />'; } ?>

<tr valign="top">
<th scope="row"><?php _e('Name', 'wp_affiliate'); ?></th>
<td><input name="name" type="text" id="name" value="<?php if(isset($editingrecord->name))echo $editingrecord->name; ?>" size="40" /><br/><?php _e('Name of the Banner', 'wp_affiliate'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Target URL', 'wp_affiliate'); ?></th>
<td><input name="refurl" type="text" id="refurl" value="<?php if(isset($editingrecord->ref_url))echo $editingrecord->ref_url; ?>" size="100" /><br/><?php _e('URL of the Target Page, eg. the URL of your products page', 'wp_affiliate'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Anchor/Alt Text', 'wp_affiliate'); ?></th>
<td><input name="linktext" type="text" id="linktext" value="<?php if(isset($editingrecord->link_text))echo $editingrecord->link_text; ?>" size="40" /><br/><?php _e('This text is used as the Anchor Text for a Text Link or as the Alt text when using an image banner', 'wp_affiliate'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Image URL', 'wp_affiliate'); ?></th>
<td><input name="imageurl" type="text" id="imageurl" value="<?php if(isset($editingrecord->image))echo $editingrecord->image; ?>" size="100" /><br/><?php _e('The URL of the image to be used for the banner. Leave empty when creating a Text Link', 'wp_affiliate'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Description', 'wp_affiliate'); ?></th>
<td><textarea name="description" cols="40" rows="5"><?php if(isset($editingrecord->description))echo $editingrecord->description; ?></textarea><br/><?php _e('A description for this Banner/Link', 'wp_affiliate'); ?></td>
</tr>
</table>
</div></div>

<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Banner', 'wp_affiliate'); ?>" /> &nbsp; <?php if (isset($_GET['editrecord'])) { ?><input type="submit" name="deleterecord" value="<?php _e('Delete Banner', 'wp_affiliate'); ?>" /><?php } ?></p>
</form>

<?php

	echo '<a href="admin.php?page=manage_banners" class="button rbutton">'.__('Manage Banners', 'wp_affiliate').'</a>';	
}

function wp_aff_edit_articles_menu()
{
	echo "<h2>Configure Creative</h2>";
	global $wpdb;
	$banners_table_name = $wpdb->prefix . "affiliates_banners_tbl";
	
	//If being edited, grab current info
	if (isset($_GET['editrecord']))
	{
		$theid = $_GET['editrecord'];
		$editingrecord = $wpdb->get_row("SELECT * FROM $banners_table_name WHERE number = '$theid'", OBJECT);
	}	
	if (isset($_POST['Submit']))
	{
		$post_editedrecord = $wpdb->escape($_POST['editedrecord']);
		$post_name = $wpdb->escape(strip_tags(stripslashes($_POST['name'])));

		$tmpdescription = htmlentities(stripslashes($_POST['description']) , ENT_COMPAT, "UTF-8");
		$post_description = $wpdb->escape($tmpdescription);		
		//$post_description = $wpdb->escape($_POST['description']);
		$creative_type = "3";

		if ($post_editedrecord=='')
		{
			// Add the record to the DB
			$updatedb = "INSERT INTO $banners_table_name (name, description, creative_type) VALUES ('$post_name','$post_description','$creative_type')";
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>Item &quot;'.$post_name.'&quot; created.</p></div>';
		}
		else
		{
			// Update the info
			$updatedb = "UPDATE $banners_table_name SET name = '$post_name', description = '$post_description' WHERE number='$post_editedrecord'";
			$results = $wpdb->query($updatedb);
			
			$_GET['editrecord'] = $post_editedrecord;
			$editingrecord = $wpdb->get_row("SELECT * FROM $banners_table_name WHERE number = '$post_editedrecord'", OBJECT);			
			echo '<div id="message" class="updated fade"><p>'.__('Item', 'wp_affiliate').' &quot;'.$post_name.'&quot; '.__('updated.', 'wp_affiliate').'</p></div>';
		}
	}
	// Delete
	if (isset($_POST['deleterecord']))
	{
		$post_editedrecord = $wpdb->escape($_POST['editedrecord']);
		echo '<div id="message" class="updated fade"><p>'.__('Do you really want to delete this item? This action cannot be undone.', 'wp_affiliate').' <a href="admin.php?page=edit_banners&action=article&deleterecord='.$post_editedrecord.'">'.__('Yes', 'wp_affiliate').'</a> &nbsp; <a href="admin.php?page=edit_banners&action=article&editrecord='.$post_editedrecord.'">'.__('No!', 'wp_affiliate').'</a></p></div>';
	}
	if (isset($_GET['deleterecord']))
	{
		$therecord=$_GET['deleterecord'];
		$updatedb = "DELETE FROM $banners_table_name WHERE number='$therecord'";
		$results = $wpdb->query($updatedb);
		echo '<div id="message" class="updated fade"><p>'.__('Item deleted.', 'wp_affiliate').'</p></div>';
	}
?>

You can add pre written and optimized text copy of several hundred words with links built into it so your affiliates can use it to promote your products.
<br /><br />

<form method="post" action="">

<div class="postbox">
<h3><label for="title">Add/Edit Creative for Your Affiliates to Use</label></h3>
<div class="inside">
	
<table class="form-table">
<?php if (isset($_GET['editrecord'])) { echo '<input name="editedrecord" type="hidden" value="'.$_GET['editrecord'].'" />'; } ?>

<tr valign="top">
<th scope="row"><?php _e('Name', 'wp_affiliate'); ?></th>
<td><input name="name" type="text" id="name" value="<?php if(isset($editingrecord->name)){echo $editingrecord->name;} ?>" size="40" />
<p class="description">Enter a name for this creative</p></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Creative Code', 'wp_affiliate'); ?></th>
<td><textarea name="description" cols="80" rows="10"><?php if(isset($editingrecord->description)){echo $editingrecord->description;} ?></textarea>
<p class="description">The HTML code of the creative. Your affiliates will copy this and use it on their sites. 
<br />Use XXXX where the affiliate ID should be. The plugin will dynamically replace XXXX with the correct affiliate ID when your affiliates view this creative in their affiliate area.
<br />What it means is that if you use the following (as an example) in your creative then when an affiliate logs into his/her account and views the code it will automatically put his/her affiliate ID in place of the "XXXX" so he/she can just do a copy and paste
<br /><code>http://www.example.com/my-landing-page?ap_id=XXXX</code>
</p>
</td>
</tr>
</table>
</div></div>

<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save', 'wp_affiliate'); ?>" /> &nbsp; <?php if (isset($_GET['editrecord'])) { ?><input type="submit" name="deleterecord" value="<?php _e('Delete', 'wp_affiliate'); ?>" /><?php } ?></p>
</form>

<?php
	echo '<a href="admin.php?page=manage_banners&action=article" class="button rbutton">'.__('Manage Creatives', 'wp_affiliate').'</a>';
}

function wp_affiliate_delete_ad_data($ad_id)
{
    global $wpdb;
	$banners_table_name = $wpdb->prefix . "affiliates_banners_tbl";;
	
	$updatedb = "DELETE FROM $banners_table_name WHERE number='$ad_id'";
	$results = $wpdb->query($updatedb);
    if($results>0){
        return true;
    }
    else{
        return false;
    }	
}
?>
