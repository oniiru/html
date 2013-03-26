<?php
add_action('template_redirect', 'my_redirect', 1);

function my_redirect() {
    if (is_user_logged_in() && is_front_page()) {
        wp_redirect(site_url('member'));
        die();
    }
	 if (!is_user_logged_in() && is_page('member')) {
        wp_redirect(site_url());
        die();
    }
}
/* Insert custom functions here */
define('KARMA_JS', ABSPATH . 'wp-content/themes/Karma/js/');
define('KARMA_TEMPLATEPATH', TEMPLATEPATH . '-Child-Theme');
define('PMPRO_PLUGIN_PATH', ABSPATH . 'wp-content/plugins/paid-memberships-pro');

if (function_exists('theme_my_login')) {

    function your_widget_display() {

        if (is_user_logged_in()) {

            echo '<ul>
			<li class="signup"><a href="' . get_bloginfo('url') . '/pricing-2/">Sign Up</a></li>
			<li class="signin logout"><a href="' . $GLOBALS['theme_my_login']->get_login_page_link('action=logout') . '">Log Out</a></li></ul>';
        } else {
            echo '<ul><li class="signup"><a href="' . get_bloginfo('url') . '/pricing-2/">Sign Up</a></li>
			<li class="signin"><a href="' . $GLOBALS['theme_my_login']->get_login_page_link('action=login') . '">Log In</a></li></ul>';
        }
    }

    wp_register_sidebar_widget(
            'your_widget_1', // your unique widget id
            'SolidWize Widget', // widget name
            'your_widget_display', // callback function
            array(// options
        'description' => 'Description of what your widget does'
            )
    );
}

function custom_limit_content($content_length = 250, $allowtags = true, $allowedtags = '', $id = '', $readmore_text = '') {
    global $post;
    $content = $post->post_content;

    if (!empty($id)) {
        $title = get_the_title($id);
        $permalink = get_permalink($id);

        $link = ' ... <a href="' . $permalink . '" rel="bookmark" title="' . $title . '"><span>' . $readmore_text . '</span></a>';
    } else {
        $link = '...';
    }

    $content = apply_filters('the_content', $content);
    if (!$allowtags) {
        $allowedtags .= '<style>';
        $content = strip_tags($content, $allowedtags);
    }
    $wordarray = explode(' ', $content, $content_length + 1);
    if (count($wordarray) > $content_length) :
        array_pop($wordarray);
        array_push($wordarray, $link);
        $content = implode(' ', $wordarray);
        $content .= "</p>";
    endif;

    echo $content;
}

if (class_exists('MultiPostThumbnails')) {
    new MultiPostThumbnails(array(
                'label' => 'Secondary Image',
                'id' => 'secondary-image',
                'post_type' => 'post'
                    )
    );
}
/* Make Folder to save files upload for form studens */
$fpath = str_replace('\\', '/', ABSPATH);

$fpath.='wp-content/files';
if (!is_dir($fpath))
    mkdir($fpath, 0777, true);
/* End Make folder */
add_action('wp_head', 'add_var_script');

function add_var_script() {
    ?>
    <script type='text/javascript'>
        var urltemp="<?php print get_bloginfo('stylesheet_directory'); ?>/";
        var urlhome="<?php bloginfo('wpurl'); ?>/";
    </script>
    <?php
}

function my_scripts_method() {
    wp_enqueue_script('boxcriptv', get_bloginfo('stylesheet_directory') . "/js/validationf.js");
    wp_enqueue_script('boxcript', get_bloginfo('stylesheet_directory') . "/js/app.js");
}

add_action('wp_enqueue_scripts', 'my_scripts_method');
add_action('wp_ajax_contactEmail_action', 'implement_ajax_email');
add_action('wp_ajax_nopriv_contactEmail_action', 'implement_ajax_email');
add_action('wp_ajax_contactuser_action', 'implement_ajax_user');
add_action('wp_ajax_nopriv_contactuser_action', 'implement_ajax_user');
add_action('wp_ajax_contactstudent_action', 'implement_ajax_student');
add_action('wp_ajax_nopriv_contactstudent_action', 'implement_ajax_student');
add_action('wp_ajax_loginpop_action', 'implement_ajax_loginpop');
add_action('wp_ajax_nopriv_loginpop_action', 'implement_ajax_loginpop');

function implement_ajax_email() {

    require_once 'contact.php';
    die();
}

function implement_ajax_user() {

    require_once 'contact-user.php';
    die();
}

function implement_ajax_student() {

    require_once 'contact-student.php';
    die();
}

function implement_ajax_loginpop() {

    require_once 'loginpop.php';
    die();
}

add_action('wp_ajax_contact_pro', 'implement_contact_pro');
add_action('wp_ajax_nopriv_contact_pro', 'implement_contact_pro');

function implement_contact_pro() {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $company = $_POST['company'];
    $message = $_POST['message'];
    $number_user = $_POST['numberuser'];
    $file = $_POST['fileattact'];
    if ($file) {
        if (sendEmailAtt($name, $email, $company, $message, $file))
            print '{"contactf":202}';
    }else {
        if (sendEmailMan($name, $email, $company, $message, $number_user))
            print '{"contactf":202}';
    }
    die();
}

function sendEmailMan($name, $email, $company, $mess, $number_user = '') {
    if ($number_user)
        $subject = 'Contact User ';
    else
        $subject = 'Contact Form ';
    $to = 'rohit@solidwize.com';

    $headers = "From: SolidWize<info@ttvtech.com> \r\n";
    $headers .= "Reply-To: SolidWize <" . $email . ">\r\n";
    $headers .= "Return-Path: SolidWize <" . $email . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $message = '<h2>SolidWize Contact</h2>';
    $message.='<p>Name : ' . $name . '</p>';
    $message.='<p>Email : ' . $email . '</p>';
    $message.='<p>Company : ' . $company . '</p>';

    $message.='<p>Message : ' . $mess . '</p>';
    if ($number_user)
        $message.='<p>Number User : ' . $number_user . '</p>';
    if (mail($to, $subject, $message, $headers))
        return true;
    return false;
}

function sendEmailAtt($name, $email, $company, $mess, $file) {
    $fpath = str_replace('\\', '/', ABSPATH);
    $fpath.='wp-content/files';
    $mail_to = "rohit@solidwize.com";
    $from_mail = $email;
    $from_name = "SolidWize Contact";
    $reply_to = $email;
    ;
    $subject = "Contact Students";
    $message = '<h2>SolidWize Contact Student</h2>';
    $message.='<p>Name : ' . $name . '</p>';
    $message.='<p>Email : ' . $email . '</p>';
    $message.='<p>Company : ' . $company . '</p>';
    $message.='<p>Message : ' . $mess . '</p>';

    $file_name = $file;
    $path = $fpath . '/';

    $file = $path . $file_name;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));

    /* Set the email header */

    $boundary = md5(uniqid(time()));

    // Email header
    $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
    $header .= "Reply-To: " . $reply_to . "\r\n";
    $header .= "MIME-Version: 1.0\r\n";

    $header .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--" . $boundary . "\r\n";

    $header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= "$message\r\n";
    $header .= "--" . $boundary . "\r\n";

    $header .= "Content-Type: application/xml; name=\"" . $file_name . "\"\r\n";
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n\r\n";
    $header .= $content . "\r\n";
    $header .= "--" . $boundary . "--";

    if (mail($mail_to, $subject, "", $header))
        return true;

    return false;
}

class custom_walker extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth, $args) {

        global $wp_query;

        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $output .= $indent . '<li id="testitem-' . $item->ID . '"' . $value . $class_names . '>';

        if (strpos($item->attr_title, '[contact]') || $item->attr_title == '[contact]')
            $attributes = ' id="contact"' . $item->ID . ' class="contactform"';
        if (strpos($item->attr_title, '[contact-user]') || $item->attr_title == '[contact-user]')
            $attributes = ' id="contact-user" ';
        if (strpos($item->attr_title, '[contact-student]') || $item->attr_title == '[contact-student]')
            $attributes = ' id="contact-student" ';
        if (strpos($item->attr_title, '[login-pop]') || $item->attr_title == '[login-pop]')
            $attributes = ' id="login-pop" ';
        $item->attr_title = str_replace('[contact]', '', $item->attr_title);
        $item->attr_title = str_replace('[contact-user]', '', $item->attr_title);
        $item->attr_title = str_replace('[contact-student]', '', $item->attr_title);
        $item->attr_title = str_replace('[login-pop]', '', $item->attr_title);
        $attributes .=!empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $prepend = '<strong>';
        $append = '</strong>';
        $description = !empty($item->description) ? '<span class="navi-description">' . esc_attr($item->description) . '</span>' : '';

        if ($depth != 0) {
            $description = $append = $prepend = "";
        }


        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '><span>';
        $item_output .= $args->link_before . $prepend . apply_filters('the_title', $item->title, $item->ID) . $append;
        $item_output .= $description . $args->link_after;
        $item_output .= '</span></a>';
        $item_output .= $args->after;


        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

}

function my_page_filter($args) {

    if ($args['theme_location'] == 'Primary Navigation' || $args['theme_location'] == 'visitors' || $args['theme_location'] == 'signedin' || $args['menu']->name == 'Footer Menu') {
        print $args['name'];
        $args = array_merge($args, array('walker' => new custom_walker()));
    }
    return $args;
}

add_filter('wp_nav_menu_args', 'my_page_filter');
require_once 'multi-post-thumbnails.php';
if (class_exists('MultiThumbnails')) {
    new MultiThumbnails(array(
                'label' => 'Box Image',
                'id' => 'box-image',
                'post_type' => 'post'
                    )
    );
    new MultiThumbnails(array(
                'label' => 'Box Image Hover',
                'id' => 'box-image-hover',
                'post_type' => 'post'
                    )
    );
}
add_action('add_meta_boxes', 'add_url_metabox');
add_action('save_post', 'urlpage_save_postdata');

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
/*
  Add this code to your theme's functions.php file to update the checkout page to support international credit cards.
 */
/*
  First we need to enable international addresses. We just use the pmpro_international_addresses hook and return true.
  This will add a "countries" dropdown to the checkout page.
 */

function my_pmpro_international_addresses() {
    return true;
}

add_filter("pmpro_international_addresses", "my_pmpro_international_addresses");

/*
  (optional) Now we want to change the default country from US to say the United Kingdom (GB)
  Use the 2-letter acronym.
 */

function my_pmpro_default_country($default) {
    return "US";
}

add_filter("pmpro_default_country", "my_pmpro_default_country");

/*
  Change some of the billing fields to be not required to support international addresses that don't have a state, etc.
  Default fields are: bfirstname, blastname, baddress1, bcity, bstate, bzipcode, bphone, bemail, bcountry, CardType, AccountNumber, ExpirationMonth, ExpirationYear, CVV
 */

function my_pmpro_required_billing_fields($fields) {
    //remove state and zip
    unset($fields['bstate']);
    unset($fields['bzipcode']);

    return $fields;
}

add_filter("pmpro_required_billing_fields", "my_pmpro_required_billing_fields");

/*
  Make the city, state, and zip/postal code fields show up on their own lines.
 */

function my_pmpro_longform_address() {
    return true;
}

add_filter("pmpro_longform_address", "my_pmpro_longform_address");
/*
  This code will make members signing up for membership level #1 authors and make them subscribers when they cancel.
 */

function my_pmpro_after_change_membership_level($level_id, $user_id) {

    if ($level_id == 0) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("standardmember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)))
            $wp_user_object->set_role('freemember');
    }
    if ($level_id == 1) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("promotionalmember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)))
            $wp_user_object->set_role('freemember');
    }

    if (($level_id == 2) || ($level_id == 3)) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)))
            $wp_user_object->set_role('standardmember');
    }
    else {
        $wp_user_object = new WP_User($user_id);
        if (in_array("standardmember", $wp_user_object->roles))
            $wp_user_object->set_role('freemember');
    }

    if (($level_id == 4) || ($level_id == 5) || ($level_id == 5)) {
        $wp_user_object = new WP_User($user_id);
        if ((in_array("freemember", $wp_user_object->roles)) || (in_array("subscriber", $wp_user_object->roles)) || (in_array("standardmember", $wp_user_object->roles)))
            $wp_user_object->set_role('promotionalmember');
    }
    else {
        $wp_user_object = new WP_User($user_id);
        if (in_array("promotionalmember", $wp_user_object->roles))
            $wp_user_object->set_role('freemember');
    }
}

add_action("pmpro_after_change_membership_level", "my_pmpro_after_change_membership_level", 10, 2);

/*
  30 day free trial for annual plan
 */

function my_pmpro_profile_start_date($date, $order) {
    if (($order->membership_id == 2) || ($order->membership_id == 3) || ($order->membership_id == 4))
        $date = date("Y-m-d", strtotime("+ 1 Days")) . "T0:0:0";

    return $date;
}

add_filter("pmpro_profile_start_date", "my_pmpro_profile_start_date", 10, 2);

function my_pmpro_level_cost_text($cost, $level) {
    if ($level->id == 3) {
        $cost = str_replace("Year.", "Year", $cost);
        $cost .= " after your <strong>1 day trial</strong>.";
    }

    if ($level->id == 2) {
        $cost = str_replace("Month.", "Month", $cost);
        $cost .= " after your <strong>1 day trial</strong>.";
    }

    if ($level->id == 4) {
        $cost = str_replace("Month.", "Month", $cost);
        $cost .= " after your <strong>1 day trial</strong>.";
    }

    return $cost;
}

add_filter("pmpro_level_cost_text", "my_pmpro_level_cost_text", 10, 2);

//function my_pmpro_after_change_membership_level($level, $user_id) {
//    if ($level == 1) {
//        $order = new MemberOrder();
//        $order->getLastMemberOrder($user_id);
//        $order->cancel();
//    }
//}

//add_action('pmpro_after_change_membership_level', 'my_pmpro_after_change_membership_level');
/*
  Don't send WP's default notification email.
 */

function my_pmpro_wp_new_user_notification($notify) {
    return false;
}

add_filter("pmpro_wp_new_user_notification", "my_pmpro_wp_new_user_notification");

remove_action("wp", "pmpro_wp", 1);
add_action("wp", "custom_pmpro_wp", 1);

//this code runs after $post is set, but before template output
function custom_pmpro_wp() {
    if (!is_admin()) {
        global $post, $pmpro_pages, $pmpro_page_name, $pmpro_page_id;

        //run the appropriate preheader function
        foreach ($pmpro_pages as $pmpro_page_name => $pmpro_page_id) {
            if ($pmpro_page_name == "checkout") {
                continue;  //we do the checkout shortcode every time now
            }

            if (!empty($post->ID) && $pmpro_page_id == $post->ID) {

                require_once(KARMA_TEMPLATEPATH . "/preheaders/" . $pmpro_page_name . ".php");

                function pmpro_pages_shortcode($atts, $content = null, $code = "") {
                    global $pmpro_page_name;
                    ob_start();
                    include(KARMA_TEMPLATEPATH . "/pages/" . $pmpro_page_name . ".php");
                    $temp_content = ob_get_contents();
                    ob_end_clean();
                    return apply_filters("pmpro_pages_shortcode_" . $pmpro_page_name, $temp_content);
                }

                add_shortcode("pmpro_" . $pmpro_page_name, "pmpro_pages_shortcode");
                break; //only the first page found gets a shortcode replacement
            }
        }

        //make sure you load the preheader for the checkout page. the shortcode for checkout is loaded below
        if (!empty($post->post_content) && strpos($post->post_content, "[pmpro_checkout]") !== false) {
            require_once(KARMA_TEMPLATEPATH . "/preheaders/checkout.php");
        }
    }
}
?>
<?php
/*
	Adding First and Last Name to Checkout Form
*/

//add the fields to the form 
function my_pmpro_checkout_after_email() 
{
	if(!empty($_REQUEST['firstname']))
		$firstname = $_REQUEST['firstname'];
	else
		$firstname = "";
	if(!empty($_REQUEST['lastname']))
		$lastname = $_REQUEST['lastname']; 
	else
		$lastname = "";
?>
	<div>
		<label for="firstname">First Name</label>
		<input id="firstname" name="firstname" type="text" class="input" size="30" value="<?=esc_attr($firstname)?>" />
	</div>
	<div>
		<label for="lastname">Last Name</label>
		<input id="lastname" name="lastname" type="text" class="input" size="30" value="<?=esc_attr($lastname)?>" />
	</div>
	  <div>
                                            <label for="bphone">Phone</label>
                                            <input id="bphone" name="bphone" type="text" class="input" size="30" value="<?php echo esc_attr($bphone) ?>" />
                                            <?php echo formatPhone($bphone); ?>
                                        </div> 
	
<?php
}
add_action('pmpro_checkout_after_email', 'my_pmpro_checkout_after_email');

//update the user after checkout
function my_update_first_and_last_name_after_checkout($user_id)
{
	if(isset($_REQUEST['firstname']))
	{
		$firstname = $_REQUEST['firstname'];
		$lastname = $_REQUEST['lastname'];
	}
	elseif(isset($_SESSION['firstname']))
	{
		//maybe in sessions?
		$firstname = $_SESSION['firstname'];
		$lastname = $_SESSION['lastname'];

		//unset
		unset($_SESSION['firstname']);
		unset($_SESSION['lastname']);
	
	}

	if(isset($firstname))	
		update_user_meta($user_id, "first_name", $firstname);
	if(isset($lastname))
		update_user_meta($user_id, "last_name", $lastname);
}
add_action('pmpro_after_checkout', 'my_update_first_and_last_name_after_checkout');

//require the fields
function my_pmpro_registration_checks()
{
	global $pmpro_msg, $pmpro_msgt, $current_user;
	$firstname = $_REQUEST['firstname'];
	$lastname = $_REQUEST['lastname'];
 
	if($firstname && $lastname || $current_user->ID)
	{
		//all good
		return true;
	}
	else
	{
		$pmpro_msg = "First and last name are required.";
		$pmpro_msgt = "pmpro_error";
		return false;
	}
}
add_filter("pmpro_registration_checks", "my_pmpro_registration_checks");


?>
<?php
/* Add a custom field to the field editor (See editor screenshot) */
add_action("gform_field_standard_settings", "my_standard_settings", 10, 2);

function my_standard_settings($position, $form_id){

// Create settings on position 25 (right after Field Label)

if($position == 25){
?>
		
<li class="admin_label_setting field_setting" style="display: list-item; ">
<label for="field_placeholder">Placeholder Text

<!-- Tooltip to help users understand what this field does -->
<a href="javascript:void(0);" class="tooltip tooltip_form_field_placeholder" tooltip="&lt;h6&gt;Placeholder&lt;/h6&gt;Enter the placeholder/default text for this field.">(?)</a>
			
</label>
		
<input type="text" id="field_placeholder" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('placeholder', this.value);">
		
</li>
<?php
}
}

/* Now we execute some javascript technicalitites for the field to load correctly */

add_action("gform_editor_js", "my_gform_editor_js");

function my_gform_editor_js(){
?>
<script>
//binding to the load field settings event to initialize the checkbox
jQuery(document).bind("gform_load_field_settings", function(event, field, form){
jQuery("#field_placeholder").val(field["placeholder"]);
});
</script>

<?php
}

/* We use jQuery to read the placeholder value and inject it to its field */

add_action('gform_enqueue_scripts',"my_gform_enqueue_scripts", 10, 2);

function my_gform_enqueue_scripts($form, $is_ajax=false){
?>
<script>

jQuery(function(){
<?php

/* Go through each one of the form fields */

foreach($form['fields'] as $i=>$field){

/* Check if the field has an assigned placeholder */
			
if(isset($field['placeholder']) && !empty($field['placeholder'])){
				
/* If a placeholder text exists, inject it as a new property to the field using jQuery */
				
?>
				
jQuery('#input_<?php echo $form['id']?>_<?php echo $field['id']?>').attr('placeholder','<?php echo $field['placeholder']?>');
				
<?php
}
}
?>
});
</script>
<?php
}
function wpa_pmpro_after_checkout($user_id)
{
	$morder = new MemberOrder();
	$morder->getLastMemberOrder();
	
	if(!empty($morder->InitialPayment))
	{
		$sale_amt = $morder->InitialPayment; //TODO - The commission will be calculated based on this amount
		$unique_transaction_id = $morder->code; //TODO - The unique transaction ID for reference
		$email = $morder->Email; //TODO - Customer email for record
		$referrer = $_COOKIE['ap_id'];
		do_action('wp_affiliate_process_cart_commission', array("referrer" => $referrer, "sale_amt" =>$sale_amt, "txn_id"=>$unique_transaction_id, "buyer_email"=>$email));
	}
}
add_action("pmpro_after_checkout", "wpa_pmpro_after_checkout");
?>