<?php

// Filter the content if it is marked as premium
//
function dl_ps_filter_post( $post ) {

	// Don't filter admin pages
	//
	if( is_admin() ) {
		return $post;
	}

	// Don't filter twice.
	//
	if( $post->filtered ) {
		return $post;
	}
	$post->filtered = true;

	// Assume that this is not premium content and
	//	the user has access.
	$is_premium = false;
	$is_subscribed = true;

	// Get the post's meta data
	//
	$key = DlPs_Options::PostMetaKey();
	$post_level_ids = get_post_meta( $post->ID, $key, true );
	if( is_array( $post_level_ids ) && count( $post_level_ids ) > 0 ) {

		// This is premium content.
		//
		$is_premium = true;
		$is_subscribed = false;

		// Get the user's data
		//
		$wp_user = wp_get_current_user();
		$user = new DlPs_User( $wp_user );
		$user_level_id = $user->level_id;

		// Ensure the user is not expired.
		//
		if($user->expiration > mktime() ) {

			// Unlock if this post has this user's level.
			//
			if( in_array( $user_level_id, $post_level_ids ) ) {
				$is_subscribed = true;
			}
		} else {

			// This user is expired.
			//

			// TODO:
		}

	}

	$post->dlps_is_premium = $is_premium;
	$post->dlps_is_subscribed = $is_subscribed;
	$post->dlps_hide = false;

	if( $is_premium ) {

		// This is premium content.
		//
		if( $is_subscribed ) {

			// This user can access this content.
			//
			if( DlPs_Options::IsLockIconVisible() ) {
				$post->post_title = "<span class='unlocked'>" . $post->post_title . "</span>";
			}
		} 
		else {

			// This user cannot access this content.
			//
			$no_access_html = DlPs_Options::GetNoAccessHtml();
			if( $no_access_html != "" ) {

				// Substitute the body and show the lock.
				//
				if( DlPs_Options::IsLockIconVisible() ) {
					$post->post_title = "<span class='locked'>" . $post->post_title . "</span>";
				}
				$post->post_content = $no_access_html;
				$post->comment_status = 'registered_only';

			} else {

				// User wants to hide the content all together (no title or body)
				//
				$post->dlps_hide = true;
			}
		}
	}

	return $post;

}
function dl_ps_filter_title( $title, $id ) {

	global $post;

	if( $post->ID == $id ) {

		dl_ps_filter_post( $post );

		return $post->post_title;
	}
	return $title;
}
add_filter( 'the_title', 'dl_ps_filter_title', 10, 2 );

function dl_ps_filter_content( $content ) {
	global $post;
	
	if( $post->ID == $id ) {

		dl_ps_filter_post( $post );

		return $post->post_content;
	}

	return $content;
}
add_filter( 'the_content', 'dl_ps_filter_content' );

function dl_ps_filter_posts( $posts ) {

	$result = array();
	foreach( $posts as $post ) {

		dl_ps_filter_post( $post );

		if( !$post->dlps_hide ) {
			$result[] = $post;
		}
	}	

	return $result;
}
add_filter( 'the_posts', 'dl_ps_filter_posts' );
add_filter( 'get_pages', 'dl_ps_filter_posts' );

?>