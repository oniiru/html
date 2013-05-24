<?php
/*
Template Name: Overview Template
*/
?>

<?php get_header(); ?>
<div class="closedprojectbar">
	<div class="closedinner">
		<h1><?php echo the_title()	?></h1>
	</div>
</div>
			<div id="content" class="clearfix learningmain row-fluid">
			
				<div id="main" style="text-align:left" class="clearfix" role="main">
					<div class="warringup learningheader">
						Following the SolidWize Learning Path below is the most effective way to learn SolidWorks. If you have a specific question, you can sort our project-based lessons by category or dive into our SolidWorks Knowledge Base.
					</div>
					<ul class="nav nav-tabs" id="pathtabs">
					  <li class="active"><a class="realones" href="#learningpath">SolidWize Learning Path</a></li>
					  <li><a class="realones" href="#bycat">Sort By Category</a></li>
					  <li><a href="<?php bloginfo('url'); ?>/training">The Knowledge Base</a></li>
					</ul>
 
					
 
					
					<div class="tab-content">
					  <div class="tab-pane fade in active" id="learningpath">
					
		
			 	    <?php
		
			 	       $mypost = array(
			 			   		'post_type' => 'project_views',
			 					
			 					'posts_per_page' => 40,			
			 						);
			 	       $loop = new WP_Query( $mypost );
		   
			 		   while ( $loop->have_posts() ) : $loop->the_post();
			 		   ?>
			   
			 			 <article class="hello duber firstpanel" style="display:table-cell; text-align:center" id="post-<?php the_ID(); ?>">
			 	  		<?php   $post_image_id = get_post_thumbnail_id($post_to_use->ID);
			 	  		   		if ($post_image_id) {
			 	  		   			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
			 	  		   			if ($thumbnail) (string)$thumbnail = $thumbnail[0];
			 					    global $full_mb;
			 					   		 $techniquemeta = $full_mb->the_meta();
			 							 $mooo = $techniquemeta['Type'];
			 	  		   		} ?>
							<div style="background-image:url('<?php echo $thumbnail; ?>')" class="theinfo">	
			 			<a href="<?php the_permalink() ?>"><img style="" src="<?php echo get_stylesheet_directory_uri(); ?>/images/bookbg.png">
						<p>	<?php the_title(); ?></p></a>
					</div>
					
			 				
			 	           </article>
						   
			 	       <?php endwhile;   wp_reset_query();?>
				   </div>
 					  <div class="tab-pane fade in" id="bycat">
						  <?php
						
 
 
						         // List posts by the terms for a custom taxonomy of any post type
						         $post_type = 'project_views';
						         $tax = 'trainingcategory';
						         $tax_terms = get_terms( $tax, 'orderby=name&order=ASC');
						         if ($tax_terms) {
						                 foreach ($tax_terms  as $tax_term) {
						                         $args = array(
						                                 'post_type'                     => $post_type,
						                                 "$tax"                          => $tax_term->slug,
						                                 'post_status'           => 'publish',
						                                 'posts_per_page'        => -1
						                         );
 
						                         $my_query = null;
						                         $my_query = new WP_Query($args);
 
						                         if( $my_query->have_posts() ) : ?>
  <div class="trainingcatview clearfix">
	<h3><?php echo $tax_term->name; ?></h3>
 
	 <?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
 
 
  		<?php   $post_image_id = get_post_thumbnail_id($post_to_use->ID);
  		   		if ($post_image_id) {
  		   			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
  		   			if ($thumbnail) (string)$thumbnail = $thumbnail[0];
				    global $full_mb;
				   		 $techniquemeta = $full_mb->the_meta();
						 $mooo = $techniquemeta['Type'];
  		   		} ?>
				<div style="background-image:url('<?php echo $thumbnail; ?>')" class="theinfo">	
 			<a href="<?php the_permalink() ?>"><img style="" src="<?php echo get_stylesheet_directory_uri(); ?>/images/bookbg.png">
			<p>	<?php the_title(); ?></p></a>
		</div>
				
 
		 <?php endwhile; // end of loop ?>
				 </div>
 
			  <?php endif; // if have_posts()
			   wp_reset_query();
 
				} // end foreach #tax_terms
				  } // end if tax_terms
						  
					 ?>
						  
					  </div>
 					
 					</div>
					  
		   			<script>
					
					
		   			jQuery(document).ready(
		   				function(){
						    var divs = jQuery(".firstpanel");
						    for(var i = 0; i < divs.length; i+=4) {
						      divs.slice(i, i+4).wrapAll("<div class='projectrow' style='display:table;width:80%;margin:auto'></div>");
						    }
							jQuery('.projectrow:even').addClass('fromleft');
		   					jQuery('.projectrow:odd').addClass('fromright');
							
							jQuery(".projectrow").each(function(){
								var chickens = jQuery(this).children('.hello').length;
								var cows = 4-chickens;
									while (cows-- > 0 ) {
									jQuery('<div class="duber fillerthing" style="display:table-cell;"></div>').insertAfter('.hello:last');
								};
							    });
								
								jQuery(".projectrow").each(function(){
								    jQuery('.hello + .hello').before($('<div style="text-align:center" class="horizontal"></div>'));
								
								    });
									jQuery(".projectrow").each(function(){
									    jQuery('.hello + .fillerthing').before($('<div style="text-align:center" class="noline"></div>'));
								
									    });
										jQuery(".projectrow").each(function(){
										    jQuery('.fillerthing + .fillerthing').before($('<div style="text-align:center" class="noline"></div>'));
								
										    });
											 jQuery('.projectrow + .projectrow').before($("<div class='projectrowfiller' style='display:table;width:80%;margin:auto;height:75px'></div>"));
											 
				 							jQuery('.fromleft').next('.projectrowfiller').addClass('vertlineright');
								
									jQuery('.fromright').next('.projectrowfiller').addClass('vertlineleft');
								
									    var divs2 = jQuery(".trainingcatview");
									    for(var i = 0; i < divs2.length; i+=4) {
									      divs2.slice(i, i+4).wrapAll("<div class='projectrow2' style='display:table;width:90%;margin: 0 auto 40px auto;'></div>");
									    }
										jQuery(".projectrow2").each(function(){
											var chickens2 = jQuery(this).children('.trainingcatview').length;
											var cows2 = 4-chickens2;
												while (cows2-- > 0 ) {
												jQuery('<div class="duber2 fillerthing" style="display:table-cell;"></div>').insertAfter('.trainingcatview:last');
											};
										    });
											
											jQuery(".trainingcatview").each(function(){
											    jQuery('.theinfo + .theinfo').before($('<div style="text-align:center" class="vertlinemiddle"></div>'));
								
											    });
											
							});
					
							
		   			</script>
			   	 <script>
			   	 jQuery('a.realones').click(function (e) {
			  	   e.preventDefault();
					 
			   	   jQuery(this).tab('show');
			   	 })
			   	 </script>
					
			 	   </div>
				

				</div> <!-- end #main -->

    
			</div> <!-- end #content -->
			
			
<?php get_footer(); ?>