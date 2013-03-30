			<footer role="contentinfo">

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
		mixpanel.track_links('.accordion-toggle', 'Opened FAQ', {
		    'Page': '<?php the_title(); ?>'
		});
		mixpanel.track_links('.businessplan a', 'Clicked Business Plan Contact Us');
		   
		mixpanel.track_links('.freeplan a', 'Clicked Signup Button', {
		    'Type': 'Free Plan',
		});
		mixpanel.track_links('.monthlyplan a', 'Clicked Signup Button', {
		    'Type': 'Standard Plan',
		    'Page': '<?php the_title(); ?>'
		});
		mixpanel.track_links('#other_discount_code_a', 'Clicked Coupon Link');
		
		
		</script>
		<!-- MixPanel End -->
		
	</body>

</html>