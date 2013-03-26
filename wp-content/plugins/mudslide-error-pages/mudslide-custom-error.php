<?php
/*
Plugin Name: Mudslide Custom Error
Plugin URI: http://mudslidedesign.co.uk
Description: Wordpress errors on the website can now be templated by creating an error.php file in you template
Author: Mudslide Design
Version: 1.0
Author URI: http://mudslidedesign.co.uk
*/ 

add_action("init", "init_mudslide_custom_error");

function init_mudslide_custom_error() {
    global $mudslide_custom_error;
    $mudslide_custom_error = new MudslideCustomError();
}

class MudslideCustomError {

    /**
     * Construct a new instance of the Mudslide Custom Error plugin, which in turn sets up all the necessary
     * actions and filters
     */
    public function __construct() {
        //the current wp_die_handler is not very friendly to see on the website, change it
        add_filter('wp_die_handler', array(&$this, 'get_custom_wp_die_handler'));
    }
	
    /**
     * Set the die handler to be our custom one
     */
    function get_custom_wp_die_handler() {
        return array(&$this, 'mudslide_website_wp_die_handler');
    }

    /**
     * Look for error.php and use it if it exists
     */
    function mudslide_website_wp_die_handler($message, $title = '', $args = array()) {
        
        $errorTemplate = get_theme_root(). '/'. get_template(). '/error.php';	
		
        if(!is_admin() && file_exists($errorTemplate)) {
            $defaults = array( 'response' => 500 );
            $r = wp_parse_args($args, $defaults);

            $have_gettext = function_exists('__');

            if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
                if ( empty( $title ) ) {
                    $error_data = $message->get_error_data();
                    if ( is_array( $error_data ) && isset( $error_data['title'] ) )
                        $title = $error_data['title'];
                }
                $errors = $message->get_error_messages();
                
                switch ( count( $errors ) ) :
                    case 0 :
                        $message = '';
                        break;
                    case 1 :
                        $message = "<p>{$errors[0]}</p>";
                        break;
                    default :
                        $message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
                        break;
                endswitch;
            } elseif ( is_string( $message ) ) {
            
                $message = "<p>$message</p>";
            }


           if ( empty($title) )
            $title = $have_gettext ? __('WordPress &rsaquo; Error') : 'WordPress &rsaquo; Error';
		
           require_once($errorTemplate);
		
           die();
        } else {
            _default_wp_die_handler($message, $title, $args);
        }
    }
	
}


?>
