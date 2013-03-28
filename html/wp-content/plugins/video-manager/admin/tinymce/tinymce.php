<?php
/**
 * add new buttons for TinyMCE
 */

class ion_extend_tinyMCE {
	
	var $url = '';
	var $plugin_name = 'VideoDirectory';
	var $vid_dir_shortcode = 'videodirectory'; // Video Directory Shortcode
	
	function ion_extend_tinyMCE() {
		$this->url = $GLOBALS['url_path'] .'/admin/tinymce/';
		add_action('init', array (&$this, 'addbuttons'));
	}
	
	function addbuttons() {
	   // Don't bother doing this stuff if the current user lacks permissions
	   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		 return;
	 
	   // Add only in Rich Editor mode
	   if ( get_user_option('rich_editing') == 'true') {		if($_GET['post_type']!='question'){
		 add_filter( 'mce_external_plugins', array(&$this, 'add_tinymce_plugin') );
		 add_filter( 'mce_buttons', array(&$this, 'register_button') );		 }
	   }
	}
	
	function register_button($buttons) {
		array_push($buttons, "separator", $this->plugin_name);
		return $buttons;
	}
	
	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function add_tinymce_plugin($plugin_array) {
		$plugin_array[$this->vid_dir_shortcode] = $this->url .'js/editor_plugin.js';
		return $plugin_array;
	}
}

$ion_extend_tinymce = &new ion_extend_tinyMCE();
?>