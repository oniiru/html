<?php
 /*
 Template Name: plain page
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="MSSmartTagsPreventParsing" content="true" />
  <meta http-equiv="Imagetoolbar" content="No" />
  <title>My Form Title</title>
  <style type="text/css">
  	body {font-size:13px;}
  </style>
  <link rel='stylesheet' id='gforms_css-css'  href='<?php bloginfo('url'); ?>/wp-content/plugins/gravityforms/css/forms.css?ver=3.0.1' type='text/css' media='all' />
  <script type='text/javascript' src='<?php bloginfo('stylesheet_directory'); ?>/includes/js/jquery-142.js?ver=1.4.1'></script>
  <script type='text/javascript' src='<?php bloginfo('url'); ?>/wp-content/plugins/gravityforms/js/conditional_logic.js?ver=1.3.13.1'></script>
  <?php wp_head(); ?>
	<script>
	jQuery(document).ready(function(){
	jQuery('.gform_button').addClass('btn btn-success');
	jQuery('a[title="contactform"]').attr("data-toggle", "modal");
});


</script>
  <style>
  body {
	  padding-top:0px !important;
  }
  </style>
  </head>
<body>
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <?php the_content('read more'); ?>
  <?php endwhile; endif; ?>
  <?php wp_footer(); ?>
  
	
</body>
</html>