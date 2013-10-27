<?php

/**
 * Class to authenticate users if they are allowed to view the video
 * @since 1.0.0
 * @global $ion_auth_users
 */
class AuthenticateUsers {

	/**
	 * Return bool value if the user is allowed to view the video
	 * @since 1.0.0
	 * @var bool
	 */
	public $check = false;

	function all($options) {

		if (is_super_admin()) {
			return true;
		}

		if (!is_array($options))
			return true;

		# TO-DO
		if ($options['chargify'])
			return $this->chargify($options['chargify']) || $this->role($options['roles']);

		return $this->role($options['roles']);
	}

	function role($capability) {
		if (is_super_admin()) {
			return true;
		}

		if (is_user_logged_in()) {
			if (is_array($capability)) {
				foreach ($capability as $role) {
					if (current_user_can($role)) {
						return true;
					}
				}
			}
		}
	}

	function chargify($levels) {
		$u = wp_get_current_user();
		if (in_array($u->chargify_level, $levels)) {
			return true;
		}

		return false;
	}

}

$ion_auth_users = new AuthenticateUsers();
?>