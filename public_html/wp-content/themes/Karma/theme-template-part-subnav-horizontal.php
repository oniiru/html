<div id="horizontal_nav">
<?php wp_nav_menu(array('theme_location' => 'Primary Navigation' , 'depth' => 0 , 'container' =>false , 'walker' => new sub_nav_walker() )); ?>
</div><!-- end sub_nav -->