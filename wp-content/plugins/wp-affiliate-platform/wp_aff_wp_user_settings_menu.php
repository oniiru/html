<?php

function wp_aff_wp_user_settings_menu_page() {

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	if (isset($_POST['info_update'])) {

		$wp_aff_platform_config->setValue('wp_aff_auto_create_aff_account', ($_POST['wp_aff_auto_create_aff_account'] == '1') ? '1' : '' );

		$wp_aff_platform_config->setValue('wp_aff_auto_login_to_aff_account', ($_POST['wp_aff_auto_login_to_aff_account'] == '1') ? '1' : '' );

		$wp_aff_platform_config->setValue('wp_aff_auto_logout_aff_account', ($_POST['wp_aff_auto_logout_aff_account'] == '1') ? '1' : '' );



		$wp_aff_platform_config->saveConfig();

		echo '<div id="message" class="updated fade"><p><strong>';

		echo 'Options Updated!';

		echo '</strong></p></div>';
	}
	?>



	<p class="wp_affiliate_grey_box">

		These are optional settings that can be handy if you use WordPress users on your site. For example, you can choose to automatically create affiliate accounts when a WordPress user gets created on your site.

	</p>



	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

		<input type="hidden" name="info_update" id="info_update" value="true" />



		<div class="postbox">

			<h3><label for="title">WordPress User Integration Settings</label></h3>

			<div class="inside">



				<table width="100%" border="0" cellspacing="0" cellpadding="6">



					<tr valign="top"><td width="25%" align="left">

							Automatically Create Affiliate Account

						</td><td align="left">

							<input name="wp_aff_auto_create_aff_account" type="checkbox"<?php if ($wp_aff_platform_config->getValue('wp_aff_auto_create_aff_account') != '') echo ' checked="checked"'; ?> value="1" />

							<br /><i>If checked, it will automatically create a corresponding affiliate account when a user registers for an WordPress user account on your site.</i><br /><br />

						</td></tr>



					<tr valign="top"><td width="25%" align="left">

							Automatically Log into Affiliate Account

						</td><td align="left">

							<input name="wp_aff_auto_login_to_aff_account" type="checkbox"<?php if ($wp_aff_platform_config->getValue('wp_aff_auto_login_to_aff_account') != '') echo ' checked="checked"'; ?> value="1" />

							<br /><i>If checked, when a WordPress user logs into the site it will automatically log him/her into the corresponding affiliate account (given the affiliate username is the same as the WP Username).</i><br /><br />

						</td></tr>



					<tr valign="top"><td width="25%" align="left">

							Automatically Log out

						</td><td align="left">

							<input name="wp_aff_auto_logout_aff_account" type="checkbox"<?php if ($wp_aff_platform_config->getValue('wp_aff_auto_logout_aff_account') != '') echo ' checked="checked"'; ?> value="1" />

							<br /><i>If checked, when a WordPress user logs out, the user will automatically be logged out from the corresponding affiliate account.</i><br /><br />

						</td></tr>



				</table>

			</div></div>



		<div class="submit">

			<input type="submit" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />

		</div>



	</form>

	<?php
}
?>