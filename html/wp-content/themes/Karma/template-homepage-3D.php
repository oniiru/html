<?php
/*
Template Name: Homepage :: 3D
*/
?>
<?php get_header(); ?>

  <div class="home-flash-slider">
  <?php
  $slider_id = get_option('ka_cu3er_slider_id');
  $slider_output = '[CU3ER slider=\''.$slider_id.'\']';
  echo '<div id="CU3ER'.$slider_id.'" class="embedCU3ER">'.do_shortcode($slider_output).'</div><!-- end CU3ER -->';
  ?>
  </div><!-- end home-flash-slider -->
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div id="main">
<div class="main-area home-main-area flash-main-area">
<div class="main-holder home-holder">
<div class="content_full_width">
<?php if(have_posts()) : while(have_posts()) : the_post(); the_content(); truethemes_link_pages(); endwhile; endif; ?>
</div><!-- end content -->
</div><!-- end main-holder -->
</div><!-- main-area -->


<?php get_footer(); ?>