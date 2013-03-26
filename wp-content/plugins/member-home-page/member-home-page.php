<?php
/**
 * Plugin Name: Member Home Page
 * Description: Enables administrator to take conrol over content of the page a customer gets to once logged in
 * @author Anton Matiyenko (amatiyenko@gmail.com)
 */

/**
 * Run init function if plugin is enabled
 */
member_home_page_init();

/**
 * Hooks necessary processing
 */
add_action('plugins_loaded', 'member_home_page_run', 1);

/**
 * Variable that holds all associated config data.
 * Is fetched only once to minimize the DB load
 */
global $MHPConfig;

/**
 * Fetches the Member Homepage config data
 * @global array $MHPConfig
 * @param string $key
 * @return mixed
 */
function get_mhp_option($key){
	global $MHPConfig;
	if(empty($MHPConfig)) {
		$MHPConfig = unserialize(get_option('mhp_config'));
	}
	if(isset($MHPConfig[$key]))
		return $MHPConfig[$key];
	return false;
}

/**
 * Updates the config data for Member Homepage
 * @global array $MHPConfig
 * @param string $key
 * @param mixed $value
 * @return type
 */
function update_mhp_option($key, $value){
	global $MHPConfig;
	if(empty($MHPConfig)) {
		$MHPConfig = unserialize(get_option('mhp_config'));
	}
	$MHPConfig[$key] = $value;
	return update_option('mhp_config', serialize($MHPConfig));
}

/**
 * Inits the necessary basics
 */
function member_home_page_init() {
	define('MHP_PATH', dirname(__FILE__));
	if (is_admin()) {
		/**
		 * Determines the parent page of all pages to select featured ones from
		 * To be changed if used in different DB where "Training" page ID differs from '11'
		 */
		define('MHP_TRAINING_PARENT_PAGE', 11);
		require_once( MHP_PATH . DIRECTORY_SEPARATOR . 'admin.php');
	}
}

/**
 * Hooks admin menu item
 */
function member_home_page_run() {
	if (is_admin()) {
		add_action('admin_menu', array('MHPAdmin', 'init'));
	}
}

/**
 * UNUSED FOR NOW: render complete content for member hompage
 */
function member_home_page_show_featured() {
	require_once(MHP_PATH . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'mhp_frontend_template.php');
	$data = array();
	$data['mhp_day_of_the_week'] = get_mhp_option('mhp_day_of_the_week');
	$data['mhp_time_starts'] = get_mhp_option('mhp_time_starts');
	$data['mhp_time_ends'] = get_mhp_option('mhp_time_ends');
	$data['mhp_signup_url'] = get_mhp_option('mhp_signup_url');
	$data['mhp_promo_page_1_id'] = get_mhp_option('mhp_promo_page_1_id');
	$data['mhp_promo_page_2_id'] = get_mhp_option('mhp_promo_page_2_id');
	MHP_Frontend_Template::display($data);
}

/**
 * Fetches and throws stored signup URL
 * @return string
 */
function mhp_url_func(){
	return get_mhp_option('mhp_signup_url');
}
/**
 * Hooks a shortcode for URL
 */
add_shortcode( 'mhp_url', 'mhp_url_func' );

/**
 * Fetches office hours and prepares proper format to show it
 * @return type
 */
function mhp_office_hours_func(){
	$timeStarts = get_mhp_option('mhp_time_starts');
	$timeStartsParts = explode(' ', $timeStarts);
	$timeStartsTime = explode(':', $timeStartsParts[0]);
	$timeEnds = get_mhp_option('mhp_time_ends');
	$timeEndsParts = explode(' ', $timeEnds);
	$timeEndsTime = explode(':', $timeEndsParts[0]);
	if($timeStartsParts[1] == $timeEndsParts[1]) {
		$output = (($timeStartsTime[1]=='00')?$timeStartsTime[0]:$timeStartsTime[0] . ':' . $timeStartsTime[1]) . '&ndash;' .
				(($timeEndsTime[1]=='00')?$timeEndsTime[0]:$timeEndsTime[0] . ':' . $timeEndsTime[1]) . $timeStartsParts[1];
	} else {
		$output = (($timeStartsTime[1]=='00')?$timeStartsTime[0]:$timeStartsTime[0] . ':' . $timeStartsTime[1]) . $timeStartsParts[1] . '&ndash;' .
				 (($timeEndsTime[1]=='00')?$timeEndsTime[0]:$timeEndsTime[0] . ':' . $timeEndsTime[1]) . $timeEndsParts[1];
	}
	$dayOfWeek = get_mhp_option('mhp_day_of_the_week');
	return $dayOfWeek . ' ' . $output . ' PST';
}
/**
 * Shortcode hook
 */
add_shortcode( 'mhp_office_hours', 'mhp_office_hours_func' );

/**
 * Renders a preview link to a promo page - one at a time
 * @param integer $page
 * @return string
 */
function mhp_show_promo_page($page){
	$optionID = 'mhp_promo_page_' . intval($page) . '_id';
	$pageID = get_mhp_option($optionID);
	$output = '';
	if(!empty($pageID)) {
		$page = get_page($pageID);
		//Image
		$featured_image_id = get_post_thumbnail_id($pageID); 
		$featured_image_src = wp_get_attachment_image_src($featured_image_id, 'full');
		$permalink = get_permalink($pageID);
		if(isset($featured_image_src[0]))
			$output .= '<a href="' . $permalink . '" title="' . $page->post_title . '"><img style="margin-top:0px;" width="183" height="100" src="' . $featured_image_src[0] . '"></a>';
		$output .= '<a href="' . $permalink . '">' . $page->post_title . '</a>';
	}
	return $output;
}

/**
 * Shows promo pages links block
 * @param array $options params passed via shortcode block
 * @return type
 */
function mhp_promo_page_func($options){
	$output = '';
	switch(isset($options['page'])):
		case true:
			$output = mhp_show_promo_page($options['page']);
			break;
		default:
			$output = mhp_show_promo_page(1);
			$output .= mhp_show_promo_page(2);
			break;
	endswitch;
	return $output;
}
/**
 * Hook a shortcode pointing to function
 */
add_shortcode( 'mhp_promo_page', 'mhp_promo_page_func' );


?>