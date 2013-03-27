<?php
/*
Template Name: SolidWize Homepage
*/
?>

<?php get_header(); ?>
<div class="header-background-image">
	<div class="CTAsection">
		<h1>Design Better Products, Faster.</h1>
		<h2>Online SolidWorks Training available 24/7.</h2>		
	</div>
	<a href="<?php bloginfo('url'); ?>/training/" class="btn btn-custom btn-large">Check out our training</a>
	<img class="homeimage" src="<?php echo get_stylesheet_directory_uri(); ?>/images/solidworkshomewindows.png" >
</div>
<div id="header-background-Footer"></div>

			<div id="content" class="clearfix row-fluid">
				<div id="main" class="clearfix homepage rawr" role="main">
				<div class="homesec1">
					<div class="videohome">
						<a href="#videomodal" data-toggle="modal">
											
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/playvideo.jpg">
						<h3>First time here?</h3>
						<p>Watch this video to see what we're all about.</p>  
					</div>
				</a>
				
				
				<div id="videomodal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">	
				<div class="modal-header"></div><div class="modal-body modalvid"></div>	<div class="modal-footer"></div></div>		
	

				<script>
				jQuery(document).ready(function() {
				jQuery('#videomodal').on('show', function () {
				  jQuery('div.modalvid').html('<iframe src="http://player.vimeo.com/video/58140022?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" class="modaliframevid" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');  
				});

				jQuery('#videomodal').on('hide', function () {
				  jQuery('div.modalvid').html('&nbsp;');  
				});

				});
				</script>    
				
					<div class="homesecright">
						<h3>Become a SolidWorks Master</h3>
						<p>Our membership-based online SolidWorks training is geared to make you the most efficient and creative SolidWorks user possible. It works whether you are learning from the ground up, need to top off your skills, or just want a place to consult when you get stuck.  </p>
					</div>
					
					
				</div>

				<div class="homesec2">
					<h2>How SolidWize Works</h2>
					<div class="leftthing">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/learn.jpg">
						<h3>Learn</h3>
						<p>Learn from hundreds of tutorials covering all of the major SolidWorks toolsets. Whether you are just starting out or already more advanced, you'll be able to learn at your own pace from any internet-ready device. Our material is updated weekly, so you will always be able to continue progressing.</p>
					</div>
					<div class="middlething">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/practice.jpg">
						<h3>Practice</h3>
						<p>Excercise files and quizzes accompany each course, so you'll have plenty of time to practice. We even have weekly member only webinars, where you can ask our trainers specific questions in person. When it comes to your education, we mean business!</p>
					</div>
					<div class="rightthing">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/design.jpg">
						<h3>Design</h3>
						<p>Design that innovative product that's been burning in your mind. If you get stuck, we're here to help. You study SolidWorks to design and build amazing products. Our training and fanatical customer support are here to help you do just that. <a href="<?php bloginfo('url'); ?>/pricing">Let's get started.</a></p>
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