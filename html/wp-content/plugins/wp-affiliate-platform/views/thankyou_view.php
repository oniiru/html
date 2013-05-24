<?php
function wp_aff_thankyou_view()
{
	$output .= wp_aff_view_get_navbar();
	$output .= '<div id="wp_aff_inside">';
	$output .= wp_aff_show_thankyou_page();
	$output .= '</div>';
	$output .= wp_aff_view_get_footer();
	return $output;
}
function wp_aff_show_thankyou_page()
{
	
	$output .= '<h2 class="wp_aff_title">'.AFF_THANK_YOU.'</h2>';
	$output .= '<h3>'.AFF_REGO_COMPLETE.'</h3>';
	$output .= '<p class="message">'.AFF_REGO_COMPLETE_MESSAGE_ON_PAGE_VIEW;
	$output .= '<a style="color:#CC0000;" href="'.wp_aff_view_get_url_with_separator("login").'"> '.AFF_LOGIN_HERE.'</a></p>';
	return $output;	
}
?>