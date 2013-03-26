<?php
function wp_aff_creatives_view()
{
	$output .= wp_aff_view_get_navbar();
	$output .= '<div id="wp_aff_inside">';	
	$output .= wp_aff_show_creatives();
	$output .= '</div>';
	$output .= wp_aff_view_get_footer();
	return $output;
}

function wp_aff_show_creatives()
{	
	global $wpdb;
	$aff_banners_table =  WP_AFF_BANNERS_TBL_NAME;    	 	
	$resultset = $wpdb->get_results("select * from $aff_banners_table ORDER BY name asc",OBJECT); 
	
	$output .= '<div id="subnav">';
	$output .= '<li><a href="'.wp_aff_view_get_url_with_separator("ads").'">'.AFF_NAV_BANNERS.'</a></li>';
	$output .= '<li><a href="'.wp_aff_view_get_url_with_separator("creatives").'">'.AFF_NAV_CREATIVES.'</a></li></div>';
	$output .= '<div style="clear:both;"></div><br />';
	
	$output .= "<h3 class='wp_aff_title'>".AFF_B_CREATIVE_PAGE_TITLE."</h3>";
	$output .= "<p style='text-align:left;'>".AFF_B_CREATIVE_PAGE_MESSAGE."</p>";
	
	if ($resultset) 
	{
		$output .= '
		<table width="100%" id="gallery">
		<thead><tr>
		<th scope="col" class="tableheader">'.AFF_C_NAME.'</th>
		<th scope="col" class="tableheader">'.AFF_B_CODE.'</th>
		</tr></thead>
		<tbody>';
		    
	    foreach ($resultset as $resultset) 
	    {
	    	if($resultset->creative_type =="3")
	    	{ 	
	    		$ad_code = str_replace("xxxx",$_SESSION['user_id'],$resultset->description);
	    		$ad_code = str_replace("XXXX",$_SESSION['user_id'],$ad_code);
				$output .= '<tr>';
				$output .= '<td class="creatives_col_1"><strong>'.$resultset->name.'</strong></td>';
		        $output .= '<td class="creatives_col_2"><textarea rows=5 class="creatives_code_area">';
		      	$output .= $ad_code;
		      	$output .= "</textarea></td>";				
				$output .= '</tr>';
	    	}
	    }
		$output .= '</tbody>
		</table>';
	}
	else
	{
		$output .= "<p class='message'>".AFF_B_NO_CREATIVE."</p>";
	} 
	return $output;
}
?>