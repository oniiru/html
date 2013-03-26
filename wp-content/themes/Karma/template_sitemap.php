<?php
/*
Template Name: Sitemap
*/
?>
<?php get_header(); ?>
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div id="main">

<?php get_template_part('theme-template-part-tools','childtheme'); ?>

<div class="main-holder">
<div id="content" class="content_full_width">
<?php if(have_posts()) : while(have_posts()) : the_post(); the_content(); truethemes_link_pages(); endwhile; endif; ?>

<?php function five_get_ancestors(){
//get all ancestor pages, pages with no post parent from wordpress database

global $wpdb;
$result = $wpdb->get_results("SELECT ID, post_title, guid FROM $wpdb->posts WHERE post_type = 'page' AND post_parent = 0 AND post_status = 'publish'");
return $result;
}

function five_print_ancestors_with_child(){
//get ancestors
$result = five_get_ancestors();
	if($result){
		//loop result from above database query and list child pages of ancestors
		foreach ($result as $res){
		//prepare ancestor id for wp_list_pages
		$ancestor_id = $res->ID;
		//get all child and grand child of ancestor page
		$children = wp_list_pages("title_li=&child_of=$ancestor_id&echo=0");
			//if there is children
			if($children){
			echo '<div class="sitemap_with_child">';
			$link = get_permalink($ancestor_id);
			//this is the ancestors
			//echo "<a href='$res->guid'>$res->post_title</a><ul>";
			echo "<a href='$link'>$res->post_title</a><ul>";
			//this is the children
			echo $children;
			echo '</ul></div>';
			}
		}
	}
}



function five_print_ancestors_without_child(){
//get ancestors
$result = five_get_ancestors();
	if($result){
		//prepare div to contain all pages without children
		echo '<div class="sitemap_without_child">';
		echo '<ul>';
		//loop result from above database query and list child pages of ancestors
		foreach ($result as $res){
		//prepare ancestor id for wp_list_pages
		$ancestor_id = $res->ID;
		//check if got any child and grand child of this ancestor page
		$children = wp_list_pages("title_li=&child_of=$ancestor_id&echo=0");
			//if there is no children
			if(!$children){
				//this is the ancestors only
			$link = get_permalink($ancestor_id);	
			//echo "<li><a href='$res->guid'>$res->post_title</a></li>";
			echo "<li><a href='$link'>$res->post_title</a></li>";			
			}
		}
		echo '</ul>';
		echo '</div>';
	}
}
//print out results, all no child first in one div, 
//than follow by those with children in their own div container.
five_print_ancestors_without_child();
five_print_ancestors_with_child();
?>




</div><!-- end content -->
</div><!-- end main-holder -->
</div><!-- main-area -->



<?php get_footer(); ?>