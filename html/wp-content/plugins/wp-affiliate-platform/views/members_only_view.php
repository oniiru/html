<?php
function wp_aff_members_only_view()
{
	$output = "";
	ob_start();

	echo wp_aff_view_get_navbar();
	echo '<div id="wp_aff_inside">';
	echo '<img src="'.WP_AFF_PLATFORM_URL.'/affiliates/images/wp_aff_stats.jpg" alt="Stats Icon" />';
	wp_aff_show_stats();
	echo '</div>';
	echo wp_aff_view_get_footer();
	
	$output .= ob_get_contents();
	ob_end_clean();
	
	return $output;	
}
function wp_aff_show_stats()
{
	global $wpdb;
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	$wp_aff_affiliates_db = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '".$_SESSION['user_id']."'", OBJECT);
	echo '<h3>'.AFF_WELCOME.' '.$wp_aff_affiliates_db->firstname.'</h3>';

	//Default affiliate link
	$default_landing_page = get_option('wp_aff_default_affiliate_landing_url');
	if(empty($default_landing_page))
	{
		$default_affiliate_home_url = get_bloginfo('home');
	}
	else
	{
		$default_affiliate_home_url = $default_landing_page;
	}
	$separator='?';
	$url = $default_affiliate_home_url;
	if(strpos($url,'?')!==false) 
	{
		$separator='&';
	}
	$aff_url = $url.$separator.'ap_id='.$_SESSION['user_id'];
	$affiliate_link = '<a href="'.$aff_url.'" target="_blank">'.$aff_url.'</a>';	
	echo '<strong>'.AFF_YOUR_AFF_LINK.$affiliate_link.'</strong>';
	echo "<br />";

	//Welcome message
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	$wp_aff_welcome_page_msg = $wp_aff_platform_config->getValue('wp_aff_welcome_page_msg');
	if(!empty($wp_aff_welcome_page_msg)){
		$wp_aff_welcome_page_msg = html_entity_decode($wp_aff_welcome_page_msg, ENT_COMPAT, "UTF-8");
		echo '<div class="wp_aff_welcome_page_msg">'.$wp_aff_welcome_page_msg.'</div>';
	}
	
	//Summary report
    include_once ("aff_view_reports.php");

	if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        echo '<h4>';
        echo AFF_STATS_OVERVIEW_BETWEEN.' <font style="color:#222">'.$start_date.'</font> '.AFF_AND.' <font style="color:#222">'. $end_date;
        echo '</font></h4>';

        wp_aff_show_stats_between_dates($start_date,$end_date);
    }
    else
    {
		$curr_date = (date ("Y-m-d"));
		$m = date('m');
		$y = date('Y');
		$start_date = $y.'-'.$m.'-01';
		$end_date = $curr_date;

	    echo '<h4>';
	    echo AFF_STATS_OVERVIEW;
	    echo '</h4>';

		wp_aff_show_stats_between_dates($start_date,$end_date);
    }
}

function wp_aff_show_stats_between_dates($start_date,$end_date)
{
	global $wpdb;
	$affiliates_clickthroughs_table = WP_AFF_CLICKS_TBL_NAME;
	$sales_table = WP_AFF_SALES_TBL_NAME;
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;

	$query = $wpdb->get_row("SELECT count(*) as total_record FROM $affiliates_clickthroughs_table WHERE refid = '".$_SESSION['user_id']."' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$total_clicks = $query->total_record;
	if (empty($total_clicks))
	{
		$total_clicks = "0";
	}
	
	$query = $wpdb->get_row("SELECT count(*) as total_record FROM $sales_table WHERE payment > 0 AND refid = '".$_SESSION['user_id']."' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$number_of_sales = $query->total_record;
	if (empty($number_of_sales))
	{
		$number_of_sales = "0";
	}
		
	$row = $wpdb->get_row("select SUM(sale_amount) AS total from $sales_table where refid = '".$_SESSION['user_id']."' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$total_sales = $row->total;
	if (empty($total_sales))
	{
		$total_sales = "0.00";
	}
	
	$row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where refid = '".$_SESSION['user_id']."' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$total_commission = $row->total;
	if (empty($total_commission))
	{
		$total_commission = "0.00";
	}

	$wp_aff_affiliates_db = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '".$_SESSION['user_id']."'", OBJECT);
	$commission_level = $wp_aff_affiliates_db->commissionlevel;
	
	$currency = get_option('wp_aff_currency');
	echo '
	<table id="reports" width="300">
	<tbody>';

		echo '<tr>';
		echo '<td><strong>'.AFF_TOTAL_CLICKS.' : </strong></td>';
		echo '<td>'.$total_clicks.'</td>';
		echo '<td></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><strong>'.AFF_NUMBER_OF_SALES.' : </strong></td>';
		echo '<td>'.$number_of_sales.'</td>';
		echo '<td></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><strong>'.AFF_TOTAL_SALES_AMOUNT.' : </strong></td>';
		echo '<td>'.$total_sales.'</td>';
		echo '<td>'.$currency.'</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td><strong>'.AFF_TOTAL_COMMISSION.' : </strong></td>';
		echo '<td>'.$total_commission.'</td>';
		echo '<td>'.$currency.'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><strong>'.AFF_COMMISSION_LEVEL.' : </strong></td>';
		echo '<td>'.$commission_level.'</td>';
		
        if (get_option('wp_aff_use_fixed_commission'))
		{
            echo '<td>'.$currency.'</td>';
        }
        else
        {
            echo '<td>%</td>';
        }
		echo '</tr>';

		if (get_option('wp_aff_use_2tier'))
		{
			$second_tier_commission_level = $wp_aff_affiliates_db->sec_tier_commissionlevel;
			if(empty($second_tier_commission_level)){
				$second_tier_commission_level = get_option('wp_aff_2nd_tier_commission_level');
			}
			echo '<tr>';
			echo '<td><strong>'.AFF_2ND_TIER_COMMISSION_LEVEL.' : </strong></td>';
			echo '<td>'.$second_tier_commission_level.'</td>';
			
	        if (get_option('wp_aff_use_fixed_commission'))
			{
	            echo '<td>'.$currency.'</td>';
	        }
	        else
	        {
	            echo '<td>%</td>';
	        }
			echo '</tr>';	
		}		
								
	echo '</tbody></table>';
}
?>