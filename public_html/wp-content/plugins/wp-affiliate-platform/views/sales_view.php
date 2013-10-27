<?php
function wp_aff_sales_view()
{
	$output = "";
	ob_start();	
	echo wp_aff_view_get_navbar();
	echo '<div id="wp_aff_inside">';
	echo '<img src="'.WP_AFF_PLATFORM_URL.'/affiliates/images/currency_dollar.png" alt="sales icon" />';
	wp_aff_show_sales();
	echo '</div>';
	echo wp_aff_view_get_footer();
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;	
}

function wp_aff_show_sales()
{
	$currency = get_option('wp_aff_currency');
	global $wpdb;
	$aff_sales_table = WP_AFF_SALES_TBL_NAME;  
	
	wp_aff_sales_history($aff_sales_table);
	
	echo '<strong>';  
	echo "<br>".AFF_S_TOTAL.": ";
	 
	$row = $wpdb->get_row("select SUM(payment) AS total from $aff_sales_table where refid = '".$_SESSION['user_id']."'", OBJECT);
	$total = round($row->total,2);
	echo ($total != '' ? $total : '0');
	echo " "; 
	echo $currency; 
	echo "<br><br>";
	echo '</strong>';	
}

function wp_aff_sales_history($aff_sales_table)
{
    include_once("aff_view_reports.php");
	
	$currency = get_option('wp_aff_currency');
	global $wpdb,$wp_aff_platform_config;
	
    if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        echo '<h4>';
        echo AFF_S_DISPLAYING_SALES_HISTORY.' <font style="color:#222;">'.$start_date.'</font> '.AFF_AND.' <font style="color:#222;">'. $end_date;
        echo '</font></h4>';
		        	
		$curr_date = (date ("Y-m-d"));		
   
		$wp_aff_sales = $wpdb->get_results("select * from $aff_sales_table where refid = '".$_SESSION['user_id']."' AND date BETWEEN '$start_date' AND '$end_date'",OBJECT);
		
		if ($wp_aff_sales)
		{		
		    echo "<table id='reports'>";
		    echo "<tr><th>".AFF_G_DATE."</th><th>".AFF_G_TIME."</th>";
		    echo "<th>".AFF_S_EARNED."</th>";
			if(get_option('wp_aff_show_buyer_details_to_affiliates') || $wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1')
			{
				echo "<th>".AFF_BUYER_DETAILS."</th>";
			}		    
		    echo "</tr>";
		    
		    foreach ($wp_aff_sales as $resultset) 
		    {
		        echo "<tr>";
		      	echo "<td class='reportscol col1'>";
		      	echo $resultset->date;
		      	echo "</td>";
		      	echo "<td class='reportscol col1'>";
		      	echo $resultset->time;
		      	echo "</td>";
		      	echo "<td class='reportscol'>";
		      	echo round($resultset->payment,2);
		      	echo " ";
		      	echo $currency; 
		      	echo "</td>";
		      	if(get_option('wp_aff_show_buyer_details_to_affiliates') && $wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1')
		      	{
			      	echo "<td class='reportscol'>";
			      	echo $resultset->buyer_name.'<br />';
			      	echo $resultset->buyer_email;
			      	echo "</td>";	
		      	}	
		      	else if($wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1')
		      	{
			      	echo "<td class='reportscol'>";
			      	echo $resultset->buyer_name;
			      	echo "</td>";			      		
		      	}	 
		      	else if(get_option('wp_aff_show_buyer_details_to_affiliates'))
		      	{
			      	echo "<td class='reportscol'>";
			      	echo $resultset->buyer_email;
			      	echo "</td>";			      		
		      	}		      	
		      	echo "</tr>";
		    }
		    echo "</table>";
		}
		else
		{
			echo "<br><br><font face=arial>No Sales Record Found";
		}
    		
	}
	else
	{	
		$resultset = $wpdb->get_results("select * from $aff_sales_table where refid = '".$_SESSION['user_id']."' ORDER BY date DESC LIMIT 20",OBJECT);
		
		if ($resultset) 
		{
			echo '<strong>';
			echo "<font face=arial>".AFF_S_SALES;
			echo '</strong>';
			echo "<br>".AFF_S_SHOWING_20;
			echo "<br>";
				
		    echo "<table id='reports'>";
		    echo "<tr><th>".AFF_G_DATE."</th><th>".AFF_G_TIME."</th>";
		    echo "<th>".AFF_S_EARNED."</th>";
		    if(get_option('wp_aff_show_buyer_details_to_affiliates') || $wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1')
		    {
		   		echo "<th>".AFF_BUYER_DETAILS."</th>";
		    }
		    echo "</tr>";
		    
		    foreach ($resultset as $resultset) 
		    {
		        echo "<tr>";
		      	echo "<td class='reportscol col1'>";
		      	echo $resultset->date;
		      	echo "</td>";
		      	echo "<td class='reportscol col1'>";
		      	echo $resultset->time;
		      	echo "</td>";
		      	echo "<td class='reportscol'>";
		      	echo $resultset->payment;
		      	echo " ";
		      	echo $currency; 
		      	echo "</td>";
		    	if(get_option('wp_aff_show_buyer_details_to_affiliates') && $wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1')
		      	{
			      	echo "<td class='reportscol'>";
			      	echo $resultset->buyer_name.'<br />';
			      	echo $resultset->buyer_email;
			      	echo "</td>";	
		      	}	
		      	else if($wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates')=='1')
		      	{
			      	echo "<td class='reportscol'>";
			      	echo $resultset->buyer_name;
			      	echo "</td>";			      		
		      	}	 
		      	else if(get_option('wp_aff_show_buyer_details_to_affiliates'))
		      	{
			      	echo "<td class='reportscol'>";
			      	echo $resultset->buyer_email;
			      	echo "</td>";			      		
		      	}	      	
		      	echo "</tr>";
		    }
		    echo "</table>";
		}
		else
		{
			echo "<br><br><font face=arial>".AFF_S_NO_SALES;
		}	
	}	
}
?>