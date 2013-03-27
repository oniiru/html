			<footer role="contentinfo">
			
				<div id="inner-footer" class="clearfix">
		          <hr />
		          <div id="widget-footer" class="clearfix row-fluid">
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer1') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer2') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer3') ) : ?>
		            <?php endif; ?>
		          </div>
					
						<?php bones_footer_links(); // Adjust using Menus in Wordpress Admin ?>
					<a href="https://mixpanel.com/f/partner"><img src="//cdn.mxpnl.com/site_media/images/partner/badge_light.png" alt="Mobile Analytics" /></a>
			
				
				</div> <!-- end #inner-footer -->
				<!-- MixPanel Start -->
				
				<script type="text/javascript">
				mixpanel.name_tag('<?php global $current_user;
				get_currentuserinfo(); echo $current_user->user_login; ?>');
				mixpanel.track('Viewed Post', {
				    "Title":"<?php single_post_title(); ?>",
				    "Author":"<?php the_author(); ?>",
				    "Tags":"<?php $posttags = get_the_tags(); $count=0;
				   if ($posttags) { foreach($posttags as $tag) { $count++;
				   if (1 == $count) { echo $tag->name . ''; } } } ?>",
				});
				</script>
				<!-- MixPanel End -->
				
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
				
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>

	</body>
	<script>
	jQuery('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { 
	    e.stopPropagation(); 
	});
	</script>
</html>