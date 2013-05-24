<?php

/*
  Plugin Name: Video Manager
  Author: Zhang Ming Zhe
  Version: v1.0.3
 */


/* GLOBAL VARIABLES */
$folder_name = WP_PLUGIN_DIR . '/video-manager'; // Rename the path per versioning
$url_path = plugins_url('', __FILE__);


/* DEFINE FILE DIRECTORIES */
define('ION_ADMIN', $folder_name . '/admin/');
define('ION_DISPLAY', $folder_name . '/display/');
define('ION_MODKORE', $folder_name . '/modkore/');
define('ION_DIR', $folder_name);
define('ION_TABLE_DROP_ALL', false);

/* LOAD DATABASE */

class DB_install {

	var $dbversion = '1.0.0';

	function DB_install() {
		register_activation_hook(__FILE__, array(&$this, 'activate'));
		register_deactivation_hook(__FILE__, array(&$this, 'deactivate_options'));
	}

	function activate() {

		if (version_compare(PHP_VERSION, '5.2.0', '<')) {
			deactivate_plugins(plugin_basename(__FILE__)); // Deactivate ourself
			wp_die("Sorry, but you can't run this plugin, it requires PHP 5.2 or higher.");
			return;
		}

		$this->install_tables();
	}

	function install_tables() {
		global $wpdb;

		// upgrade function changed in WordPress 2.3	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$v_directory_ = $wpdb->prefix . "video_directory";
		$v_listings_ = $wpdb->prefix . "video_list";
		$v_popup_ = $wpdb->prefix . "video_popup";

		if (!$wpdb->get_var("SHOW TABLES LIKE '$v_listings_'")) { // Veriry if table exist
			$sql = "CREATE TABLE " . $v_listings_ . " (
					ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,							   
					dir_id INT NOT NULL ,
					video_name VARCHAR( 1000 ) NOT NULL ,
					embed_value TEXT NULL ,
					options TEXT NOT NULL,
					INDEX ( dir_id )
					)";
			dbDelta($sql);
		}
		
		if (!$wpdb->get_var("SHOW TABLES LIKE '$v_directory_'")) { // Veriry if table exist
			$sql = "CREATE TABLE " . $v_directory_ . " (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					popup_id INT NOT NULL ,
					directory_name VARCHAR( 1000 ) NOT NULL ,
					video_count INT NOT NULL ,
					date_created DATE DEFAULT '0000-00-00' NOT NULL ,
					video_order TINYTEXT NOT NULL
					)";
			dbDelta($sql);
		} else {
//			$sql = 'ALTER TABLE '.$v_directory_ .' ADD popup_id INT NOT NULL';
//			$wpdb->query($sql);
		}

		if (!$wpdb->get_var("SHOW TABLES LIKE '$v_popup_'")) { // Veriry if table exist
			$sql = "CREATE TABLE " . $v_popup_ . " (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					popup_options VARCHAR( 5000 ) NOT NULL ,
					date_created DATE DEFAULT '0000-00-00' NOT NULL 
					)";
			dbDelta($sql);
			$popup_settings = $this->set_popup_default();

			$wpdb->insert($v_popup_, array('popup_options' => serialize($popup_settings), 'date_created' => date('Y-m-d')));
			$popup_settings['popup_id'] = $wpdb->insert_id;
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$v_directory_'")
						&& $wpdb->get_var("SHOW TABLES LIKE '$v_listings_'")
						&& $wpdb->get_var("SHOW TABLES LIKE '$v_popup_'")) {
			add_option("ion_dbversion", $this->dbversion);
			add_option("v_directory_", $v_directory_);
			add_option("v_listings_", $v_listings_);
			add_option('v_popup_default_settings_', $popup_settings);
			add_option('member_tab_settings', array('title' => 'This section is for subscribers only.', 'text' => 'Subscribers are provided with the same SolidWorks files that Rohit uses during each tutorial. Make the most of your learning experience by following along with each lesson.'));
		}
	}

	function deactivate_options() {
		delete_option("ion_dbversion");
		delete_option("v_directory_");
		delete_option("v_listings_");
		delete_option('v_popup_default_settings_');

		global $wpdb;

		$v_popup_ = $wpdb->prefix . "video_popup";
		$wpdb->query('DROP TABLE '. $v_popup_);
		
		if (ION_TABLE_DROP_ALL){
			$v_directory_ = $wpdb->prefix . "video_directory";
			$v_listings_ = $wpdb->prefix . "video_list";

			$wpdb->query('DROP TABLE '. $v_directory_);
			$wpdb->query('DROP TABLE '. $v_listings_);	
		}
	}

	public function set_popup_default() {
		$popup_content = '<div class="noaccesspopup">
	<h3>You must be a member to view this video</h3>
	<p>Each set of videos contains free sample content. Simply click on the <span>blue links</span> to view them. Become a member for full access.</p>	
	<img src="' . plugins_url('', __FILE__) . '/admin/images/linkspreview.jpg">
</div>';
		$popup_content_signup = '<div id="signup9999" class="noaccesspopupfooter">
	<a href="[signup url]" class="[color] noaccesssubscribe" title="subscribe">
		[button text]
	</a>
	<div class="alreadyamember">
		<p>Already a Member? <a href="[signin url]">Log in</a></p>
	</div>
</div>				
';
		$popup_content_email = '<div id="email9999" class="noaccesspopupfooter">
		<h3> Enter your email for immediate access</h3>
	<input type="text" class="input" name="" onfocus="if(this.value == \'Email\') { this.value = \'\'; }" value="Email" />
	<button type="submit" class="[color] emailvids">[button text]</button>
	<div class="alreadyamember">
		<p>Already a Member? <a href="[signin url]">Log in</a></p>
	</div>
</div>			
';
		$popup_default_settings = array(
				'content' => trim($popup_content),
				'signup_content' => trim($popup_content_signup),
				'email_content' => trim($popup_content_email),
				'action' => 'none',
				'button_text' => 'Become a Member »',
				'sign_in' => get_bloginfo('siteurl') . '/wp-login.php?action=login',
				'sign_up' => get_bloginfo('siteurl') . '/wp-login.php?action=register'
		);

		return $popup_default_settings;
	}

}

new DB_install();

/* LOAD ADMIN */
include_once(ION_ADMIN . '/page-templates.php');
include_once(ION_ADMIN . '/page.php');
include_once(ION_ADMIN . '/tinymce/tinymce.php');

/* LOAD DISPLAY */
include_once(ION_DISPLAY . 'loader.php');
include_once(ION_DISPLAY . 'video-directory.php');

/* LOAD MODKORE */
include_once(ION_MODKORE . 'shortcodes.php');
include_once(ION_MODKORE . 'authenticate_user.php');
add_theme_support('post-thumbnails');
?>