<?php

 get_header( ); ?>
<?php

global $ttso;

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
      <p class="breadcrumb"><a href="<?php print bloginfo('home');?>">Home</a><a href="<?php bloginfo('home');?>/questions/">Questions</a><span class="current_crumb"><?php the_title(); ?> </span></p>
      
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

<div id="single-question">
	<h1><?php the_title(); ?></h1>
	<div id="single-question-container">
		<?php the_question_voting(); ?>
		<div id="question-body">
			<div id="question-content"><?php the_content(); ?></div>
			<?php the_question_tags( __( 'Tags:', QA_TEXTDOMAIN ) . ' <span class="question-tags">', ' ', '</span>' ); ?>
			<span id="qa-lastaction"><?php _e( 'asked', QA_TEXTDOMAIN ); ?> <?php the_qa_time( get_the_ID() ); ?></span>

			<?php the_qa_action_links( get_the_ID() ); ?>

			<?php the_qa_author_box( get_the_ID() ); ?>
		</div>
	</div>
</div>

<?php if ( is_question_answered() ) { ?>
<div id="answer-list">
	<h2><?php the_answer_count(); ?></h2>
	<?php the_answer_list(); ?>
</div>
<?php } ?>

<div id="edit-answer">
	<h2><?php _e( 'Your Answer', QA_TEXTDOMAIN ); ?></h2>
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
