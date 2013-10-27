<?php
function karma_scripts(){
wp_deregister_script('comment-reply');
wp_register_script( 'comment-reply', site_url().'/wp-includes/js/comment-reply.js',$deps=null,'1.0',$in_footer = true);

	 if (!is_admin())
	 {
		wp_enqueue_script( 'jquery', KARMA_JS .'/jquery-1.4.2.min.js', array('jquery'));
		wp_enqueue_script( 'karma-custom', KARMA_JS .'/karma.js', array('jquery'),'1.0');
		wp_enqueue_script( 'comment-reply', home_url().'/wp-includes/js/comment-reply.js',$deps=null,'1.0',$in_footer = true);
		}
}
add_action('init', 'karma_scripts',100);
?>