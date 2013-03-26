<?php
function theme_styles2()  
{ 
    
    wp_register_style( 'wp-bootstrap', get_stylesheet_directory_uri() . '/style.css', array(), '1.0', 'all' );
  
}
add_action('wp_enqueue_scripts', 'theme_styles2');
register_nav_menu('visitors', 'Visitors navigation menu');
register_nav_menu('signedin', 'Signedin navigation menu');
 
function bones_main_nav2() {
	// display the wp3 menu if available
	if (!is_user_logged_in())
    wp_nav_menu( 
    	array( 
    		'menu' => 'visitors', /* menu name */
    		'menu_class' => 'nav',
    		'theme_location' => 'visitors', /* where in the theme it's assigned */
    		'container' => 'false', /* container class */
    		'fallback_cb' => 'bones_main_nav_fallback', /* menu fallback */
    		'depth' => '2', /* suppress lower levels for now */
    		'walker' => new description_walker()
    	)
    );
	else
	    wp_nav_menu( 
	    	array( 
	    		'menu' => 'signedin', /* menu name */
	    		'menu_class' => 'nav',
	    		'theme_location' => 'signedin', /* where in the theme it's assigned */
	    		'container' => 'false', /* container class */
	    		'fallback_cb' => 'bones_main_nav_fallback', /* menu fallback */
	    		'depth' => '2', /* suppress lower levels for now */
	    		'walker' => new description_walker()
	    	)
	    );
		
}

register_sidebar(array(
'name'         => 'Navlogin',
'id'            => 'nav-login',
'description'   => 'Nav Login',
'before_widget' => '<li>',
'after_widget'  => '</li>',
'before_title'  => '',
'after_title'   => ''    ));

/**
 * Removes password confirmation from registration form
 * @return boolean
 */
function remove_password_confirm(){
	return null;
}
add_filter('pmpro_checkout_confirm_password', 'remove_password_confirm');

/**
 * Removes extra email confirmation
 * @return null
 */
function remove_email_confirm(){
	return null;
}
add_filter('pmpro_checkout_confirm_email', 'remove_email_confirm');

/**
 * Gets all fields of billing address removed
 * @return boolean
 */
function remove_billing_address(){
	return true;
}
add_filter('pmpro_stripe_lite', 'remove_billing_address');

/**
 * Removes check for mandatory fields we don't need
 * @global string $gateway
 * @global WP_User $current_user
 * @param array $fields
 * @return array
 */
function remove_billing_address_fields($fields) {
	global $gateway;
	//ignore if not using stripe
	if ($gateway != "stripe")
		return $fields;
	//some fields to remove
	$remove = array('baddress1', 'bcity', 'bstate', 'bzipcode', 'bcountry', 'CardType');
	//if a user is logged in, don't require bemail either
	global $current_user;
	if (!empty($current_user->user_email))
		$remove[] = 'bemail';
	//remove the fields
	foreach ($remove as $field)
		unset($fields[$field]);
	//ship it!
	return $fields;
}

add_filter("pmpro_required_billing_fields", "remove_billing_address_fields");

/**
 * Generates associated sets of memberships (Yearly/Monthly) among all membership types
 * @global array $membershipGroups
 */
function pmpro_memberships_arrange(){
	global $membershipGroups;
	$membershipGroups = array(
		'free' => array('1'),
		'pro' => array('2', '3'),
		'student' => array('4')
	);
}
add_action('init', 'pmpro_memberships_arrange');

/**
 * Retrieves details of particular PMPro membership type
 * @global wpdb $wpdb
 * @param integer $id
 * @return StdObject
 */
function get_pmpro_level_details($id) {
	global $wpdb;
	return $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = '" . $wpdb->escape($id) . "' AND allow_signups = 1 LIMIT 1");
}
function my_pmpro_after_change_membership_level($level_id, $user_id) {

    if ($level_id == 0) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("standardmember", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)))
            $wp_user_object->set_role('freemember');
    }
    if ($level_id == 1) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("promotionalmember", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)))
            $wp_user_object->set_role('freemember');
    }

    if (($level_id == 2) || ($level_id == 3) || ($level_id == 4)) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)))
            $wp_user_object->set_role('standardmember');
    }

    if (($level_id == 5) || ($level_id == 6)) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)))
            $wp_user_object->set_role('promotionalmember');
    }

}

add_action("pmpro_after_change_membership_level", "my_pmpro_after_change_membership_level", 10, 2);
/*
  30 day free trial for annual plan
 */

function my_pmpro_profile_start_date($date, $order) {
    if (($order->membership_id == 2) || ($order->membership_id == 3) || ($order->membership_id == 4))
        $date = date("Y-m-d", strtotime("+ 7 Days")) . "T0:0:0";

    return $date;
}

add_filter("pmpro_profile_start_date", "my_pmpro_profile_start_date", 10, 2);

function my_pmpro_level_cost_text($cost, $level) {
    if ($level->id == 3) {
        $cost = str_replace("Year.", "Year", $cost);
        $cost .= "";
    }

    if ($level->id == 2) {
        $cost = str_replace("Month.", "Month", $cost);
        $cost .= "";
    }

    if ($level->id == 4) {
        $cost = str_replace("Month.", "Month", $cost);
        $cost .= "";
    }

    return $cost;
}
add_filter("pmpro_level_cost_text", "my_pmpro_level_cost_text", 10, 2);

function add_url_metabox() {
    add_meta_box("page-url", 'Url Page', 'url_meta_box', 'post', 'side', 'low');
}

function url_meta_box($post) {
    $v = get_post_meta($post->ID, 'pageurl', true);

    if (!$v)
        $v = '#';
    echo '<label for="pageurl">Enter URl Page';

    echo '</label> ';
    echo '<input type="text" id="pageurl" name="pageurl" value="' . $v . '" size="25" />';
}

function urlpage_save_postdata($post_id) {

    update_post_meta($post_id, 'pageurl', $_POST['pageurl']);
}

if (!current_user_can('edit_posts')) {
    add_filter('show_admin_bar', '__return_false');
}

function hideAdminBar() {
    echo '<style type="text/css">.show-admin-bar { display: none; }</style>';
}

add_action('admin_print_scripts-profile.php', 'hideAdminBar');
register_nav_menu('visitors', 'Visitors navigation menu');
register_nav_menu('signedin', 'Signedin navigation menu');
add_filter('nav_menu_css_class', 'special_nav_class', 10, 2);

function special_nav_class($classes, $item) {
    if ($item->title == "Contact") { //Notice you can change the conditional from is_single() and $item->title
        $classes[] = "special-class";
    }

    return $classes;
}
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    if ( is_admin() && ! current_user_can( 'administrator' ) &&
       ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url() );
        exit;
    }
}
?>