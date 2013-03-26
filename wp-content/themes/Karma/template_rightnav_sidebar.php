<?php
/*
Template Name: Right Nav + Sidebar 
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
<div id="sidebar" class="left_sidebar">
<?php generated_dynamic_sidebar(); ?>
</div><!-- end sidebar -->

<div id="content" class="content_sidebar content_right_sidebar">
<?php if(have_posts()) : while(have_posts()) : the_post(); the_content(); truethemes_link_pages(); endwhile; endif; ?>
</div><!-- end content -->

<?php get_template_part('theme-template-part-subnav-right','childtheme'); ?>

</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?>