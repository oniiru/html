			<footer role="contentinfo">
			
				<div id="inner-footer" class="clearfix">
		          <hr />
		          
					
						<?php bones_footer_links(); // Adjust using Menus in Wordpress Admin ?>
					<a href="https://mixpanel.com/f/partner"><img src="//cdn.mxpnl.com/site_media/images/partner/badge_light.png" alt="Mobile Analytics" /></a>
			
				
				</div> <!-- end #inner-footer -->
				
				
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
				
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>
		<!-- MixPanel Start -->
		<script type="text/javascript">
		<?php if (is_user_logged_in()) { ?>
		mixpanel.identify('<?php global $current_user;
		get_currentuserinfo(); echo $current_user->ID; ?>');
		<?php } ?>
		mixpanel.track('Viewed <?php single_post_title(); ?>');
		mixpanel.track_links('.videosignupbtn', 'Clicked Sign Up Button', {
		    'Type': 'Video Page Bar',
		    'Page': '<?php the_title(); ?>'
		});
		mixpanel.track_links('.videoupgradebtn', 'Clicked Upgrade Button', {
		    'Type': 'Video Page Bar',
		    'Page': '<?php the_title(); ?>'
		});
		</script>
		<!-- MixPanel End -->
	
		
	</body>
	<script>
	jQuery('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { 
	    e.stopPropagation(); 
	});
	</script>
</html>