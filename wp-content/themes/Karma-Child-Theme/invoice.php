<?php
/*Template Name: invoice*/
?>

<?php get_header();?>
</div><!-- header-area --></div><!-- end rays --></div><!-- end header-holder --></div><!-- end header -->
<?php truethemes_before_main_hook();
	// action hook, see truethemes_framework/global/hooks.php
?>

<div id="main">
<div class="main-area" style="padding-top: 0px !important;">	<div class="main-holder">
		<div id="content" class="content_full_width">
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
</div><!-- main-area -->

</body>
</html>