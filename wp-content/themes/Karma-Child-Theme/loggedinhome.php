<?php
/* Template Name: loggedinhome */
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
								<a href="/training/"><img src="http://solidwize.com/solidwize/wp-content/themes/Karma-Child-Theme/images/trainingvideos.png"></a>
								<h4><a href="/training/">Get Straight to Training.</a></h4>
								<p><a href="/training/">Click here to head straight to our training page and start learning.</a></p>
							</div>
						</div>
						<div id="newcontentmemberhome">
							<h4>- Recently Added -</h4>
						</div>
						<div id="officehoursmemberhome">
							<h4>- Office Hours -</h4>
							<p style="margin-top:10px;;font-weight:bold; margin-bottom:5px;">This week: Wednesday 5-7pm PST</p>
							<p style="font-size:12px">Every week, Rohit holds open office hours for members in an interactive webinar format. Join us and get your questions answered.<br></p>
							<p><a style="font-weight:bold;" href=""> Sign up for this week's session.</a></p>
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

					<?php if (pmpro_hasMembershipLevel('1')) { ?>
						<div id="browsememberhome">
							<div id="browsemembercontenthome">

							</div>
						</div>

						<div id="newcontentmemberhome">
							<h4>- Free Content -</h4>
							<p style="font-size:14px;line-height:1em;margin-top:5px	">The content below will always be free. <br>Click one of the buttons below to get started.</p>
							<a href="http://solidwize.com/training/intro-to-solidworks" title="Intro to SolidWorks"><img style="margin-top:0px;" width="183" height="100" src="http://solidwize.com/solidwize/wp-content/uploads/2011/11/intro.png"></a>
							<a href="/training/intro-to-solidworks">Intro to SolidWorks</a>
							<a href="http://solidwize.com/training/parts" title="Part Modeling"><img width="183" height="100" src="http://solidwize.com/solidwize/wp-content/uploads/2011/11/parts.png"></a>
							<a href="/training/parts">Part Modeling</a>
						</div>
					<?php } ?>
				</div>
			</div><!-- end content -->
		</div><!-- end main-holder -->
	</div><!-- main-area --><?php get_footer(); ?>