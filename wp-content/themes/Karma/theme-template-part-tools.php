<?php
/*
* Utility Panel
* This is a template part
* @since version 1.0
* moved to theme root from contents folder @since version 2.6
*
*/
global $ttso;
$show_tools_panel = $ttso->ka_tools_panel;
?>
<div class="main-area<?php if($show_tools_panel == 'false') {echo ' utility-area';}?>">
<?php
/*
* Check to display or hide tools panel
* @since version 2.6 development
*/
if($show_tools_panel != 'false'):
?>
<?php 
$ka_crumbs = get_option('ka_crumbs'); ?>

<div class="tools">
<div class="holder">
<div class="frame">

<?php truethemes_before_article_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<?php if ( get_post_meta($post->ID, '_pagetitle_value', true) ) { ?>
<h1><?php echo get_post_meta($post->ID, "_pagetitle_value", $single = true); ?></h1><?php }else { ?>
<h1><?php if(have_posts()) : while(have_posts()) : the_post(); ?><?php the_title(); ?><?php endwhile; endif; ?></h1><?php } ?>
<?php if ($ka_searchbar == "true"){get_template_part('searchform','childtheme');} ?>
<?php if ($ka_crumbs == "true"){ ?><?php $bc = new simple_breadcrumb; ?><?php } ?>

<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end frame -->
</div><!-- end holder -->
</div><!-- end tools -->
<?php endif; ?>