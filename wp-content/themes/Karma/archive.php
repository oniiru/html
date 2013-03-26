<?php get_header(); ?>
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div id="main">
<?php
$ka_blogtitle = get_option('ka_blogtitle');
$ka_searchbar = get_option('ka_searchbar');
$ka_crumbs = get_option('ka_crumbs');
$ka_blogbutton = get_option('ka_blogbutton');
$ka_posted_by = get_option('ka_posted_by');
$ka_post_date = get_option('ka_post_date');
if ($ka_post_date != "false"){ $ka_post_date_result = 'style="background:none !important;"';}else{$ka_post_date_result = '';}
$ka_dragshare = get_option('ka_dragshare');
$blog_image_frame = get_option('ka_blog_image_frame');
?>
<div class="main-area">
<div class="tools">
<div class="holder">
<div class="frame">

<?php truethemes_before_article_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<?php /* If this is a category archive */ if (is_category()) { ?>
	<h1>Archive for '<?php single_cat_title(); ?>'</h1>
	<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
	<h1>Posts Tagged '<?php single_tag_title(); ?>'</h1>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
	<h1>Archive for <?php the_time('F jS, Y'); ?></h1>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
	<h1>Archive for <?php the_time('F, Y'); ?></h1>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
	<h1>Archive for <?php the_time('Y'); ?></h1>
	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
	<h1>Author Archive</h1>
	<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
	<h1>Blog Archives</h1>
	<?php } ?>
<?php if ($ka_searchbar == "true"){get_template_part('searchform','childtheme');} else {} ?>
<?php if ($ka_crumbs == "true"){ $bc = new simple_breadcrumb;} else {} ?>


<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>


</div><!-- end frame -->
</div><!-- end holder -->
</div><!-- end tools -->


<div class="main-holder">
  <div id="content" class="content_blog">
  	<?php
  	//create a file in child theme called content-blog-childtheme.php to overwrite this. 
  	get_template_part('theme-template-part-content-blog','childtheme');
  	?>
  </div><!-- end content -->


<div id="sidebar" class="sidebar_blog">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Sidebar") ) : ?><?php endif; ?>
</div><!-- end sidebar -->
</div><!-- end main-holder -->
</div><!-- main-area -->



<?php get_footer(); ?>