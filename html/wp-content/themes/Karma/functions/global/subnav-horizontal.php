<?php if (function_exists('wp_nav_menu')) {	
echo '<div id="horizontal_nav">';
wp_nav_menu( array(
 'container' =>false,
 'theme_location' => 'Primary Navigation',
 'sort_column' => 'menu_order',
 'menu_class' => '',
 'echo' => true,
 'before' => '',
 'after' => '',
 'link_before' => '',
 'link_after' => '',
 'depth' => 0,
 'walker' => new sub_nav_walker())
 );
echo '</div><!-- end sub_nav -->';} ?>