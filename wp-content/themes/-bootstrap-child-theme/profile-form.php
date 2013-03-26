<?php

/*

If you would like to edit this file, copy it to your current theme's directory and edit it there.

Theme My Login will always look in your theme's directory first, before using this default template.

*/



$GLOBALS['current_user'] = $current_user = wp_get_current_user();

$GLOBALS['profileuser'] = $profileuser = get_user_to_edit( $current_user->ID );



$user_can_edit = false;

foreach ( array( 'posts', 'pages' ) as $post_cap )

	$user_can_edit |= current_user_can( "edit_$post_cap" );

?>



<div class="login profile" id="theme-my-login<?php $template->the_instance(); ?>">

	<?php $template->the_action_template_message( 'profile' ); ?>

	<?php $template->the_errors(); ?>

	<form id="your-profile" action="" method="post">

		<?php wp_nonce_field( 'update-user_' . $current_user->ID ) ?>

		<p>

			<input type="hidden" name="from" value="profile" />

			<input type="hidden" name="checkuser_id" value="<?php echo $current_user->ID; ?>" />

		</p>

		<?php if(!current_user_can('subscriber')) : ?>

		<?php do_action( 'profile_personal_options', $profileuser ); ?>

		<?php endif; // Subscribers cannot see this. IAN ?>

<div id="profileinner">
		<table class="form-table">

		<tr>

			<th><label for="user_login"><?php _e( 'Username', 'theme-my-login' ); ?></label></th>

			<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e( 'Usernames can only be changed by admins. Let us know if you need to change it.', 'theme-my-login' ); ?></span></td>

		</tr>


		</table>


		<table class="form-table">

		<tr>

			<th><label for="email"><?php _e( 'E-mail', 'theme-my-login' ); ?> <span class="description"><?php _e( '(required)', 'theme-my-login' ); ?></span></label></th>

			<td><input type="text" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ) ?>" class="regular-text" /></td>

		</tr>


		
		

		</table>


		<table class="form-table">


		<?php

		$show_password_fields = apply_filters( 'show_password_fields', true, $profileuser );

		if ( $show_password_fields ) :

		?>

		<tr id="password">

			<th><label for="pass1"><?php _e( 'New Password', 'theme-my-login' ); ?></label></th>

			<td><input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" /><br> <span class="description"><?php _e( 'If you would like to change the password type a new one. Otherwise leave this blank.', 'theme-my-login' ); ?></span><br />

				<input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" /><br> <span class="description"><?php _e( 'Type your new password again.', 'theme-my-login' ); ?></span><br />

				<div id="pass-strength-result"><?php _e( 'Strength indicator', 'theme-my-login' ); ?></div><br><br>

				<p class="description indicator-hint"><?php _e( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', 'theme-my-login' ); ?></p>

			</td>

		</tr>

		<?php endif; ?>

		</table>


		<?php

			do_action( 'show_user_profile', $profileuser );

		?>



		<?php if ( count( $profileuser->caps ) > count( $profileuser->roles ) && apply_filters( 'additional_capabilities_display', true, $profileuser ) ) { ?>

		<br class="clear" />

			<table width="99%" style="border: none;" cellspacing="2" cellpadding="3" class="editform">

				<tr>

					<th scope="row"><?php _e( 'Additional Capabilities', 'theme-my-login' ) ?></th>

					<td><?php

					$output = '';

					global $wp_roles;

					foreach ( $profileuser->caps as $cap => $value ) {

						if ( !$wp_roles->is_role( $cap ) ) {

							if ( $output != '' )

								$output .= ', ';

							$output .= $value ? $cap : "Denied: {$cap}";

						}

					}

					echo $output;

					?></td>

				</tr>

			</table>
			
		<?php } ?>



		<p class="submit" style="overflow:hidden;">

			<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>" />

			<input type="submit" class="btn btn-info profilebutton" value="<?php esc_attr_e( 'Update Profile', 'theme-my-login' ); ?>" name="submit" />

		</p>

	</form>
</div>
</div>

