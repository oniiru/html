<?php
add_action( 'wp_head', array('MixPanel','insert_tracker' ));
add_action( 'wp_footer', array('MixPanel','insert_event' ));



class MixPanel {

  /**
   * Gets the value of the key mixpanel_event_label for this specific Post
   * 
   * @return string The value of the meta box set on the page
   */
  static function get_post_event_label()
  {
    global $post;
	
    return get_post_meta( $post->ID, 'mixpanel_event_label', true );
  }

  /**
   * Inserts the value for the mixpanel.track() API Call
   * @return boolean technically this should be html.. 
   */
  function insert_event()
  {
    $event_label = self::get_post_event_label(); 
    $settings = (array) get_option( 'mixpanel_settings' );

    if(!isset($settings['token_id'])) {
      self::no_mixpanel_token_found();
      return false;  
    }
    echo "<script type='text/javascript'>
	     mixpanel.register_once({ 'first_referrer': document.referrer });
       mixpanel.track(\"WP $event_label\", {'referrer': document.referrer });
    </script> "; 



    return true; 
  }


  /**
   * Adds the Javascript necessary to start tracking via MixPanel. 
   * this gets added to the <head> section usually. 
   *
   * @return [type] [description]
   */
  function insert_tracker()
  {
    $settings = (array) get_option( 'mixpanel_settings' );
    if(!isset($settings['token_id'])) {
      self::no_mixpanel_token_found();
      return false;  
    }

    require_once dirname(__FILE__) . '/mixpaneljs.php';  
  

    return true;  
  }

  static function no_mixpanel_token_found()
  {
    echo "<!-- No MixPanel Token Defined -->"; 
  }

}


?>
