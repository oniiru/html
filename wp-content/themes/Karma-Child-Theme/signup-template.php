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
			<?php $current_level = ($current_user->membership_level->ID == $level->id);
	?>
			<div class="boxes">
				<h1 style="margin-bottom:5px;">Learn SolidWorks Today!</h1>	
				<h2 style="margin-bottom:20px;">7 day risk-free trial. Cancel at any time. </h2>
					
					
					<div id="offers" class="group">

	<div id="offer-one">
<h3>Free<br> Membership</h3>
<h4>Free Forever</h4>
	<?php if(pmpro_hasMembershipLevel('1')) 
	{ ?>  
			<h5>This is your current level</h5>

  	<?php } else { ?>    
		<a class="red signupbutton" href="<?php echo pmpro_url("checkout", "?level=" . 1,"https")?>" title="">Get Started</a>

	<?php }?>


<ul style="padding-top:10px;">
<li> A great option for those just getting started with SolidWorks.</li>
<li> Unlimited Access to the Intro to SolidWorks and Part Modeling sections.</li>
<li>Comprehensive exercise files for both sections.</li>

</ul>
</div>
<div id="offer-two">
	
<div id="monthlymembership">
<h3>Standard Membership</h3>
<h4>only <span style="font-size:1.2em;">$57</span>/month</h4>

<?php if(pmpro_hasMembershipLevel('2'))
	{ ?> 
	<h5>This is your current level</h5>
	<?php } else{ ?>   
  	<a class="red signupbutton" style="left: 52px;" href="<?php echo pmpro_url("checkout", "?level=" . 2,"https")?>" title="">Get Started</a>
 	<?php } ?>
	

<ul style="padding-top:10px;">
<li>No long term commitment</li>
<li>Unlimited access to our entire library of training content.</li>
<li>Comprehensive exercise files and exams.</li>
<li>30 Day Money-back Guarantee.</li>
<li>Insane Customer Support - email, phone, chat - we go above and beyond to make sure you are learning.</li>
<li><span style="color:#0087C2;font-weight:bold;font-size:14px;">For a limited time: FREE 1 hour consultation with founder, Rohit Mitra</span></li>

</ul>
</div>
<div id="yearlymembership">
<h3>Standard<br> Membership</h3>
<h4 style="padding-bottom:0px;">only <span style="font-size:1.2em;">$34</span>/month</h4>
<center><p style="font-size:12px"><i>*Paid Once Annually*</i></p></center>
<?php if(pmpro_hasMembershipLevel('3'))
	{ ?> 
	<h5>This is your current level</h5>
	<?php } else{ ?>   
  	<a style="left: 52px;" class="red signupbutton" href="<?php echo pmpro_url("checkout", "?level=" . 3,"https")?>" title="">Get Started</a>
 	<?php } ?>
<ul style="padding-top:10px;">
<li>Over 40% off our standard price</li>
<li>Unlimited access to our entire library of training content.</li>
<li>Comprehensive exercise files and exams.</li>
<li>30 Day Money-back Guarantee.</li>
<li>Insane Customer Support - email, phone, chat - we go above and beyond to make sure you are learning.</li>
<li><span style="color:#0087C2;font-weight:bold;font-size:14px;">For a limited time: FREE 1 hour consultation with founder, Rohit Mitra</span></li>

</ul>
</div>

</div>

<div id="offer-three">
<h3>Enterprise<br> Membership</h3>
<h4 style="margin-bottom: 1px;">Multi-User Plan</h4>

  	<a class="red signupbutton" id="contact-user" href="#" title="">Contact Us!</a>
<ul style="padding-top:10px;">
<li>Get your entire team up to speed.</li>
<li>Deep Discounts for Multi-user accounts.</li>
<li>Unlimited access to our entire library of training material for your whole team.</li>
<li>Custom Content based on your needs.</li>
<li>Insane Customer Support - email, phone, chat - we go above and beyond to make sure you are learning.</li>

</ul>

</div>

					
			</div>	
				</div>
				<div id="pricingvalid">
					<p><i>
I've tried so many SolidWorks books, but there are always things lost in translation. With SolidWize you can see every click and get an explanation of each step of the process.</i> <br><span>-Andy, <i>UCLA R&D Shop</i></span>
</p></div>
				
			<!-- 	<div id="browse">
		<div id="browsecontent">
			<img src="http://solidwize.com/solidwize/wp-content/themes/Karma-Child-Theme/images/trainingvideos.png">
			<h4><a href="/training/">First time here? Browse our training videos.</a></h4>
			<p><a href="/training/">Take a look at our videos and see how we can help you stay competitive, and design better products.</a></p>
		</div> 
</div>  -->
			<div style="margin-top: 70px">
			
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
</div><!-- main-area -->
<?php get_footer();?>