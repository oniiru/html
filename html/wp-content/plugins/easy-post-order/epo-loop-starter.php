<?php
	/** 
	* EPO Loop Starter
	* 
	* This is the default template from which you may create custom loop templates.
	* Save your custom loop as epo-loop-YOURLOOPNAME.php in the current theme folder.
	*/

if ( $my_query->have_posts() ) : 
	while ( $my_query->have_posts() ) :	$my_query->the_post();
	
	/* DO STUFF WITHIN THE LOOP */
	
	endwhile;
endif;
?>