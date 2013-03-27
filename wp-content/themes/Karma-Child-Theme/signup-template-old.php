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
				<h1>Learn SolidWorks Today!</h1>	
				<h2>7 day risk-free trial. Cancel at any time. </h2>
					
					
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
<h3>Monthly Membership</h3>
<h4>only <span style="font-size:1.2em;">$57</span>/month</h4>

<?php if(pmpro_hasMembershipLevel('2'))
	{ ?> 
	<h5>This is your current level</h5>
	<?php } else{ ?>   
  	<a class="red signupbutton" href="<?php echo pmpro_url("checkout", "?level=" . 2,"https")?>" title="">Get Started</a>
 	<?php } ?>
	

<ul style="padding-top:10px;">
<li>No long term commitment</li>
<li>Unlimited access to our entire library of over 150 SolidWorks tutorials, with new videos added weekly</li>
<li>Comprehensive exercise files</li>
<li>30 Day Money-back Guarantee</li>
<li>Insane Customer Support - email, phone, chat - we go above and beyond to make sure you are learning.</li>
<li><span style="color:#0087C2;font-weight:bold;font-size:14px;">For a limited time: FREE 1 hour consultation with founder, Rohit Mitra</span></li>

</ul>

</div>

<div id="offer-three">
<h3>Yearly<br> Membership</h3>
<h4>only <span style="font-size:1.2em;">$407</span>/year</h4>
<?php if(pmpro_hasMembershipLevel('3'))
	{ ?> 
	<h5>This is your current level</h5>
	<?php } else{ ?>   
  	<a class="red signupbutton" href="<?php echo pmpro_url("checkout", "?level=" . 3,"https")?>" title="">Get Started</a>
 	<?php } ?>
<ul style="padding-top:10px;">
<li>Over 40% off our standard price</li>
<li>Unlimited access to our entire library of over 150 SolidWorks tutorials, with new videos added weekly</li>
<li>Comprehensive exercise files</li>
<li>30 Day Money-back Guarantee</li>
<li>Insane Customer Support - email, phone, chat - we go above and beyond to make sure you are learning.</li>
<li><span style="color:#0087C2;font-weight:bold;font-size:14px;">For a limited time: FREE 1 hour consultation with founder, Rohit Mitra</span></li>

</ul>

</div>

					
			</div>	
				</div>
				<div id="pricingvalid">
					<p><i>
I've tried so many SolidWorks books, but there are always things lost in translation. With SolidWize you can see every click and get an explanation of each step of the process.</i> <br><span>-Andy, <i>UCLA R&D Shop</i></span>
</p></div>
				
				<!-- <div id="browse">
		<div id="browsecontent">
			<img src="http://solidwize.com/solidwize/wp-content/themes/Karma-Child-Theme/images/trainingvideos.png">
			<h4><a href="/training/">First time here? Browse our training videos.</a></h4>
			<p><a href="/training/">Take a look at our videos and see how we can help you stay competitive, and design better products.</a></p>
		</div> 
</div>  -->
			<div>
			
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
<!-- begin olark code --><script data-cfasync="false" type='text/javascript'>/*{literal}<![CDATA[*/
window.olark||(function(c){var f=window,d=document,l=f.location.protocol=="https:"?"https:":"http:",z=c.name,r="load";var nt=function(){f[z]=function(){(a.s=a.s||[]).push(arguments)};var a=f[z]._={},q=c.methods.length;while(q--){(function(n){f[z][n]=function(){f[z]("call",n,arguments)}})(c.methods[q])}a.l=c.loader;a.i=nt;a.p={0:+new Date};a.P=function(u){a.p[u]=new Date-a.p[0]};function s(){a.P(r);f[z](r)}f.addEventListener?f.addEventListener(r,s,false):f.attachEvent("on"+r,s);var ld=function(){function p(hd){hd="head";return["<",hd,"></",hd,"><",i,' onl' + 'oad="var d=',g,";d.getElementsByTagName('head')[0].",j,"(d.",h,"('script')).",k,"='",l,"//",a.l,"'",'"',"></",i,">"].join("")}var i="body",m=d[i];if(!m){return setTimeout(ld,100)}a.P(1);var j="appendChild",h="createElement",k="src",n=d[h]("div"),v=n[j](d[h](z)),b=d[h]("iframe"),g="document",e="domain",o;n.style.display="none";m.insertBefore(n,m.firstChild).id=z;b.frameBorder="0";b.id=z+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){b.src="javascript:false"}b.allowTransparency="true";v[j](b);try{b.contentWindow[g].open()}catch(w){c[e]=d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{var t=b.contentWindow[g];t.write(p());t.close()}catch(x){b[k]=o+'d.write("'+p().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}a.P(2)};ld()};nt()})({loader: "static.olark.com/jsclient/loader0.js",name:"olark",methods:["configure","extend","declare","identify"]});
/* custom configuration goes here (www.olark.com/documentation) */
olark.identify('4207-385-10-5356');/*]]>{/literal}*/</script><noscript><a href="https://www.olark.com/site/4207-385-10-5356/contact" title="Contact us" target="_blank">Questions? Feedback?</a> powered by <a href="http://www.olark.com?welcome" title="Olark live chat software">Olark live chat software</a></noscript><!-- end olark code -->
<?php get_footer();?>