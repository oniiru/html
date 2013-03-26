<?php
/*Template Name: NewSignup Template*/
?>
<?php get_header();?>
</div><!-- header-area --></div><!-- end rays --></div><!-- end header-holder --></div><!-- end header -->
<?php truethemes_before_main_hook();
	// action hook, see truethemes_framework/global/hooks.php
?>

<div id="main">
<div class="main-area" style="padding-top: 0px !important;">	<div class="main-holder">
		<div id="content" class="content_full_width">
			<div class="boxes">
				<h1>Get Started Today!</h1>	
				<h2>Free 7 day trial. No risk. Cancel at any time. </h2>
					
					
					<div id="offers" class="group">
						<?php $subacess = get_option('chargify');
					$anual = $subacess["annualplan"];
					$month = $subacess["monthlyplan"];
					$products = ion_chargify::products();
					foreach ($products as $pro)
						if ($pro -> id == $month)
							$p1 = $pro;
					if ($pro -> id == $anual)
						$p2 = $pro;
					?><script type="text/javascript">
						function submitplan(id) {
							switch(id) {
								case 1:
									document.monthlyform.submit();
									break;
								case 2:
									document.annualform.submit();
									break;
							}
						}
					</script>
<div id="offer-one">
<h3>Quarterly Membership</h3>
<h4>only <span style="font-size:1.2em;">$199</span>/quarter</h4>
<ul>
<li>Over 30% off regular price</li>
<li>Over 100 SolidWorks tutorials with new videos added weekly</li>
<li>Comprehensive exercise files with each lesson</li>
<li>Access to our exclusive Q&A Community section</li>
<li>30 Day Money Back Guarantee</li>
<li><span style="color:red;font-weight:bold;font-size:14px;">For a limited time: FREE 1 hour consultation with founder, Rohit Mitra</span></li>

</ul><form name="quarterlyform" action="<?php print $subacess["signuppageurl"];?>" method="post">

<p><a class="sweet-button" href="javascript:submitplan(2);" title="">Choose plan</a>		<input type="hidden" name="annualoption" value="<?php print $subacess["annualplan"];?>"/>			</form>	


<p></div>
<div id="offer-two">
<h3>Monthly Membership</h3>
<h4>only <span style="font-size:1.2em;">$99</span>/month</h4>
<ul>
<li>Great Value, no long term commitment</li>
<li>Over 100 SolidWorks tutorials with new videos added weekly</li>
<li>Comprehensive exercise files with each lesson</li>
<li>Access to our exclusive Q&A Community section</li>
<li>30 Day Money Back Guarantee</li>
<li><span style="color:red;font-weight:bold;font-size:14px;">For a limited time: FREE 1 hour consultation with founder, Rohit Mitra</span></li>

</ul>
<form name="monthlyform" action="<?php print $subacess["signuppageurl"];?>" method="post">

<p><a class="sweet-button" href="javascript:submitplan(1);" title="">Choose plan</a>
<input type="hidden" name="monthlyoption" value="<?php print $subacess["monthlyplan"];?>"/>
</form>
<p></div>
<div id="offer-three">
<h3>Yearly<br> Membership</h3>
<h4>only <span style="font-size:1.2em;">$699</span>/year</h4>
<ul>
<li>over 40% off regular price</li>
<li>Over 100 SolidWorks tutorials with new videos added weekly</li>
<li>Comprehensive exercise files with each lesson</li>
<li>Access to our exclusive Q&A Community section</li>
<li>30 Day Money Back Guarantee</li>
<li><span style="color:red;font-weight:bold;font-size:14px;">For a limited time: FREE 1 hour consultation with founder, Rohit Mitra</span></li>

</ul>
<form name="annualform" action="<?php print $subacess["signuppageurl"];?>" method="post">

<p><a class="sweet-button" href="javascript:submitplan(2);" title="">Choose planTest</a>		
<input type="hidden" name="annualoption" value="<?php print $subacess["annualplan"];?>"/>			
</form>	
<p></div>

					
					
			</div>	
				</div>
			
			
			<?php
			if (have_posts()) :
				while (have_posts()) : the_post();
					the_content();
					truethemes_link_pages();
				endwhile;
			endif;
			?>
		</div><!-- end content -->
	</div><!-- end main-holder -->
</div><!-- main-area --><?php get_footer();?>