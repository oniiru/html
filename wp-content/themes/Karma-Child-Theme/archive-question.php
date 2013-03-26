<?php
/*Template Name: Template :: Archive FAQ
*/
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
      <p class="breadcrumb"><a href="<?php print bloginfo('home');?>">Home</a><span class="current_crumb">Questions </span></p>
      
<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>      
      
      </div><!-- end frame -->
    </div><!-- end holder -->
  </div><!-- end tools -->
  
  <?php endif; //end show tools panel check @since version 2.6 dev ?>

<div class="main-holder">
<div id="content" class="content_blog">

<div id="qa-page-wrapper">

<?php the_qa_error_notice(); ?>
<?php the_qa_menu(); ?>

<?php if ( !have_posts() ) : ?>

<p><?php $question_ptype = get_post_type_object( 'question' ); echo $question_ptype->labels->not_found; ?></p>

<?php else: ?>

<div id="question-list">
<?php while ( have_posts() ) : the_post(); ?>
	<div class="question">
		<div class="question-stats">
			<?php the_question_score(); ?>
			<?php the_question_status(); ?>
		</div>
		<div class="question-summary">
			<h3><?php the_question_link(); ?></h3>
			<?php the_question_tags( '<div class="question-tags">', ' ', '</div>' ); ?>
			<div class="question-started">
				<?php the_qa_time( get_the_ID() ); ?>
				<?php the_qa_user_link( $post->post_author ); ?>
			</div>
		</div>
	</div>
<?php endwhile; ?>
</div><!--#question-list-->

<?php the_qa_pagination(); ?>

<?php endif; ?>

</div><!--#qa-page-wrapper-->

 </div><!-- end content -->
  <div id="sidebar" class="sidebar_blog">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("FAQ Sidebar") ) : ?><?php endif; ?>
</div><!-- end sidebar -->
</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?> 
