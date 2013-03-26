<?php

/*-----------------------------------------------------------------------------------*/
/* Theme Header Output - wp_head() */
/*-----------------------------------------------------------------------------------*/

// This sets up the layouts and styles selected from the options panel

if (!function_exists('siteoptions_wp_head')) {
	function siteoptions_wp_head() { 
		$shortname = "ka";
	    
		//Styles
	          
			$GLOBALS['main_stylesheet'] = get_option('ka_main_scheme');
	        if($GLOBALS['main_stylesheet'] != '')
	               echo '<link href="'. KARMA_CSS . $GLOBALS['main_stylesheet'] .'.css'.'" rel="stylesheet" type="text/css" />'."\n";
				   
			if($GLOBALS['main_stylesheet'] == '')
	               echo '<link href="'. KARMA_CSS . 'karma-dark' .'.css'.'" rel="stylesheet" type="text/css" />'."\n"; 
				   
				    
			  $GLOBALS['secondary_stylesheet'] = get_option('ka_secondary_scheme');
	          if($GLOBALS['secondary_stylesheet'] != 'default')
	               echo '<link href="'. KARMA_CSS . $GLOBALS['secondary_stylesheet'] .'.css'.'" rel="stylesheet" type="text/css" />'."\n";
				   
				   echo '<link href="'. get_stylesheet_directory_uri() . '/style.css' .'" rel="stylesheet" type="text/css" />'."\n";            
	     }       
			
	}

add_action('wp_head', 'siteoptions_wp_head');



/*-----------------------------------------------------------------------------------*/
/* Custom CSS Output */
/*-----------------------------------------------------------------------------------*/

function karma_settings_css(){
$css_array = array();
$css_link_container = array();
//get all css settings
$custom_css = get_option('ka_custom_css');
$dropdown_css = get_option('ka_dropdown');
$nav_description = get_option('ka_nav_description');
$google_font = get_option('ka_google_font');
$custom_google_font = get_option('ka_custom_google_font');
$blog_image_frame = get_option('ka_blog_image_frame');



//push in css if not empty from setting

    //custom css
	if(!empty($custom_css)){
     array_push($css_array,$custom_css);
	}

	
	//navigation css
	if($dropdown_css!='false'){
		$drop_css_code = '#menu-main-nav li .drop {display:none !important;}#menu-main-nav li.parent:hover {background:transparent url('.get_template_directory_uri().'/images/_global/seperator-main-nav.png) 0 50% no-repeat !important;}#menu-main-nav li {padding: 3px 31px 5px 13px;}#menu-main-nav li.parent, #menu-main-nav li.parent:hover{padding: 3px 31px 5px 13px !important;}*:first-child+html .big-banner #menu-main-nav {margin-bottom:16px;}';
		array_push($css_array,$drop_css_code);	
	}
	
	if($nav_description!= 'false'){
	$nav_css_code = '#menu-main-nav a .navi-description{display:none !important;}#menu-main-nav li.parent:hover{padding-bottom:21px;}#menu-main-nav .drop {top: 41px;}#menu-main-nav {margin-top:12px;}*:first-child+html .big-banner #menu-main-nav {margin-bottom:16px;}#menu-main-nav li {background:none !important;padding-right:20px !important;}';
	  array_push($css_array,$nav_css_code);
	}	
	
		if($dropdown_css != 'false' && $nav_description != 'false'){
	$nav_com_css = '#menu-main-nav li {background:none !important;padding-right:20px !important;}#menu-main-nav li.parent:hover{background: none !important;}#menu-main-nav li.parent, #menu-main-nav li.parent:hover{background:none !important;padding-right:20px !important;}';
	     array_push($css_array,$nav_com_css);		
		}
		
	//google font css
    if( ($google_font != 'nofont' && $custom_google_font == '')){
	        $google_font_link = '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family='.$google_font.'" />'."\n";	
			$google_font_code = 'h1, h2, h3, h4, h5 #main .comment-title, .four_o_four, .callout-wrap span, .search-title,.callout2, .comment-author-about, .logo-text {font-family:\''.$google_font.'\', Arial, sans-serif;}'."\n";
			array_push($css_link_container,$google_font_link);
			array_push($css_array,$google_font_code);
			  }
	
	if($custom_google_font != ''){
	
	        //remove space and add + sign if there is space found in user entered custom font name.
	        //the google font name in css link has a plus sign.
	        $custom_google_font_name = str_replace(" ","+",$custom_google_font); 
	
	        $google_custom_link =  '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family='.$custom_google_font_name.'">'."\n";	
	        
	        $sanitize = array('+','-'); //some font name have plus parameter, such as Special+Elite
            // remove the plus and add space to custom font name, if there is a plus between the font name.
	        $sanitized_google_font_name = str_replace($sanitize,' ',$custom_google_font);
	        //the google font name in css item, does not have plus sign and needs a space.
	        
			$google_custom_font_code = 'h1, h2, h3, h4, h5 #main .comment-title, .four_o_four, .callout-wrap span, .search-title,.callout2, .comment-author-about, .logo-text {font-family:\''.$sanitized_google_font_name.'\', Arial, sans-serif;}'."\n";
			array_push($css_link_container,$google_custom_link);
			array_push($css_array,$google_custom_font_code);			
			 }
			 
			 
			 //blog shadow frame
			 if($blog_image_frame == 'shadow'){
	$nav_com_css = '.post_thumb {background-position: -4px -1470px !important;}';
	     array_push($css_array,$nav_com_css);		
		}
			 
			  
			  
//construct items and links to print in <head>			

//if not empty css_link_container
    if(!empty($css_link_container)){
       foreach($css_link_container as $css_link){
        echo $css_link."\n";
       }
    }		
	
//if not empty $css_array, print it out in <head>	
	if(!empty($css_array)){
	  echo"<style type='text/css'>\n";
	        foreach($css_array as $css_item){
	         echo $css_item."\n";	        
	        }
	  echo"</style>\n";
	}

}
add_action('wp_head','karma_settings_css',90);








/*-----------------------------------------------------------------------------------*/
/* Add Favicon
/*-----------------------------------------------------------------------------------*/

function karma_favicon() {
	$GLOBALS['favicon'] = get_option('ka_favicon');
	          if($GLOBALS['favicon'] != '')
	        echo '<link rel="shortcut icon" href="'.  $GLOBALS['favicon'] .'"/>'."\n";
	    }

add_action('wp_head', 'karma_favicon');








/*-----------------------------------------------------------------------------------
Add drag-to-share to footer (if_enabled)
----------------------------------------------------------------------------------- */

function karma_share(){
	
	$GLOBALS['dragshare'] = get_option('ka_dragshare');
	          if($GLOBALS['dragshare'] == "true" && is_home() || is_single())
			  
			  echo '<script type="text/javascript" charset="utf-8" src="http://bit.ly/javascript-api.js?version=latest&login=scaron&apiKey=R_6d2a7b26f3f521e79060a081e248770a"></script>
			  <script src="'. KARMA_HOME .'/js/jquery.prettySociable.js" type="text/javascript" charset="utf-8"></script>
			  <script type="text/javascript" charset="utf-8">
			// Init prettySociable
			var TTjquery = jQuery.noConflict();
			TTjquery.prettySociable();
			TTjquery.prettySociable.settings.urlshortener.bitly.active = true;
		</script>
			  ';

		
}
add_action('wp_footer','karma_share');











/*-----------------------------------------------------------------------------------*/
/* Add analytics code to footer */
/*-----------------------------------------------------------------------------------*/

function karma_analytics(){
	
	$GLOBALS['google'] = get_option('ka_google_analytics');
	          if($GLOBALS['google'] != '')
		echo stripslashes($GLOBALS['google']) . "\n";
}
add_action('wp_footer','karma_analytics');







/*-----------------------------------------------------------------------------------*/
/* Hide Meta Boxes (if_enabled) */
/*-----------------------------------------------------------------------------------*/
function karma_metaboxes(){
	$GLOBALS['hide_metaboxes'] = get_option('ka_hidemetabox');
	          if($GLOBALS['hide_metaboxes'] == "true"){
				  
				  
/* pages */
remove_meta_box('commentstatusdiv','page','normal'); // Comments
remove_meta_box('commentsdiv','page','normal'); // Comments
remove_meta_box('trackbacksdiv','page','normal'); // Trackbacks
remove_meta_box('postcustom','page','normal'); // Custom Fields
remove_meta_box('authordiv','page','normal'); // Author
//remove_meta_box('slugdiv','page','normal'); // Slug

/* posts */
remove_meta_box('commentsdiv','post','normal'); // Comments
remove_meta_box('postcustom','post','normal'); // Custom Fields
//remove_meta_box('slugdiv','post','normal'); // Slug

		
}
}
add_action('admin_menu','karma_metaboxes',90);
function karma_css_hide_slug_metabox(){
	$GLOBALS['hide_metaboxes'] = get_option('ka_hidemetabox');
	          if($GLOBALS['hide_metaboxes'] == "true"){
	echo"<style>#slugdiv, #slugdiv-hide, label[for='slugdiv-hide']{display:none!important;}</style>";
	}          
}
add_action('admin_head','karma_css_hide_slug_metabox');
?>