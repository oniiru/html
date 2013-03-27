<div id="footer">
<?php truethemes_begin_footer_hook()// action hook, see truethemes_framework/global/hooks.php ?>
<?php
add_filter('pre_get_posts','wploop_exclude');
$footer_layout = get_option('ka_footer_layout');
$ka_footer_columns = get_option('ka_footer_columns');
$ka_scrolltoplink = get_option('ka_scrolltoplink');
$ka_scrolltoptext = get_option('ka_scrolltoplinktext');

if (($footer_layout == "full_bottom") || ($footer_layout == "full")){ ?>
<div class="footer-area">
<div class="footer-wrapper">
<div class="footer-holder">

<?php $footer_columns = range(1,$ka_footer_columns);$footer_count = 1;$sidebar = 6;
foreach ($footer_columns as $footer => $column){
$class = ($ka_footer_columns == 1) ? '' : '';
$class = ($ka_footer_columns == 2) ? 'one_half' : $class;
$class = ($ka_footer_columns == 3) ? 'one_third' : $class;
$class = ($ka_footer_columns == 4) ? 'one_fourth' : $class;
$class = ($ka_footer_columns == 5) ? 'one_fifth' : $class;
$class = ($ka_footer_columns == 6) ? 'one_sixth' : $class; 
$lastclass = (($footer_count == $ka_footer_columns) && ($ka_footer_columns != 1)) ? '_last': '';
?><div class="<?php echo $class.$lastclass; ?>"><?php dynamic_sidebar($sidebar) ?></div><?php $footer_count++; $sidebar++; } ?>


</div><!-- footer-holder -->
</div><!-- end footer-wrapper -->
</div><!-- end footer-area -->
<?php } else {echo '<br />';} ?>
</div><!-- end footer -->


<?php if (($footer_layout == "full_bottom") || ($footer_layout == "bottom")){ ?>
<div id="footer_bottom">
  <div class="info">
      <div id="foot_left">&nbsp;<?php truethemes_begin_footer_left_hook()// action hook, see truethemes_framework/global/hooks.php ?><?php dynamic_sidebar("Footer Copyright - Left Side"); ?></div><!-- end foot_left -->
      <div id="foot_right"><?php if ($ka_scrolltoplink == "true"){ echo '<div class="top-footer"><a href="#" class="link-top">'.$ka_scrolltoptext.'</a></div>'; }?>


<?php // Check to see if user has footer menu set, if so display it 

if(has_nav_menu('Footer Navigation')): ?>
<ul>
<?php wp_nav_menu(array('theme_location' => 'Footer Navigation' , 'depth' => 0 , 'container' =>false)); ?>
</ul>
      
		<?php elseif(is_active_sidebar(13)): ?>
      		<ul><?php dynamic_sidebar("Footer Menu - Right Side"); ?></ul>
		<?php endif; ?>

        <?php truethemes_end_footer_right_hook()// action hook, see truethemes_framework/global/hooks.php ?>
            
      </div><!-- end foot_right -->
  </div><!-- end info -->
</div><!-- end footer_bottom -->
<?php } ?>


</div><!-- end main -->
</div><!-- end wrapper -->
<?php 
//codes to load scripts has been moved to truethemes_framework/global/javascript.php
wp_footer();
?>
</body>
</html>