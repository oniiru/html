<?php

// Login handler
//
add_action( 'init', 'dl_ps_widget_login', 0 );
function dl_ps_widget_login() {

	if( isset( $_GET['logout'] )) {

		// User clicked the logout link in the sidebar
		wp_logout();

		$url = get_bloginfo( 'url' ) . "/";
		wp_safe_redirect( $url );
		exit;
	}

	if( isset( $_POST['dlps_login_posted'] ) && 
		! empty( $_POST['dlps_login_posted'] ) ) {

		// The widget login form caused this post
		$data = array();
		$data[ 'user_login' ] = $_POST[ 'user' ];
		$data[ 'user_password' ] = $_POST[ 'pass' ];
		$data[ 'remember' ] = ( isset( $_POST[ 'remember' ] ) ) ? true : false;

		$wp_user = wp_signon( $data, false );
		if( is_wp_error( $wp_user ) ) {
			global $wpError;
			$wpError = '<strong>Invalid credentials!</strong>';
		} else {

			$user = new DlPs_User( $wp_user );
			$url = get_bloginfo( 'url' ) . "/";
			if( $user->level_id == null || $user->expiration < mktime() ) {

				$url = DlPs_Options::GetSubscribeUrl();				
			}

			wp_safe_redirect( $url );
			exit;
		}

	}

	if( isset( $_POST['dlps_register_posted'] ) && 
		! empty( $_POST['dlps_register_posted'] ) ) {

		// The widget registration form caused this post
		$username = $_POST[ 'user' ];
		$email = $_POST[ 'email' ];
		$pass1 = $_POST[ 'pass1' ];
		$pass2 = $_POST[ 'pass2' ];

		if( $pass1 != $pass2 ) {
			global $wpError;
			$wpError = '<strong>Passwords</strong> do not match!';
		} else {
			global $wpError;
			$wpError = DlPs_User::Create( $username, $pass1, $email );
			$int = (int)$wpError;
			if( $int != 0 ) {
				$data = array();
				$data[ 'user_login' ] = $username;
				$data[ 'user_password' ] = $pass1;
				$data[ 'remember' ] = false;
				$user = wp_signon( $data, false );

				$url = DlPs_Options::GetSubscribeUrl();
				wp_safe_redirect( $url );
				exit;
			} 
		}
	}
}

?>