<?php

$affiliates_clickthroughs_table = $wpdb->prefix . "affiliates_clickthroughs_tbl";
$sales_table = $wpdb->prefix . "affiliates_sales_tbl";

function wp_aff_show_stats()
{
	echo '<div class="wrap"><h2>WP Affiliate Platform - Stats Overview</h2>';

	if(!aff_detect_ie())
	{
    	aff_handle_date_form();    	
	}
	else
	{
		aff_handle_date_form_in_ie();
	}	
	
    if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Affiliate Stats Overview Between '.$start_date.' And '. $end_date;
        echo '</strong></p></div>';  
        
        show_stats_between_dates($start_date,$end_date);  	
    }
    else
    {	
		$curr_date = (date ("Y-m-d"));
		$m = date('m');
		$y = date('Y'); 
		$start_date = $y.'-'.$m.'-01';
		$end_date = $curr_date;
		
	    echo '<div id="message" class="updated fade"><p><strong>';
	    echo 'Affiliate Stats Overview for This Month';
	    echo '</strong></p></div>';	  
	    	
		show_stats_between_dates($start_date,$end_date);
    }
	
	echo '</div>';
}

function show_stats_between_dates($start_date,$end_date)
{
	global $wpdb;
	global $affiliates_clickthroughs_table;
	global $sales_table;
		
	$query = $wpdb->get_row("SELECT count(*) as total_record FROM $affiliates_clickthroughs_table WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$total_clicks = $query->total_record;
	if (empty($total_clicks))
	{
		$total_clicks = "0";
	}
	
	$query = $wpdb->get_row("SELECT count(*) as total_record FROM $sales_table WHERE payment > 0 AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$number_of_sales = $query->total_record;
	if (empty($number_of_sales))
	{
		$number_of_sales = "0";
	}
		
	$row = $wpdb->get_row("select SUM(sale_amount) AS total from $sales_table where date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$total_sales = $row->total;
	if (empty($total_sales))
	{
		$total_sales = "0.00";
	}
	
	$row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	$total_commission = $row->total;
	if (empty($total_commission))
	{
		$total_commission = "0.00";
	}

	$currency = get_option('wp_aff_currency');
	echo '
	<table width="300">
	<thead><tr>
	<th scope="col"></th>
	<th scope="col"></th>
	<th scope="col"></th>
	</tr></thead>
	<tbody>';

		echo '<tr>';
		echo '<td><strong>Total Clicks : </strong></td>';
		echo '<td>'.$total_clicks.'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><strong>Number of Sales : </strong></td>';
		echo '<td>'.$number_of_sales.'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><strong>Total Sales Amount : </strong></td>';
		echo '<td>'.$total_sales.'</td>';
		echo '<td>'.$currency.'</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td><strong>Total Commission : </strong></td>';
		echo '<td>'.$total_commission.'</td>';
		echo '<td>'.$currency.'</td>';
		echo '</tr>';
						
	echo '</tbody></table>';	
}
?>
