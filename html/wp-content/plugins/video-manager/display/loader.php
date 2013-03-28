<?php

/**
 * Load all scripts and stylesheet for the WP front-end
 * @since 1.0.0
 */
function video_directory_style() {
	wp_enqueue_style('video-directory-style', $GLOBALS['url_path'] . '/css/video-directory.css', false, '1.0.0', 'screen');

	$custom_css = get_stylesheet_directory() . '/' . 'video-directory.css';
	if (file_exists($custom_css)) {
		$stylesheet = get_stylesheet();
		wp_enqueue_style('video-directory-style-custom', get_theme_root_uri() . '/' . $stylesheet . '/video-directory.css', false, '1.0.0', 'screen');
	}

	wp_enqueue_style('video-directory-style');
	wp_enqueue_style('video-directory-style-custom');

	wp_enqueue_style('ui-custom', $GLOBALS['url_path'] . '/css/ui-dialog.css', false, '1.0.0', 'screen');
	wp_enqueue_style('ui-custom');
}

function video_directory_scripts() {
	wp_register_script('video-directory-scripts', $GLOBALS['url_path'] . '/js/video-directory.js', array('jquery'), '1.0.0');
	wp_enqueue_script('video-directory-scripts');
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('jquery-ui-resizable');
	wp_enqueue_script('jquery-ui-tabs');
}

function facybox() {
	wp_register_script('facybox-mousewheel', $GLOBALS['url_path'] . '/display/fancybox/jquery.mousewheel-3.0.4.pack.js', array('jquery'), '1.0.0');
	wp_enqueue_script('facybox-mousewheel');
	wp_register_script('facybox-js', $GLOBALS['url_path'] . '/display/fancybox/jquery.fancybox-1.3.4.js', array('jquery'), '1.0.0');
	wp_enqueue_script('facybox-js');

	wp_enqueue_style('fancybox-stylesheet', $GLOBALS['url_path'] . '/display/fancybox/jquery.fancybox-1.3.4.css', false, '1.0.0', 'screen');
	wp_enqueue_style('fancybox-stylesheet');
}

function jw_player() {
	wp_register_script('jw-player', $GLOBALS['url_path'] . '/display/mediaplayer-5.8/jwplayer.js');
	wp_enqueue_script('jw-player');
}

function video_manager_ajax_register() {
	global $video_user_email;
	if (!empty($_REQUEST['video_manager_ajax'])) {
		$return = array();
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			require_once( ABSPATH . WPINC . '/registration.php');
			$errors = video_manager_register_new_user($_POST['user_login'], $_POST['user_email']);
			if (!is_wp_error($errors)) {
				//Success
				$user = new WP_User($errors['user_id']);
				if (has_action('tml_new_user_registered')) {
					$video_user_email = $user->get('user_login');
					add_filter('login_url', 'video_manger_ajax_only_for');
					add_filter('tml_custom_email_variables', 'video_tml_custom_email_variables');
					do_action('tml_new_user_registered', $user->get('user_login'), $errors['user_pass']);
				} else {
					wp_new_user_notification($errors['user_id'], $errors['user_pass']);
				}
				$user->set_role($_POST['user_type']);
				$return['result'] = true;
				$return['message'] = __('Registration complete. Please check your e-mail.');
			} else {
				//Something's wrong
				$return['result'] = false;
				$return['error'] = $errors->get_error_message();
			}
		}
		echo json_encode($return);
		exit();
	}
}

function video_tml_custom_email_variables($replacement) {
	global $video_user_email;

	$replacement['%user_email%'] = $video_user_email;
	return $replacement;
}

function video_manger_ajax_only_for($login_url) {
	return str_replace('wp-login.php', 'login/', $login_url);
}

/**
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
function video_manager_register_new_user($user_login, $user_email) {
	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user($user_login);
	$user_email = apply_filters('user_registration_email', $user_email);

	// Check the username
	if ($sanitized_user_login == '') {
		$errors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.'));
	} elseif (!validate_username($user_login)) {
		$errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.'));
		$sanitized_user_login = '';
	} elseif (username_exists($sanitized_user_login)) {
		$errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.'));
	}

	// Check the e-mail address
	if ($user_email == '') {
		$errors->add('empty_email', __('<strong>ERROR</strong>: Please type your e-mail address.'));
	} elseif (!is_email($user_email)) {
		$errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
		$user_email = '';
	} elseif (email_exists($user_email)) {
		$errors->add('email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.'));
	}

	do_action('register_post', $sanitized_user_login, $user_email, $errors);

	$errors = apply_filters('registration_errors', $errors, $sanitized_user_login, $user_email);

	if ($errors->get_error_code())
		return $errors;

	$user_pass = wp_generate_password(12, false);
	$user_id = wp_create_user($sanitized_user_login, $user_pass, $user_email);
	if (!$user_id) {
		$errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email')));
		return $errors;
	}

	update_user_option($user_id, 'default_password_nag', true, true); //Set up the Password change nag.

	return array('user_id' => $user_id, 'user_pass' => $user_pass);
}

if (!is_admin()) {
	add_action('wp_enqueue_scripts', 'facybox');
	add_action('wp_enqueue_scripts', 'jw_player');
	add_action('init', 'video_directory_style');
	add_action('wp_enqueue_scripts', 'video_directory_scripts');
}
add_action('wp_ajax_nopriv_video_manager_ajax_register', 'video_manager_ajax_register');
add_action('admin_init', 'facybox');
?>