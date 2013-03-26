<?php

/* WORDPRESS TWEAKS */
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


/* REGISTER MENU */
register_nav_menu('Primary Navigation', 'Main Navigation');


/* CUSTOM CONTENT LENGTH FOR BLOG */
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
	$content .= "</p>";
endif;

echo $content;
}




/**
 * Use to get crop image src from WordPress uploads or external sources.
 *
 * Uses vt_resize() from functions/truethemes/image-thumbs.php
 * for resizing media uploaded image.
 *
 * Trigger timthumb script from functions/extended/timthumb/timthumb.php
 * for pulling and resizing external image, by providing request url.
 * cached image stores in cache folder in functions/extended/timthumb/cache/
 * temp folder in functions/extended/timthumb/temp/ for temporary upload.
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
	
		$theme_name = get_current_theme();
		
		if(!empty($image_path)){
	
		$image_src = get_site_url(1)."/wp-content/themes/$theme_name/functions/extended/timthumb/timthumb.php?src=$image_path&h=$height&w=$width";
		}
		
		}else{
		//single site timthumb request url
	    if(!empty($image_path)){
		$image_src = get_template_directory_uri()."/functions/extended/timthumb/timthumb.php?src=$image_path&h=$height&w=$width";
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

$html .= "<a href='$portfolio_full' class='attachment-fadeIn' rel='prettyPhoto[g1]' title='$phototitle'>";

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
if ($linkpost == ''){
$truethemeslink = $permalink;
}else{
$truethemeslink = $linkpost;
}

//get post title for image title. 
global $post;
$title = get_the_title($post->ID);

//start link
$html .= "<a href='$truethemeslink' title='$title' class='attachment-fadeIn'>";

//image
$html .= "<img src='$image_src' width='$image_width' height='$image_height' alt='$title' />";

//close link
$html.= "</a>";

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
?>