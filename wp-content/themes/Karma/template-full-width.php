<?php
/*
Template Name: Full Width
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

<div id="content" class="content_full_width">
<?php if(have_posts()) : while(have_posts()) : the_post(); the_content(); truethemes_link_pages(); endwhile; endif; ?>
</div><!-- end content -->
</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?>