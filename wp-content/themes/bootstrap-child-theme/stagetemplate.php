<?php
/*
Template Name: Stage Template
*/
?>

<?php get_header(); ?>
			
			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="clearfix homepage" role="main">

					<div class="testdiv">
					<div class="indivstep">
						<div style="background:url('/SWZTEST/swztest/wp-content/themes/bootstrap-child-theme/images/tableimg.png');" class="stepimg">
							<div class="stepicon iconred">
								<img src="<?php bloginfo('stylesheet_directory');?>/images/checkmark.png">
							</div>
						</div>
						<div class="stepdetails">
							<ul>
								<li>hi</li>
								<li>there</li>
							</ul>
						</div>
						<div class="stepinfo">
							<h3 class="lessonnumber">Lesson 1</h3>
							<h4 class="lessontitle">Why you doing this?</h4>
							<p class="lessondesc">lorum ipsom salts</p>
							
						</div>
					</div>
					
				
					
				</div>
					
		

				</div> <!-- end #main -->

    
			</div> <!-- end #content -->
					
			<script>
			jQuery(document).ready(
				function(){
					jQuery('.indivstep').click(function(){
						jQuery(this).find('.stepdetails').slideToggle('slow');
							
					});
				});
			</script>
<?php get_footer(); ?>