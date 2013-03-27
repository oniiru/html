<?php
/*
Template Name: Pricing Page
*/
?>

<?php
	include (STYLESHEETPATH . '/pricingheader.php');
?>			
			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="span12 clearfix" role="main">
					<center>
					<h1>Learn SolidWorks the right way</h1>
					<h3>7 Day Free Trial, Cancel at any time.</h3>
				</center>
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
						
						<header>
							<div class="pricingcontainer">
								<?php if(!pmpro_hasMembershipLevel('1')) 
								{ ?> 
									<a href="<?php echo pmpro_url("checkout", "?level=" . 1,"https")?>">
										<?php }?>
								<div class="freeplan pricingplan">
								
									<div class="pricingplanimg">
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/intropricing.png">
									</div>
									<h2>Free</h2>
									<h4><br>Includes:</h4>	
									<p>
									Free access to our Intro and Part Modeling sections forever. </p>
									<p style="border-bottom:0px">Comprehensive excercise files for both sections.
									</p>
									<?php if(pmpro_hasMembershipLevel('1')) 
									{ ?>  
											<h5>This is your current level</h5>

								  	<?php } else { ?>    
										<a class="btn btn-custom btn-large " href="<?php echo pmpro_url("checkout", "?level=" . 1,"https")?>">Get Started</a>
									<?php }?>
									
								</div></a>
								<?php if(!pmpro_hasMembershipLevel(array(2,3,5,6)))
								{ ?> 
								<a href="<?php echo pmpro_url("checkout", "?level=" . 2,"https")?>">
									<?php }?>
								<div class="monthlyplan pricingplan">
									<div class="pricingplanimg">
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/proimage.png">
									</div>
									<h2>Pro</h2>
									<h4><br>Includes:</h4>	
									
									<p>
									Unlimited access to our entire library of training material. <img class="pricingq" src="<?php echo get_stylesheet_directory_uri(); ?>/images/question.png"> </p>
									<p>Comprehensive excercise files, quizzes and CSWP prep course. </p>
								<p>	Fanatical customer support - office hours, chat, email, phone, you name it!</p>
									<p class="mooo">Starting at $34/Month (if paid annually)
									
									</p>
									<?php if(pmpro_hasMembershipLevel(array(2,3,5,6)))									
									{ ?>  
											<h5>This is your current level</h5>

								  	<?php } elseif(pmpro_hasMembershipLevel('1')){ ?>    
										<a class="btn btn-custom btn-large " href="<?php echo pmpro_url("checkout", "?level=" . 2,"https")?>">Upgrade</a>
									<?php } else { ?>
										<a class="btn btn-custom btn-large " href="<?php echo pmpro_url("checkout", "?level=" . 2,"https")?>">Start 7 Day Free Trial</a>
										<?php }?>
									
								</div>
							</a>
							<a href="#businesscontactmodal" data-toggle="modal">
								<div class="businessplan pricingplan">
									<div class="pricingplanimg">
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/enterprise.png">
									</div>
									<h2>Teams</h2>
									<h4><br>Includes:</h4>	
									
									<p>
										Get your whole team up to speed with Pro Memberships.	<br>
									 </p>
									<p> Custom content based on your specific needs.
									</p>
									 	<p class="mooo">Deep discounts for multi-user accounts.
									</p> 
									<a class="btn btn-custom btn-large" href="#businesscontactmodal" data-toggle="modal">Contact Us</a>
								</div>
							</a>
							<p class="planguarantee">All plans come with a free 7-day trial and 30 day money-back guarantee!</p>
							<div class="alert alert-info studentalert">
								Still in School? Checkout our killer <a href="<?php bloginfo('url'); ?>/student"><b> student discount.</b> </a>
							</div>
						</header> <!-- end article header -->
						<div class="accordion" id="accordion2">
						  <div class="accordion-group">
						    <div class="accordion-heading pricingfaq">
						      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
						        <i class="icon-chevron-down"></i> <p>Frequently Asked Questions...</p> <i class="icon-chevron-down"></i>
						      </a>
						    </div>
						    <div id="collapseOne" class="accordion-body collapse">
						      <div class="accordion-inner">
								  <div class="row">
								  <div class="span6">
									 <h3>7 day trial you say?</h3>
									 <p>With your subscription, you will have 7 days to access all of our great training content free of charge. After which, your credit card will automatically be billed. Cancel at any time during the 7 day trial, and you won't pay a thing.</p>
						 
								  </div>
								  <div class="span6">
									  <h3>What's included with my subscription?</h3>
									  <p>While actively subscribed, you will have unlimited access to all video tutorials and exercise files (additional material added every Monday).</p>
								  </div>
							  </div>
							  <div class="row">
								  <div class="span6">
									 <h3>How do I cancel the service?</h3>
									 <p>We'd hate to see you go, but if you'd like to cancel, just follow the link at the bottom of your user profile. Canceling is an instantaneous and no-questions-asked process.</p>
						 
								  </div>
								  <div class="span6">
									  <h3>How does your 30 day money-back guarantee work?</h3>
									  <p>Wf you are not 100% satisfied with your membership, just shoot us an email before your first month has finished and we'll refund your money, no questions asked. If you're not happy, I'm not happy. Including the 7 day free trial, that's a total of 37 days to try out our training risk free.</p>
								  </div>
							  </div>
							  <div class="row">
								  <div class="span6">
									 <h3>Is your site secure?</h3>
									 <p>Of course! Our site is secured by Godaddy and Stripe. We go to great lengths to keep your data secured.</p>
									 <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/api-cloud.png" style="float:right">
						 <div style="float:right; margin-right: 20px; margin-top: 30px;"> <span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=7BtpZZvt6QuT91PTamzlUZdFu62QjzQ5h3eno4XpGhnOjLof6J2"></script></span></div>
								  </div>
								  <div class="span6">
									  <h3>Are you affiliated with SolidWorks?</h3>
									  <p>We are an official SolidWorks Solutions Partner, and as such can display this nifty logo!</p>
									  <img style="float:right; margin-top:20px;" src="<?php echo get_stylesheet_directory_uri(); ?>/images/solutions.png">
								  </div>
							  </div>
							  <div class="row">
								  <div class="span12" style="text-align:center">
									  <h3>Any other questions?</h3>
									  <p>Contact me via <a href="mailto:andrew@solidwize.com">email </a>or call at 877.688.7563, I'll be happy to answer any other questions you may have.</p>
								  </div>
							  </div>
					
			      </div>
			    </div>
			  </div>
						<footer>
			
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					
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
			
				</div> <!-- end #main -->
    
				<?php //get_sidebar(); // sidebar 1 ?>
    
			</div> <!-- end #content -->
							<div id="businesscontactmodal" class="modal hide fade">
							  <div class="modal-header">
							    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							    <h3>SolidWize Enterprise</h3>
							  </div>
							  <div class="modal-body">
								<p> Contact us regarding information and pricing on our multi-member plans. This is the perfect option for businesses and educational institutions.<br><br>
								<span style="color:rgb(255, 92, 0);margin-top:5px">  Call Us: 877.688.7563<br>
								  Email:<a href="mailto:andrew@solidwize.com"> Andrew@SolidWize.com</a></span></p>
							    <p><?php echo do_shortcode('[gravityform id="3" name="Business Contact Form" title="false" description="false"]') ?>
										</p>
							  </div>
							  <div class="modal-footer">
								  
							  </div>
							</div>
							<script>
							jQuery(document).ready(function(){
							jQuery('#gform_submit_button_3').addClass('btn btn-success');
							
						});
						</script>
			<?php
				include (STYLESHEETPATH . '/footercheckout.php');
			?>	