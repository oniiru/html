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
?>