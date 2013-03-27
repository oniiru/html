<?php
/*
Template Name: Blog
*/
?>

<?php get_header(); ?>
<?php
/*
 * @since version 1.0
 * modified @since 2.6 development
 * use global variable $ttso
*/ 
global $ttso;
$ka_blogtitle = $ttso->ka_blogtitle;
$ka_searchbar = $ttso->ka_searchbar;
$ka_crumbs = $ttso->ka_crumbs; 
$show_tools_panel = $ttso->ka_tools_panel;
?>
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div id="main">
<div class="main-area">
	
	<?php
	/*
	* Check to display or hide tools panel
	* @since version 2.6 development
	*/
	if($show_tools_panel != 'false'):
	?>
	
  <div class="tools">
    <div class="holder">
      <div class="frame">
      
      <?php truethemes_before_article_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>
      
      <h1><?php echo $ka_blogtitle; ?></h1>
      <?php if ($ka_searchbar == "true"){
      //parent theme uses searchform.php
      //create a file in child theme named searchform-childtheme.php to overwrite.
      get_template_part('searchform','childtheme');
      } else {} 
      ?>
      <?php if ($ka_crumbs == "true"){ $bc = new simple_breadcrumb;} else {} ?>
      
<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>      
      
      </div><!-- end frame -->
    </div><!-- end holder -->
  </div><!-- end tools -->
  
  <?php endif; //end show tools panel check @since version 2.6 dev ?>

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