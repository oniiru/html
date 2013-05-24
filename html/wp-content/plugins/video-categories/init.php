<?php

/*
  Plugin Name: Video Categories
  Author: Zhang Ming Zhe
  Version: v1.0.1
 */

/* DEFINE FILE DIRECTORIES */
global $wpdb;

define('VIDEO_CATEGORY_LOG_TABLE', $wpdb->prefix . 'video_category_search_log');

class Video_categories_initial {

	var $dbversion = '1.0.0';

	function Video_categories_initial() {
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

		if (!$wpdb->get_var('SHOW TABLES LIKE "' . VIDEO_CATEGORY_LOG_TABLE . '"')) { // Veriry if table exist
			$sql = "CREATE TABLE " . VIDEO_CATEGORY_LOG_TABLE . " (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					search_content VARCHAR( 200 ) NOT NULL ,
					date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ,
					count INT NOT NULL
					)";
			dbDelta($sql);
		}
	}

	function deactivate_options() {

	}

}

new Video_categories_initial();

include_once 'log_list_table.php';
include_once 'categoriesVideo.php';

add_action('admin_head', array('Log_List_Table', 'admin_header'));
?>