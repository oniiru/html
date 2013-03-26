<?php
function wp_aff_ads_view()
{
	$output = "";
	$output .= wp_aff_view_get_navbar();
	$output .= '<div id="wp_aff_inside">';	
	$output .= wp_aff_show_banners();
	$output .= '</div>';
	$output .= wp_aff_view_get_footer();
	return $output;
}

function wp_aff_show_banners()
{
?>
<script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL.'/views/js/jquery.lightbox-0.5.min.js'; ?>"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $(function() {
    	$('[id=wp_aff_inside]').find('a[rel*=lightbox]').lightBox({
        	imageLoading: '<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/lightbox-ico-loading.gif'; ?>',
        	imageBtnClose: '<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/lightbox-btn-close.gif'; ?>',
        	imageBtnPrev: '<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/lightbox-btn-prev.gif'; ?>',
        	imageBtnNext: '<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/lightbox-btn-next.gif'; ?>',
        	imageBlank: '<?php echo WP_AFF_PLATFORM_URL.'/affiliates/images/lightbox-blank.gif'; ?>'
         	});
    });    
});
</script>
<?php 	
	global $wpdb;
	$aff_banners_table =  WP_AFF_BANNERS_TBL_NAME;    	
	$resultset = $wpdb->get_results("select * from $aff_banners_table ORDER BY name asc",OBJECT); 
	
	$output = "";
	$output .= '<div id="subnav">';
	$output .= '<li><a href="'.wp_aff_view_get_url_with_separator("ads").'">'.AFF_NAV_BANNERS.'</a></li>';
	$output .= '<li><a href="'.wp_aff_view_get_url_with_separator("creatives").'">'.AFF_NAV_CREATIVES.'</a></li></div>';
	$output .= '<div style="clear:both;"></div><br />';
	
	$output .= "<h3 class='wp_aff_title'>".AFF_B_BANNER_PAGE_TITLE."</h3>";
	$output .= "<p style='text-align:left;'>".AFF_B_BANNERS_PAGE_MESSAGE."</p>";
	$output .= "<p><strong><font face=arial>".AFF_B_BANNERS;
	$output .= "</strong></p>";
	
	if ($resultset) 
	{
		$output .= '
		<table width="100%" id="gallery">
		<thead><tr>
		<th scope="col" class="tableheader">'.AFF_B_BANNER_NAME.'</th>
		<th scope="col" class="tableheader">'.AFF_B_BANNER_LINK.'</th>
		<th scope="col" class="tableheader">'.AFF_B_CODE.'</th>
		</tr></thead>
		<tbody>';
		    
	    foreach ($resultset as $resultset) 
	    {
	    	if($resultset->creative_type =="0")
	    	{
				$separator='?';
				$url=$resultset->ref_url;
				if(strpos($url,'?')!==false) 
				{
					$separator='&';
				}	
			    if (empty($resultset->image))
			    {
			        // Text Link
			        $aff_url = $resultset->ref_url.$separator."ap_id=".$_SESSION['user_id'];
			        $code = "<a href=\"$aff_url\" target=\"blank\">$resultset->link_text</a>";
		            $banner = "<a href=\"$aff_url\" target=\"blank\">$resultset->link_text</a>";
			    }
			    else
			    {
			      	//Banner image
			      	$aff_url = $resultset->ref_url.$separator."ap_id=".$_SESSION['user_id'];
					$code = "<a href=\"$aff_url\" target=\"_blank\"><img src=\"$resultset->image\" alt=\"$resultset->link_text\" border=\"0\" /></a>";
		            $banner = "<div id=\"lightbox\"><a rel=\"lightbox\" href=\"$resultset->image\" ><img src=\"$resultset->image\" alt=\"$resultset->link_text\" border=\"0\" /></a></div>";
			    }    	
				$output .= '<tr>';
				$output .= '<td class="col1"><strong>'.$resultset->name.'</strong></td>';
				$output .= '<td class="col2"><strong>'.$banner.'</strong></td>';
		        $output .= '<td><textarea rows=5>';
		      	$output .= $code;
		      	$output .= "</textarea></td>";				
				$output .= '</tr>';
	    	}
	    }
		$output .= '</tbody>
		</table>';
	}
	else
	{
		$output .= "<p class='message'>".AFF_B_NO_BANNER."</p>";
	}	
	return $output;
}
?>