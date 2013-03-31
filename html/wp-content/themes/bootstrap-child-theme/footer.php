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
		mixpanel.name_tag('<?php echo $current_user->display_name?>');
		mixpanel.people.set({
		    "$email": "<?php echo $current_user->user_email ?>", 
		    "membership_Level": "<?php echo $current_user->membership_level->name ?>",                   
			"$name": "<?php echo $current_user->display_name?>",
		});
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
		var FileVersion1 = jQuery('#tab2 a').text();
		mixpanel.track_links('#tab2 a', 'Downloaded Fileset', {
		    'Page': '<?php the_title(); ?>',
			'Version': FileVersion1,
			
		});
		var FileVersion2 = jQuery('#tab3 a').text();
		mixpanel.track_links('#tab3 a', 'Downloaded Fileset', {
		    'Page': '<?php the_title(); ?>',
			'Version': FileVersion1,
			
		});
		mixpanel.track_links('.videohome a', 'Watched Intro Video');
		mixpanel.track_links('.homesec4 a', 'Clicked Bottom CTA Button');
		mixpanel.track_links('.headersignupbtn', 'Clicked Sign Up Button', {
		    'Type': 'Header Button',
		    'Page': '<?php the_title(); ?>'	
		});
		
		mixpanel.track_links('.headerupgradebtn', 'Clicked Upgrade Button', {
		    'Type': 'Header Button',
		    'Page': '<?php the_title(); ?>'	
		});
		
		mixpanel.track_links('.catsignupbtn', 'Clicked Sign Up Button', {
		    'Type': 'Category Page Bar',
		});
		
		mixpanel.track_links('.catupgradebtn', 'Clicked Upgrade Button', {
		    'Type': 'Category Page Bar',
		});
		
		mixpanel.track_links('.blogpitchout', 'Clicked Blog Banner', {
		    'Type': 'Goes to Training Page',
		    'Page': '<?php the_title(); ?>'	
		});
		
		mixpanel.track_links('.blogpitchin', 'Clicked Upgrade Button', {
		    'Type': 'Bottom of blog page',
		    'Page': '<?php the_title(); ?>'	
		});
		
		mixpanel.track_links('.singlepitchout', 'Clicked Blog Banner', {
		    'Type': 'Goes to Training Page',
		    'Page': '<?php the_title(); ?>'	
		});
		
		mixpanel.track_links('.singlepitchin', 'Clicked Upgrade Button', {
		    'Type': 'Bottom of post page',
		    'Page': '<?php the_title(); ?>'	
		});
		
		mixpanel.track_links('.blogpitchsidebar', 'Clicked Sign Up Button', {
		    'Type': 'Blog Side Bar',
		    'Page': '<?php the_title(); ?>'	
		});
		
		mixpanel.track_links('.rightthing a', 'Clicked Sign Up Button', {
		    'Type': 'Link in Homepage Paragraph',	
		    'Page': '<?php the_title(); ?>'	
		});
		mixpanel.track_links('.pmprochange', 'Clicked Upgrade Button', {
		    'Type': 'Link in PMPro account box',	
		    'Page': '<?php the_title(); ?>'	
		});
		mixpanel.track_links('.freevidpopup', 'Clicked Signup Button', {
		    'Type': 'Free Vid Button in Pop Up',	
		    'Page': '<?php the_title(); ?>'	
		});
		mixpanel.track_links('.paidvidpopup', 'Clicked Signup Button', {
		    'Type': 'Paid Vid Button in Pop Up',	
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