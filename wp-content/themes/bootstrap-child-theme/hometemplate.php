<?php
/*
Template Name: SolidWize Homepage
*/
?>

<?php get_header(); ?>
<div class="header-background-image">
	<div id="header-background-Footer"></div>
	<div class="CTAsection">
		<h1>Design Better Products, Faster.</h1>
		<h2>Online SolidWorks Training available 24/7.</h2>		
	</div>
	<a href="<?php bloginfo('url'); ?>/training/" class="btn btn-custom btn-large">Check out our training</a>
	<img class="homeimage" src="<?php echo get_stylesheet_directory_uri(); ?>/images/solidworkshomewindows.png" >
</div>
			<div id="content" class="clearfix row-fluid">
				<div id="main" class="clearfix homepage rawr" role="main">
				<div class="homesec1">
					<div class="videohome">
						<a href="#videomodal" data-toggle="modal">
											
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/playvideo.jpg">
						<h3>First time here?</h3>
						<p>Watch the video to see what we're all about.</p>  
					</div>
				</a>
				
				
				<div id="videomodal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">	
				<div class="modal-header"></div><div class="modal-body"></div>	<div class="modal-footer"></div></div>		
	

				<script>
				$(document).ready(function() {
				$('#videomodal').on('show', function () {
				  $('div.modal-body').html('<iframe src="http://player.vimeo.com/video/58140022?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" width="700" height="394" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');  
				});

				$('#videomodal').on('hide', function () {
				  $('div.modal-body').html('&nbsp;');  
				});

				});
				</script>    
				
					<div class="homesecright">
						<h3>From zero experience to job-ready</h3>
						<p>The extensive Treehouse library of step-by-step video courses and training exercises will give you a wide range of competitive, in-demand technology skills that will help you land your next dream job or build your startup idea. No experience? </p>
					</div>
					
					
				</div>

				<div class="homesec2">
					<h2>How SolidWize Works</h2>
					<div class="leftthing">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/learn.jpg">
						<h3>Learn</h3>
						<p>Learn from over 650 videos created by our expert teachers on web design, coding, business, and much more. Our library is continually refreshed with the latest on web technology so you'll never fall behind.</p>
					</div>
					<div class="middlething">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/practice.jpg">
						<h3>Practice</h3>
						<p>Practice what you've learned through quizzes and interactive Code Challenges. This style of practicing will allow you to retain information you've learned so you can apply it to your own future projects.</p>
					</div>
					<div class="rightthing">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/design.jpg">
						<h3>Design</h3>
						<p>You'll earn badges as you journey through our extensive library of courses. These badges are an indicator of what skills you currently possess and are viewable by anyone (even recruiters from big companies!).</p>
					</div>
				</div>
				<div class="homesec3">
					<h2 style="text-align:center"> What our customers are saying </h2>
					<ul class="bxslider">
					  <li><div class="sliderinner"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/faketest.png"><blockquote><p>Thanks to you both, I am grateful for your help and feel fortunate to be on my way back to gainful employment.</p><small>G.A. <cite title="Source Title">LYNNWOOD, WA</cite></small></blockquote>  
</div></li>
					  
					  <li><div class="sliderinner"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/faketest.png"> <blockquote><p>SolidWorks has so many widgets and features, it can be hard to keep up. You guys took a ton of frustration out of the modeling process.</p><small>Evan <cite title="Source Title">SCRIPPS OCEANOGRAPHY</cite></small></blockquote></div></li>
					  <li><div class="sliderinner"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/faketest.png"><blockquote><p>I've tried all kinds of SolidWorks books, but there are always things lost in translation. With SolidWize you can see every click and get an explanation of each step of the process.</p><small>Andy <cite title="Source Title">UCLA R&D SHOP</cite></small></blockquote></div></li>
					  <li><div class="sliderinner"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/faketest.png"><blockquote><p>Killer training. Unbelievably efficient and concise.</p><small>Adam <cite title="Source Title">DEL WEST USA</cite></small></blockquote></div></li>
					  
					</ul>
					<script>
					$(document).ready(function(){
					  $('.bxslider').bxSlider({
						  mode: 'fade',
					  speed: 500,
					  infiniteLoop: true,
					  auto: true,
					  pause: 6000,
					  autoHover: true,
					  
				  });
					});
					</script>
				</div>
				<div class="homesec4">
					<div class="sec4left">
					<h2>Unlock your full potential.</h2>
					<h4>Start your SolidWorks journey today. </h4>
				</div>
					<a class="btn btn-large btn-custom" href="<?php bloginfo('url'); ?>/pricing">See Plans and Pricing </a>
				</div> <!-- end #main -->  
			</div> <!-- end #content -->


<?php get_footer(); ?>