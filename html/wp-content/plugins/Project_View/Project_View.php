<?php
/*
Plugin Name: Projects
Plugin URI: http://solidwize.com
Description: Creates the Projects Custom Post Types
Version: 1.0
Author: Andrew O'Neal
Author URI: http://SolidWize.com
License: GPLv2
*/


add_action( 'init', 'create_project_view' );


function create_project_view() {
register_post_type( 'project_views',
array(
'labels' => array(
'name' => 'Projects',
'singular_name' => 'Project',
'add_new' => 'Add New',
'add_new_item' => 'Add New Project',
'edit' => 'Edit',
'edit_item' => 'Edit Project',
'new_item' => 'New Project',
'view' => 'View',
'view_item' => 'View Project',
'search_items' => 'Search Projects',
'not_found' => 'No Projects found',
'not_found_in_trash' =>
'No Projects found in Trash',
'parent' => 'Parent Project'
),
'public' => true,
'rewrite' => array( 'slug' => 'courses' ),

'menu_position' => 5,
'supports' =>
array( 'title', 'editor', 'comments',
'thumbnail',  ),
'taxonomies' => array( '' ),
'has_archive' => false
)
);
}
add_action( 'admin_init', 'projects_admin' );

function projects_admin() {
    add_meta_box( 'projects_appendix_meta_box',
        'Appendix Video List',
        'display_projects_appendix_meta_box',
        'project_views', 'normal', 'high'
    );
}



function display_projects_appendix_meta_box( $project_view ) {
    // Retrieve current name of the Director and Movie Rating based on review ID
    $Project_appendix1 = 
		esc_html( get_post_meta( $project_view->ID, 'Project_appendix1', true ) );
    $Project_appendix2 = 
		esc_html( get_post_meta( $project_view->ID, 'Project_appendix2', true ) );
    $Project_appendix3 = 
		esc_html( get_post_meta( $project_view->ID, 'Project_appendix3', true ) );
    $Project_appendix1_title = 
		esc_html( get_post_meta( $project_view->ID, 'Project_appendix1_title', true ) );
    $Project_appendix2_title = 
		esc_html( get_post_meta( $project_view->ID, 'Project_appendix2_title', true ) );
    $Project_appendix3_title = 
		esc_html( get_post_meta( $project_view->ID, 'Project_appendix3_title', true ) );
    ?>
	<table>
	<tr>
		<td style="width: 49%">
		 <input type="text" style="width:400px;" placeholder="Appendix 1 Title" name="project_appendix_1_title" value="<?php echo $Project_appendix1_title; ?>" />
		</td>
	<td style="width: 49%">
	 <input type="text" style="width:400px;" placeholder="Appendix 1 (shortcode)" name="project_appendix_1" value="<?php echo $Project_appendix1; ?>" />
	</td>
	</tr>
	<tr>
		<td style="width: 49%">
		 <input type="text" style="width:400px;" placeholder="Appendix 1 Title" name="project_appendix_2_title" value="<?php echo $Project_appendix2_title; ?>" />
		</td>
	<td style="width: 49%">
	 <input type="text" style="width:400px;" placeholder="Appendix 2 (shortcode)" name="project_appendix_2" value="<?php echo $Project_appendix2; ?>" />
	</td>
	</tr>
	<tr>
		<td style="width: 49%">
		 <input type="text" style="width:400px;" placeholder="Appendix 2 Title" name="project_appendix_3_title" value="<?php echo $Project_appendix3_title; ?>" />
		</td>
	<td style="width: 49%">
	 <input type="text" style="width:400px;" placeholder="Appendix 3 (shortcode)" name="project_appendix_3" value="<?php echo $Project_appendix3; ?>" />
	</td>
	</tr>
	</table>
   
    <?php
}

add_action( 'save_post', 'add_projects_appendix_fields', 10, 2 );

function add_projects_appendix_fields( $project_view_id, $project_view ) {
	// Check post type for movie reviews
	if ( $project_view->post_type == 'project_views' ) {
	// Store data in post meta table if present in post data
	if ( isset( $_POST['project_appendix_1'] ) ) {
	update_post_meta( $project_view_id, 'Project_appendix1',
	$_POST['project_appendix_1'] );
	}
	if ( isset( $_POST['project_appendix_2'] )  ) {
	update_post_meta( $project_view_id, 'Project_appendix2',
	$_POST['project_appendix_2'] );
	}
	if ( isset( $_POST['project_appendix_3'] ) ) {
	update_post_meta( $project_view_id, 'Project_appendix3',
	$_POST['project_appendix_3'] );
	}
	if ( isset( $_POST['project_appendix_1_title'] ) ) {
	update_post_meta( $project_view_id, 'Project_appendix1_title',
	$_POST['project_appendix_1_title'] );
	}
	if ( isset( $_POST['project_appendix_2_title'] ) ) {
	update_post_meta( $project_view_id, 'Project_appendix2_title',
	$_POST['project_appendix_2_title'] );
	}
	if ( isset( $_POST['project_appendix_3_title'] ) ) {
	update_post_meta( $project_view_id, 'Project_appendix3_title',
	$_POST['project_appendix_3_title'] );
	}
	}
	}
  
	
add_filter( 'template_include', 'include_template_function', 1 );

function include_template_function( $template_path ) {
    if ( get_post_type() == 'project_views' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'project_template.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/project_template.php';
            }
        }
    }
    return $template_path;
}

	
?>