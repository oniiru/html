<?php


//These shortcodes are deprecated and they are left for backward compatibility,
//Please do not use them.



 

/* ----- SHADOW IMAGE FRAME ----- */
function karma_shadow_frame($atts, $content = null) {
  extract(shortcode_atts(array(
  'size' => 'shadow_',
  'image_path' => '',
  'description' => '',
  'link_to_page' => '',
  'target' => '',
  ), $atts));
  
 
 /* fullsize banner */ 
 if ($size == 'shadow_banner_full' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='banner_full']");
	  }

	  
elseif ($size == 'shadow_banner_full' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='banner_full']");
	  }
		  
		  




		  
 /* regular banner */ 
 if ($size == 'shadow_banner_regular' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='banner_regular']");
	}
	  
	
elseif ($size == 'shadow_banner_regular' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='banner_regular']");
	}
		
		




		  
 /* half banner */ 
 if ($size == 'shadow_banner_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='banner_small']");
	}

	
elseif ($size == 'shadow_banner_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='banner_small']");
	}	  






		  
 /* two_col_large */ 
elseif ($size == 'shadow_two_col_large' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='two_col_large']");
	}

	
elseif ($size == 'shadow_two_col_large' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='two_col_large']");
	}







  
/* two_col_small */ 
elseif ($size == 'shadow_two_col_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='two_col_small']");
	}


elseif ($size == 'shadow_two_col_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='two_col_small']");
	}


	
	
	
	
	  
	  
/* three_col_large */ 
elseif ($size == 'shadow_three_col_large' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='three_col_large']");
	}

	
elseif ($size == 'shadow_three_col_large' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='three_col_large']");
	}
	  





	  
/* three_col_small */ 
elseif ($size == 'shadow_three_col_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='three_col_small']");
	}

	
elseif ($size == 'shadow_three_col_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='three_col_small']");
	}





/* four_col_large */
elseif ($size == 'shadow_four_col_large' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='four_col_large']");
	}

	
elseif ($size == 'shadow_four_col_large' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='four_col_large']");
	}
	




		  



		  
		  
/* four_col_small */
elseif ($size == 'shadow_four_col_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='four_col_small']");
	}

	
elseif ($size == 'shadow_four_col_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='shadow' image_path='$image_path' target='$target' description='$description' size='four_col_small']");
	}
		  		  

  return $output;
}
add_shortcode('shadowframe', 'karma_shadow_frame');


















/* ----- MODERN IMAGE FRAME ----- */
function karma_modern_frame($atts, $content = null) {
  extract(shortcode_atts(array(
  'size' => '',
  'image_path' => '',
  'description' => '',
  'link_to_page' => '',
  'target' => '',
  ), $atts));
  
 
 /* fullsize banner */ 
 if ($size == 'modern_banner_full' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='banner_full']");
	  }

	  
elseif ($size == 'modern_banner_full' && $link_to_page == ''){
	$output =  do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='banner_full']");
	  }
		  
		  



	  
 /* regular banner */ 
 if ($size == 'modern_banner_regular' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='banner_regular']");
	}
	  
	
elseif ($size == 'modern_banner_regular' && $link_to_page == ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='banner_regular']");
	}
		
		





		  
 /* half banner */ 
 if ($size == 'modern_banner_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='banner_small']");
	}

	
elseif ($size == 'modern_banner_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='banner_small']");
	}	  







		  
 /* two_col_large */ 
elseif ($size == 'modern_two_col_large' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='two_col_large']");
	}

	
elseif ($size == 'modern_two_col_large' && $link_to_page == ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='two_col_large']");
	}








  
/* two_col_small */ 
elseif ($size == 'modern_two_col_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='two_col_small']");
	}



elseif ($size == 'modern_two_col_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='two_col_small']");
	}


	



	  
	  
/* three_col_large */ 
elseif ($size == 'modern_three_col_large' && $link_to_page != ''){
	$output =  do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='three_col_large']");
	}
	
	
elseif ($size == 'modern_three_col_large' && $link_to_page == ''){
		$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='three_col_large']");
	}
	  



	  
/* three_col_small */ 
elseif ($size == 'modern_three_col_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='three_col_small']");
	}
	
	
	
elseif ($size == 'modern_three_col_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='three_col_small']");
	}





/* four_col_large */
elseif ($size == 'modern_four_col_large' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='four_col_large']");
	}
	
	
	
elseif ($size == 'modern_four_col_large' && $link_to_page == ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='four_col_large']");
	}
		  



		  
		  
/* four_col_small */
elseif ($size == 'modern_four_col_small' && $link_to_page != ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' link_to_page='$link_to_page' target='$target' description='$description' size='four_col_small']");
	}

	
elseif ($size == 'modern_four_col_small' && $link_to_page == ''){
	$output = do_shortcode("[frame style='modern' image_path='$image_path' target='$target' description='$description' size='four_col_small']");
	}
		  		  

  return $output;
}
add_shortcode('modernframe', 'karma_modern_frame');







/* =================================== */
// NOTIFY BOXES 
/* =================================== */
function karma_green_callout( $atts, $content = null ) {
   return '[raw]<p class="message_green">' . do_shortcode($content) . '</p><br class="clear" />[/raw]';
}
add_shortcode('green_callout', 'karma_green_callout');


function karma_blue_callout( $atts, $content = null ) {
   return '[raw]<p class="message_blue">' . do_shortcode($content) . '</p><br class="clear" />[/raw]';
}
add_shortcode('blue_callout', 'karma_blue_callout');


function karma_red_callout( $atts, $content = null ) {
   return '[raw]<p class="message_red">' . do_shortcode($content) . '</p><br class="clear" />[/raw]';
}
add_shortcode('red_callout', 'karma_red_callout');


function karma_yellow_callout( $atts, $content = null ) {
   return '[raw]<p class="message_yellow">' . do_shortcode($content) . '</p><br class="clear" />[/raw]';
}
add_shortcode('yellow_callout', 'karma_yellow_callout');

?>