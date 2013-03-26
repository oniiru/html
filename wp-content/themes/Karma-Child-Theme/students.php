<?php
/* Template Name: students */
?>

<?php get_header(); ?>

</div><!-- header-area --></div><!-- end rays --></div><!-- end header-holder --></div><!-- end header -->

<?php
truethemes_before_main_hook();

// action hook, see truethemes_framework/global/hooks.php
?>



<div id="main">

	<div class="main-area" style="padding-top: 0px !important;">	<div class="main-holder">

			<div id="content" class="content_full_width">

				<div class="boxes">

					<h1>Learn SolidWorks Today!</h1>

					<h2>7 day risk-free trial. Cancel at any time. </h2>


					<div id="offers" class="group">

						<?php
						$subacess = get_option('chargify');
						$student = $subacess["studentplan"];
						$products = ion_chargify::products();
						foreach ($products as $pro)
							if ($pro->id == $sudent)
								$p = $pro;
						?><script type="text/javascript">
							function submitplan() {
								document.studentform.submit();
							}

						</script>



						<div id="offer-two" style="margin:0 auto 0 300px">

							<h3>Student Membership</h3>

							<h4>only <span style="font-size:1.2em;">$25</span>/month</h4>

							<form name="studentform" action="<?php print $subacess["signuppageurl"]; ?>" method="post">

								<a class="red signupbutton" href="javascript:submitplan();" title="">Get Started</a>

								<input type="hidden" name="studentoption" value="<?php print $subacess["studentplan"]; ?>"/>

							</form>

							<ul style="padding-top:10px;">

								<li>Priced exclusively for students, with no long-term commitments</li>

								<li>Unlimited access to our entire library of over 150 SolidWorks tutorials, with new videos added weekly</li>

								<li>Comprehensive exercise files</li>

								<li>30 Day Money-back Guarantee</li>

								<li>Insane Customer Support - email, phone, chat - we go above and beyond to make sure you are learning.</li>

								<li><span style="color:#0087C2;font-weight:bold;font-size:14px;">For a limited time: FREE 30 minute consultation with founder, Rohit Mitra</span></li>
							</ul>
						</div>
					</div>
				</div>
				<div style="margin-top: 30px">
					<?php
					if (have_posts()) :

						while (have_posts()) : the_post();

							the_content();

							truethemes_link_pages();

						endwhile;

					endif;
					?></div>

			</div><!-- end content -->

		</div><!-- end main-holder -->

	</div><!-- main-area --><?php get_footer(); ?>