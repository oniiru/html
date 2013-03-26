<?php
/*
Template Name: Portfolio :: 2 Columns
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




<div id="content" class="content_full_width portfolio_layout">
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

$count++; $col ++; $mod = ($count % 2 == 0) ? 0 : 2 - $count % 2;
?>

<?php

//retrieve all post meta of posts in the loop.
 
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
$portfolio_full = get_post_meta($post->ID, "_portimage_full_value", $single = true);
$phototitle = get_post_meta($post->ID, "_portimage_desc_value", $single = true);
$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);

//prepare to get image
$thumb = get_post_thumbnail_id();
$image_width = 437;
$image_height = 234;

//use truethemes image croping script, function moved to truethemes_framework/global/basic.php
$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);

?>
     
<div class="one_half<?php if($col == 2){  echo '_last'; $col = 0; } ?>">
<div class="portfolio_content_top">

<?php if(!empty($image_src)): //there is either post thumbnail of external image ?>

<div class="port_img_two">
<div class="preload preload_two">


<?php 
//function truethemes_generate_portfolio_image() in basic.php
$html = truethemes_generate_portfolio_image($image_src,$image_width,$image_height,$linkpost,$portfolio_full,$phototitle,'2'); 

echo $html;

?>


</div><!-- end preload_two -->
</div><!-- end port_img_two -->

<?php endif; ?>

</div><!-- end portfolio_content_top -->


<div class="portfolio_content">
<h3><?php the_title(); ?></h3>
<?php the_content(); ?>
</div><!-- end portfolio_content -->


</div><!-- end portfolio_two_column -->   
<?php if ( $mod == 0 ){ echo '<br class="clear" />';}endwhile; endif;?>
<?php  wp_pagenavi();  ?>
</div><!-- end content -->
</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?>