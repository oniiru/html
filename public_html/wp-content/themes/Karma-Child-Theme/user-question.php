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
      get_template_part('searchform','childtheme');
      } else {} 
      ?> -->
      <?php if ($ka_crumbs == "true"){ $bc = new simple_breadcrumb;} else {} ?>
      
<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>      
      
      </div><!-- end frame -->
    </div><!-- end holder -->
  </div><!-- end tools -->
  
  <?php endif; //end show tools panel check @since version 2.6 dev ?>

<div class="main-holder">
<div id="content" class="content_blog">

<div id="qa-page-wrapper">

<?php the_qa_menu(); ?>

<div id="qa-user-box">
	<?php echo get_avatar( get_queried_object_id(), 128 ); ?>
	<?php the_qa_user_rep( get_queried_object_id() ); ?>
</div>

<table id="qa-user-details">
	<tr>
		<th><?php _e( 'Name', QA_TEXTDOMAIN ); ?></th>
		<td><strong><?php echo get_queried_object()->display_name; ?></strong></td>
	</tr>
	<tr>
		<th><?php _e( 'Member for', QA_TEXTDOMAIN ); ?></th>
		<td><?php echo human_time_diff( strtotime( get_queried_object()->user_registered ) ); ?></td>
	</tr>
	<tr>
		<th><?php _e( 'Website', QA_TEXTDOMAIN ); ?></th>
		<td><?php echo make_clickable( get_queried_object()->user_url ); ?></td>
	</tr>
</table>

<?php
$answer_query = new WP_Query( array(
	'author' => get_queried_object_id(),
	'post_type' => 'answer',
	'posts_per_page' => 20,
	'update_post_term_cache' => false
) );

$fav_query = new WP_Query( array(
	'post_type' => 'question',
	'meta_key' => '_fav',
	'meta_value' => get_queried_object_id(),
	'posts_per_page' => 20,
) );
?>

<div id="qa-user-tabs-wrapper">
	<ul id="qa-user-tabs">
		<li><a href="#qa-user-questions">
			<span id="user-questions-total"><?php echo number_format_i18n( $wp_query->found_posts ); ?></span>
			<?php echo _n( 'Question', 'Questions', $wp_query->found_posts, QA_TEXTDOMAIN ); ?>
		</a></li>

		<li><a href="#qa-user-answers">
			<span id="user-answers-total"><?php echo number_format_i18n( $answer_query->found_posts ); ?></span>
			<?php echo _n( 'Answer', 'Answers', $answer_query->found_posts, QA_TEXTDOMAIN ); ?>		
		</a></li>
	</ul>

	<div id="qa-user-questions">
		<div id="question-list">
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="question">
				<div class="question-stats">
					<?php the_question_score(); ?>
					<?php the_question_status(); ?>
				</div>
				<div class="question-summary">
					<h3><?php the_question_link(); ?></h3>
					<?php the_question_tags(); ?>
					<div class="question-started">
						<?php the_qa_time( get_the_ID() ); ?>
					</div>
				</div>
			</div>
		<?php endwhile; ?>
		</div><!--#question-list-->
	</div><!--#qa-user-questions-->

	<div id="qa-user-answers">
		<ul>
		<?php
			while ( $answer_query->have_posts() ) : $answer_query->the_post();
				list( $up, $down ) = qa_get_votes( get_the_ID() );

				echo '<li>';
					echo "<div class='answer-score'>";
					echo number_format_i18n( $up - $down );
					echo "</div> ";
					the_answer_link( get_the_ID() );
				echo '</li>';
			endwhile;
		?>
		</ul>
	</div><!--#qa-user-answers-->

</div><!--#qa-user-tabs-wrapper-->

</div><!--#qa-page-wrapper-->
  </div><!-- end content -->
  <div id="sidebar" class="sidebar_blog">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("FAQ Sidebar") ) : ?><?php endif; ?>
</div><!-- end sidebar -->
</div><!-- end main-holder -->
</div><!-- main-area -->

<?php get_footer(); ?> 