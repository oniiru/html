<?php
/* Template Name: Member Homepage with shortcodes */
?>

<?php get_header(); ?>
</div><!-- header-area --></div><!-- end rays --></div><!-- end header-holder --></div><!-- end header -->
<?php
truethemes_before_main_hook();
// action hook, see truethemes_framework/global/hooks.php
?>

<div id="main">
	<div class="main-area" style="padding-top: 0px !important;">
		<div class="main-holder">
			<div id="content" class="content_full_width confirmationh9">
				<div id="memberholder">
					<?php if (pmpro_hasMembershipLevel(array(2, 3, 4, 5, 6, 7, 8))) { ?>
						<div id="browsememberhome">
							<div id="browsemembercontenthome">
								<a href="/training/"><img src="http://solidwize.com/wp-content/themes/Karma-Child-Theme/images/trainingvideos.png"></a>
								<h4><a href="/training/">Get Straight to Training.</a></h4>
								<p><a href="/training/">Click here to head straight to our training page and start learning.</a></p>
							</div>
						</div>
						<div id="newcontentmemberhome">
							<h4>- Recently Added -</h4>
							<?php echo do_shortcode('[mhp_promo_page]') ?>
						</div>
						<div id="officehoursmemberhome">
							<h4>- Office Hours -</h4>
							<p style="margin-top:14px !important;font-weight:bold; margin-bottom:5px;">This week: <?php echo do_shortcode('[mhp_office_hours]') ?></p>
							<p style="font-size:15px !important;line-height: 1.1em; margin-bottom:7px !important">Every week, Rohit holds open office hours for members in an interactive webinar format. Join us and get your questions answered.<br></p>
							<p><a style="font-weight:bold;" href="<?php echo do_shortcode('[mhp_url]') ?>"> Sign up for this week's session.</a></p>
						</div>
						<div id="randommemberhome">
							<h4>What do you want to learn?</h4>
							<?php echo do_shortcode('[gravityform id="1" name="Member requests" title="false" description="false"]'); ?>

							<!-- <form name="requests">
								<textarea class="formfield" name="message" onFocus="this.value=''" style="margin: 5px 0px 5px 0px; width: 380px; height: 60px; max-width: 450px;max-height:120px	">We create content based on what is most requested. Let us know what you want to learn...</textarea>
								<input type="submit" name="submit" class="red signupbutton" value="Submit">
							</form> -->
						</div>
					<?php } ?>

					<?php if (pmpro_hasMembershipLevel(array(0, 1))) { ?>
						<div id="browsememberhome">
							<div id="browsemembercontenthome">
								<a href="/training/"><img src="http://solidwize.com/wp-content/themes/Karma-Child-Theme/images/trainingvideos.png"></a>
								<h4><a href="/training/">Get Straight to Training.</a></h4>
								<p><a href="/training/">Click here to head straight to our training page and start learning.</a></p>
						
							</div>
						</div>

						<div id="newcontentmemberhome">
							<h4>- Free Content -</h4>
							<p style="font-size:14px;line-height:1em;margin-top:5px	">The content below will always be free. <br>Click one of the buttons below to get started.</p>
							<a href="/training/intro-to-solidworks" title="Intro to SolidWorks"><img style="margin-top:0px;" width="183" height="100" src="http://solidwize.com/wp-content/uploads/2011/11/intro.png"></a>
							<a href="/training/intro-to-solidworks">Intro to SolidWorks</a>
							<a href="/training/parts" title="Part Modeling"><img width="183" height="100" src="http://solidwize.com/
							wp-content/uploads/2011/11/parts.png"></a>
							<a href="/training/parts">Part Modeling</a>
						</div>
						<div id="freememberhome">
							<h4>Get <span style="color:darkred">20%</span> off your <br>premium membership! </h4>
							<p style="font-size:18px; margin:0px 5px 30px 5px;">Start the year off right! For a limited time, use the coupon code <span style="color:darkred;font-weight:bold;font-size:1.2em">ROCKSOLIDWORKS</span> to get 20% off any of our training packages. </p>
							<a href="/pricing-2" style="left:0px !important" class="red signupbutton">Upgrade Now!</a>
						</div>
					<?php } ?>
				</div>
			</div><!-- end content -->
		</div><!-- end main-holder -->
	</div><!-- main-area --><?php get_footer(); ?>
