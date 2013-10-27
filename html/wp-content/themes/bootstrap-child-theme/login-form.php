<?php

/*

If you would like to edit this file, copy it to your current theme's directory and edit it there.

Theme My Login will always look in your theme's directory first, before using this default template.

*/

?>

<div class="login" id="theme-my-login<?php $template->the_instance(); ?>">

	<?php $template->the_action_template_message( 'login' ); ?>

	<?php $template->the_errors(); ?>

	<form name="loginform" id="loginform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'login' ); ?>" method="post">

		<p>

			<label for="user_login<?php $template->the_instance(); ?>"><?php _e( 'Email', 'theme-my-login' ) ?></label>

			<input type="text" placeholder="Email" name="log" id="user_login<?php $template->the_instance(); ?>" class="input" value="<?php $template->the_posted_value( 'log' ); ?>" size="20" />

		</p>

		<p>

			<label for="user_pass<?php $template->the_instance(); ?>"><?php _e( 'Password', 'theme-my-login' ) ?></label>

			<input placeholder="Password" type="password" name="pwd" id="user_pass<?php $template->the_instance(); ?>" class="input" value="" size="20" />

		</p>

<?php

do_action( 'login_form' ); // Wordpress hook

do_action_ref_array( 'tml_login_form', array( &$template ) ); // TML hook

?>

		<p class="submit">

			<input type="submit" name="wp-submit" class="btn btn-custom" id="wp-submit<?php $template->the_instance(); ?>" value="<?php _e( 'Log In', 'theme-my-login' ); ?>" />

			<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'login' ); ?>" />

			<input type="hidden" name="testcookie" value="1" />

			<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />

		</p>
        
        <p id="custom_action_links">
        	<a href="login/?action=lostpassword">Forgot your password?</a>
          <div class="hidethis">  No account yet? <a href="<?php echo get_bloginfo('url').'/pricing-2/' ?>">Sign Up Here</a></div>
        </p>
        
        <?php //$template->the_action_links( array( 'login' => true, 'register' => true, 'lostpassword' => true ) ); ?>

	</form>
</div>