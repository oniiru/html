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
        if ((in_array("standardmember", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("ecocadmember", $wp_user_object->roles)) || (in_array("bestroboticsmember", $wp_user_object->roles)) || (in_array("intro", $wp_user_object->roles)) || (in_array("desktop", $wp_user_object->roles)))
            $wp_user_object->set_role('freemember');
    }
    if ($level_id == 1) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("promotionalmember", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("ecocadmember", $wp_user_object->roles)) || (in_array("bestroboticsmember", $wp_user_object->roles)) || (in_array("intro", $wp_user_object->roles)) || (in_array("desktop", $wp_user_object->roles)))
            $wp_user_object->set_role('freemember');
    }

    if (($level_id == 2) || ($level_id == 3) || ($level_id == 4) || ($level_id == 7)){
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)) || (in_array("ecocadmember", $wp_user_object->roles)) || (in_array("bestroboticsmember", $wp_user_object->roles)) || (in_array("intro", $wp_user_object->roles)) || (in_array("desktop", $wp_user_object->roles)))
            $wp_user_object->set_role('standardmember');
    }

    if (($level_id == 5) || ($level_id == 6)) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)) || (in_array("ecocadmember", $wp_user_object->roles)) || (in_array("bestroboticsmember", $wp_user_object->roles)) || (in_array("intro", $wp_user_object->roles)) || (in_array("desktop", $wp_user_object->roles)))
            $wp_user_object->set_role('promotionalmember');
    }
	
    if ($level_id == 8) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)) || (in_array("ecocadmember", $wp_user_object->roles)) || (in_array("intro", $wp_user_object->roles)) || (in_array("desktop", $wp_user_object->roles)))
            $wp_user_object->set_role('bestroboticsmember');
    }
    if ($level_id == 9) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)) || (in_array("bestroboticsmember", $wp_user_object->roles)) || (in_array("intro", $wp_user_object->roles)) || (in_array("desktop", $wp_user_object->roles)))
            $wp_user_object->set_role('ecocadmember');
    }
    if ($level_id == 10) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)) || (in_array("bestroboticsmember", $wp_user_object->roles)) || (in_array("ecocadmember", $wp_user_object->roles)) || (in_array("desktop", $wp_user_object->roles)))
            $wp_user_object->set_role('intro');
    }
    if ($level_id == 11) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)) || (in_array("promotionalmember", $wp_user_object->roles)) || (in_array("bestroboticsmember", $wp_user_object->roles)) || (in_array("ecocadmember", $wp_user_object->roles)) || (in_array("intro", $wp_user_object->roles)))
            $wp_user_object->set_role('desktop');
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
    if ( is_admin() && ! current_user_can( 'edit_pages' ) &&
       ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url() );
        exit;
    }
}
include_once 'metaboxes/setup.php';

//include_once 'metaboxes/simple-spec.php';
 
include_once 'metaboxes/full-spec.php';
include_once 'metaboxes/project-spec.php';

// 
// include_once 'metaboxes/checkbox-spec.php';
// 
// include_once 'metaboxes/radio-spec.php';
// 
// include_once 'metaboxes/select-spec.php';

/*
  Only let level 1 members sign up if they use a discount code.
  Place this code in your active theme's functions.php or a custom plugin.
*/
function my_pmpro_registration_checks($pmpro_continue_registration)
{
  //only bother if things are okay so far
	if(!$pmpro_continue_registration)
		return $pmpro_continue_registration;
 
	//level = 1 and there is no discount code, then show an error message
	global $pmpro_level, $discount_code;
	
  //if($pmpro_level->id == 1 && (empty($discount_code) || $discount_code != "REQUIRED_CODE")) //use this conditional to check for a specific code.
  if($pmpro_level->id == 7 && (empty($discount_code) || $discount_code != "JCSCHOOLS1"))
	{
		pmpro_setMessage("A valid registration key is required. Please contact your instructor for further assistance.", "pmpro_error");
		return false;
	}
    //if($pmpro_level->id == 1 && (empty($discount_code) || $discount_code != "REQUIRED_CODE")) //use this conditional to check for a specific code.
    if($pmpro_level->id == 8 && (empty($discount_code) || $discount_code != "BESTROBOTICS2013"))
  	{
  		pmpro_setMessage("A valid registration key is required..", "pmpro_error");
  		return false;
  	}
	
	return $pmpro_continue_registration;
}
add_filter("pmpro_registration_checks", "my_pmpro_registration_checks");

//update the user after checkout
function update_course_title($user_id)
{
	if(isset($_REQUEST['classtitle']))
	{
		$classtitle = $_REQUEST['classtitle'];
	}
	elseif(isset($_SESSION['classtitle']))
	{
		//maybe in sessions?
		$classtitle = $_SESSION['classtitle'];
		
		//unset
		
		unset($_SESSION['classtitle']);
	}
	
	
	if(isset($classtitle))
		update_user_meta($user_id, "class_title", $classtitle);
}
add_action('pmpro_after_checkout', 'update_course_title');

//update the user after checkout
function update_hub_name($user_id)
{
	if(isset($_REQUEST['hubname']))
	{
		$hubname = $_REQUEST['hubname'];
	}
	elseif(isset($_SESSION['hubname']))
	{
		//maybe in sessions?
		$hubname = $_SESSION['hubname'];
		
		//unset
		
		unset($_SESSION['hubname']);
	}
	
	
	if(isset($hubname))
		update_user_meta($user_id, "hub_name", $hubname);
}
add_action('pmpro_after_checkout', 'update_hub_name');
//update the user after checkout
function update_team_name($user_id)
{
	if(isset($_REQUEST['teamname']))
	{
		$teamname = $_REQUEST['teamname'];
	}
	elseif(isset($_SESSION['teamname']))
	{
		//maybe in sessions?
		$teamname = $_SESSION['teamname'];
		
		//unset
		
		unset($_SESSION['teamname']);
	}
	
	
	if(isset($teamname))
		update_user_meta($user_id, "team_name", $teamname);
}
add_action('pmpro_after_checkout', 'update_team_name');
//require the fields
function course_registration_checks()
{
	global $pmpro_msg, $pmpro_msgt, $current_user, $pmpro_level;
	$classtitle = $_REQUEST['classtitle'];
 
		if((($pmpro_level->id == 7) && $classtitle) || ($pmpro_level->id != 7))
		{
		//all good
		return true;
		}
		else
		{
		$pmpro_msg = "Please complete all required fields.";
		$pmpro_msgt = "pmpro_error";
		return false;
		}
	}

add_filter("pmpro_registration_checks", "course_registration_checks");

function hub_registration_checks()
{
	global $pmpro_msg, $pmpro_msgt, $current_user, $pmpro_level;
	$hubname = $_REQUEST['hubname'];
 
		if((($pmpro_level->id == 8) && $hubname) || ($pmpro_level->id != 8))
		{
		//all good
		return true;
		}
		else
		{
		$pmpro_msg = "Please complete all required fields.";
		$pmpro_msgt = "pmpro_error";
		return false;
		}
	}

add_filter("pmpro_registration_checks", "hub_registration_checks");
function team_registration_checks()
{
	global $pmpro_msg, $pmpro_msgt, $current_user, $pmpro_level;
	$teamname = $_REQUEST['teamname'];
 
		if((($pmpro_level->id == 8) && $teamname) || ($pmpro_level->id != 8))
		{
		//all good
		return true;
		}
		else
		{
		$pmpro_msg = "Please complete all required fields.";
		$pmpro_msgt = "pmpro_error";
		return false;
		}
	}

add_filter("pmpro_registration_checks", "hub_registration_checks");

function my_show_extra_profile_fields($user)
{	
	$newmemberlevel = $user->membership_level->ID;
?>
<?php if ($newmemberlevel == '7') {?> 

	<h3>Extra profile information</h3>
 
	<table class="form-table">
		
		<tr>
			<th><label for="repname">Course Title</label></th>
 
			<td>
				<input type="text" name="classtitle" id="classtitle" value="<?php echo esc_attr( get_user_meta($user->ID, 'class_title', true) ); ?>" class="regular-text" /><br />				
			</td>
		</tr>
 
	</table>
<?php
}
elseif ($newmemberlevel == '8') {?> 

		<h3>Best Robotics Information</h3>
 
		<table class="form-table">
		
			<tr>
				<th><label for="repname">Hub Name</label></th>
 
				<td>
					<input type="text" name="hubname" id="hubname" value="<?php echo esc_attr( get_user_meta($user->ID, 'hub_name', true) ); ?>" class="regular-text" /><br />				
				</td>
			</tr>
			<tr>
				<th><label for="repname">Team Name</label></th>
 
				<td>
					<input type="text" name="teamname" id="teamname" value="<?php echo esc_attr( get_user_meta($user->ID, 'team_name', true) ); ?>" class="regular-text" /><br />				
				</td>
			</tr>
 
		</table>

	<?php
		
		
}

}
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );
function my_save_extra_profile_fields( $user_id ) 
{
 
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
 
	
	if(isset($_POST['classtitle']))
		update_usermeta( $user_id, 'class_title', $_POST['classtitle'] );
	if(isset($_POST['hubname']))
		update_usermeta( $user_id, 'hub_name', $_POST['hubname'] );
	if(isset($_POST['teamname']))
		update_usermeta( $user_id, 'team_name', $_POST['teamname'] );
}
add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );
?>
<?php
function form_submit_button($button,$form){
	if($form['id'] == '4') {
    return '<input type="submit" class="yeslink btn btn-danger" id="gform_submit_button_' . $form['id'] . '" value="' . $form['button']['text'] . '">';
}
else {
	return '<input type="submit" class="btn btn-custom" id="gform_submit_button_' . $form['id'] . '" value="' . $form['button']['text'] . '">';
}
}
add_filter('gform_submit_button','form_submit_button',10,2);
/*
  Cancel subscriptions when the subscription is deleted at Stripe
  
  Requires PMPro 1.6 or higher.
*/
function my_pmpro_stripe_subscription_deleted($user_id)
{
	//cancel the membership
	pmpro_changeMembershipLevel(0, $user_id);
}
add_action("pmpro_stripe_subscription_deleted", "my_pmpro_stripe_subscription_deleted");
//update the user after checkout
function add_to_sendy()
{
	//-------------------------- You need to set these --------------------------//
	$your_installation_url = 'http://solidwize.com/sendy'; //Your Sendy installation (without the trailing slash)
	$list = 'rhih9CzBo4VHp4kgEjZxAQ'; //Can be retrieved from "View all lists" page

	//POST variables
	$sendyname = 'andrew omally';
	$sendyemail =  'omally@rawr.com';
	$sendyfname = '';
	$sendylname = '';
	$sendydate = '';
	$sendyphone = '';
	
	
	$boolean = 'true';
	
	//Subscribe
	$postdata = http_build_query(
	    array(
	    'name' => $sendyname,
	    'email' => $sendyemail,
	    'list' => $list,
		'firstname' => $sendyfname,
		'lastname' => $sendylname,
		'subscribedate' => $sendydate,
		'phonenumber' => $sendyphone,
	    'boolean' => 'true'
	    )
	);
	$opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
	$context  = stream_context_create($opts);
	$result = file_get_contents($your_installation_url.'/subscribe', false, $context);

}
add_action('pmpro_after_checkout', 'add_to_sendy');
?>