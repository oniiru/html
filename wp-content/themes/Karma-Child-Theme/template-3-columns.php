<?php
/*
Template Name: Template :: 3 Columns
*/
?>
<?php get_header(); ?>
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>
<script type="text/javascript">
//<![CDATA[
 jQuery(document).ready(function($) {
 $('.attachments').each(function(){
 
	$(this).mouseenter(function(){

		$(this).children('img.imgs').fadeTo('slow', 0);
		$(this).children('img.imgshover').fadeTo('slow', 1);
	});
	$(this).mouseleave(function(){

		$(this).children('img.imgs').fadeTo('slow', 1);;
		$(this).children('img.imgshover').fadeTo('slow', 0);
	});
	
 });
 });
//]]>
</script>
<div id="main">
<?php get_template_part('tools','childtheme'); ?>
	
	
	
<div class="main-holder">
<?php  
//retrieve value for sub-nav checkbox
global $post;
$post_id = $post->ID;
$meta_value = get_post_meta($post_id,'truethemes_page_checkbox',true);

if(empty($meta_value)){
get_template_part('subnav-horizontal','childtheme');}else{
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

$count++; $col ++; $mod = ($count % 3 == 0) ? 0 : 3 - $count % 3;
?>

<?php

//retrieve all post meta of posts in the loop.
 


//prepare to get image
$thumb = get_post_thumbnail_id();
$image_width = 275;
$image_height = 145;


$image_src=MultiThumbnails::get_the_post_thumbnail_src('post', 'box-image');
$page_url=get_post_meta($post->ID, 'pageurl', true);

?>


<div class="one_third<?php if($col == 3){  echo '_last'; $col = 0; } ?>">



<div class="portfolio_content_top_three">

<?php if(!empty($image_src)): //there is either post thumbnail of external image ?>

<div class="port_img_three">
<div class="nopreload_three">


<?php 
//function truethemes_generate_portfolio_image() in basic.php
//$html = truethemes_generate_portfolio_image($image_src,$image_width,$image_height,$linkpost,$portfolio_full,$phototitle,'3'); 


 $img_hover=MultiThumbnails::get_the_post_thumbnail_src('post', 'box-image-hover');

?>

<a title="" class="attachments" href="<?php echo $page_url; ?>" style="display: inline;">
<img class="imgshover" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>"  alt="" style="position: absolute; opacity: 0;" src="<?php echo $img_hover[0]; ?>">
<img class="imgs" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" alt="" src="<?php echo $image_src[0]; ?>">
</a>


</div><!-- end preload_three -->
</div><!-- end port_img_three -->

<?php endif; ?>

</div><!-- end portfolio_content_top_three -->

<div class="portfolio_content">
<h3><?php the_title(); ?></h3>
<?php the_content(); ?>
</div><!-- end portfolio_content -->


</div><!-- end portfolio_three_column -->
<?php if ( $mod == 0 ){ echo '<br class="clear" />';}endwhile; endif;?>
<?php  wp_pagenavi();  ?>
</div><!-- end content -->
</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?>