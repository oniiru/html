<?php
 /*Template Name: Project Page Template
 */
 
get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
	
	
<div class="closedprojectbar">
	<div class="closedinner">
		<h1> <?php the_title(); ?>	</h1>
		<div class="closedinnerright">
			<p> More Info <span class="caret"></span></p>
		</div>
	</div>
	<div class="openinner">
		<div class="openinnerleft">
		<h1> <?php the_title(); ?></h1>
		<p>	<?php the_content(); ?></p>
	</div>
	<div class="openinnerright">
		<?php echo get_post_meta( get_the_ID(), 'Project_vid_embed', true ); ?>
	</div>
	</div>
</div>
<div id="content" class="clearfix row-fluid">
	
	<div id="main" class="clearfix homepage rawr" role="main">
	   <div style="display:block">
		
	    <?php
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
		   
		   while ( $loop->have_posts() ) : $loop->the_post();
		   ?>	
		
		
			   
			
	  		<?php   $post_image_id = get_post_thumbnail_id($post_to_use->ID);
	  		   		if ($post_image_id) {
	  		   			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
	  		   			if ($thumbnail) (string)$thumbnail = $thumbnail[0];
					    global $full_mb;
					   		 $techniquemeta = $full_mb->the_meta();
							 $mooo = $techniquemeta['Type'];
	  		   		} ?>
					
					<?php if ($mooo == "a") { ?>
						 <article id="post-<?php the_ID(); ?>" class="hello duber3 firstpanel indivstep" style="border: 5px solid #c6dfed !important;
"><a href="<?php the_permalink() ?>">
					<?php } ?>
					<?php if ($mooo == "b") { ?> 
						<article id="post-<?php the_ID(); ?>" class="hello duber3 firstpanel indivstep" style="	border: 5px solid #E08484 !important;
"><a href="<?php the_permalink() ?>">
					<?php } ?>
					<?php if ($mooo == "c") { ?>
						 <article id="post-<?php the_ID(); ?>" class="hello duber3 firstpanel indivstep" style="	border: 5px solid #7dbd78 !important;
"><a href="<?php the_permalink() ?>">
					<?php } ?>
					
			<div style=" background-size: cover!important;-webkit-background-size: cover!important;background-image: url('<?php echo $thumbnail; ?>');" class="stepimg">
					
					
			
						
				</div>
				<div class="stepinfo">
					<?php if ($mooo == "a") { ?>
					<h3 style="color:rgb(133, 133, 133)" class="lessonnumber2"><?php echo $techniquemeta['lessontitle'] ?></h3>
					<?php } ?>
					<?php if ($mooo == "b") { ?>
					<h3 style="color:#E08484" class="lessonnumber2"><?php echo $techniquemeta['lessontitle'] ?></h3>
					<?php } ?>
					<?php if ($mooo == "c") { ?>
					<h3 style="color:#7dbd78" class="lessonnumber2"><?php echo $techniquemeta['lessontitle'] ?></h3>
					<?php } ?>
					
			</div> </a>
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
	<h3> <?php echo var_dump($post->ID)?> </h3>
	<p class="techniquesexplained"> Click below to bypass the lesson and learn only about that technique or toolset. </p>
	<div class="innerprojecttechniques">
    <?php
	
	$terms = get_the_terms( $post->ID, 'lessons' );

		$varlesson = array();
			foreach ( $terms as $term ) {
		$varlesson[] = $term->name;
	};
				
		$current_terms = join( ", ", $varlesson );
	
       $mypost = array(
		   		'post_type' => 'lesson_views',
				'lessons' => $current_terms,
				'posts_per_page' => 40,			
					);
       $loop = new WP_Query( $mypost );
	   
	   while ( $loop->have_posts() ) : $loop->the_post();
	   ?>
	   
	 <article id="techniques-<?php the_ID(); ?>" class="techniques">
		 <?php 	 global $full_mb;
		 $techniquemeta = $full_mb->the_meta();
		 $techniquedoober = $techniquemeta['techniques'];
		 if ($techniquedoober != '') {
		 ?>
		 
		 <h4><?php echo $techniquemeta['lessontitle'] ?></h4>
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
		
         </article>

     <?php endwhile; ?>
 </div>
</div>
</div>
</div>
<script>
jQuery(document).ready( function(){
		jQuery('.closedinnerright p').click(function() {
			jQuery('.closedinner').fadeOut(400, function() {
				jQuery('.openinner').slideDown(1000);
			
			});
			
		});
jQuery('.btn-primary').addClass('btn-success');
jQuery('h3#comments').text('Discussion');	
jQuery('#comment-form-title').text('Questions?');
jQuery(".appendix ul.video-list li:nth-child(odd)").addClass("appendixleft");
jQuery(".appendix ul.video-list li:nth-child(even)").addClass("appendixright");
	
		
		
});
</script>
  
<script>


jQuery(document).ready(
	function(){
	    var divs = jQuery(".firstpanel");
	    for(var i = 0; i < divs.length; i+=3) {
	      divs.slice(i, i+3).wrapAll("<div class='projectrow2' style='display:table;width:95%;margin:auto'></div>");
	    }
		jQuery('.projectrow2:even').addClass('fromleft');
		jQuery('.projectrow2:odd').addClass('fromright');
		
		jQuery(".projectrow2").each(function(){
			var chickens = jQuery(this).children('.hello').length;
			var cows = 3-chickens;
				while (cows-- > 0 ) {
				jQuery('<div class="duber3 fillerthing" style="display:table-cell;"></div>').insertAfter('.hello:last');
			};
		    });
			
			jQuery(".projectrow2").each(function(){
			    jQuery('.hello + .hello').before($('<div style="text-align:center" class="horizontal2"></div>'));
			
			    });
				jQuery(".projectrow2").each(function(){
				    jQuery('.hello + .fillerthing').before($('<div style="text-align:center" class="noline2"></div>'));
			
				    });
					jQuery(".projectrow2").each(function(){
					    jQuery('.fillerthing + .fillerthing').before($('<div style="text-align:center" class="noline2"></div>'));
			
					    });
						 jQuery('.projectrow2 + .projectrow2').before($("<div class='projectrow2filler' style='display:table;width:95%;margin:auto;height:75px'></div>"));
						 
						jQuery('.fromleft').next('.projectrow2filler').addClass('vertlineright2');
			
				jQuery('.fromright').next('.projectrow2filler').addClass('vertlineleft2');
				
				
			});
			</script>
<?php get_footer(); ?>