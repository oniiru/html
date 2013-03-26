<?php

if( !class_exists( 'Dl_Wp_Post_Helper' ) ) {

	class Dl_Wp_Post_Helper {

		function updatePage($title, $slug, $content="This text will never be shown. You shouldn't edit it") {
		
		    global $wpdb;
		
			if(!$slug) {
				$slug = $this->generateSlug($title);
			}
			
		    $the_page = get_page_by_title( $title );
		
		    if ( ! $the_page ) {
		
		        // Create new post object
		        $_p = array();
		        $_p['post_title'] = $title;
		        $_p['post_content'] = $content;
		        $_p['post_status'] = 'publish';
		        $_p['post_type'] = 'page';
		        $_p['comment_status'] = 'closed';
		        $_p['ping_status'] = 'closed';
		        $_p['post_category'] = array(1); // the default 'Uncatagorised'
		
		        // Insert the post into the database
		        $the_page_id = wp_insert_post( $_p );
		        
		        return true;
		    }
		    return false;
		}
		
		function deletePage($id) {
		
		    global $wpdb;
			$the_page = get_page_by_title($id);

			if( !$the_page ) {
				$the_page = get_page($id);
			}
			if( !$the_page ) {
				return false;
			}
		    
		    wp_delete_post( $the_page->ID, true); // this will trash, not delete
		
		}
		
		function generateSlug($phrase, $maxLength=50) {
		    $result = strtolower($phrase);
		
		    $result = preg_replace("/[^a-z0-9\s-]/", "", $result);
		    $result = trim(preg_replace("/[\s-]+/", " ", $result));
		    $result = trim(substr($result, 0, $maxLength));
		    $result = preg_replace("/\s/", "-", $result);
		
		    return $result;
		}
	}

}
?>