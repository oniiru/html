<?php
 /*Template Name: Project Page Template
 */
 
get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
    <?php  global $full_mb;
      		 $projectmeta = $project_mb->the_meta(); ?>
			 <style type="text/css">
			 .closedprojectbar {
				 <?php $backgroundimage = $projectmeta['backgroundimage']; 
				if ( NULL == $backgroundimage){ ?>
					background-image: url('<?php echo plugins_url() ?>/Project_View/projectdefaultbg.png');
					<?php } else {?> 
					background-image: url('<?php echo $backgroundimage ?>');	
						<?php }; ?>
				
				 background-color:<?php echo $projectmeta['color']?> !important;
				 background-repeat:no-repeat;
				 background-position:right -75px;
				 <?php echo $projectmeta['bgadjustments']?>
			 }
				 .projectquiz {
 					background-image: url('<?php echo plugins_url() ?>/Project_View/checkmark.png');
					background-position:15px 0px; 
   					background-repeat:no-repeat;
					
			 }
			 
			 .vertline {
				background-image: url('<?php echo plugins_url() ?>/Project_View/vertline.png');
			 }
			 
			 </style>
<div class="closedprojectbar">
	<div class="closedinner">
		<div class="closedinnerleft">
		<h1> <?php the_title(); ?></h1>
		<p><?php echo $projectmeta['description'] ?></p>
		<p class="extraprojectinfo"> Skill Level: <?php echo $projectmeta['difficulty']?> 
		  <span>  <?php
			$terms = get_the_terms( $post->ID, 'lessons' );

				$varlesson = array();
			foreach ( $terms as $term ) {
				$varlesson[] = $term->name;
			}
					
				$current_terms = join( ", ", $varlesson );
		
		
		       $mypost = array(
				   		'post_type' => 'lesson_views',
						'lessons' => $current_terms,
	 					'posts_per_page' => 40,	
							);
		       $loop = new WP_Query( $mypost );
			echo $loop->post_count;
			?> Lessons &nbsp;&#8226;&nbsp; <?php echo $projectmeta['totallength']?> Min. Total</p> </span>
	</div>
		<div class="closedinnerright">
<?php echo '<iframe id="main_video" src="http://player.vimeo.com/video/' . $projectmeta['vidembed'] . '?api=1&amp;player_id=main_video" width="350" height="219" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>' ?>		</div>
	</div>
</div>

<div id="content" class="clearfix row-fluid projectcontent">
	<div class="projectimage"> 
	<img src="<?php echo $projectmeta['projectimage']  ?>">	
	</div>
	<div class="vertline firstvert"></div>
	<div id="main" class="clearfix homepage projectcontainer" role="main">
	   <div style="display:block">
		
		   <?php
		   		   
		   while ( $loop->have_posts() ) : $loop->the_post();
		   ?>	
		   
		
			   
			
	  		<?php   $post_image_id = get_post_thumbnail_id($post_to_use->ID);
	  		   		if ($post_image_id) {
	  		   			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'full', false);
	  		   			if ($thumbnail) (string)$thumbnail = $thumbnail[0];
					    global $full_mb;
					   		 $techniquemeta = $full_mb->the_meta();
							 $mooo = $techniquemeta['Type'];
	  		   		} ?>
					
						 <article id="post-<?php the_ID(); ?>" class="projectindivs" <a href="<?php the_permalink() ?>">
							 <?php if($mooo == 'b') {?>
								 <a href="<?php the_permalink() ?>"> 
								 
								 <div class="projectquiz">
									<h2>Section Quiz</h2>		
								</div>	 
								  </a>
								 <?php } else {?>
	<div class="projectindivimg">
		<a href="<?php the_permalink() ?>">
		<img src="<?php echo $thumbnail; ?>">
	</a>
	</div>
	<div class="projectindivtext">
	<a href="<?php the_permalink() ?>">
		<h2> <?php echo $techniquemeta['lessontitle']?> </h2>
		<p><?php echo $techniquemeta['description']?> </p>
	</a>
	</div>
	<?php }; ?>
	           </article>
		  
	       <?php endwhile; ?>
	   </div>
<?php endwhile; ?>			

<?php else : ?>
<article id="post-not-found">
    <header>
    	<h1><?php _e("Not Found", "bonestheme"); ?></h1>
    </header>
    <section class="post_content">
    	<p><?php _e("Sorry, but the requested resource was not found on this site.", "bonestheme"); ?></p>
    </section>
    <footer>
    </footer>
</article>
<?php endif; ?>
<?php wp_reset_query(); ?>
<?php $teehee = get_adjacent_post(false, "", false );
$teeheeid = $teehee->ID;
$nextpostmeta = $project_mb->the_meta($teeheeid); 
if(NULL !== $teeheeid) {
?>
<div class="vertline"></div>
<a href="<?php echo get_permalink( $teeheeid ); ?>">
	<style type="text/css">
	.nextprojectimage {
		background-image: url('<?php echo $nextpostmeta['projectimage']  ?>');
		width:210px;
		height:210px;
		margin:auto;
	}
	</style>
<div class="nextprojectimage"> 
	<div class="nextprojectinfo">
		<p class="nextup">NEXT UP:</p>
		<p><?php echo get_the_title($teeheeid);  ?>
	</div>
</div></a>
<?php } else{ };?>

<?php $appendix1 = get_post_meta( get_the_ID(), 'Project_appendix1', true );
$appendix1_title = get_post_meta( get_the_ID(), 'Project_appendix1_title', true );
$appendix2 = get_post_meta( get_the_ID(), 'Project_appendix2', true );
$appendix2_title = get_post_meta( get_the_ID(), 'Project_appendix2_title', true );
$appendix3 = get_post_meta( get_the_ID(), 'Project_appendix3', true );
$appendix3_title = get_post_meta( get_the_ID(), 'Project_appendix3_title', true );

if ($appendix1 != '') { ?>

	<div class="appendix">
		<h3><?php echo $appendix1_title?></h3>
		<?php echo do_shortcode($appendix1) ?>
	</div>
 	<?php };
	if ($appendix2 != '') { ?>

		<div class="appendix">
			<h3><?php echo $appendix2_title?></h3>
			<?php echo do_shortcode($appendix2) ?>
		</div>
	 	<?php };
		if ($appendix3 != '') { ?>

			<div class="appendix">
				<h3><?php echo $appendix3_title?><h3>
				<?php echo do_shortcode($appendix3) ?>
			</div> 
			<?php };
 ?>
 <div class="projectcomments">
 <?php comments_template('',true); ?>
</div>

<div class="projecttechniques">
	<h3> Techniques and Toolsets Covered </h3>
	<p class="techniquesexplained"> Click below to bypass the lesson and learn only about that technique or toolset. </p>
	<div class="innerprojecttechniques">
		<?php	   
	   while ( $loop->have_posts() ) : $loop->the_post();
	   ?>
	   <div class="accordion" id="accordion2">
	 <article id="techniques-<?php the_ID(); ?>" class="techniques">
		 <?php 	 global $full_mb;
		 $techniquemeta = $full_mb->the_meta();
		 $techniquedoober = $techniquemeta['techniques'];
		 if ($techniquedoober != '') {
		 ?>
		 
		 <div class="accordion-group accordion-caret">
		     <div class="accordion-heading">
		       <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#collapse-<?php the_ID(); ?>">
		 <h4><?php echo $techniquemeta['lessontitle'] ?></h4></a></div>
		 <div id="collapse-<?php the_ID(); ?>" class="accordion-body collapse">
		      <div class="accordion-inner">
		 <?php
		 
		 foreach ($techniquemeta['techniques'] as $techniqueindiv)
		 { 
			 $realmin = str_pad($techniqueindiv['Min'], 2, '0', STR_PAD_LEFT); 
			 $realsec = str_pad($techniqueindiv['Sec'], 2, '0', STR_PAD_LEFT); 
			 $theinteger = ($realmin*60)+$realsec;
			 ?>
			 <a href="<?php echo rtrim(get_permalink( $id ),'/')?>?ST=<?php echo $theinteger  ?>">
				<span> <?php 
		     echo $realmin . ':' . $realsec  ; ?> </span> <?php
			 
		     echo $techniqueindiv['title'] . '<br>'; ?>
		 </a>
		 <?php
			 
		 }
	 }
		 ?>
	 </div>
	 </div>
	 </div>
	   
         </article>
</div>
     <?php endwhile; ?>
 </div>
</div>
</div>
</div>
<script>
jQuery('.btn-primary').addClass('btn-success');
jQuery('h3#comments').text('Discussion');	
jQuery('#comment-form-title').text('Questions?');
jQuery(".appendix ul.video-list li:nth-child(odd)").addClass("appendixleft");
jQuery(".appendix ul.video-list li:nth-child(even)").addClass("appendixright");


			
 jQuery('.projectindivs + .projectindivs').before($('<div class="vertline"></div>'));

			</script>
<?php get_footer(); ?>