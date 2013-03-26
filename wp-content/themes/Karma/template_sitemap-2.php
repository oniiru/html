<?php
/*
Template Name: Sitemap 2
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
<?php $sitemap_column1 = get_option('ka_sitemap2_column1');$sitemap_column2 = get_option('ka_sitemap2_column2');$sitemap_column3 = get_option('ka_sitemap2_column3'); ?>
<div class="main-holder">
<div id="content">
	<div class="one_third">
    <h2><?php echo $sitemap_column1; ?></h2>
    <?php if (function_exists('wp_nav_menu')) {	
	echo '<ul class="list sitemap-list">';
	wp_nav_menu( array(
	 'container' =>false,
	 'theme_location' => 'Primary Navigation',
	 'sort_column' => 'menu_order',
	 'menu_class' => '',
	 'echo' => true,
	 'before' => '',
	 'after' => '',
	 'link_before' => '',
	 'link_after' => '',
	 'depth' => 0)
	 );
	echo '</ul>';} ?>
    </div><!-- end one_third -->
    
    
    
    <div class="one_third">
    <h2><?php echo $sitemap_column2; ?></h2>
    <ul class="list">
	<?php 
	$neg_excluded = B_getExcludedCats();
	$neg_cats = $neg_excluded;
	wp_get_archives(apply_filters('widget_archives_args', array('type' => 'postbypost', 'show_post_count' => $c, 'cat' => $neg_cats)));
	?>
    </ul>
    </div><!-- end one_third -->
    
    
    
    <div class="one_third_last sitemap-last">
    <?php echo $sitemap_column3; ?>
    </div><!-- end one_third_last -->
    
    
    
    
</div><!-- end content -->


<div id="sidebar" class="right_sidebar">
<?php generated_dynamic_sidebar(); ?>
</div><!-- end sidebar -->
</div><!-- end main-holder -->
</div><!-- main-area -->



<?php get_footer(); ?>