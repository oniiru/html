<?php

if( $_POST[ 'supdate' ] ){

	$html = stripslashes( $_POST[ 'no-access' ] );
	DlPs_Options::UpdateNoAccessHtml( $html );

	$reg_url = $_POST[ 'reg-url' ];
	DlPs_Options::UpdateRegisterUrl( $reg_url );

	$login_url = $_POST[ 'login-url' ];
	DlPs_Options::UpdateLoginUrl( $login_url );

	$subscribe_url = $_POST[ 'subscribe-url' ];
	DlPs_Options::UpdateSubscribeUrl( $subscribe_url );

	if( $_POST[ 'show_lock_icons' ] ) {
		DlPs_Options::SetIsLockIconVisible( true );
	} else {
		DlPs_Options::SetIsLockIconVisible( false );
	}

/*	if( $_POST[ 'payment_gateway' ] ) {
		DlPs_Options::UpdateGateway( $_POST[ 'payment_gateway' ] );
	} else {
		DlPs_Options::UpdateGateway( null );
	}*/

	echo "<div class='updated'><p><strong>User(s) updated.</strong></p></div>";
}

// Get fresh data for rendering
//
$no_access_html = DlPs_Options::GetNoAccessHtml( false );
$reg_url = DlPs_Options::GetRegisterUrl();
$login_url = DlPs_Options::GetLoginUrl();
$subscribe_url = DlPs_Options::GetSubscribeUrl();

// Is the stripe payment plugin active?
//
$is_stripe_found = false;
if( is_plugin_active( 'diglabs-stripe-payments/diglabs-stripe-payments.php' ) ) {
	$is_stripe_found = true;
}

?>


<h3>Additional Setup</h3>
<form method="post" action="">

	<table class="form-table">

		<tr valign="top">
			<th scope="row"><label for="no-access">
				HTML for no access content.<br />
				<small>
					This content is substituted for the premium content body. 
					So a link to the registration page might be appropriate.
					Leave blank to show no title or body.<br /><br />
					<strong>Wildcards</strong><br />
					<code>{login_url}</code> - login URL set below.<br />
					<code>{register_url}</code> - registeration URL set below.
					<code>{subscribe_url}</code> - subscription URL set below.
				</small>
			</label></th>
			<td><textarea name="no-access" cols=60 rows=10><?php echo $no_access_html; ?></textarea></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="reg-url">
				Registration Page URL<br />
				<small>
					This will be used to generate links to allow unregistered
					users to register. Use <code>[diglabs_premium_register]</code>
					on the page to generate the registration form. Once registered
					users will be able to login.
				</small>
			</label></th>
			<td><input name="reg-url" value="<?php echo $reg_url; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="subscribe-url">
				Subscription Page URL<br />
				<small>
					This will be used to generate links to allow logged in
					users to subscribe. Use the <code>[diglabs_premium_subscribe]</code>
					shortcode in a Stripe payment form to generate the payment options.
				</small>
			</label></th>
			<td><input name="subscribe-url" value="<?php echo $subscribe_url; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="login-url">
				Login Page URL<br />
				<small>
					This will be used to generate links to allow registered
					users to login. Use <code>[diglabs_premium_login]</code>
					on the page to generate the login form.
				</small>
			</label></th>
			<td><input name="login-url" value="<?php echo $login_url; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="show_lock_icons">
				Show Lock Icons<br />
				<small>
					This will show a small lock indicating premium content status. The lock
					will be closed for non-subscribers and open for subscribers. The lock
				</small>
			</label></th>
			<td>
				<input name="show_lock_icons" type="checkbox" value="show" <?php if(DlPs_Options::IsLockIconVisible()){echo "checked=checked";}?> />
			</td>
		</tr>

	</table>
	<p><input class="button-primary" name="supdate" type="submit" value="Update" /></p>
</form>