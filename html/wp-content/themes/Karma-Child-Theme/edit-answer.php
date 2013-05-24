<?php

 get_header( ); ?>
<?php

global $ttso;

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
      
      <h1>Community</h1>
   <!--    <?php if ($ka_searchbar == "true"){
      //parent theme uses searchform.php
      //create a file in child theme named searchform-childtheme.php to overwrite.
     
      } else {} 
      ?> -->
      <p class="breadcrumb"><a href="<?php print bloginfo('home');?>">Home</a><span class="current_crumb"><?php the_title(); ?> </span></p>
      
<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>      
      
      </div><!-- end frame -->
    </div><!-- end holder -->
  </div><!-- end tools -->
  
  <?php endif; //end show tools panel check @since version 2.6 dev ?>

<div class="main-holder">
<div id="content" class="content_blog">

<div id="qa-page-wrapper">

<?php the_qa_menu(); ?>

<?php the_post(); ?>

<div id="answer-form">
	<h2><?php _e( 'Answer for ', QA_TEXTDOMAIN ); the_question_link( $post->post_parent ); ?></h2>
	<?php the_answer_form(); ?>
</div>

</div><!--#qa-page-wrapper-->
</div><!-- end content -->
  <div id="sidebar" class="sidebar_blog">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("FAQ Sidebar") ) : ?><?php endif; ?>
</div><!-- end sidebar -->
</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?> 

