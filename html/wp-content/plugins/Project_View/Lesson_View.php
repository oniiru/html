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
'menu_position' => 5,
'supports' =>
array( 'title', 'editor', 'comments',
'thumbnail',  ),
'taxonomies' => array( '' ),
'has_archive' => false
)
);
}

?>