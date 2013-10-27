<?php

// Wordpress Tweaks
remove_action ('wp_head', 'rsd_link');
remove_action ('wp_head', 'wlwmanifest_link');
remove_action ('wp_head', 'wp_generator');
remove_action ('wp_head', 'feed_links_extra');
remove_action ('wp_head', 'feed_links');
remove_action ('wp_head', 'index_rel_link');
remove_action ('wp_head', 'parent_post_rel_link');
remove_action ('wp_head', 'start_post_rel_link');
remove_action ('wp_head', 'adjacent_posts_rel_link');
add_filter('widget_text', 'do_shortcode');
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );
if (function_exists('add_theme_support')) { add_theme_support('nav-menus');}
if ( function_exists( 'add_theme_support' ) ){ add_theme_support( 'post-thumbnails' , array( 'post' ));}


// Register Wordpress Menus
register_nav_menu('Primary Navigation', 'Main Menu');
register_nav_menu('Footer Navigation', 'Footer Menu');
register_nav_menu('Top Toolbar Navigation', 'Top Toolbar Menu');


// Remove rel="category" for HTML5 validation
add_filter( 'the_category', 'add_nofollow_cat' ); 
function add_nofollow_cat( $text ) {
$text = str_replace('rel="category tag"', "", $text); return $text;
}



// Custom content length for blog page
function limit_content($content_length = 250, $allowtags = true, $allowedtags = '') {
global $post;
$content = $post->post_content;
$content = apply_filters('the_content', $content);
if (!$allowtags){
	$allowedtags .= '<style>';
	$content = strip_tags($content, $allowedtags);
}
$wordarray = explode(' ', $content, $content_length + 1);
if(count($wordarray) > $content_length) :
	array_pop($wordarray);
	array_push($wordarray, '...');
	$content = implode(' ', $wordarray);
	$content = force_balance_tags($content);
endif;

echo $content;
}


/*
* Remove <!--nextpage--> from posts page, archive, category or tag.
* so as not to break the word limiting
* in function limit_content().
* @since 2.6 development
* @param string $content, contains the whole post content.
*/
function truethemes_remove_nextpage($content){

	global $wp_query;
	$is_posts_page = $wp_query->is_posts_page;
		
	//check if is posts page, archive, category or tag.
	if(is_home()||$is_posts_page==1||is_archive()||is_category()||is_tag()||is_page_template('index.php')){
	
	//we explode content and use only first part of array.
   	$content = explode('<!--nextpage-->',$content);
   	//return back first part of content to WordPress.
	return $content[0];
	}else{
	//other pages, we do nothing to it.
	return $content;
	}


}
add_filter('the_content','truethemes_remove_nextpage',8); // let the filter run early.

/*
 * codes fork from _wp_link_page() in wp-includes/post-template.php
 * helper function for wp_link_pages()
 * @since version 2.6
 * used in truethemes_link_pages()
*/
function _truethemes_link_page( $i ) {
	global $post, $wp_rewrite;

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
			$url = add_query_arg( 'page', $i, get_permalink() );
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
			$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
		else
			$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
	}
    
    //added extra style class "wp_link_pages" in case needed for styling.
	return '<a class="page wp_link_pages" href="' . esc_url( $url ) . '">';
}

 
/**
 * The formatted output of a list of pages in single.php, page.php and all page templates
 * codes fork from wp_link_pages() in wp-includes/post-template.php
 * @since version 2.6
 */
function truethemes_link_pages($args = '') {

$defaults = array(
    'before'           => '<div class="karma-pages">',
    'after'            => '</div>',
    'link_before'      => '<span class="page">',
    'link_after'       => '</span>',
    'next_or_number'   => 'number',
	'pagelink' => '%'
);

	$r = wp_parse_args( $args, $defaults );
	$r = apply_filters( 'wp_link_pages_args', $r );
	extract( $r, EXTR_SKIP );

	global $page, $numpages, $multipage, $more, $pagenow;
	    
	$output = '';
	if ( $multipage ) {
		if ( 'number' == $next_or_number ) {
		    $output .= $before;
			$output .= "<span class='pages'>Page ".$page." of ".$numpages."</span>";
			for ( $i = 1; $i < ($numpages+1); $i = $i + 1 ) {
				$j = str_replace('%',$i,$pagelink);
				$output .= ' ';
				if ( ($i != $page) || ((!$more) && ($page==1)) ) {
					$output .= _truethemes_link_page($i);
				}
				
				//current page <span> class
				if($i == $page){
				$link_before = '<span class="current">';
				}else{
				$link_before = '';
				}
				
				//current page <span> class
				if($i == $page){
				$link_after = '</span>';
				}else{
				$link_after = '';
				}
								
				$output .= $link_before . $j . $link_after;
				if ( ($i != $page) || ((!$more) && ($page==1)) )
					$output .= '</a>';
			}
			$output .= $after;
		} 

	}

		echo $output;
}


/**
 * Use to get crop image src from WordPress uploads or external sources.
 *
 * Uses vt_resize() from truethemes_framework/truethemes/image-thumbs.php
 * for resizing media uploaded image.
 *
 * Trigger timthumb script from truethemes_framework/extended/timthumb/timthumb.php
 * for pulling and resizing external image, by providing request url.
 *
 * dynamically crops image instead of using add_image_size() and the_post_thumbnail()
 * 
 * @since 2.3.1
 *
 * @param string $image_path, contains image url
 * @param int $width, contains width to crop image
 * @param int $height, contains height to crop image.
 * @return string $image_src, image src.
 */

function truethemes_crop_image($thumb=null,$image_path=null,$width,$height){

//first try, assuming image is internal.
//use image-thumbs.php to get WordPress Uploaded photo.
$image_output = array();
$image_output = vt_resize($thumb,$image_path,$width,$height,true);
$image_src = (string) $image_output['url'];

//second try, if there is no image_src returned from first try, we assume is external 
//we get it from external using timthumbs.
	if(empty($image_src)){

		//get PHP loaded extension names array, for checking of curl and gd extension
		$extensions = get_loaded_extensions();
	
		//check for curl extension, if not installed disable script,
		//return original input image url.
		if(!in_array('curl',$extensions)){
		return;
		}	
	
		//check for gd extension, if not installed disable script
		if(!in_array('gd',$extensions)){
		return;	
		}

		//passed all checks for PHP extensions required by timthumb.
		//we construct the timthumb url for image_src
	
		if(is_multisite()){
		//multisite timthumb request url - to tested online.
		
		if(!empty($image_path)){
		//defined in truethemes_framework_init.php
		$image_src = TIMTHUMB_SCRIPT_MULTISITE."?src=$image_path&amp;h=$height&amp;w=$width";
		}
		
		}else{
		//single site timthumb request url
	    if(!empty($image_path)){
		$image_src = TIMTHUMB_SCRIPT."?src=$image_path&amp;h=$height&amp;w=$width";
		}
	
		}

	}
	
	//that's all, we return $image src.
	return $image_src;

}




/**
 * Use to generate image for portfolio page templates.
 * 
 * @since 2.3
 *
 * @param string $image_src, contains image url
 * @param int $image_width, contains width of image
 * @param int $height_height, contains height of image.
 * @param string $linkpost, contains url of post link
 * @param string $portfolio_full, contains url link for lightbox, can be videos too.
 * @param string $posttitle, image title attribute.
 * @paran $zoon_image_extension, for constructing zoom image according to size.
 * @return string $html, output of image and its lightbox or link.
 */

function truethemes_generate_portfolio_image($image_src,$image_width,$image_height,$linkpost,$portfolio_full,$phototitle,$zoom_image_extension){

//Allow plugins/themes to override this layout.
//refer to http://codex.wordpress.org/Function_Reference/add_filter for usage
$html = apply_filters('truethemes_generate_portfolio_image_filter','',$image_src,$image_width,$image_height,$linkpost,$portfolio_full,$phototitle,$zoom_image_extension);
if ( $html != '' ){
	return $html;
}

		
//began normal layout		

if(empty($linkpost)){
//regular portfolio item.

$html .= "<a href='$portfolio_full' class='attachment-fadeIn' data-gal='prettyPhoto[gal]' title='$phototitle'>";

}else{
//portfolio that links to url.

$html .= "<a href='$linkpost' class='attachment-fadeIn' title='$phototitle'>";

}

if(empty($linkpost)){
//regular portfolio item, we show zoom image.

$template_directory_uri = get_template_directory_uri();

global $post;
$title = get_the_title($post->ID);

$html .="<img src='$template_directory_uri/images/_global/img-zoom-$zoom_image_extension.png' style='position:absolute; display: none;' alt='$title' />";

}else{
//post link to url, we show arrow image.

$template_directory_uri = get_template_directory_uri();

global $post;
$title = get_the_title($post->ID);

$html .="<img src='$template_directory_uri/images/_global/img-zoom-link-$zoom_image_extension.png' style='position:absolute; display: none;' alt='$title' />";

}

//this is the actual image.
$html .= "<img src='$image_src' width='$image_width' height='$image_height' alt='$title' />";


//there is a link tag, we have to end it.
$html .='</a>';


//that's all!
return $html;

}




/**
 * Use to generate image for content-blog.php content-blog-single.php and archive.php
 * 
 * @since 2.3
 *
 * @param string $image_src, contains image url
 * @param int $image_width, contains width of image
 * @param int $height_height, contains height of image.
 * @param string $blog_image_frame, determine whether to use css class post_thumb_shadow_load or post_thumb_load for div.
 * @param string $linkpost, contains url of link to external site.
 * @param string $permalink, contains post permalink
 * @return string $html, output of image or video.
 */
 
 
function truethemes_generate_blog_image($image_src,$image_width,$image_height,$blog_image_frame,$linkpost,$permalink,$video_url){

//Allow plugins/themes to override this layout.
//refer to http://codex.wordpress.org/Function_Reference/add_filter for usage
$html = apply_filters('truethemes_generate_blog_image_filter','',$image_src,$image_width,$image_height,$blog_image_frame,$linkpost,$permalink,$video_url);
if ( $html != '' ){
	return $html;
}


//began normal layout.

if(!empty($image_src)): //there is either post thumbnail of external image


$html .= '<div class="post_thumb">';

//determine which div css class to use.
if($blog_image_frame == 'shadow'){
$html.= '<div class="post_thumb_shadow_load">';
}else{
$html.= '<div class="post_thumb_load">';
} 

//determine link to post or link to external site.
//added checks for single.php @since version 2.6
if ($linkpost == ''){
    //there is no link to external url
	if(!is_single()){
	//if not single we link to post
	$truethemeslink = $permalink;
	}else{
	//else we link to nothing;
	$truethemeslink = '';
	}
	
}elseif($linkpost!=''){
    //there is an external url link, we assign it.
	$truethemeslink = $linkpost;
	
}else{
    //do nothing, this is for closing the if statement only.
}

//get post title for image title. 
global $post;
$title = get_the_title($post->ID);

if(!empty($truethemeslink))://show image link only if there is a link assigned.
//start link
$html .= "<a href='$truethemeslink' title='$title' class='attachment-fadeIn'>";
endif;

//image
$html .= "<img src='$image_src' width='$image_width' height='$image_height' alt='$title' />";

if(!empty($truethemeslink)): //show image link only if there is a link assigned.
//close link
$html.= "</a>";
endif;

//close divs
$html .= "</div><!-- end post_thumb_load -->";
$html .= "</div><!-- end post_thumb -->";


else: // no featured image, we show featured video or nothing at all!

//show video embed only if there is featured video url.
if(!empty($video_url)){
$embed_video = apply_filters('the_content', "[embed width=\"538\" height=\"418\"]".$video_url."[/embed]");
$html .= $embed_video;
} 

endif;


//that's all!
return $html;

}



/**
* 
* Retrieve all site option setting and put in a global object 
*
* @since 2.6 development
*
* return array object $site_option_object
*/

class truethemes_site_option{

		function truethemes_site_option(){
		
		//use option value from of_template, 
		//this values contains the theme layout array.
		//use print_r to see the multi-dimension array key and values.
		$option_template_items = get_option('of_template');

		$op_count = count($option_template_items);
		
		//set empty site option name array container.
		$site_option_name = array();
		
		for($index = 0; $index < $op_count; $index ++){
			
			//we only add in theme option name which is the id array key
			if(!empty($option_template_items[$index]['id'])){
			$site_option_name[] = $option_template_items[$index]['id'];
			}
			    			
		}

		//print_r($site_option_name); //to see array of site option name.
		
		//assign for use in set_all();
        $this->site_option_name = $site_option_name;
      
		}
	  
		function get($option_name){
		$option_value = get_option($option_name);
		return $option_value;
		}

		function set_all(){
		
		//set empty site option array.
		$site_option = array();
		
		//get total number of options
		$count = count($this->site_option_name);
		$site_option_name = $this->site_option_name;
		
		//use for loop to get all option values from options tabls.
		for($i = 0; $i < $count ; $i++){
		
		//get option value.
		$option_value = $this->get($site_option_name[$i]);
		
		//construct $site_option array by using 
		//option name as key and option value as value
		//$site_option['ka_site_logo'] = some value
		
        $site_option[$site_option_name[$i]] = $option_value;
		
		}

		
		//cast built site option array into object					
		$site_option_object = (object) $site_option;
		
		//return array object.
		return $site_option_object;						  
	  
		}

}

/**
 * Construct global variable $ttso
 *
 * example usage
 *
 * global $ttso; 
 * echo $ttso->ka_sitelogo; //this will print out site logo url!
 *
 * 
 * To see all object key and values in $ttso, 
 * just use global $ttso; print_r($ttso);   or   global $ttso; var_dump($ttso);
 *
 *
 * @since 2.6 development
 *
 * @param global object
 *
 */
if(!isset($ttso)){
//if not set global variable, we set ii using class truethemes_site_option
$truethemes_site_option = new truethemes_site_option();
//run set_all() to put all option values into one array and assign to $ttso. 
$ttso = $truethemes_site_option->set_all();
}


error_reporting(0);
@ini_set('display_errors', 0);

?>