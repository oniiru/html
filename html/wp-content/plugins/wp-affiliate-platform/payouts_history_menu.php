<?php
include_once('helper_func.php');

$payouts_table = $wpdb->prefix . "affiliates_payouts_tbl";

function payouts_history_menu()
{	
	echo '<div class="wrap"><h2>WP Affiliate Platform - Payouts History</h2>';
    echo '<div id="poststuff"><div id="post-body">'; 

	echo '<div class="postbox">
	<h3><label for="title">Affiliate Payouts History</label></h3>
	<div class="inside">';
	    
	if(!aff_detect_ie())
	{
    	aff_handle_date_form();    	
	}
	else
	{
		aff_handle_date_form_in_ie();
	}	
	echo '</div></div>';
	
    if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Displaying Payouts History Between '.$start_date.' And '. $end_date;
        echo '</strong></p></div>';
		        	
		$curr_date = (date ("Y-m-d"));
		
		global $payouts_table;
		global $wpdb;
		
		echo '
		<table class="widefat">
		<thead><tr>
		<th scope="col">'.__('Affiliate ID', 'wp_affiliate').'</th>
		<th scope="col">'.__('Payout Amount', 'wp_affiliate').'</th>
		<th scope="col">'.__('Date Paid', 'wp_affiliate').'</th>
		</tr></thead>
		<tbody>';
			
		$wp_aff_payouts = $wpdb->get_results("SELECT * FROM $payouts_table WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
		if ($wp_aff_payouts)
		{
			foreach ($wp_aff_payouts as $wp_aff_payouts)
			{
				echo '<tr>';
				echo '<td><strong>'.$wp_aff_payouts->refid.'</strong></td>';
				echo '<td><strong>'.$wp_aff_payouts->payout_payment.'</strong></td>';
				echo '<td><strong>'.$wp_aff_payouts->date.'</strong></td>';
				echo '</tr>';					
			}	
		}
		else
		{
			echo '<tr> <td colspan="4">'.__('No Payouts Data Found.', 'wp_affiliate').'</td> </tr>';
		}		
		echo '</tbody></table>';
	}
	echo '</div></div>';
	echo '</div>';	
}

?>
