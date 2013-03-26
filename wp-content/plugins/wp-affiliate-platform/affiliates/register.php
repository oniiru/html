<?php

function wp_aff_wp_user_integraton_hooks_handler() { 

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	if ($wp_aff_platform_config->getValue('wp_aff_auto_login_to_aff_account') == '1') {

		add_action('wp_login', 'handle_wp_user_login', 10, 2);
	}

	if ($wp_aff_platform_config->getValue('wp_aff_auto_logout_aff_account') == '1') {

		add_action('wp_logout', 'wp_aff_handle_wp_user_logout');

		add_action('wp_aff_logout', 'wp_aff_handle_affiliate_logout');
	}

	if ($wp_aff_platform_config->getValue('wp_aff_auto_create_aff_account') == '1') {

		add_action('user_register', 'wp_aff_handle_wp_user_registration');

		add_action('profile_update', 'wp_aff_sync_wp_user_profile', 10, 2);

		add_action('wp_aff_profile_update', 'wp_aff_profile_update_handler', 10, 2);
	}
}

function handle_wp_user_login($username, $user_obj) {

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	if ($wp_aff_platform_config->getValue('wp_aff_auto_login_to_aff_account') == '1') {

		wp_affiliate_log_debug("WP User Integration - attempting to log this user into the affiliate account. Username: " . $username, true);

		if (!wp_aff_is_logged_in()) {

			if (wp_aff_check_if_account_exists_by_affiliate_id($username)) {

				$_SESSION['user_id'] = $username;

				setcookie("user_id", $username, time() + 60 * 60 * 6, "/", COOKIE_DOMAIN); //set cookie for 6 hours

				wp_affiliate_log_debug("Found a corresponding affiliate account for this WP User! Logging the user into affiliate account.", true);
			} else {

				wp_affiliate_log_debug("No corresponding affiliate ID exists for this WP User (" . $username . ") so can't log this user in!", true);
			}
		}
	}
}

function wp_aff_handle_wp_user_logout() {

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	$wp_aff_auto_affiliate_logout = $wp_aff_platform_config->getValue('wp_aff_auto_logout_aff_account');

	if ($wp_aff_auto_affiliate_logout) {//logout the affiliate account
		wp_affiliate_log_debug("WP User Integration - logging out from affiliate account", true);

		unset($_SESSION['user_id']);

		setcookie("user_id", "", time() - 60 * 60 * 24 * 7, "/", COOKIE_DOMAIN);
	}
}

function wp_aff_handle_affiliate_logout($aff_id) {

	wp_affiliate_log_debug("WP User Integration - logging out from WP User account: " . $aff_id, true);

	wp_clear_auth_cookie();
}

function wp_aff_handle_wp_user_registration($user_id) {

	wp_affiliate_log_debug("WP User Integration - A WP User account just got created. Checking if an affiliate account needs to be created for this user: " . $user_id, true);

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	if ($wp_aff_platform_config->getValue('wp_aff_auto_create_aff_account') != '1') {

		wp_affiliate_log_debug("WP User Integration - auto user creation feature is disabled. No affiliate account will be created!", true);

		return;
	}

	$user_info = get_userdata($user_id); //get_user_by('user_login', $username);

	$fields = array();

	$fields['refid'] = $user_info->user_login;

	$fields['pass'] = $user_info->user_pass;

	$fields['email'] = $user_info->user_email;

	$fields['firstname'] = $user_info->first_name;

	$fields['lastname'] = $user_info->last_name;

	$fields['date'] = (date("Y-m-d"));

	$fields['commissionlevel'] = get_option('wp_aff_commission_level');

	$fields['referrer'] = wp_affiliate_get_referrer();
	echo '<pre>';
	print_r($_REQUEST);
	print_r($fields);
	echo '</pre>';

die('ok');

	if (wp_aff_check_if_account_exists_by_affiliate_id($fields['refid'])) {

		wp_affiliate_log_debug("WP User Integration - an affiliate account with this affiliate ID already exists. No account will be created.", true);

		return;
	}

	wp_affiliate_log_debug("WP User Integration - Creating affiliate account for the folloiwng user.", true);

	wp_aff_write_debug_array($fields, true);

	wp_aff_create_affilate_using_array_data($fields);

	wp_affiliate_log_debug("WP User Integration - Affiliate account successfully created!", true);
}

function wp_aff_sync_wp_user_profile($wp_user_id) {

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	if ($wp_aff_platform_config->getValue('wp_aff_auto_create_aff_account') != '1') {//This feature is disabled
		return;
	}

	global $wpdb;

	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;

	$wp_user_data = get_userdata($wp_user_id);

	$aff_user_id = $wp_user_data->user_login;

	wp_affiliate_log_debug("WP User Integration - updating affiliate account details for affiliate ID: " . $aff_user_id, true);



	$email = $wp_user_data->user_email;

	$password = $wp_user_data->user_pass;

	$firstname = $wp_user_data->user_firstname;

	$lastname = $wp_user_data->user_lastname;

	$updatedb = "UPDATE $affiliates_table_name SET pass = '" . $password . "', firstname = '" . $firstname . "', lastname = '" . $lastname . "', email = '" . $email . "' WHERE refid = '" . $aff_user_id . "'";

	$results = $wpdb->query($updatedb);
}

function wp_aff_profile_update_handler($aff_id, $fields) {

	$wp_user_id = username_exists($aff_id);

	wp_affiliate_log_debug("WP User Integration - wp_aff_profile_update_handler() - updating WP User with ID: " . $wp_user_id, true);

	if ($wp_user_id) {

		$wp_user_info = array();

		$wp_user_info['first_name'] = strip_tags($fields['clientfirstname']);

		$wp_user_info['last_name'] = strip_tags($fields['clientlastname']);

		$wp_user_info['user_email'] = strip_tags($fields['clientemail']);

		$wp_user_info['ID'] = $wp_user_id;

		if (isset($fields['password']) && !empty($fields['password'])) {
			$wp_user_info['user_pass'] = $fields['password'];
		}

		wp_update_user($wp_user_info);
	}
}

/* * * Deprecated function - Do not use ** */

function wp_aff_check_wp_user_login_data() {

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	if ($wp_aff_platform_config->getValue('wp_aff_auto_login_to_aff_account') != '') {

		if (is_user_logged_in() && !wp_aff_is_logged_in()) {

			$current_user = wp_get_current_user();

			$username = $current_user->user_login;

			if (wp_aff_check_if_account_exists_by_affiliate_id($username)) {

				$_SESSION['user_id'] = $username;

				setcookie("user_id", $username, time() + 60 * 60 * 6, "/", COOKIE_DOMAIN); //set cookie for 6 hours

				wp_affiliate_log_debug("WP User Integration - Found a corresponding affiliate account for this WP User! Logging the user into affiliate account.", true);
			} else {

				wp_affiliate_log_debug("WP User Integration - No corresponding affiliate ID exists for this WP User (" . $username . ") so can't log this user in!", true);
			}
		}
	}
}

/* * * End of deprecated function ** */
?>
