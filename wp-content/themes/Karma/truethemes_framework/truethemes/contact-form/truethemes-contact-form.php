<?php
/*

PLUGIN extended by TrueThemes. Original plugin:

Plugin Name: Grunion Contact Form
Description: Add a contact form to any post, page or text widget.  Emails will be sent to the post's author by default, or any email address you choose.  As seen on WordPress.com.
Plugin URI: http://automattic.com/#
AUthor: Automattic, Inc.
Author URI: http://automattic.com/
Version: 2.0
License: GPL3

*/

define('TRUETHEMES_PLUGIN_DIR', TEMPLATEPATH . '/truethemes_framework/truethemes/contact-form');
define('TRUETHEMES_PLUGIN_URL', get_template_directory_uri() . '/truethemes_framework/truethemes/contact-form');
//get recaptcha library
require_once TRUETHEMES_PLUGIN_DIR . '/truethemes-recaptchalib.php';


if ( is_admin())
	require_once TRUETHEMES_PLUGIN_DIR . '/admin.php';

// take the content of a contact-form shortcode and parse it into a list of field types
function contact_form_parse( $content ) {
	// first parse all the contact-field shortcodes into an array
	global $contact_form_fields, $grunion_form;
	$contact_form_fields = array();
	
	$out = do_shortcode( $content );
	
	if ( empty($contact_form_fields) || !is_array($contact_form_fields) ) {
		
		
		// default form
		$default_form = '
		[contact-field label="'.__('Name').'" type="name" required="true" /]
		[contact-field label="'.__('Email Address').'" type="email" required="true" /]
		[contact-field label="'.__('Website').'" type="url" /]';
		if ( 'yes' == strtolower($grunion_form->show_subject) )
			$default_form .= '
			[contact-field label="'.__('Subject').'" type="subject" /]';
		$default_form .= '
		[contact-field label="'.__('Comments').'" type="textarea" /]';

		$out = do_shortcode( $default_form );
	}

	return $out;
}





function contact_form_render_field( $field ) {
$contact_successmsg = get_option('ka_contact_successmsg');
$contact_required = get_option('ka_contact_required');
global $contact_form_last_id, $contact_form_errors, $contact_form_fields, $current_user, $user_identity;
	
	
	$r = '';
	
	$field_id = $field['id'];
	if ( isset($_POST[ $field_id ]) ) {
		$field_value = stripslashes( $_POST[ $field_id ] );
	} elseif ( is_user_logged_in() ) {
		// Special defaults for logged-in users
		if ( $field['type'] == 'email' )
			$field_value = $current_user->data->user_email;
		elseif ( $field['type'] == 'name' )
			$field_value = $user_identity;
		elseif ( $field['type'] == 'url' )
			$field_value = $current_user->data->user_url;
		else
			$field_value = $field['default'];
	} else {
		$field_value = $field['default'];
	}
	
	$field_value = wp_kses($field_value, array());

	$field['label'] = html_entity_decode( $field['label'] );
	$field['label'] = wp_kses( $field['label'], array() );
	
	
	
	
	

	if ( $field['type'] == 'email' ) {
		$r .= "\n<div>\n";
		$r .= "\t\t<label for='".esc_attr($field_id)."' class='grunion-field-label ".esc_attr($field['type']) . ( contact_form_is_error($field_id) ? ' form-error' : '' ) . "'>" . htmlspecialchars( $field['label'] ) . ( $field['required'] ? '<span> '. __($contact_required) . '</span>' : '' ) . "</label>\n";
		$r .= "\t\t<input type='text' name='".esc_attr($field_id)."' id='".esc_attr($field_id)."' value='".esc_attr($field_value)."' class='".esc_attr($field['type'])."'/>\n";
		$r .= "\t</div>\n";
		
		
		
		
		
	} elseif ( $field['type'] == 'textarea' ) {
		$r .= "\n<div>\n";
		$r .= "\t\t<label for='".esc_attr($field_id)."' class='".esc_attr($field['type']) . ( contact_form_is_error($field_id) ? ' form-error' : '' ) . "'>" . htmlspecialchars( $field['label'] ) . ( $field['required'] ? '<span> '. __($contact_required) . '</span>' : '' ) . "</label>";$r .= "\t\t<textarea name='".esc_attr($field_id)."' id='".esc_attr($field_id)."' rows='20' cols='20'>".htmlspecialchars($field_value)."</textarea>\n";
		$r .= "\t</div>\n";
		
		
		
		
		
	} elseif ( $field['type'] == 'radio' ) {
		$r .= "\t<div><label class='". ( contact_form_is_error($field_id) ? ' form-error' : '' ) . "'>" . htmlspecialchars( $field['label'] ) . ( $field['required'] ? '<span> '. __($contact_required) . '</span>' : '' ) . "</label>\n";
		foreach ( $field['options'] as $option ) {
			$r .= "\t\t<input type='radio' name='".esc_attr($field_id)."' value='".esc_attr($option)."' class='".esc_attr($field['type'])."' ".( $option == $field_value ? "checked='checked' " : "")." />\n";
 			$r .= "\t\t<label class='".esc_attr($field['type']) . ( contact_form_is_error($field_id) ? ' form-error' : '' ) . "'>". htmlspecialchars( $option ) . "</label>\n";
			$r .= "\t\t<div class='clear-form'></div>\n";
		}
		$r .= "\t\t</div>\n";
		
		
		
		
		
	} elseif ( $field['type'] == 'checkbox' ) {
		$r .= "\t<div>\n";
		$r .= "\t\t<input type='checkbox' name='".esc_attr($field_id)."' value='".__('Yes')."' class='".esc_attr($field['type'])."' ".( $field_value ? "checked='checked' " : "")." />\n";
		$r .= "\t\t<label class='".esc_attr($field['type']) . ( contact_form_is_error($field_id) ? ' form-error' : '' ) . "'>\n";
		$r .= "\t\t". htmlspecialchars( $field['label'] ) . ( $field['required'] ? '<span> '. __($contact_required) . '</span>' : '' ) . "</label>\n";
		$r .= "\t\t<div class='clear-form'></div>\n";
		$r .= "\t</div>\n";
		
		
		
		
		
	} elseif ( $field['type'] == 'select' ) {
		$r .= "\n<div>\n";
		$r .= "\t\t<label for='".esc_attr($field_id)."' class='".esc_attr($field['type']) . ( contact_form_is_error($field_id) ? ' form-error' : '' ) . "'>" . htmlspecialchars( $field['label'] ) . ( $field['required'] ? '<span> '. __($contact_required) . '</span>' : '' ) . "</label>\n";
		$r .= "\t<select name='".esc_attr($field_id)."' id='".esc_attr($field_id)."' class='".esc_attr($field['type'])."'>\n";
		foreach ( $field['options'] as $option ) {
			$option = html_entity_decode( $option );
			$option = wp_kses( $option, array() );
			$r .= "\t\t<option".( $option == $field_value ? " selected='selected'" : "").">". esc_html( $option ) ."</option>\n";
		}
		$r .= "\t</select>\n";
		$r .= "\t</div>\n";
		
		
		
		
		
	} else {
		// default: text field
		// note that any unknown types will produce a text input, so we can use arbitrary type names to handle
		// input fields like name, email, url that require special validation or handling at POST
		$r .= "\n<div>\n";
		$r .= "\t\t<label for='".esc_attr($field_id)."' class='".esc_attr($field['type']) . ( contact_form_is_error($field_id) ? ' form-error' : '' ) . "'>" . htmlspecialchars( $field['label'] ) . ( $field['required'] ? '<span> '. __($contact_required) . '</span>' : '' ) . "</label>\n";
		$r .= "\t\t<input type='text' name='".esc_attr($field_id)."' id='".esc_attr($field_id)."' value='".esc_attr($field_value)."' class='".esc_attr($field['type'])."'/>\n";
		$r .= "\t</div>\n";
	}
	
	return $r;
}







function contact_form_validate_field( $field ) {
    global $contact_form_last_id, $contact_form_errors, $contact_form_values;
                
    $field_id = $field['id'];
    $field_value = isset($_POST[ $field_id ]) ? stripslashes($_POST[ $field_id ]) : '';
    
    //Mod by denzel
    //email validation
    if(strstr($field_id,'email')){// if $field_id contains email
      if(!empty($field_value)){ //if value is not empty
        //use regular expression to check for email validity
        if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$",$field_value)){
            //if not valid, we add in error message.  
            if ( !is_wp_error($contact_form_errors) )
            $contact_form_errors = new WP_Error();
            $contact_form_errors->add( $field_id, sprintf( __('Please provide a valid email address','truethemes'),$field['label']));            
        }
      }
    }
     
    if ( $field['required'] && !trim($field_value) ) {
        if ( !is_wp_error($contact_form_errors) )
            $contact_form_errors = new WP_Error();
        $contact_form_errors->add( $field_id, sprintf( __('%s is required','truethemes'), $field['label'] ) );
    }
    
    $contact_form_values[ $field_id ] = $field_value;
}

function contact_form_is_error( $field_id ) {
	global $contact_form_errors;
	
	return ( is_wp_error( $contact_form_errors ) && $contact_form_errors->get_error_message( $field_id ) );
}

// generic shortcode that handles all of the major input types
// this parses the field attributes into an array that is used by other functions for rendering, validation etc
function contact_form_field( $atts, $content, $tag ) {
	global $contact_form_fields, $contact_form_last_id, $grunion_form;
	
	$field = shortcode_atts( array(
		'label' => null,
		'type' => 'text',
		'required' => false,
		'options' => array(),
		'id' => null,
		'default' => null,
	), $atts);
	
	// special default for subject field
	if ( $field['type'] == 'subject' && is_null($field['default']) )
		$field['default'] = $grunion_form->subject;
	
	// allow required=1 or required=true
	if ( $field['required'] == '1' || strtolower($field['required']) == 'true' )
		$field['required'] = true;
	else
		$field['required'] = false;
		
	// parse out comma-separated options list
	if ( !empty($field['options']) && is_string($field['options']) )
		$field['options'] = array_map('trim', explode(',', $field['options']));

	// make a unique field ID based on the label, with an incrementing number if needed to avoid clashes
	$id = $field['id'];
	if ( empty($id) ) {
		$id = sanitize_title_with_dashes( 'a' . $contact_form_last_id . '-' . $field['label'] );
		$i = 0;
		while ( isset( $contact_form_fields[ $id ] ) ) {
			$i++;
			$id = sanitize_title_with_dashes( 'a' . $contact_form_last_id . '-' . $field['label'] . '-' . $i );
		}
		$field['id'] = $id;
	}
	
	$contact_form_fields[ $id ] = $field;
	
	if ( $_POST )
		contact_form_validate_field( $field );
	
	return contact_form_render_field( $field );
}

add_shortcode('contact-field', 'contact_form_field');






function contact_form_shortcode( $atts, $content ) {
	global $post;

	$default_to = get_option( 'admin_email' );
	$default_subject = "[" . get_option( 'blogname' ) . "]";

	if ( !empty( $atts['widget'] ) && $atts['widget'] ) {
		$default_subject .=  " Sidebar";
	} elseif ( $post->ID ) {
		$default_subject .= " ". wp_kses( $post->post_title, array() );
		$post_author = get_userdata( $post->post_author );
		$default_to = $post_author->user_email;
	}

	extract( shortcode_atts( array(
		'to' => $default_to,
		'subject' => $default_subject,
		'show_subject' => 'no', // only used in back-compat mode
		'widget' => 0 //This is not exposed to the user. Works with contact_form_widget_atts
	), $atts ) );

	 $widget = esc_attr( $widget );

	if ( ( function_exists( 'faux_faux' ) && faux_faux() ) || is_feed() )
		return '[contact-form]';

	global $wp_query, $grunion_form, $contact_form_errors, $contact_form_values, $user_identity, $contact_form_last_id, $contact_form_message;
	
	// used to store attributes, configuration etc for access by contact-field shortcodes
	$grunion_form = new stdClass();
	$grunion_form->to = $to;
	$grunion_form->subject = $subject;
	$grunion_form->show_subject = $show_subject;

	if ( $widget )
		$id = 'widget-' . $widget;
	elseif ( is_singular() )
		$id = $wp_query->get_queried_object_id();
	else
		$id = $GLOBALS['post']->ID;
	if ( !$id ) // something terrible has happened
		return '[contact-form]';

	if ( $id == $contact_form_last_id )
		return;
	else
		$contact_form_last_id = $id;

	ob_start();
		wp_nonce_field( 'contact-form_' . $id );
		$nonce = ob_get_contents();
	ob_end_clean();


	$body = contact_form_parse( $content );

	$r = "<div id='contact-form-$id'>\n";
	
	$errors = array();
	if ( is_wp_error( $contact_form_errors ) && $errors = (array) $contact_form_errors->get_error_codes() ) {
		$r .= "<div class='form-error'><p class=\"message_yellow\">\n";
		foreach ( $contact_form_errors->get_error_messages() as $message )
			$r .= "\t- $message<br />";
		$r .= "</p>";
		$r .= "\n</div>\n\n";
		
	}
	
	$r .= "<form action='#' method='post' class='contact-form commentsblock'>\n";
	$r .= $body;

//start of reCAPTCHA form
	$captcha_public = get_option('ka_publickey');
	if($captcha_public){
	    //since version 2.6, allows recaptcha themes, or custom code.
	    global $ttso;
	    $themes = $ttso->ka_recaptcha_theme;
	    $custom_theme_code = $ttso->ka_recaptcha_custom;
	    
	    if($custom_theme_code == ''):
	    
	    //no reCAPTCHA custom theme, we show one of the 4 standard themes's
	    //javascript option.
	    	
	    	if($themes=="white_theme"){
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'white'};</script>";
	        }elseif($themes=="black_theme"){
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'blackglass'};</script>";	        
	        }elseif($themes=="clean_theme"){
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'clean'};</script>";	        
	        }else{
	        //show default theme
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'red'};</script>";	        
	        }
	    
	    endif;
	    
	    
	    if($custom_theme_code !== ''):
	    //there is custom reCAPTCHA theme code    
	    $r .= $custom_theme_code;
	    $r .= tt_recaptcha_get_html($captcha_public);
    	$r .= "<br/>";
        else:	    
	    //no custom recaptcha code, we show default reCAPTCHA form
	    $r .= tt_recaptcha_get_html($captcha_public);
    	$r .= "<br/>";
    	endif;
    }	
    $r .= "\t<p class='contact-submit'>\n";
    
    /**
    * checks Site Option - Form Setting for submit button text 
    * defaults to "Submit".
    * @since version 2.6 development
    */
    global $ttso;
    $submit_text = $ttso->ka_submit_button_text;
    if(!empty($submit_text)){
    $submit_button_text = $submit_text;
    }else{
    $submit_button_text = "SUBMIT";
    }
    
	$r .= "\t\t<input type='submit' value='" . __( "$submit_button_text","truethemes" ) . "' class='ka-form-submit'/>\n";
	
	$r .= "\t\t$nonce\n";
	$r .= "\t\t<input type='hidden' name='contact-form-id' value='$id' />\n";
	$r .= "\t</p>\n";
	$r .= "</form>\n</div>";
	
	// form wasn't submitted, just a GET
	if ( empty($_POST) )
		return $r;
		
    //check recaptcha
	require_once TRUETHEMES_PLUGIN_DIR . '/truethemes-recaptchalib.php';
	$captcha_public = get_option('ka_publickey');
	$captcha_private = get_option('ka_privatekey');//get recaptcha private key from options panel
	if(!empty($captcha_private)&&!empty($captcha_public)){
	$resp = tt_recaptcha_check_answer ($captcha_private,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]); //verify captcha entered with server.
     
	     //if invalid we return error                           
 	    if (!$resp->is_valid) {
	        global $contact_form_errors;
			$contact_form_errors = new WP_Error();
			$contact_form_errors->add('', sprintf( __('Invalid CAPTCHA','truethemes'), '' ) );
			$r = "<div id='contact-form-$id'>\n";
	
			$errors = array();
			if ( is_wp_error( $contact_form_errors ) && $errors = (array) $contact_form_errors->get_error_codes() ) {
			$r .= "<div class='form-error'><p class=\"message_yellow\">\n";
				foreach ( $contact_form_errors->get_error_messages() as $message ){
				$r .= "\t- $message<br />";
				$r .= "</p>";
				$r .= "\n</div>\n\n";
				}//end foreach		
			}//end if(is_wp_error
	
			$r .= "<form action='' method='post' class='contact-form commentsblock'>\n";
			$r .= $body;
			$captcha_public = get_option('ka_publickey');


//start of reCAPTCHA form
			if($captcha_public){
	   
	   
	    //since version 2.6, allows recaptcha themes.
	    global $ttso;
	    $themes = $ttso->ka_recaptcha_theme;
	    $custom_theme_code = $ttso->ka_recaptcha_custom;
	    
	    if($custom_theme_code == ''):
	    
	    //no reCAPTCHA custom theme, we show one of the 4 standard themes's
	    //javascript option.
	    	
	    	if($themes=="white_theme"){
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'white'};</script>";
	        }elseif($themes=="black_theme"){
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'blackglass'};</script>";	        
	        }elseif($themes=="clean_theme"){
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'clean'};</script>";	        
	        }else{
	        //show default theme
		echo "<script type='text/javascript'>var RecaptchaOptions = {theme : 'red'};</script>";	        
	        }
	    
	    endif;
	    
	    
	    if($custom_theme_code !== ''):
	    //there is custom reCAPTCHA theme code    
	    $r .= $custom_theme_code;
	    $r .= tt_recaptcha_get_html($captcha_public);
    	$r .= "<br/>";	    
	    else:	    
	    //no custom recaptcha code, we show default reCAPTCHA form
	    $r .= tt_recaptcha_get_html($captcha_public);
    	$r .= "<br/>";
    	endif;
    	
    	
    	
   			 }
   			 
   			 
   	/**
    * checks Site Option - Form Setting for submit button text 
    * defaults to "Submit".
    * @since version 2.6 development
    */
    global $ttso;
    $submit_text = $ttso->ka_submit_button_text;
    if(!empty($submit_text)){
    $submit_button_text = $submit_text;
    }else{
    $submit_button_text = "SUBMIT";
    }   			 
   			 
   			 $r .= "\t<p class='contact-submit'>\n";
			$r .= "\t\t<input type='submit' value='" . __( "$submit_button_text","truethemes" ) . "' class='ka-form-submit'/>\n";
			$r .= "\t\t$nonce\n";
			$r .= "\t\t<input type='hidden' name='contact-form-id' value='$id' />\n";
			$r .= "\t</p>\n";
			$r .= "</form>\n</div>";
			return $r;
     	} //end of recaptcha is_valid check.

	}//end if($captcha_private)
	
	   
   if ( is_wp_error($contact_form_errors) )
		return $r;
		
	
	$emails = str_replace( ' ', '', $to );
	$emails = explode( ',', $emails );
	foreach ( (array) $emails as $email ) {
		if ( is_email( $email ) && ( !function_exists( 'is_email_address_unsafe' ) || !is_email_address_unsafe( $email ) ) )
			$valid_emails[] = $email;
	}

	$to = ( $valid_emails ) ? $valid_emails : $default_to;

	$message_sent = contact_form_send_message( $to, $subject, $widget );

	if ( is_array( $contact_form_values ) )
		extract( $contact_form_values );

	if ( !isset( $comment_content ) )
		$comment_content = '';
	else
		$comment_content = wp_kses( $comment_content, array() );


	$r = "<div id='contact-form-$id'>\n";

	$errors = array();
	if ( is_wp_error( $contact_form_errors ) && $errors = (array) $contact_form_errors->get_error_codes() ) :
		$r .= "<div class='form-error'><p class=\"message_yellow\">\n";
		foreach ( $contact_form_errors->get_error_messages() as $message )
			$r .= "\t- $message<br />";
		$r .= "</p>";
		$r .= "\n</div>\n\n";
	else :
		$r .= "</div>";
		$contact_successmsg = get_option('ka_contact_successmsg');
$contact_required = get_option('ka_contact_required');
		
		echo '<p class="message_green">'.$contact_successmsg.'</p>';
		return $r;
	endif;

	return $r;
}
add_shortcode( 'contact-form', 'contact_form_shortcode' );

function contact_form_send_message( $to, $subject, $widget ) {
	global $post;
	
 	if ( !isset( $_POST['contact-form-id'] ) )
		return;
		                             
                                		
	if ( ( $widget && 'widget-' . $widget != $_POST['contact-form-id'] ) || ( !$widget && $post->ID != $_POST['contact-form-id'] ) )
		return;

	if ( $widget )
		check_admin_referer( 'contact-form_widget-' . $widget );
	else
		check_admin_referer( 'contact-form_' . $post->ID );

	global $contact_form_values, $contact_form_errors, $current_user, $user_identity;
	global $contact_form_fields, $contact_form_message;
	
	// compact the fields and values into an array of Label => Value pairs
	// also find values for comment_author_email and other significant fields
	$all_values = $extra_values = array();
	
	foreach ( $contact_form_fields as $id => $field ) {
		if ( $field['type'] == 'email' && !isset( $comment_author_email ) ) {
			$comment_author_email = $contact_form_values[ $id ];
			$comment_author_email_label = $field['label'];
		} elseif  ( $field['type'] == 'name' && !isset( $comment_author ) ) {
			$comment_author = $contact_form_values[ $id ];
			$comment_author_label = $field['label'];
		} elseif ( $field['type'] == 'url' && !isset( $comment_author_url ) ) {
			$comment_author_url = $contact_form_values[ $id ];
			$comment_author_url_label = $field['label'];
		} elseif ( $field['type'] == 'textarea' && !isset( $comment_content ) ) {
			$comment_content = $contact_form_values[ $id ];
			$comment_content_label = $field['label'];
		} else {
			$extra_values[ $field['label'] ] = $contact_form_values[ $id ];
		}
		
		$all_values[ $field['label'] ] = $contact_form_values[ $id ];
	}

/*
	$contact_form_values = array();
	$contact_form_errors = new WP_Error();

	list($comment_author, $comment_author_email, $comment_author_url) = is_user_logged_in() ?
		add_magic_quotes( array( $user_identity, $current_user->data->user_email, $current_user->data->user_url ) ) :
		array( $_POST['comment_author'], $_POST['comment_author_email'], $_POST['comment_author_url'] );
*/

	$comment_author = stripslashes( apply_filters( 'pre_comment_author_name', $comment_author ) );

	$comment_author_email = stripslashes( apply_filters( 'pre_comment_author_email', $comment_author_email ) );

	$comment_author_url = stripslashes( apply_filters( 'pre_comment_author_url', $comment_author_url ) );
	if ( 'http://' == $comment_author_url )
		$comment_author_url = '';

	$comment_content = stripslashes( $comment_content );
	$comment_content = trim( wp_kses( $comment_content, array() ) );

	if ( empty( $contact_form_subject ) )
		$contact_form_subject = $subject;
	else
		$contact_form_subject = trim( wp_kses( $contact_form_subject, array() ) );
		
	$comment_author_IP = $_SERVER['REMOTE_ADDR'];

	$vars = array( 'comment_author', 'comment_author_email', 'comment_author_url', 'contact_form_subject', 'comment_author_IP' );
	foreach ( $vars as $var )
		$$var = str_replace( array("\n", "\r" ), '', $$var ); // I don't know if it's possible to inject this
	$vars[] = 'comment_content';

	$contact_form_values = compact( $vars );

	if ( function_exists( 'akismet_http_post' ) ) {
		$spam = '';
		$akismet_values = contact_form_prepare_for_akismet( $contact_form_values );
		$is_spam = contact_form_is_spam_akismet( $akismet_values );
		if ( is_wp_error( $is_spam ) )
			return; // abort
		else if ( $is_spam )
			$spam = '***SPAM*** ';
	}

	if ( !$comment_author )
		$comment_author = $comment_author_email;
		
	$headers = 'From: ' . wp_kses( $comment_author, array() ) .
		' <' . wp_kses( $comment_author_email, array() ) . ">\r\n" .
		'Reply-To: ' . wp_kses( $comment_author_email, array() ) . "\r\n" .
		"Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\""; 
	$subject = apply_filters( 'contact_form_subject', $spam . $contact_form_subject );
	$subject = wp_kses( $subject, array() );

	$time = date_i18n( __('l F j, Y \a\t g:i a','truethemes'), current_time( 'timestamp' ) );


/** comment out original comment message setup 
	

	$extra_content = '';
	
	foreach ( $extra_values as $label => $value ) {
		$extra_content .= $label . ': ' . trim($value) . "\n";
		$extra_content_br .= wp_kses( $label, array() ) . ': ' . wp_kses( trim($value), array() ) . "<br />";
	}


$message = $comment_author_label . ": " . $comment_author . "\n" . $comment_author_email_label . ": " . $comment_author_email . "\n" . $comment_author_url_label . ": " . $comment_author_url . "\n" . $comment_content_label . ": " . $comment_content . "
$extra_content
" . __( "Message was sent on" ) . " " . $time . "

";

********/



//  construct message from posted form data!

/**
Important Notes!
For the default fields, name, email, url and comment, there is no auto sequencing, you will need to rearrange the below message output sequence, by rearranging the codes!
For extra user added fields, the plugin had saved the sequance in an array and assigned to $extra_values.
I would not know how to made all auto sequencing!
**/   
    
    //added by denzel

    
    $message = null;
    
    //name, this is default content
    if(!empty($comment_author)){ //check whether got name enter.
    $message .= "$comment_author_label : $comment_author <br/>\n\n";   
    }
    
    //email, this is default content	
	if(!empty($comment_author_email)){ //check whether got email address enter.
    $message .= "$comment_author_email_label : $comment_author_email <br/>\n\n";   
    }
    
    //url, this is default content
    if(!empty($comment_author_url)){ //check whether got url enter.
    $message .= "$comment_author__url_label : $comment_author_url <br/>\n\n";   
    }
        
    //extra fields added by user;
    
    $extra_content = null;
	
	foreach ( $extra_values as $label => $value ) {
	    if(!empty($value)){
		$extra_content .= $label . ': ' . trim($value) . "<br/>\n\n";
		}
	}    
    
    if(!empty($extra_content)){
    $message .= $extra_content;   
    }
    
    //comment, this is default content
    if(!empty($comment_content)){ //check whether got comment content enter.
    $message .= "$comment_content_label : $comment_content <br/>\n\n\n";   
    }
        
    $message.= "Message was sent on"." ".$time."\n";			
	
	/**end of email form message! ended by denzel **/
	


	
	// Construct message that is returned to user
	$contact_form_message = "<blockquote>";
	if (isset($comment_author_label))
		$contact_form_message .= wp_kses( $comment_author_label, array() ) . ": " . wp_kses( $comment_author, array() ) . "<br />";
    if (isset($comment_author_email_label))
		$contact_form_message .= wp_kses( $comment_author_email_label, array() ) . ": " . wp_kses( $comment_author_email, array() ) . "<br />"; 
    if (isset($comment_author_url_label))
		$contact_form_message .= wp_kses( $comment_author_url_label, array() ) . ": " . wp_kses( $comment_author_url, array() ) . "<br />";
	if (isset($comment_content_label))
		$contact_form_message .= wp_kses( $comment_content_label, array() ) . ": " . wp_kses( $comment_content, array() ) . "<br />";
	if (isset($extra_content_br))
		$contact_form_message .= $extra_content_br;
	$contact_form_message .= "</blockquote><br /><br />";

	if ( is_user_logged_in() ) {
		$message .= ( "" );
	} else {
		$message .= ( "" );
	}

	$message = apply_filters( 'contact_form_message', $message );
	$message = wp_kses( $message, array() );

	$to = apply_filters( 'contact_form_to', $to );
	$to = wp_kses( $to, array() );

	// keep a copy of the feedback as a custom post type
	$feedback_mysql_time = current_time( 'mysql' );
	$feedback_title = "{$comment_author} - {$feedback_mysql_time}";
	$feedback_status = 'publish';
	if ( $is_spam )
		$feedback_status = 'spam';

	$post_id = wp_insert_post( array(
		'post_date'		=> $feedback_mysql_time,
		'post_type'		=> 'feedback',
		'post_status'	=> $feedback_status,
		'post_parent'	=> $post->ID,
		'post_title'	=> wp_kses( $feedback_title, array() ),
		'post_content'	=> wp_kses($comment_content . "\n<!--more-->\n" . "AUTHOR: {$comment_author}\nAUTHOR EMAIL: {$comment_author_email}\nAUTHOR URL: {$comment_author_url}\nSUBJECT: {$contact_form_subject}\nIP: {$comment_author_IP}\n" . print_r( $all_values, TRUE ), array()), // so that search will pick up this data
		'post_name'		=> md5( $feedback_title )
	) );
	update_post_meta( $post_id, '_feedback_author', wp_kses( $comment_author, array() ) );
	update_post_meta( $post_id, '_feedback_author_email', wp_kses( $comment_author_email, array() ) );
	update_post_meta( $post_id, '_feedback_author_url', wp_kses( $comment_author_url, array() ) );
	update_post_meta( $post_id, '_feedback_subject', wp_kses( $contact_form_subject, array() ) );
	update_post_meta( $post_id, '_feedback_ip', wp_kses( $comment_author_IP, array() ) );
	update_post_meta( $post_id, '_feedback_all_fields', wp_kses( $all_values, array() ) );
	update_post_meta( $post_id, '_feedback_extra_fields', wp_kses( $extra_values, array() ) );
	update_post_meta( $post_id, '_feedback_akismet_values', wp_kses( $akismet_values, array() ) );
	update_post_meta( $post_id, '_feedback_email', array( 'to' => $to, 'subject' => $subject, 'message' => $message, 'headers' => $headers ) );

	// Only send the email if it's not spam
	if ( !$is_spam )
		return wp_mail( $to, $subject, $message, $headers );
	return true;
}

// populate an array with all values necessary to submit a NEW comment to Akismet
// note that this includes the current user_ip etc, so this should only be called when accepting a new item via $_POST
function contact_form_prepare_for_akismet( $form ) {

	$form['comment_type'] = 'contact_form';
	$form['user_ip']      = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
	$form['user_agent']   = $_SERVER['HTTP_USER_AGENT'];
	$form['referrer']     = $_SERVER['HTTP_REFERER'];
	$form['blog']         = home_url();

	$ignore = array( 'HTTP_COOKIE' );

	foreach ( $_SERVER as $k => $value )
		if ( !in_array( $k, $ignore ) && is_string( $value ) )
			$form["$k"] = $value;
			
	return $form;
}

// submit an array to Akismet. If you're accepting a new item via $_POST, run it through contact_form_prepare_for_akismet() first
function contact_form_is_spam_akismet( $form ) {
	global $akismet_api_host, $akismet_api_port;

	$query_string = '';
	foreach ( array_keys( $form ) as $k )
		$query_string .= $k . '=' . urlencode( $form[$k] ) . '&';

	$response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port );
	$result = false;
	if ( 'true' == trim( $response[1] ) ) // 'true' is spam
		$result = true;
	return apply_filters( 'contact_form_is_spam_akismet', $result, $form );
}

// submit a comment as either spam or ham
// $as should be a string (either 'spam' or 'ham'), $form should be the comment array
function contact_form_akismet_submit( $as, $form ) {
	global $akismet_api_host, $akismet_api_port;
	
	if ( !in_array( $as, array( 'ham', 'spam' ) ) )
		return false;

	$query_string = '';
	foreach ( array_keys( $form ) as $k )
		$query_string .= $k . '=' . urlencode( $form[$k] ) . '&';

	$response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/submit-'.$as, $akismet_api_port );
	return trim( $response[1] );
}

function contact_form_widget_atts( $text ) {
	static $widget = 0;
	
	$widget++;

	return str_replace( '[contact-form', '[contact-form widget="' . $widget . '"', $text );
}
add_filter( 'widget_text', 'contact_form_widget_atts', 0 );

function contact_form_widget_shortcode_hack( $text ) {
	$old = $GLOBALS['shortcode_tags'];
	remove_all_shortcodes();
	add_shortcode( 'contact-form', 'contact_form_shortcode' );
	$text = do_shortcode( $text );
	$GLOBALS['shortcode_tags'] = $old;
	return $text;
}

function contact_form_init() {
	if ( function_exists( 'akismet_http_post' ) )
		add_filter( 'contact_form_is_spam', 'contact_form_is_spam_akismet', 10, 2 );
	if ( !has_filter( 'widget_text', 'do_shortcode' ) )
		add_filter( 'widget_text', 'contact_form_widget_shortcode_hack', 5 );

	// custom post type we'll use to keep copies of the feedback items
	register_post_type( 'feedback', array(
		'labels'	=> array(
			'name'			=> __( 'Messages','truethemes' ),
			'singular_name'	=> __( 'Message','truethemes' ),
			'search_items'	=> __( 'Search Message','truethemes' ),
			'not_found'		=> __( 'No message found','truethemes' ),
			'not_found_in_trash'	=> __( 'No message found','truethemes' )
		),
		'menu_icon'		=> TRUETHEMES_PLUGIN_URL . '/images/grunion-menu.png',
		'show_ui'		=> TRUE,
		'public'		=> FALSE,
		'rewrite'		=> FALSE,
		'query_var'		=> FALSE,
		'capability_type'	=> 'page'
	) );

	register_post_status( 'spam', array(
		'label'			=> 'Spam',
		'public'		=> FALSE,
		'exclude_from_search'	=> TRUE,
		'show_in_admin_all_list'=> FALSE,
		'label_count' => _n_noop( 'Spam <span class="count">(%s)</span>', 'Spam <span class="count">(%s)</span>' ),
		'protected'		=> TRUE,
		'_builtin'		=> FALSE
	) );
}
add_action( 'init', 'contact_form_init' );

/**
 * Add a contact form button to the post composition screen
 */
add_action( 'media_buttons', 'grunion_media_button', 999 );
function grunion_media_button( ) {
	global $post_ID, $temp_ID;
	$iframe_post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$title = esc_attr( __( 'Add a custom form','truethemes' ) );
	$plugin_url = esc_url( TRUETHEMES_PLUGIN_URL );
	$site_url = admin_url( "/admin-ajax.php?post_id=$iframe_post_id&amp;grunion=form-builder&amp;action=grunion_form_builder&amp;TB_iframe=true&amp;width=768" );

	echo '<a href="' . $site_url . '&id=add_form" class="thickbox" title="' . $title . '"><img src="' . $plugin_url . '/images/grunion-form.png" alt="' . $title . '" width="13" height="12" /></a>';
}


if ( !empty( $_GET['grunion'] ) && $_GET['grunion'] == 'form-builder' ) {
	add_action( 'parse_request', 'parse_wp_request' );
	add_action( 'wp_ajax_grunion_form_builder', 'parse_wp_request' );
}

function parse_wp_request( $wp ) {
	display_form_view( );
	exit;
}

function display_form_view( ) {
	require_once TRUETHEMES_PLUGIN_DIR . '/truethemes-form-view.php';
}

function menu_alter() {
    echo '
	<style>
	#menu-posts-feedback .wp-menu-image img { display: none; }
	#adminmenu .menu-icon-feedback:hover div.wp-menu-image, #adminmenu .menu-icon-feedback.wp-has-current-submenu div.wp-menu-image, #adminmenu .menu-icon-feedback.current div.wp-menu-image { background: url("' .TRUETHEMES_PLUGIN_URL . '/images/grunion-menu-hover.png") no-repeat 6px 7px !important; }
	#adminmenu .menu-icon-feedback div.wp-menu-image, #adminmenu .menu-icon-feedback div.wp-menu-image, #adminmenu .menu-icon-feedback div.wp-menu-image { background: url("' . TRUETHEMES_PLUGIN_URL . '/images/grunion-menu.png") no-repeat 6px 7px !important; }
	</style>';
}

add_action('admin_head', 'menu_alter');
