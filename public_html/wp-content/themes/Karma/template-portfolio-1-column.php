<?php
/*
Template Name: Portfolio :: 1 Column
*/
?>
<?php get_header(); ?>
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div id="main">
<?php get_template_part('theme-template-part-tools','childtheme'); ?>
			
			
<div class="main-holder">
<?php  
//retrieve value for sub-nav checkbox
global $post;
$post_id = $post->ID;
$meta_value = get_post_meta($post_id,'truethemes_page_checkbox',true);

if(empty($meta_value)){
get_template_part('theme-template-part-subnav-horizontal','childtheme');}else{
// do nothing
}
?>

<div id="content" class="portfolio_full_width">
<?php if(have_posts()) : while(have_posts()) : the_post(); the_content(); truethemes_link_pages(); endwhile; endif; ?>
<?php
remove_filter('pre_get_posts','wploop_exclude');
$portfolio_count = get_post_meta($post->ID, "_sc_port_count_value", $single = true);
$posts_p_p = stripslashes($portfolio_count);
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$category_id = get_post_meta($post->ID, '_multiple_portfolio_cat_id', true);
$query_string ="posts_per_page=$posts_p_p&cat=$category_id&paged=$paged&order=ASC";
query_posts($query_string);

$count = 0; $col = 0;

if (have_posts()) : while (have_posts()) : the_post();

$count++; $col ++; $mod = ($count % 1 == 0) ? 0 : 1 - $count % 1;
?>

<?php

//retrieve all post meta of posts in the loop.
 
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$portfolio_full = get_post_meta($post->ID, "_portimage_full_value", $single = true);
$phototitle = get_post_meta($post->ID, "_portimage_desc_value", $single = true);
$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);

//prepare to get image
$thumb = get_post_thumbnail_id();
$image_width = 703;
$image_height = 563;

//use truethemes image croping script, function moved to truethemes_framework/global/basic.php
$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);

?>


            
<div class="portfolio_wrap">
<div class="portfolio_one_column_last">

<?php if(!empty($image_src)): //there is either post thumbnail of external image ?>

<div class="port_img_one">
<div class="preload preload_one">

<?php 
//function truethemes_generate_portfolio_image() in basic.php
$html = truethemes_generate_portfolio_image($image_src,$image_width,$image_height,$linkpost,$portfolio_full,$phototitle,'1'); 

echo $html;

?>

</div><!-- end preload_one -->
</div><!-- end port_img_one -->

<?php endif; ?>

</div><!-- end portfolio_one_column -->


<div class="portfolio_one_column">
<h3><?php the_title(); ?></h3>
<?php the_content(); ?>
</div><!-- end portfolio_one_column -->
</div><!-- end portfolio_wrap -->

<div class="port_sep"><div class="hr_top_link"></div><a href="#" class="link-top">top</a></div><!-- end port_sep -->

<?php endwhile; endif; ?>
<?php  wp_pagenavi();  ?>
</div><!-- end content -->
</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?>