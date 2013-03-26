<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php truethemes_meta_hook();// action hook, see truethemes_framework/global/hooks.php ?>
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
<link href='<?php bloginfo('stylesheet_directory'); ?>/style2.css' rel='stylesheet' type='text/css'>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<?php wp_head(); ?>
<!--[if lte IE 8]>
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/lt8.css" media="screen"/>
<![endif]-->
<!-- <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery.howdydo-bar.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/inputfocus.js"></script>

<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.main.js"></script>

<!-- <script type="text/javascript">
jQuery(document).ready( function(){
			jQuery( '#howdy' ).howdyDo({
				action		: 'push',
				effect		: 'slide',
				keepState	: 'false',
				easing		: 'easeOutBounce',
				duration	: 500,
				closeAnchor	: '<img src="http://solidwize.com/wp-content/uploads/2012/01/close-16x16.png" border=0 />',
						openAnchor	: '<img src="http://swztest.solidwize.com/wp-content/uploads/2012/01/up.png" border=0 />',
				delay		:  7000,
				autostart   :  true,
			});
		});
	</script> -->
<script type="text/javascript">
			function footerPosition () {
			
				var bodyHeight = jQuery('#wrapper').outerHeight();
				var footerHeight = jQuery('#footer').outerHeight();
				
				if (bodyHeight<=jQuery(window).outerHeight()) 
					{
					jQuery('#footer').addClass('sticky');
					}
				else
					{
					jQuery('#footer.sticky').removeClass('sticky');
					}
				}
		</script>
 		<script type='text/javascript'>
//<![CDATA[
jQuery(document).ready(function(){
	function adjust_container_height(){
		//get the height of the current testimonial slide
		var hegt = jQuery(this).height();
		//set the container's height to that of the current slide
		jQuery(this).parent().animate({height:hegt});
	}
    jQuery('.testimonials').after('<div class="testimonial-pager">&nbsp;</div>').cycle({
		fx: 'fade',
		timeout: '9000',
		height: 'auto',
		pause: '0',
		pager: '.testimonial-pager',
		before: adjust_container_height,
		cleartypeNoBg: true

	});
});

//]]>
</script> 
		
</head>

<body <?php body_class(); ?>>
<script type="text/javascript">
		jQuery(document).ready(function() {
			footerPosition();
		});
		
		jQuery(window).resize(function() {
		  	footerPosition();
		});
	</script>
<div id="wrapper" <?php if (is_page_template('template-homepage-3D.php') || is_page_template('template-homepage-jquery-2.php')) {echo 'class="big-banner"';} ?>>
<div id="header" <?php if (is_page_template('template-homepage-3D.php')){echo "style='height: 560px;'";} ?>>

<?php
// retrieve values from site options panel
global $ttso;
$ka_sitelogo = $ttso->ka_sitelogo;
$ka_logo_icon = $ttso->ka_logo_icon;
$ka_logo_text = $ttso->ka_logo_text;
$ka_toolbar = $ttso->ka_toolbar;

// show the toolbar if selected by the user :
if ($ka_toolbar == "true"): 
?>
<div class="top-block">
<div class="top-holder">

  <?php truethemes_before_top_toolbar_menu_hook();// action hook, see truethemes_framework/global/hooks.php ?>
  <?php if(has_nav_menu('Top Toolbar Navigation')): ?>
  <div class="toolbar-left">  
  <ul>
  <?php wp_nav_menu(array('theme_location' => 'Top Toolbar Navigation' , 'depth' => 0 , 'container' =>false )); ?>
  </ul>
  </div><!-- end toolbar-left -->

  <?php
  //if top toolbar menu not set, we show dynamic sidebar  
  elseif(is_active_sidebar(1)): 
  ?>
  <div class="toolbar-left">  
  <ul><?php dynamic_sidebar("Toolbar - Left Side"); ?></ul>
  </div><!-- end toolbar-left -->
  <?php endif; ?>
  
  <?php
if ( !is_user_logged_in() ){

  if(is_active_sidebar(2)): ?>
  <div class="toolbar-right-index">
  <?php dynamic_sidebar("Toolbar - Right Side"); ?>
  </div><!-- end toolbar-right -->
  <?php endif; 
  }
  ?>

<?php truethemes_after_top_toolbar_menu_hook();// action hook, see truethemes_framework/global/hooks.php ?>
</div><!-- end top-holder -->
</div><!-- end top-block -->
<?php endif; //end if($toolbar == 'true') ?>



<?php truethemes_before_header_holder_hook();// action hook, see truethemes_framework/global/hooks.php ?>
<div class="header-holder-index">
<div class="header-area-index<?php if (is_search()) {echo ' search-header';} ?><?php if (is_404()) {echo ' error-header';} ?><?php if (is_page_template('template_sitemap.php')) {echo ' search-header';} ?>">

<?php // Website Logo
if ($ka_logo_text == ''){
?>
<a href="<?php echo home_url(); ?>" class="logo-index"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/bluelogo.png" alt="<?php bloginfo('name'); ?>" /></a>
<?php }else{?>
<a href="<?php echo home_url(); ?>" class="custom-logo"><img src="<?php echo get_template_directory_uri(); ?>/images/_global/<?php echo $ka_logo_icon; ?>" alt="<?php bloginfo('name'); ?>" /><span class="logo-text"><?php echo $ka_logo_text; echo '</span></a>';}?>
<div class="callus">Questions? Call us: 877.688.7563 <span style="margin: 0 5px;"> or</span> <a id="contact" title="Contact" href="#">email us.</a></div>
<div class="clearright"></div>
<div id="index-menu" class="<?php if (is_user_logged_in() ) print 'divnavlogged'; ?>">
	<ul>
		<li><a href="<?php bloginfo('siteurel'); ?>/pricing-2"> Pricing</a></li>
		<li><a href="<?php bloginfo('siteurel'); ?>/training">Training</a></li>
		<li><a href="<?php bloginfo('siteurel'); ?>/blog">Blog</a></li>
		<li><a href="<?php bloginfo('siteurel'); ?>/about">About</a></li>
		
	</ul>
</div>

<?php truethemes_before_primary_navigation_hook();// action hook, see truethemes_framework/global/hooks.php ?>
<?php if(has_nav_menu('Primary Navigation')): ?>

<?php endif; ?>

<?php truethemes_after_primary_navigation_hook();// action hook, see truethemes_framework/global/hooks.php ?>