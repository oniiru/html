<?php require_once('config.php');
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) wp_die(__("You are not allowed to be here","truethemes")); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Shortcodes</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<script language="javascript" type="text/javascript" src="<?php echo get_template_directory_uri() ?>/truethemes_framework/truethemes/wysiwyg/wysiwyg.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<base target="_self" />
</head>
<body onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none" id="link">
<form name="karma_shortcode_form" action="#">
<div style="height:100px;width:250px;margin:0 auto;padding-top:50px;text-align:center;" class="shortcode_wrap">
<div id="shortcode_panel" class="current" style="height:50px;">
<fieldset style="border:0;width:100%;text-align:center;">
<select id="style_shortcode" name="style_shortcode" style="width:250px">
<option value="0">Select a Shortcode...</option>
<option value="0" style="font-weight:bold;font-style:italic;">Homepage Layouts</option>
<option value="homepage_layout_3_columns">Homepage - 3 Columns + Images</option>
<option value="homepage_layout_4_columns">Homepage - 4 Columns + Images</option>
<option value="homepage_layout_video_left">Homepage - Video (left)</option>
<option value="homepage_layout_video_right">Homepage - Video (right)</option>



<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Column Layouts</option>
<option value="two_columns">2 Columns</option>
<option value="three_columns">3 Columns</option>
<option value="four_columns">4 Columns</option>
<option value="five_columns">5 Columns</option>
<option value="six_columns">6 Columns</option>
<option value="one_fourth_three_fourth_columns">1/4 Column + 3/4 Column</option>
<option value="three_fourth_one_fourth_columns">3/4 Column + 1/4 Column</option>
<option value="two_thirds_one_third_columns">2/3 Column + 1/3 Column</option>
<option value="one_third_two_thirds_columns">1/3 Column + 2/3 Column</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>  
<option value="0" style="font-weight:bold;font-style:italic;">Column Layouts with Images</option>
<option value="layouts_full_2col_images">Full width - 2 Columns + Images</option>
<option value="layouts_full_3col_images">Full width - 3 Columns + Images</option>
<option value="layouts_full_3col_portrait_images">Full width - 3 Columns + Portrait Images</option>
<option value="layouts_full_4col_images">Full width - 4 Columns + Images</option> 
<option value="0">---</option> 
<option value="layouts_sidenav_2col_images">Side nav - 2 Columns + Images</option>
<option value="layouts_sidenav_3col_images">Side nav - 3 Columns + Images</option>
<option value="layouts_sidenav_4col_images">Side nav - 4 Columns + Images</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>  
<option value="0" style="font-weight:bold;font-style:italic;">Column Layouts with Lightboxes</option>
<option value="lightboxes_full_2col_images">Full width - 2 Columns + Lightboxes</option>
<option value="lightboxes_full_3col_images">Full width - 3 Columns + Lightboxes</option>
<option value="lightboxes_full_3col_portrait_images">Full width - 3 Columns + Portrait Lightboxes</option>
<option value="lightboxes_full_4col_images">Full width - 4 Columns + Lightboxes</option> 
<option value="0">---</option> 
<option value="lightboxes_sidenav_2col_images">Side nav - 2 Columns + Lightboxes</option>
<option value="lightboxes_sidenav_3col_images">Side nav - 3 Columns + Lightboxes</option>
<option value="lightboxes_sidenav_4col_images">Side nav - 4 Columns + Lightboxes</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Layout Elements</option>
<option value="single_accordion">Single Accordions</option>
<option value="multiple_accordion">Multiple Accordions</option>
<option value="tabs">Tabs</option>
<option value="layouts_video_left">Video Left</option>
<option value="layouts_video_right">Video Right</option>
<option value="layouts_flash_slider_wrap">Flash Slider Wrap</option>




<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Latest Blog Posts</option>
<option value="blog_posts">Default Layout (small thumbnails)</option>
<option value="0">---</option> 
<option value="blog_posts_full_2_column">Full width - 2 Columns</option>
<option value="blog_posts_full_3_column">Full width - 3 Columns</option>
<option value="blog_posts_full_4_column">Full width - 4 Columns</option>
<option value="0">---</option>
<option value="blog_posts_side_2_column">Side nav - 2 Columns</option>
<option value="blog_posts_side_3_column">Side nav - 3 Columns</option>
<option value="blog_posts_side_4_column">Side nav - 4 Columns</option> 




<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Text Styling</option>
<option value="text_callout1">Callout Text 1</option>
<option value="text_callout2">Callout Text 2</option>
<option value="text_h1">Heading 1 (H1)</option> 
<option value="text_h2">Heading 2 (H2)</option> 
<option value="text_h3">Heading 3 (H3)</option> 
<option value="text_h4">Heading 4 (H4)</option> 
<option value="text_h5">Heading 5 (H5)</option> 
<option value="text_h6">Heading 6 (H6)</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Lists</option>
<option value="arrow_list">Arrow List</option>
<option value="star_list">Star List</option>
<option value="circle_list">Circle List</option>
<option value="check_mark_list">Check Mark List</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Notify Boxes</option>
<option value="callout_green">Green Notify Box</option>
<option value="callout_blue">Blue Notify Box</option>
<option value="callout_red">Red Notify Box</option>
<option value="callout_yellow">Yellow Notify Box</option>



<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Callout Boxes</option>
<option value="color_callout_Autumn">Autumn</option>
<option value="color_callout_Black">Black</option>
<option value="color_callout_BlueGrey">Blue Grey</option>
<option value="color_callout_Cherry">Cherry</option>
<option value="color_callout_CoolBlue">Cool Blue</option>
<option value="color_callout_Coffee">Coffee</option>
<option value="color_callout_ForestGreen">Forest Green</option>
<option value="color_callout_Fire">Fire</option>
<option value="color_callout_Golden">Golden</option>
<option value="color_callout_Grey">Grey</option>
<option value="color_callout_LimeGreen">Lime Green</option>
<option value="color_callout_Periwinkle">Periwinkle</option>
<option value="color_callout_Pink">Pink</option>
<option value="color_callout_Purple">Purple</option>
<option value="color_callout_RoyalBlue">Royal Blue</option>
<option value="color_callout_SkyBlue">Sky Blue</option>
<option value="color_callout_Silver">Silver</option>
<option value="color_callout_Teal">Teal</option>
<option value="color_callout_TealGrey">Teal Grey</option>
<option value="color_callout_Violet">Violet</option>



<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Buttons</option>
<option value="Autumn_button">Autumn</option>
<option value="Black_button">Black</option>
<option value="BlueGrey_button">Blue Grey</option>
<option value="Cherry_button">Cherry</option>
<option value="CoolBlue_button">Cool Blue</option>
<option value="Coffee_button">Coffee</option>
<option value="ForestGreen_button">Forest Green</option>
<option value="Fire_button">Fire</option>
<option value="Golden_button">Golden</option>
<option value="Grey_button">Grey</option>
<option value="LimeGreen_button">Lime Green</option>
<option value="Periwinkle_button">Periwinkle</option>
<option value="Pink_button">Pink</option>
<option value="Purple_button">Purple</option>
<option value="RoyalBlue_button">Royal Blue</option>
<option value="SkyBlue_button">Sky Blue</option>
<option value="Silver_button">Silver</option>
<option value="Teal_button">Teal</option>
<option value="TealGrey_button">Teal Grey</option>
<option value="Violet_button">Violet</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Dividers</option>
<option value="basic_divider">Basic Divider</option> 
<option value="shadow_divider">Shadow Divider</option>
<option value="toplink_divider">Basic Divider + Top Link</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Individual Image Frames</option>
<option value="image_frame_banner_large">Full width (banner)</option>
<option value="image_frame_portrait_large">Full width (portrait fullsize)</option>
<option value="image_frame_portrait_small">Full width (portrait thumbnail)</option>
<option value="image_frame_full_2col">Full width (one_half)</option>
<option value="image_frame_full_3col">Full width (one_third)</option>
<option value="image_frame_full_4col">Full width (one_fourth)</option>
<option value="0">---</option>
<option value="image_frame_banner_medium">Side nav (banner)</option>
<option value="image_frame_sidenav_2col">Side nav (one_half)</option>
<option value="image_frame_sidenav_3col">Side nav (one_third)</option>
<option value="image_frame_sidenav_4col">Side nav (one_fourth)</option>
<option value="0">---</option>
<option value="image_frame_banner_small">Sidebar + side nav (banner)</option>




<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Individual Lightboxes</option>
<option value="lightbox_full_portrait_large">Full width (portrait fullsize)</option>
<option value="lightbox_full_portrait_small">Full width (portrait thumbnail)</option>
<option value="lightbox_full_2col">Full width (one_half)</option>
<option value="lightbox_full_3col">Full width (one_third)</option>
<option value="lightbox_full_4col">Full width (one_fourth)</option>
<option value="0">---</option>
<option value="lightbox_sidenav_2col">Side nav (one_half)</option>
<option value="lightbox_sidenav_3col">Side nav (one_third)</option>
<option value="lightbox_sidenav_4col">Side nav (one_fourth)</option>


<option value="0"> </option>
<option value="0"> </option>
<option value="0"> </option>
<option value="0" style="font-weight:bold;font-style:italic;">Other Shortcodes</option>
<option value="testimonials">Testimonials Slider</option>
<option value="latest_tweets">Latest Tweets</option>
<option value="related_posts">Related Posts</option>
</select>
</fieldset>
</div><!-- end shortcode_panel -->
<div style="float:left"><input type="button" id="cancel" name="cancel" value="<?php echo "Close"; ?>" onClick="tinyMCEPopup.close();" /></div>
<div style="float:right"><input type="submit" id="insert" name="insert" value="<?php echo "Insert"; ?>" onClick="embedshortcode();" /></div>
</div><!-- end shortcode_wrap -->
</form>
</body>
</html>