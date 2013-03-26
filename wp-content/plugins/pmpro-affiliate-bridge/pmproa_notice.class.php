<?php
/**
 * Contains Notices processor class
 */

/**
 * Notices processor
 */
class PMPROA_notice {
	
	/**
	 * Array of produced error messages
	 * @var array 
	 */
	private $messages = array();
	
	/**
	 * Convert into "slug" form
	 * @param string $name
	 * @return string
	 */
	public function dehumanize($name){
		return strtolower( preg_replace('/[^a-z]+/i', '_', $name) );
	}

	/**
	 * Generate missing plugin error message box content
	 * @global object $current_user
	 * @param string $pluginName
	 */
	public function error_broken_dependency($pluginName) {
		global $current_user;
		$ignoreTitle = 'pmproa_dependencies_' . self::dehumanize($pluginName) . '_error_hide';
		//Get last moment the message was ignored if any
		$ignoredTime = get_user_meta($current_user->ID, $ignoreTitle);
		//Show compatibility message if not dismissed or otherwise on the next day if dismissed.
		$message = '';
		//Add message if not ignored and not hidden for 24 hours
		if (!$ignoredTime[0] || (time()-$ignoredTime[0])>24*3600) {
			$message = '<div class="error"><p>';
			$message .= sprintf(__('Plugin "' . $pluginName . '" is required for "PMPRO Affiliate bridge" plugin work. Please make sure it is installed and active. | <a href="%1$s">Dismiss</a>'), '?pmproa_ignore=' . $ignoreTitle);
			$message .= "</p></div>";
			$this->messages[] = $message;
		}
	}
	
	/**
	 * Helps to turn error off for 24 hours
	 * @global object $current_user
	 * @param string $ignoreTitle
	 */
	public function ignore_error($ignoreTitle) {
		global $current_user;
		$user_id = $current_user->ID;
		if(!empty($user_id)) {
			delete_user_meta($user_id, mysql_real_escape_string($ignoreTitle));
			add_user_meta($user_id, mysql_real_escape_string($ignoreTitle), time(), true);
		}
	}
	
	/**
	 * Showes admin error boxes.
	 * Triggered through a hook
	 */
	public function showMessages(){
		if(!empty($this->messages)) {
			foreach($this->messages as $message) {
				if(!empty($message))
					echo $message;
			}
		}
	}
}
?>
