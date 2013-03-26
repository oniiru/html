<?php

// Widget test
class Dl_ps_login_widget extends WP_Widget {

	function Dl_ps_login_widget() {

		$widget_ops = array(
				'classname'		=> 'Dl_ps_login_widget_class',
				'description'	=> 'Display a login / registration form to access premium content.'
			);
		$this->WP_Widget( 'Dl_ps_login_widget', 'Dig Labs Premium Subscription', $widget_ops );
	}

	function form( $instance ) { 
		$defaults = array( 'title' => 'Premium Subscriptions', 'reg_url' => '' ); 
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$title = $instance['title'];
		?>
		<p>Title: <input class="widefat" name=" <?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php 
	}

	function update( $new_instance, $old_instance ) { 
		$instance = $old_instance; 
		$instance['title'] = strip_tags( $new_instance['title'] ); 
		return $instance; 
	}

	function widget( $args, $instance ) { 

		extract($args); 
		
		// Before widget markup
		//
		echo $before_widget; 
		
		// Title
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( !empty( $title ) ) { 
			echo $before_title . $title . $after_title; 
		}; 

		// Content
		if( ! is_user_logged_in() ) {

			// Show the login form.
			//

			$register_url = DlPs_Options::GetRegisterUrl();
			?>

			<form action="" method="post">
				<input type="hidden" name="dlps_login_posted" value="1" />
				<?php
				// Messages
				global $wpError;
				if( !empty( $wpError ) ) {
					echo "<p>" . $wpError . "</p>";
				}
				?>
				<p><label>Username <input type="text" name="user" id="user" /></label></p>
				<p><label>Password <input type="password" name="pass" id="pass" /></label></p>
				<p><label><input type="checkbox" checked="checked" name="remember" id="remember" /> Remember me!</label></p>
				<p><input type="submit" name="login" id="login" value="Login" /></p>
				<p>Not a member? <a href="<?php echo $register_url; ?>/">Register Now!</a>
			</form>
			
			<?php

		} else {

			// The user is logged in. Show their info.
			//

			global $current_user;

			$user = new DlPs_User( $current_user );

			get_currentuserinfo();
			$username = (trim( $current_user->user_firstname )=='') ? $current_user->user_login : $current_user->user_firstname;
			?>
			<ul>
				<?php
				echo "<li><a href='" .get_bloginfo('url') . "/wp-admin/'>My Account</a></li>";
				if( $user->expiration < mktime() ) {

					// This user's subscription is expired
					//
					$pay_url = DlPs_Options::GetSubscribeUrl();

					echo "<li><a href='$pay_url'>EXPIRED on " . date("F j, Y", $user->expiration) . "</a></li>";
				} else {
					echo "<li>Expiration Date: " . date("F j, Y", $user->expiration) . "</li>";
				}
				?>

				<li><a href=""
				<li><a href="<?php echo get_bloginfo('url'); ?>/?logout=true">Log out (<?php echo $username; ?>)</a></li>
			</ul>
			<?php	
		}

		// After widget markup
		echo $after_widget;
	}

}

?>