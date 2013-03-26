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
		'pro' => array('2', '3')
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
?>