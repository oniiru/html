<?php 
/* =================================== */
// UNFORMAT TEXT 
/* =================================== */
function my_formatter($content) {
	$new_content = '';
	$pattern_full = '{(\[raw\].*?\[/raw\])}is';
	$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
	$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	foreach ($pieces as $piece) {
		if (preg_match($pattern_contents, $piece, $matches)) {
			$new_content .= $matches[1];
		} else {
			$new_content .= wptexturize(wpautop($piece));
		}
	}

	return $new_content;
}

remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');
add_filter('the_content', 'my_formatter', 99);
add_filter('widget_text', 'my_formatter', 99);








/* =================================== */
// BASIC 
/* =================================== */
function karma_h1( $atts, $content = null ) {
   return '<h1>' . do_shortcode($content) . '</h1>';
} add_shortcode('h1', 'karma_h1');

function karma_h2( $atts, $content = null ) {
   return '<h2>' . do_shortcode($content) . '</h2>';
} add_shortcode('h2', 'karma_h2');

function karma_h3( $atts, $content = null ) {
   return '<h3>' . do_shortcode($content) . '</h3>';
} add_shortcode('h3', 'karma_h3');

function karma_h4( $atts, $content = null ) {
   return '<h4>' . do_shortcode($content) . '</h4>';
} add_shortcode('h4', 'karma_h4');

function karma_h5( $atts, $content = null ) {
   return '<h5>' . do_shortcode($content) . '</h5>';
} add_shortcode('h5', 'karma_h5');

function karma_h6( $atts, $content = null ) {
   return '<h6>' . do_shortcode($content) . '</h6>';
} add_shortcode('h6', 'karma_h6');



/**
 * Use to construct HTML output of truethemes_image_frame().
 * prevents duplication of codes and allows easy modification.
 *
 * uses truethemes_crop_image() to get cropped image src.
 * see functions/global/basic.php
 *
 * @since 2.3.1
 *
 * @param string $style style class.
 * @param string $frame_class style class 
 * @param string $image_path contains images url input from shortcode
 * @param int $width defines width of final cropped image
 * @param int $height defines height of final cropped image
 * @param string $framesize style class
 * @param string $link_to_page optional, contains link url from shortcode.
 * @param string $target optional, contains link target , example _blank, _self
 * @param string $description optional, contains alt of image html tag.
 * @return HTML $output value.
 */

function truethemes_image_frame_constructor($style,$frame_class,$image_path,$width,$height,$framesize,$link_to_page,$target,$description){

//Allow plugins/themes to override this layout.
//refer to http://codex.wordpress.org/Function_Reference/add_filter for usage
$output = apply_filters('truethemes_image_frame_filter','',$style,$frame_class,$image_path,$width,$height,$framesize,$link_to_page,$target,$description);
if ( $output != '' ){
		return $output;
}


//began normal layout

//crop image function from functions/global/basic.php
$image_src = truethemes_crop_image($thumb=null,$image_path,$width,$height); //see above

//output the shortcode HTML
	
$output .= '[raw]<div class="'.$style.'_img_frame '.$framesize.'">';
$output .= '<div class="'.$style.$frame_class.'">';
$output .= '<div class="attachment-fadeIn">';

//if there is a link url we display it.
if(!empty($link_to_page)){

	$output.='<a href="'.$link_to_page.'" target="'.$target.'">';

}

$output .= "<img src='".$image_src."' alt='".$description."' />";

//if there is a link url we display it.
if(!empty($link_to_page)){
	$output.='</a>';
}

$output.='</div></div></div><!-- end img_frame -->[/raw]';

return $output;

}






/* =================================== */
// IMAGE FRAMES 
/* =================================== */

function truethemes_image_frame($atts, $content = null) {
  extract(shortcode_atts(array(  
  'style' => '',
  'image_path' => '',
  'link_to_page' => '',
  'target' => '',
  'description' => '',
  'size' => '',
  ), $atts));
  
 
 $framesize = $style.'_'.$size;
 $output = null;
 
 
/* --- FULL WIDTH -  BANNER --- */
if ($size == 'banner_full'){

//function from image frames processor, see above.
//used to generate the html content of this shortcode.
//applies to all sizes, including links or not.

$output .= truethemes_image_frame_constructor($style,"_preload_full",$image_path,922,201,$framesize,$link_to_page,$target,$description);
}


/* --- FULL WIDTH -  ONE_HALF (2 Column) --- */
if ($size == 'two_col_large'){
$output .= truethemes_image_frame_constructor($style,"_preload_two_col_large",$image_path,437,234,$framesize,$link_to_page,$target,$description);
}


/* --- FULL WIDTH -  ONE_THIRD (3 Column) --- */
if ($size == 'three_col_large'){
$output .= truethemes_image_frame_constructor($style,"_preload_three_col_large",$image_path,275,145,$framesize,$link_to_page,$target,$description);
}


/* --- FULL WIDTH -  ONE_FOURTH (4 Column) --- */
if ($size == 'four_col_large'){
$output .= truethemes_image_frame_constructor($style,"_preload_four_col_large",$image_path,190,111,$framesize,$link_to_page,$target,$description);
}



/* --- SIDE NAV -  BANNER --- */
if ($size == 'banner_regular'){
$output .= truethemes_image_frame_constructor($style,"_preload_regular",$image_path,703,201,$framesize,$link_to_page,$target,$description);
}
 

/* --- SIDE NAV -  ONE_HALF (2 Column) --- */
if ($size == 'two_col_small'){
$output .= truethemes_image_frame_constructor($style,"_preload_two_col_small",$image_path,324,180,$framesize,$link_to_page,$target,$description);
}



/* --- SIDE NAV -  ONE_THIRD (3 Column) --- */
if ($size == 'three_col_small'){
$output .= truethemes_image_frame_constructor($style,"_preload_three_col_small",$image_path,202,113,$framesize,$link_to_page,$target,$description);
}



/* --- SIDE NAV -  ONE_FOURTH (4 Column) --- */
if ($size == 'four_col_small'){
$output .= truethemes_image_frame_constructor($style,"_preload_four_col_small",$image_path,135,76,$framesize,$link_to_page,$target,$description);
}


	  
/* --- SIDE NAV + SIDEBAR -  BANNER --- */
if ($size == 'banner_small'){
$output .= truethemes_image_frame_constructor($style,"_preload_small",$image_path,493,201,$framesize,$link_to_page,$target,$description);
}



/* --- PORTRAIT STYLE - FULL --- */
if ($size == 'portrait_full'){
$output .= truethemes_image_frame_constructor($style,"_preload_portrait_full",$image_path,612,792,$framesize,$link_to_page,$target,$description);
}


/* --- PORTRAIT STYLE - THUMBNAIL --- */
if ($size == 'portrait_thumb'){
$output .= truethemes_image_frame_constructor($style,"_preload_portrait_thumb",$image_path,275,355,$framesize,$link_to_page,$target,$description);
}


  return $output;
}
add_shortcode('frame', 'truethemes_image_frame');





/**
 * Use to construct HTML output of truethemes_lightbox_frame().
 * prevents duplication of codes and allows easy modification.
 *
 * uses truethemes_crop_image() to get cropped image src.
 * see functions/golbal/basic.php
 *
 * @since 2.3.1
 *
 * @param string $style style class.
 * @param string $frame_class style class 
 * @param string $image_path contains images url input from shortcode
 * @param int $width defines width of final cropped image
 * @param int $height defines height of final cropped image
 * @param string $framesize style class
 * @param string $popup contains popup image url
 * @param string $link_to_page optional, contains link url from shortcode.
 * @param string $image_zoom_number, forms part of image src to indicators (magnifying glass or arrow)
 * @param string $target optional, contains link target , example _blank, _self
 * @param string $description optional, contains alt of image html tag.
 * @return HTML $output value.
 */

function truethemes_lightbox_frame_constructor($style,$frame_class,$image_path,$width,$height,$framesize,$popup,$link_to_page,$image_zoom_number,$target,$description){

//Allow plugins/themes to override this layout.
//refer to http://codex.wordpress.org/Function_Reference/add_filter for usage
$output = apply_filters('truethemes_lightbox_frame_filter','',$style,$frame_class,$image_path,$width,$height,$framesize,$popup,$link_to_page,$image_zoom_number,$target,$description);
if ( $output != '' ){
	return $output;
}


//began normal layout


//crop image function from functions/global/basic.php
$image_src = truethemes_crop_image($thumb=null,$image_path,$width,$height);// see above.

//output the shortcode HTML
	
$output .= '[raw]<div class="'.$style.'_img_frame '.$framesize.'">';
$output .=' <div class="'.$style.$frame_class.' preload">';

//if there is a link url we display it.
if(!empty($link_to_page)){

	$output.='<a href="'.$link_to_page.'" class="attachment-fadeIn" title="'.$description.'" target="'.$target.'">';

}else{
//we display popup
$output.='<a href="'.$popup.'" class="attachment-fadeIn" rel="prettyPhoto[g1]" title="'.$description.'">';
}
if(!empty($link_to_page)){
$output.='<img src="'.KARMA_HOME.'/images/_global/img-zoom-link-'.$image_zoom_number.'.png" style="position:absolute; display: none;" alt="'.$description.'" />';
}{
$output.='<img src="'.KARMA_HOME.'/images/_global/img-zoom-'.$image_zoom_number.'.png" style="position:absolute; display: none;" alt="'.$description.'" />';
}

$output .= "<img src='".$image_src."' alt='".$description."' />";
$output .='</a></div></div>[/raw]';


return $output;

}



/* =================================== */
// LIGHTBOX IMAGE FRAMES 
/* =================================== */
function truethemes_lightbox_frame($atts, $content = null) {
  extract(shortcode_atts(array(  
  'style' => '',
  'image_path' => '',
  'popup' => '',
  'link_to_page' => '',
  'description' => '',
  'target' => '',
  'size' => '',
  ), $atts));
  
 
 $framesize = $style.'_'.$size;
 $output = null;


/* --- FULL WIDTH -  ONE_HALF (2 Column) --- */
if ($size == 'two_col_large'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_two_col_large',$image_path,437,234,$framesize,$popup,$link_to_page,'2',$target,$description);
}



/* --- FULL WIDTH -  ONE_THIRD (3 Column) --- */
if ($size == 'three_col_large'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_three_col_large',$image_path,275,145,$framesize,$popup,$link_to_page,'3',$target,$description);
}



/* --- FULL WIDTH -  ONE_FOURTH (4 Column) */
if ($size == 'four_col_large'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_four_col_large',$image_path,190,111,$framesize,$popup,$link_to_page,'4',$target,$description);
}




/* --- SIDE NAV -  ONE_HALF (2 Column) --- */
if ($size == 'two_col_small'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_two_col_small',$image_path,324,180,$framesize,$popup,$link_to_page,'2-small',$target,$description);
}




/* --- SIDE NAV -  ONE_THIRD (3 Column) --- */
if ($size == 'three_col_small'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_three_col_small',$image_path,202,113,$framesize,$popup,$link_to_page,'3-small',$target,$description);
}




/* --- SIDE NAV -  ONE_FOURTH (4 Column) --- */
if ($size == 'four_col_small'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_four_col_small',$image_path,135,76,$framesize,$popup,$link_to_page,'4-small',$target,$description);
}




/* --- PORTRAIT STYLE - THUMBNAIL --- */
if ($size == 'portrait_thumb'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_portrait_thumb',$image_path,275,355,$framesize,$popup,$link_to_page,'portrait-small',$target,$description);
}




/* --- PORTRAIT STYLE - FULL --- */
if ($size == 'portrait_full'){
$output .= truethemes_lightbox_frame_constructor($style,'_preload_portrait_full',$image_path,612,792,$framesize,$popup,$link_to_page,'portrait-full',$target,$description);
}




  return $output;
}
add_shortcode('lightbox', 'truethemes_lightbox_frame');













/* =================================== */
// COLUMN LAYOUTS 
/* =================================== */

/* 6 */
function karma_one_sixth( $atts, $content = null ) {
   return '[raw]<div class="one_sixth">[/raw]' . do_shortcode($content) . '[raw]</div>[/raw]';
}
add_shortcode('one_sixth', 'karma_one_sixth');


function karma_one_sixth_last( $atts, $content = null ) {
   return '[raw]<div class="one_sixth_last">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('one_sixth_last', 'karma_one_sixth_last');





/* 5 */
function karma_one_fifth( $atts, $content = null ) {
   return '[raw]<div class="one_fifth">[/raw]' . do_shortcode($content) . '[raw]</div>[/raw]';
}
add_shortcode('one_fifth', 'karma_one_fifth');


function karma_one_fifth_last( $atts, $content = null ) {
   return '[raw]<div class="one_fifth_last">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('one_fifth_last', 'karma_one_fifth_last');




/* 4 */
function karma_one_fourth( $atts, $content = null ) {
   return '[raw]<div class="one_fourth">[/raw]' . do_shortcode($content) . '[raw]</div>[/raw]';
}
add_shortcode('one_fourth', 'karma_one_fourth');


function karma_one_fourth_last( $atts, $content = null ) {
   return '[raw]<div class="one_fourth_last">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('one_fourth_last', 'karma_one_fourth_last');




/* 3 */
function karma_one_third( $atts, $content = null ) {
   return '[raw]<div class="one_third">[/raw]' . do_shortcode($content) . '[raw]</div>[/raw]';
}
add_shortcode('one_third', 'karma_one_third');


function karma_one_third_last( $atts, $content = null ) {
   return '[raw]<div class="one_third_last">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('one_third_last', 'karma_one_third_last');




/* 2 */
function karma_one_half( $atts, $content = null ) {
   return '[raw]<div class="one_half">[/raw]' . do_shortcode($content) . '[raw]</div>[/raw]';
}
add_shortcode('one_half', 'karma_one_half');


function karma_one_half_last( $atts, $content = null ) {
   return '[raw]<div class="one_half_last">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('one_half_last', 'karma_one_half_last');




/* 2/3 */
function karma_two_thirds( $atts, $content = null ) {
   return '[raw]<div class="two_thirds">[/raw]' . do_shortcode($content) . '[raw]</div>[/raw]';
}
add_shortcode('two_thirds', 'karma_two_thirds');


function karma_two_thirds_last( $atts, $content = null ) {
   return '[raw]<div class="two_thirds_last">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('two_thirds_last', 'karma_two_thirds_last');




/* 3/4 */
function karma_three_fourth( $atts, $content = null ) {
   return '[raw]<div class="three_fourth">[/raw]' . do_shortcode($content) . '[raw]</div>[/raw]';
}
add_shortcode('three_fourth', 'karma_three_fourth');


function karma_three_fourth_last( $atts, $content = null ) {
   return '[raw]<div class="three_fourth_last">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('three_fourth_last', 'karma_three_fourth_last');


function karma_flash_wrap( $atts, $content = null ) {
   return '[raw]<div class="flash_wrap">[/raw]' . do_shortcode($content) . '[raw]</div><br class="clear" />[/raw]';
}
add_shortcode('flash_wrap', 'karma_flash_wrap');
















/* =================================== */
// DIVIDERS 
/* =================================== */
function karma_hr_shadow() {
    return '[raw]<div class="hr_shadow">&nbsp;</div>[/raw]';
}
add_shortcode('hr_shadow', 'karma_hr_shadow');



function karma_hr() {
    return '[raw]<div class="hr">&nbsp;</div>[/raw]';
}
add_shortcode('hr', 'karma_hr');



function karma_top_link( $atts, $content = null ) {
   return '[raw]<div class="hr_top_link">&nbsp;</div><a href="#" class="link-top">' . do_shortcode($content) . '</a><br class="clear" />[/raw]';
}
add_shortcode('top_link', 'karma_top_link');










/* =================================== */
// LISTS 
/* =================================== */
function karma_list1( $atts, $content = null ) {
   return '<ul class="list">' . do_shortcode($content) . '</ul>';
}
add_shortcode('arrow_list', 'karma_list1');



function karma_list2( $atts, $content = null ) {
   return '<ul class="list list2">' . do_shortcode($content) . '</ul>';
}
add_shortcode('star_list', 'karma_list2');



function karma_list3( $atts, $content = null ) {
   return '<ul class="list list3">' . do_shortcode($content) . '</ul>';
}
add_shortcode('circle_list', 'karma_list3');



function karma_list4( $atts, $content = null ) {
   return '<ul class="list list4">' . do_shortcode($content) . '</ul>';
}
add_shortcode('check_list', 'karma_list4');


function truethemes_list_item( $atts, $content = null ) {
   return '<li>' . do_shortcode($content) . '</li>';
}
add_shortcode('list_item', 'truethemes_list_item');












/* =================================== */
// INTERACTIVE SHORTCODES 
/* =================================== */


/* --- ACCORDIONS --- */
function karma_accordion( $atts, $content = null ) {
	extract(shortcode_atts(array(), $atts));
	$output = '';
	$output .= '[raw]<ul class="accordion">[/raw]';
	$output .= do_shortcode($content) ;
	$output .= '[raw]</ul>[/raw]';
	return $output;
	
}
add_shortcode('accordion', 'karma_accordion');

function karma_slide( $atts, $content = null ) {
	extract(shortcode_atts(array(), $atts));
	$slide = $atts['name'];
	$output = '';
	$output .= '[raw]<li><a href="#" class="opener"><strong>' .$slide. '</strong></a>[/raw]';
	$output .= '[raw]<div class="slide-holder"><div class="slide">[/raw]';
	
	$output .= '' . do_shortcode($content) .'';
	
	$output .= '[raw]</div></div></li>[/raw]';
	return $output;
}
add_shortcode('slide', 'karma_slide');




/* --- TABS --- */
$i = 0;
function karma_tab_set( $atts, $content = null ) {
	global $i;
	extract(shortcode_atts(array(), $atts));
	$output = '';
	$output .= '[raw]<div class="tabs-area">[/raw]';
	$output .= '[raw]<ul class="tabset">[/raw]';
	foreach ($atts as $tab) {
		$tabID = "tab-" . $i++;
		$output .= '[raw]<li><a href="#' . $tabID . '" class="tab"><span>' .$tab. '</span></a></li>[/raw]';
	}
	$output .= '[raw]</ul>[/raw]';
	$output .= do_shortcode($content) .'[raw]</div>[/raw]';
	return $output;
	
}
add_shortcode('tabset', 'karma_tab_set');

$j = 0;
function karma_tabs( $atts, $content = null ) {
	global $j;
	extract(shortcode_atts(array(), $atts));
	$output = '';
	$tabID = "tab-" . $j++;
	$output .= '[raw]<div id="' . $tabID . '" class="tab-box">[/raw]' . do_shortcode($content) .'[raw]</div>[/raw]';	
	return $output;
}
add_shortcode('tab', 'karma_tabs');




/* ----- TESTIMONIALS ----- */
function karma_testimonials( $atts, $content = null ) {
   return '[raw]<div class="testimonials">' . do_shortcode($content) . '</div><!-- END testimonials -->[/raw]';
}
add_shortcode('testimonial_wrap', 'karma_testimonials');


function karma_testimonial_content( $atts, $content = null ) {
   return '<blockquote><p>' . do_shortcode($content) . '</p></blockquote>';
}
add_shortcode('testimonial', 'karma_testimonial_content');

function karma_testimonial_client( $atts, $content = null ) {
   return '<cite>&ndash;' . do_shortcode($content) . '</cite>';
}
add_shortcode('client_name', 'karma_testimonial_client');










/* =================================== */
// BUTTONS
/* =================================== */
function karma_button($atts, $content = null) {
  extract(shortcode_atts(array(
  'size' => '',
  'style' => '',
  'url' => 'http://www.',
  'target' => '',
  ), $atts));
  
  $size = ($size == 'small') ? 'small_' : $size;
  $size = ($size == 'medium') ? 'medium_' : $size;
  $size = ($size == 'large') ? 'large_' : $size;
  $target = ($target == '_blank' || $target == '_self' || $target == '_parent'|| $target == '_top') ? $target : '';
  $target = ($target == '_blank') ? '_blank' : $target;
  $target = ($target == '_self') ? '_self' : $target;
  $target = ($target == '_parent') ? '_parent' : $target;
  $target = ($target == '_top') ? '_top' : $target;
  
  $output = '<a href="'.$url.'" class="ka_button '.$size.'button '.$size.$style.'" target="'.$target.'"><span>' .do_shortcode($content). '</span></a>';
  return $output;
}
add_shortcode('button', 'karma_button');









/* =================================== */
// CALLOUT TEXT 
/* =================================== */
function karma_callout1( $atts, $content = null ) {
   return '[raw]<div class="callout-wrap"><span>' . do_shortcode($content) . '</span></div><!-- end callout-wrap --><br class="clear" />
[/raw]';
}
add_shortcode('callout1', 'karma_callout1');


function karma_callout2( $atts, $content = null ) {
   return '[raw]<p class="callout2"><span>' . do_shortcode($content) . '</span></p><br class="clear" />[/raw]';
}
add_shortcode('callout2', 'karma_callout2');






/* =================================== */
// NOTIFY BOXES 
/* =================================== */
function truethemes_notify( $atts, $content = null ) {
  extract(shortcode_atts(array(
  'font_size' => '13px',
  'style' => '',
  ), $atts));
   return '[raw]<p class="message_'.$style.'" style="font-size:'.$font_size.';">' . do_shortcode($content) . '</p><br class="clear" />[/raw]';
}
add_shortcode('notify_box', 'truethemes_notify');






/* =================================== */
// CALLOUT BOXES
/* =================================== */
function truethemes_callout( $atts, $content = null ) {
  extract(shortcode_atts(array(
  'font_size' => '13px',
  'style' => '',
  ), $atts)); 
  return '[raw]<div class="message_karma_'.$style.' colored_box"><p style="font-size:'.$font_size.';">' . do_shortcode($content) . '</p></div><br class="clear" />[/raw]';
}
add_shortcode('callout', 'truethemes_callout');







/* =================================== */
// VIDEO LAYOUT 
/* =================================== */
function karma_video_left( $atts, $content = null ) {
   return '[raw]<div class="video-wrap video_left">[/raw]' . do_shortcode($content) . '[raw]</div><!-- end video-wrap -->[/raw]';
}
add_shortcode('video_left', 'karma_video_left');

function karma_video_right( $atts, $content = null ) {
   return '[raw]<div class="video-wrap video_right">[/raw]' . do_shortcode($content) . '[raw]</div><!-- end video-wrap -->[/raw]';
}
add_shortcode('video_right', 'karma_video_right');

function karma_video_frame( $atts, $content = null ) {
   return '[raw]<div class="video-main">
	<div class="video-frame">' . do_shortcode($content) . '</div><!-- end video-frame -->
</div><!-- end video-main -->[/raw]';
}
add_shortcode('video_frame', 'karma_video_frame');

function karma_video_text( $atts, $content = null ) {
   return '[raw]<div class="video-sub">[/raw]' . do_shortcode($content) . '[raw]</div><!-- end video-sub --><br class="clear" />[/raw]';
}
add_shortcode('video_text', 'karma_video_text');






/* =================================== */
// TRUETHEMES BLOG POSTS
/* =================================== */
function truethemes_blog_posts($atts, $content=null) {
extract(shortcode_atts(array(
'title'   => '',
'count'   => '3',
'link_text'   => 'Read more',
'character_count'   => '115',
'post_category'   => '',
'layout'   => '',
'linkpost'=>'',
'image_path'=>'',
'style'=>'',
), $atts));

$title = $title;
$count = $count;
$truethemes_count = 0; $truethemes_col = 0;



global $post;
$exclude = B_getExcludedCats();

if ($post_category != ''){
$myposts = get_posts('numberposts='.$count.'&offset=0&category_name='.$post_category.'');
}else{
$myposts = get_posts('numberposts='.$count.'&offset=0&category='.$exclude);
}

$output = '[raw]<div class="blog-posts-shortcode-outer-wrap">[/raw]';
if ($title != '') {$output .= '<h3>'.$title.'</h3>';};





/* default layout */
if ($layout == "default"){
foreach($myposts as $post){
	setup_postdata($post);
		$permalink = get_permalink($post->ID);
		$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
		//get feature video url 
        $video_url = get_post_meta($post->ID,'truethemes_video_url',true);
		if ($linkpost == ''){$truethemeslink = $permalink;}else{$truethemeslink = $linkpost;}
		$output .= '[raw]<div class="blog-posts-shortcode-inner-wrap">[/raw]';
		$output .= '[raw]<div class="blog-posts-shortcode">[/raw]';
		
		
		$post_thumb = null; //declare empty variable to prevent error.

		//post meta - Feature Image (External Source)
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);		
		//post meta - Feature Image
		$thumb = get_post_thumbnail_id();

		//half width image details
		$image_width = 65;
		$image_height = 65;

		//assign half image src, uses function from functions/global/basic.php
		$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		//post thumbnail
		if (!empty($image_src)):
			$output .= '[raw]<div class="blog-posts-shortcode-thumb">[/raw]';
			$post_thumb .= "<a href='".$truethemeslink."'><img src='".$image_src."' alt='".get_the_title()."' /></a>";
			$output .= $post_thumb;
	 	    $output .= '[raw]</div>[/raw]';
					
		/* video embed
		elseif(!empty($video_url)):
		global $wp_embed;
$embed_video = $wp_embed->run_shortcode("[embed width='65' height='65']".$video_url."[/embed]");
		$post_thumb.= '<div class="video_frame">'.$embed_video.'</div>'; */
		
		//video embed
		elseif(!empty($video_url)):
		$output .= '[raw]<div class="blog-posts-shortcode-thumb">[/raw]';
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-video-post-small.jpg" width="65" height="65" alt="'.get_the_title().'"/></a>';	
		$output .= $post_thumb;
		$output .= '[raw]</div>[/raw]';
		
		
		//default blog post small image
		else:
		$output .= '[raw]<div class="blog-posts-shortcode-thumb">[/raw]';
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-blog-post-small.jpg" width="65" height="65" alt="'.get_the_title().'"/></a>';	
		$output .= $post_thumb;
		$output .= '[raw]</div>[/raw]';
		endif;
		
		
		$output .= '[raw]<div class="blog-posts-shortcode-content">[/raw]';
		$output .= '<h4><a href="'.$truethemeslink.'">'.get_the_title().'</a></h4>';
		$output .= '<p>'.substr(strip_tags($post->post_content), 0, $character_count).'...<br /><a href="'.$truethemeslink.'">'.$link_text.'</a></p>';
		$output .= '[raw]</div></div></div>[/raw]';
	
} // end foreach
} // end default layout







/* 2 column - full width */
if ($layout == "two_col_large"){
	
  $truethemes_count = 0;
  $truethemes_col = 0;

foreach($myposts as $post){
  $truethemes_count++; $truethemes_col ++; $mod = ($truethemes_count % 2 == 0) ? 0 : 2 - $truethemes_count % 2;
  if($truethemes_col == 2){$last = '_last';$truethemes_col = 0;}else{$last = '';}

setup_postdata($post);
$permalink = get_permalink($post->ID);
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$video_url = get_post_meta($post->ID,'truethemes_video_url',true);

if ($linkpost == ''){$truethemeslink = $permalink;}else{$truethemeslink = $linkpost;}
$output .= '<div class="one_half'.$last.'">
<div class="'.$style.'_img_frame '.$style.'_two_col_large">
<div class="'.$style.'_preload_two_col_large">
<div class="attachment-fadeIn">';

		$post_thumb = null; // declare empty variable to prevent error.

		//post meta - Feature Image (External Source)
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);		
		//post meta - Feature Image
		$thumb = get_post_thumbnail_id();

		//half width image details
		$image_width = 437;
		$image_height = 234;

		//assign half image src, uses function from functions/global/basic.php
		$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		//post thumbnail
		if (!empty($image_src)):
			$post_thumb .= "<a href='".$truethemeslink."'><img src='".$image_src."' alt='".get_the_title()."' /></a>";
			
			
		/* video embed
		elseif(!empty($video_url)):
		global $wp_embed;
$embed_video = $wp_embed->run_shortcode("[embed width='437' height='234']".$video_url."[/embed]");
		$post_thumb.= '<div class="video_frame">'.$embed_video.'</div>'; */
		
		//video embed
		elseif(!empty($video_url)):	
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-video-post.jpg" width="437" height="234" alt="'.get_the_title().'"/></a>';
			
			
		//default image	
		else:
			$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-blog-post.jpg" width="437" height="234" alt="'.get_the_title().'"/></a>';
			
		endif;	
		$output .= $post_thumb;
		$output .= '[raw]</div></div></div>[/raw]';
		
$output .= '<h4><a href="'.$truethemeslink.'">'.get_the_title().'</a></h4>';
$output .= '<p>'.substr(strip_tags($post->post_content), 0, $character_count).'...<br /><a href="'.$truethemeslink.'" class="ka_button small_button"><span>'.$link_text.'</span></a></p>';
$output .= '</div>';

} // end foreach
} // end 2 column






/* 3 column - full width */
if ($layout == "three_col_large"){
	
  $truethemes_count = 0;
  $truethemes_col = 0;

foreach($myposts as $post){
  $truethemes_count++; $truethemes_col ++; $mod = ($truethemes_count % 3 == 0) ? 0 : 3 - $truethemes_count % 3;
  if($truethemes_col == 3){$last = '_last';$truethemes_col = 0;}else{$last = '';}

setup_postdata($post);
$permalink = get_permalink($post->ID);
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$video_url = get_post_meta($post->ID,'truethemes_video_url',true);

if ($linkpost == ''){$truethemeslink = $permalink;}else{$truethemeslink = $linkpost;}
$output .= '<div class="one_third'.$last.'">
<div class="'.$style.'_img_frame '.$style.'_three_col_large">
<div class="'.$style.'_preload_three_col_large">
<div class="attachment-fadeIn">';

		$post_thumb = null;

		//post meta - Feature Image (External Source)
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);		
		//post meta - Feature Image
		$thumb = get_post_thumbnail_id();

		//half width image details
		$image_width = 275;
		$image_height = 145;

		//assign half image src, uses function from functions/global/basic.php
		$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		//post thumbnail
		if (!empty($image_src)):
			$post_thumb = "<a href='".$truethemeslink."'><img src='".$image_src."' alt='".get_the_title()."' /></a>";
		
		/* video embed
		elseif(!empty($video_url)):
		global $wp_embed;
$embed_video = $wp_embed->run_shortcode("[embed width='275' height='145']".$video_url."[/embed]");
		$post_thumb.= '<div class="video_frame">'.$embed_video.'</div>'; */
		
		//video embed
		elseif(!empty($video_url)):	
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-video-post.jpg" width="275" height="145" alt="'.get_the_title().'"/></a>';	
		
		
		
		//default image
		else:
			$post_thumb = '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-blog-post.jpg" width="275" height="145" alt="'.get_the_title().'"/></a>';
			
		endif;
		
		$output .= $post_thumb;
		$output .= '[raw]</div></div></div>[/raw]';
		
$output .= '<h4><a href="'.$truethemeslink.'">'.get_the_title().'</a></h4>';
$output .= '<p>'.substr(strip_tags($post->post_content), 0, $character_count).'...<br /><a href="'.$truethemeslink.'" class="ka_button small_button"><span>'.$link_text.'</span></a></p>';
$output .= '</div>';
} // end foreach
} // end 3 column





/* 4 column - full width */
if ($layout == "four_col_large"){
	
  $truethemes_count = 0;
  $truethemes_col = 0;

foreach($myposts as $post){
  $truethemes_count++; $truethemes_col ++; $mod = ($truethemes_count % 4 == 0) ? 0 : 4 - $truethemes_count % 4;
  if($truethemes_col == 4){$last = '_last';$truethemes_col = 0;}else{$last = '';}

setup_postdata($post);
$permalink = get_permalink($post->ID);
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$video_url = get_post_meta($post->ID,'truethemes_video_url',true);

if ($linkpost == ''){$truethemeslink = $permalink;}else{$truethemeslink = $linkpost;}
$output .= '<div class="one_fourth'.$last.'">
<div class="'.$style.'_img_frame '.$style.'_four_col_large">
<div class="'.$style.'_preload_four_col_large">
<div class="attachment-fadeIn">';

		$post_thumb = null;
		
		//post meta - Feature Image (External Source)
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);		
		//post meta - Feature Image
		$thumb = get_post_thumbnail_id();

		//half width image details
		$image_width = 190;
		$image_height = 111;

		//assign half image src, uses function from functions/global/basic.php
		$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		//post thumbnail
		if (!empty($image_src)):
			$post_thumb = "<a href='".$truethemeslink."'><img src='".$image_src."' alt='".get_the_title()."' /></a>";
			
		/* video embed
		elseif(!empty($video_url)):
		global $wp_embed;
$embed_video = $wp_embed->run_shortcode("[embed width='190' height='111']".$video_url."[/embed]");
		$post_thumb.= '<div class="video_frame">'.$embed_video.'</div>'; */
		
		
		//video embed
		elseif(!empty($video_url)):	
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-video-post.jpg" width="190" height="111" alt="'.get_the_title().'"/></a>';	
		
		
		//default image
		else:
			$post_thumb = '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-blog-post.jpg" width="190" height="111" alt="'.get_the_title().'"/></a>';
			
		endif;
			
		$output .= $post_thumb;
		$output .= '[raw]</div></div></div>[/raw]';
		
$output .= '<h4><a href="'.$truethemeslink.'">'.get_the_title().'</a></h4>';
$output .= '<p>'.substr(strip_tags($post->post_content), 0, $character_count).'...<br /><a href="'.$truethemeslink.'" class="ka_button small_button"><span>'.$link_text.'</span></a></p>';
$output .= '</div>';

} // end foreach
} // end 4 column







/* 2 column - side nav */
if ($layout == "two_col_small"){
	
  $truethemes_count = 0;
  $truethemes_col = 0;

foreach($myposts as $post){
  $truethemes_count++; $truethemes_col ++; $mod = ($truethemes_count % 2 == 0) ? 0 : 2 - $truethemes_count % 2;
  if($truethemes_col == 2){$last = '_last';$truethemes_col = 0;}else{$last = '';}

setup_postdata($post);
$permalink = get_permalink($post->ID);
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$video_url = get_post_meta($post->ID,'truethemes_video_url',true);

if ($linkpost == ''){$truethemeslink = $permalink;}else{$truethemeslink = $linkpost;}
$output .= '<div class="one_half'.$last.'">
<div class="'.$style.'_img_frame '.$style.'_two_col_small">
<div class="'.$style.'_preload_two_col_small">
<div class="attachment-fadeIn">';

		$post_thumb = null;

		//post meta - Feature Image (External Source)
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);		
		//post meta - Feature Image
		$thumb = get_post_thumbnail_id();

		//half width image details
		$image_width = 324;
		$image_height = 180;

		//assign half image src, uses function from functions/global/basic.php
		$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		//post thumbnail
		if (!empty($image_src)):
			$post_thumb = "<a href='".$truethemeslink."'><img src='".$image_src."' alt='".get_the_title()."' /></a>";
			
		/* video embed
		elseif(!empty($video_url)):
		global $wp_embed;
$embed_video = $wp_embed->run_shortcode("[embed width='324' height='180']".$video_url."[/embed]");
		$post_thumb.= '<div class="video_frame">'.$embed_video.'</div>'; */
		
		//video embed
		elseif(!empty($video_url)):	
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-video-post.jpg" width="324" height="180" alt="'.get_the_title().'"/></a>';
		
		else:
			$post_thumb = '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-blog-post.jpg" width="324" height="180" alt="'.get_the_title().'"/></a>';
			
		endif;
			
		$output .= $post_thumb;
		$output .= '[raw]</div></div></div>[/raw]';
		
$output .= '<h4><a href="'.$truethemeslink.'">'.get_the_title().'</a></h4>';
$output .= '<p>'.substr(strip_tags($post->post_content), 0, $character_count).'...<br /><a href="'.$truethemeslink.'" class="ka_button small_button"><span>'.$link_text.'</span></a></p>';
$output .= '</div>';

} // end foreach
} // end 2 column small





/* 3 column - side nav */
if ($layout == "three_col_small"){
	
  $truethemes_count = 0;
  $truethemes_col = 0;

foreach($myposts as $post){
  $truethemes_count++; $truethemes_col ++; $mod = ($truethemes_count % 3 == 0) ? 0 : 3 - $truethemes_count % 3;
  if($truethemes_col == 3){$last = '_last';$truethemes_col = 0;}else{$last = '';}

setup_postdata($post);
$permalink = get_permalink($post->ID);
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$video_url = get_post_meta($post->ID,'truethemes_video_url',true);

if ($linkpost == ''){$truethemeslink = $permalink;}else{$truethemeslink = $linkpost;}
$output .= '<div class="one_third'.$last.'">
<div class="'.$style.'_img_frame '.$style.'_three_col_small">
<div class="'.$style.'_preload_three_col_small">
<div class="attachment-fadeIn">';

		$post_thumb = null;

		//post meta - Feature Image (External Source)
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);		
		//post meta - Feature Image
		$thumb = get_post_thumbnail_id();

		//half width image details
		$image_width = 202;
		$image_height = 113;

		//assign half image src, uses function from functions/global/basic.php
		$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		//post thumbnail
		if (!empty($image_src)):
			$post_thumb = "<a href='".$truethemeslink."'><img src='".$image_src."' alt='".get_the_title()."' /></a>";
		
		
		/* video embed
		elseif(!empty($video_url)):
		global $wp_embed;
$embed_video = $wp_embed->run_shortcode("[embed width='202' height='113']".$video_url."[/embed]");
		$post_thumb.= '<div class="video_frame">'.$embed_video.'</div>'; */
		
		//video embed
		elseif(!empty($video_url)):	
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-video-post.jpg" width="202" height="113" alt="'.get_the_title().'"/></a>';
		
				
		else:
			$post_thumb = '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-blog-post.jpg" width="202" height="113" alt="'.get_the_title().'"/></a>';
			
		endif;	
			
		$output .= $post_thumb;
		$output .= '[raw]</div></div></div>[/raw]';
		
$output .= '<h4><a href="'.$truethemeslink.'">'.get_the_title().'</a></h4>';
$output .= '<p>'.substr(strip_tags($post->post_content), 0, $character_count).'...<br /><a href="'.$truethemeslink.'" class="ka_button small_button"><span>'.$link_text.'</span></a></p>';
$output .= '</div>';

} // end foreach
} // end 3 column small





/* 4 column - side nav */
if ($layout == "four_col_small"){
	
  $truethemes_count = 0;
  $truethemes_col = 0;

foreach($myposts as $post){
  $truethemes_count++; $truethemes_col ++; $mod = ($truethemes_count % 4 == 0) ? 0 : 4 - $truethemes_count % 4;
  if($truethemes_col == 4){$last = '_last';$truethemes_col = 0;}else{$last = '';}

setup_postdata($post);
$permalink = get_permalink($post->ID);
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$video_url = get_post_meta($post->ID,'truethemes_video_url',true);

if ($linkpost == ''){$truethemeslink = $permalink;}else{$truethemeslink = $linkpost;}
$output .= '<div class="one_fourth'.$last.'">
<div class="'.$style.'_img_frame '.$style.'_four_col_small">
<div class="'.$style.'_preload_four_col_small">
<div class="attachment-fadeIn">';

		$post_thumb = null;

		//post meta - Feature Image (External Source)
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);		
		//post meta - Feature Image
		$thumb = get_post_thumbnail_id();

		//half width image details
		$image_width = 135;
		$image_height = 76;

		//assign half image src, uses function from functions/global/basic.php
		$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		//post thumbnail
		if (!empty($image_src)):
			$post_thumb = "<a href='".$truethemeslink."'><img src='".$image_src."' alt='".get_the_title()."' /></a>";
			
		/* video embed
		elseif(!empty($video_url)):
		global $wp_embed;
$embed_video = $wp_embed->run_shortcode("[embed width='135' height='76']".$video_url."[/embed]");
		$post_thumb.= '<div class="video_frame">'.$embed_video.'</div>'; */
		
		//video embed
		elseif(!empty($video_url)):	
		$post_thumb .= '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-video-post.jpg" width="135" height="76" alt="'.get_the_title().'"/></a>';			
			
		else:
			$post_thumb = '<a href="'.$truethemeslink.'"><img src="'.KARMA_HOME.'/images/_global/default-blog-post.jpg" width="135" height="76" alt="'.get_the_title().'"/></a>';
			
		endif;
			
		$output .= $post_thumb;
		$output .= '[raw]</div></div></div>[/raw]';
		
$output .= '<h4><a href="'.$truethemeslink.'">'.get_the_title().'</a></h4>';
$output .= '<p>'.substr(strip_tags($post->post_content), 0, $character_count).'...<br /><a href="'.$truethemeslink.'" class="ka_button small_button"><span>'.$link_text.'</span></a></p>';
$output .= '</div>';

} // end foreach
} // end 4 column small






$output .= '[raw]</div><br class="clear" />[/raw]';
return $output;
}
add_shortcode('blog_posts', 'truethemes_blog_posts');










/* =================================== */
// MISCELLANEOUS
/* =================================== */

/* ----- IFRAME ----- */
function karma_iframe($atts, $content=null) {
extract(shortcode_atts(array(
'url'   => '',
'scrolling'     => 'no',
'width'     => '100%',
'height'    => '500',
'frameborder'   => '0',
'marginheight'  => '0',
), $atts));
 
if (empty($url)) return 'http://';
return '<iframe src="'.$url.'" title="" scrolling="'.$scrolling.'" width="'.$width.'" height="'.$height.'" frameborder="'.$frameborder.'" marginheight="'.$marginheight.'">'.$content.'</iframe>';
}
add_shortcode('iframe','karma_iframe');




/* ----- RELATED POSTS ----- */
function related_posts_shortcode( $atts ) {
extract(shortcode_atts(array(
	'title' => '',
	'limit' => '5',
), $atts)); 
global $wpdb, $post, $table_prefix;
if ($post->ID) {
	$retval = '[raw]<div class="related_posts"><h4>'.$title.'</h4><ul class="list">[/raw]';
// Get tags
$tags = wp_get_post_tags($post->ID);
$tagsarray = array();
foreach ($tags as $tag) {
$tagsarray[] = $tag->term_id;
}
$tagslist = implode(',', $tagsarray);

// Do the query
$q = "
SELECT p.*, count(tr.object_id) as count
FROM $wpdb->term_taxonomy AS tt, $wpdb->term_relationships AS tr, $wpdb->posts AS p
WHERE tt.taxonomy ='post_tag'
AND tt.term_taxonomy_id = tr.term_taxonomy_id
AND tr.object_id  = p.ID
AND tt.term_id IN ($tagslist)
AND p.ID != $post->ID
AND p.post_status = 'publish'
AND p.post_date_gmt < NOW()
GROUP BY tr.object_id
ORDER BY count DESC, p.post_date_gmt DESC
LIMIT $limit;";

$related = $wpdb->get_results($q);
if ( $related ) {
	foreach($related as $r) {
		$retval .= '<li><a title="'.wptexturize($r->post_title).'" href="'.get_permalink($r->ID).'">'.wptexturize($r->post_title).'</a></li>';}
} else {
	$retval .= '<li>No related posts found</li>';}
$retval .= '[raw]</ul></div><!-- end related posts -->[/raw]';
return $retval;
}return;}
add_shortcode('related_posts', 'related_posts_shortcode');





/* ----- RELATED POSTS FOR CONTENT AREA ----- */
function related_posts_content_shortcode( $atts ) {
extract(shortcode_atts(array(
	'title' => '',
	'limit' => '5',
), $atts)); 
global $wpdb, $post, $table_prefix;
if ($post->ID) {
	$retval = '<div class="related_posts"><h4>'.$title.'</h4><ul class="list">';
// Get tags
$tags = wp_get_post_tags($post->ID);
$tagsarray = array();
foreach ($tags as $tag) {
$tagsarray[] = $tag->term_id;
}
$tagslist = implode(',', $tagsarray);

// Do the query
$q = "
SELECT p.*, count(tr.object_id) as count
FROM $wpdb->term_taxonomy AS tt, $wpdb->term_relationships AS tr, $wpdb->posts AS p
WHERE tt.taxonomy ='post_tag'
AND tt.term_taxonomy_id = tr.term_taxonomy_id
AND tr.object_id  = p.ID
AND tt.term_id IN ($tagslist)
AND p.ID != $post->ID
AND p.post_status = 'publish'
AND p.post_date_gmt < NOW()
GROUP BY tr.object_id
ORDER BY count DESC, p.post_date_gmt DESC
LIMIT $limit;";

if(!empty($tagslist)){
$related = $wpdb->get_results($q);
}else{
$related = null;
}

if ( $related ) {
	foreach($related as $r) {
		$retval .= '<li><a title="'.wptexturize($r->post_title).'" href="'.get_permalink($r->ID).'">'.wptexturize($r->post_title).'</a></li>';}
} else {
	$retval .= '<li>No related posts found</li>';}
$retval .= '</ul></div><!-- end related posts -->';
return $retval;
}return;}
add_shortcode('related_posts_content', 'related_posts_content_shortcode');





/* ----- CATEGORIES ----- */
function karma_categorie_display($atts) {
	extract(shortcode_atts(array(
'title'   => 'Categories',
), $atts));
	
	$pos_excluded = positive_exlcude_cats();
	$pos_cats = $pos_excluded;
	$pos_args = array('orderby' => 'name', 'exclude' => $pos_cats, 'title_li' => __( '' ));	
	echo '<h3>'.$title.'</h3>';
	wp_list_categories($pos_args);
}
add_shortcode('post_categories', 'karma_categorie_display');
?>