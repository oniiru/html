<?php

// Meta box
//
add_action( 'add_meta_boxes', 'dl_ps_add_meta_box' );
function dl_ps_add_meta_box() {
	add_meta_box( 	'dl_ps_meta', 
					'Dig Labs Premium Subscribers', 
					'dl_ps_meta_box_function', 
					'post', 
					'side', 
					'high' );
	add_meta_box( 	'dl_ps_meta', 
					'Dig Labs Premium Content', 
					'dl_ps_meta_box_function', 
					'page', 
					'side', 
					'high' );
}
function dl_ps_meta_box_function( $post ) {

	$levels = DlPs_Level::All();

	if( count( $levels ) === 0 ) {

		// No subscriptions are set
		//
		echo "Use the admin panel to create subscriptions.";

	} else {

		// Get the level post meta
		//
		$key = DlPs_Options::PostMetaKey();
		$post_levels = get_post_meta( $post->ID, $key, true );
		if( !is_array( $post_levels ) ) {
			$post_levels = array();
		}

		foreach( $levels as $index => $level ) {

			// Retrieve the metadata values if they exist
			//
			$id = 'dlps_level_' . $level->id;
			$is_set = in_array( $level->id, $post_levels ) ? true : false;
			$checked = $is_set ? " checked=checked" : "";
			echo "<input id='$id' type='checkbox'$checked name='dlps_levels[]' value='$level->id' />&nbsp;";
			echo "<label for='$id'>$level->name</label>&nbsp;&nbsp;"; 

		}
		echo "<p>To limit access, select all levels that apply. Uncheck all for to make public.</p>";
	}

}
add_action( 'save_post', 'dl_ps_save_meta' );
function dl_ps_save_meta( $post_id ) {

	$post_levels = $_POST[ 'dlps_levels' ];
	if( !is_array( $post_levels ) ) {
		$post_levels = array();
	}

	$key = DlPs_Options::PostMetaKey();
	delete_post_meta( $post_id, $key );
	update_post_meta( $post_id, $key, $post_levels );
}

?>