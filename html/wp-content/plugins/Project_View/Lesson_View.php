<?php
/*
Plugin Name: Lessons
Plugin URI: http://solidwize.com
Description: Creates the Lessons Custom Post Types
Version: 1.0
Author: Andrew O'Neal
Author URI: http://SolidWize.com
License: GPLv2
*/


add_action( 'init', 'create_lesson_view' );


function create_lesson_view() {
register_post_type( 'lesson_views',
array(
'labels' => array(
'name' => 'Lessons',
'singular_name' => 'Lesson',
'add_new' => 'Add New',
'add_new_item' => 'Add New Lesson',
'edit' => 'Edit',
'edit_item' => 'Edit Lesson',
'new_item' => 'New Lesson',
'view' => 'View',
'view_item' => 'View Lesson',
'search_items' => 'Search Lessons',
'not_found' => 'No Lessons found',
'not_found_in_trash' =>
'No Lessons found in Trash',
'parent' => 'Parent Lesson'
),
'public' => true,
'rewrite' => array( 'slug' => 'lessons' ),

'menu_position' => 5,
'supports' =>
array( 'title', 'editor', 'comments',
'thumbnail',  ),
'taxonomies' => array( '' ),
'has_archive' => false
)
);
}
	
add_filter( 'template_include', 'include_template_function_lesson', 1 );

function include_template_function_lesson( $template_path ) {
    if ( get_post_type() == 'lesson_views' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'lesson_template.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/lesson_template.php';
            }
        }
    }
    return $template_path;
}

	
?>