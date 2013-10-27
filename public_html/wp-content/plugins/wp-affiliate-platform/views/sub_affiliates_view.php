<?php
function wp_aff_sub_affiliates_view()
{
	$output = "";
	ob_start();	
	echo wp_aff_view_get_navbar();
	echo '<div id="wp_aff_inside">';
	if (get_option('wp_aff_use_2tier'))
	{
		echo '<div id="subnav">';
		echo '<li><a href="'.wp_aff_view_get_url_with_separator("clicks").'">'.AFF_NAV_CLICKS.'</a></li>';
		echo '<li><a href="'.wp_aff_view_get_url_with_separator("sub-affiliates").'">'.AFF_NAV_SUB_AFFILIATES.'</a></li></div>';
		echo '<div style="clear:both;"></div><br />';
	}	
	echo '<img src="'.WP_AFF_PLATFORM_URL.'/affiliates/images/tier-affiliates-2.png" alt="tier affiliates icon" />';
	wp_aff_show_sub_affiliates();
	echo '</div>';
	echo wp_aff_view_get_footer();
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;	
}
function wp_aff_show_sub_affiliates()
{
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	wp_aff_clicks_history($affiliates_table_name);	
}

function wp_aff_clicks_history($affiliates_table_name)
{		
    include_once("aff_view_reports.php");

	global $wpdb;
	
    if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        echo '<p><strong>';
        echo AFF_C_DISPLAYING_REFERRALS.' <font class="blue">'.$start_date.'</font> '.AFF_AND.' <font class="blue">'. $end_date;
        echo '</font></strong></p>';
		        	
		$curr_date = (date ("Y-m-d"));		
   
		$resultset = $wpdb->get_results("select * from $affiliates_table_name where referrer = '".$_SESSION['user_id']."' AND date BETWEEN '$start_date' AND '$end_date'",OBJECT);
		
		if ($resultset)
		{		
			echo "<table id='reports'>";
		    echo "<tr><th>".AFF_TIER_SUB_AFFILIATES_DATE_JOINED."</th><th>".AFF_TIER_SUB_AFFILIATES_ID."</th>";
		    echo "</tr>";
		
		    foreach ($resultset as $resultset) 
		    {
		        print "<tr>";
		      	print "<td class='reportscol col1'>";
		      	print $resultset->date;
		      	print "</td>";
		      	print "<td class='reportscol'>";
		      	print $resultset->refid;
		      	print "</td>";
		      	print "</tr>";
		    }
		    print "</table>";
		}
		else
		{
			echo "<br><br>".AFF_TIER_SUB_AFFILIATES_NO_RECORDS;
		}
    		
	}
	else
	{	
		$resultset = $wpdb->get_results("select * from $affiliates_table_name where referrer = '".$_SESSION['user_id']."' ORDER BY date DESC LIMIT 20", OBJECT);  
		   	
		if ($resultset) 
		{
			echo '<strong>';
			echo AFF_TIER_SUB_AFFILIATES_UNDER_YOU;
			echo '</strong>';
			echo "<br>".AFF_TIER_SUB_AFFILIATES_20;
			print "<br><br>";
		
		    echo "<table id='reports'>";
		    echo "<tr><th>".AFF_TIER_SUB_AFFILIATES_DATE_JOINED."</th><th>".AFF_TIER_SUB_AFFILIATES_ID."</th>";
		    echo "</tr>";
		
		    foreach ($resultset as $resultset) 
		    {
		        print "<tr>";
		      	print "<td class='reportscol col1'>";
		      	print $resultset->date;
		      	print "</td>";
		      	print "<td class='reportscol'>";
		      	print $resultset->refid;
		      	print "</td>";
		      	print "</tr>";
		    }
		    print "</table>";
		}
		else
		{
			echo "<br><br>".AFF_TIER_SUB_AFFILIATES_NO_RECORDS;
		}
	}
}  
?>