<?php

if ( $my_query->have_posts() ) : 
	while ( $my_query->have_posts() ) :	$my_query->the_post(); ?>

	<div class="portfolio-entry">
		
							<?php							
								if (has_post_thumbnail( $post->ID ) ): 
								 $imgArr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
								 $imgUrl = $imgArr[0];
								 
								 $post_cat = get_the_category();
								 $cat_name = $post_cat[0]->slug;
							?>
								<div class="portfolio-img">
									<?php
										switch($cat_name) {
											case 'portfolio-bp':
												$cat_img = site_url().'/wp-content/themes/bluehornetstudios/includes/images/cat-bp.png';
												break;
											case 'portfolio-wp':
												$cat_img = site_url().'/wp-content/themes/bluehornetstudios/includes/images/cat-wp.png';
												break;
											case 'portfolio-ds':
												$cat_img = site_url().'/wp-content/themes/bluehornetstudios/includes/images/cat-ds.png';
												break;
											case 'portfolio-ea':
												$cat_img = site_url().'/wp-content/themes/bluehornetstudios/includes/images/cat-ea.png';
												break;
											default:
												$cat_img = '';
												break;
										}
									
									?>
									<a class="portfolio-url" target="_blank" href="<?php echo get_post_meta($post->ID, 'url', true); ?>"><img src="<?php echo $cat_img; ?>" class="portfolio-cat" /><img src="<?php echo $imgUrl; ?>" /></a>
									<img src="<?php echo $cat_img; ?>" class="portfolio-cat" />									
								</div>
								
							<?php endif; ?>
							
								<div class="portfolio-info">
									<h3><?php the_title(); ?></h3>
									<a class="portfolio-url" target="_blank" href="<?php echo get_post_meta($post->ID, 'url', true); ?>"><?php echo get_post_meta($post->ID, 'url', true); ?></a>
									<?php the_content(); ?>
								</div>
								
								<div class="clear"></div>
														
							</div>
<?php
	endwhile;
endif;
?>