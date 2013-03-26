<?php

/**
 * Use of WordPress Shortcode API for more features
 * @since 1.0.0
 */
class IonShortcodes extends IonVideoDirectoryDisplay {

	var $tablist = array();
	var $tabcontent = array();

	function IonShortcodes() {
		add_shortcode('videodirectory', array(&$this, 'get_directory'));
		//add_shortcode( 'ion_tabset', array(&$this, 'create_tabs' ) );
		add_shortcode('ion_tabset', array(&$this, 'create_tabs_'));
		//add_shortcode( 'ion_tab', array(&$this, 'single_tabs' ) );
		add_shortcode('ion_tab', array(&$this, 'single_tabs_'));
		add_shortcode('subscribebar', array(&$this, 'create_subscribebar'));
		add_shortcode('email', array(&$this, 'create_email'));
	}

	function get_directory($attr, $content = null) {
		extract(shortcode_atts(array('id' => '', 'duration' => 'off'), $attr));
		return $this->show_directory($attr);
	}

	function create_tabs() {
		return $this->container_tabs($this->tablist, $this->tabcontent);
	}

	function single_tabs($attr, $content = null) {
		extract(shortcode_atts(array('title' => ''), $attr));
		array_push($this->tablist, $title);
		array_push($this->tabcontent, $content);
	}

	function create_tabs_($attr, $content = null) {
		extract(shortcode_atts(array('title' => ''), $attr));
		return $this->container_tabs_($attr, $content);
	}
	
	function create_subscribebar($attr, $content = null){
		extract($attr);
		return $this->video_subscribebar($attr);
	}

	function create_email($attr, $content = null){
		extract($attr);
		return $this->video_email($attr);
	}
	
	function single_tabs_($attr, $content) {
		extract(shortcode_atts(array('title' => ''), $attr));
		return $this->tabs($attr, $content);
	}

	function verify_chargify_users() {
		
	}

}

$ion_shorcodes = &new IonShortcodes();
?>