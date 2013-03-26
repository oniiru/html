<?php
function clickthroughs_menu()
{
	echo '<div class="wrap"><h2>WP Affiliate Platform - Clickthrough Data</h2>';
	echo '<div id="poststuff"><div id="post-body">';	

	echo wp_aff_admin_submenu_css();
	
   ?>
   <ul class="affiliateSubMenu">
   <li><a href="admin.php?page=clickthroughs">Overall Click Data</a></li>
   <li><a href="admin.php?page=clickthroughs&action=affiliate">Individual Affiliate Click Data</a></li>   
   </ul>
   <?php
   $action = isset($_GET['action'])?$_GET['action']:'';
   switch ($action)
   {
       case 'affiliate':
           wp_aff_individual_clickthroughs();
           break;
       default:
           wp_aff_overall_clickthroughs();
           break;
   }	
   	
	echo '</div></div>';
	echo '</div>';
}

function wp_aff_individual_clickthroughs()
{
	echo "<br /><br />";
	echo '<div class="postbox">
	<h3><label for="title">Individual Affiliate Click Data</label></h3>
	<div class="inside">';
		
	if(!aff_detect_ie())
	{
    	aff_handle_date_form_with_affiliate_id_field();    	
	}
	else
	{
		aff_handle_date_form_in_ie_with_affiliate_id_field();
	}	
	echo "</div></div>";
	
    if (isset($_POST['info_update']))
    {
    	$affiliate_id = $_POST['wp_aff_referrer'];
    	if(empty($affiliate_id))
    	{
    		echo '<div id="message" class="error fade"><br />Error! You must specify an Affiliate ID<br /><br /></div>';
    		return;
    	}
    	
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];		        	
		$curr_date = (date ("Y-m-d"));
		if(empty($start_date)){
			$start_date = '2008-01-01';//from the beginning
		}
		if(empty($end_date)){
			$end_date = $curr_date;
		}
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Displaying Clicks Data Between '.$start_date.' And '. $end_date. ' For Affiliate: '.$affiliate_id;
        echo '</strong></p></div>';
        		
		global $wpdb;
		$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
		$resultset = $wpdb->get_results("SELECT * FROM $affiliates_clickthroughs_table_name WHERE (refid='$affiliate_id') AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);

		if(is_array($resultset)){
			$total_clicks = count($resultset);//mysql_num_rows($resultset);
			echo '<p>Total clicks: '.$total_clicks.'</p>';
		}

		show_resultset_clickthroughs($resultset);
	}	
}

function wp_aff_overall_clickthroughs()
{
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
        echo 'Displaying Clicks History Between '.$start_date.' And '. $end_date;
        echo '</strong></p></div>';
		        	
		$curr_date = (date ("Y-m-d"));
		
		$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
		global $wpdb;
		$resultset = $wpdb->get_results("SELECT * FROM $affiliates_clickthroughs_table_name WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
		show_resultset_clickthroughs($resultset);
	}
	else
	{
		show_last_clickthroughs();
	}	
}

function show_resultset_clickthroughs($resultset)
{
		echo '
		<table class="widefat">
		<thead><tr>
		<th scope="col">'.__('Affiliate ID', 'wp_affiliate').'</th>
		<th scope="col">'.__('Date', 'wp_affiliate').'</th>
		<th scope="col">'.__('Time', 'wp_affiliate').'</th>
		<th scope="col">'.__('IP Address', 'wp_affiliate').'</th>';
		if(get_option('wp_aff_enable_clicks_custom_field') != '')
		{
			echo '<th scope="col">'.__('Custom Value', 'wp_affiliate').'</th>';
		}
		echo '<th scope="col">'.__('Referral URL', 'wp_affiliate').'</th>
		<th scope="col"></th>
		</tr></thead>
		<tbody>';
					
		if ($resultset)
		{
			foreach ($resultset as $wp_aff_clicks)
			{
				echo '<tr>';
				echo '<td><strong>'.$wp_aff_clicks->refid.'</strong></td>';
				echo '<td><strong>'.$wp_aff_clicks->date.'</strong></td>';
				echo '<td><strong>'.$wp_aff_clicks->time.'</strong></td>';
				echo '<td><strong>'.$wp_aff_clicks->ipaddress.'</strong></td>';
				if(get_option('wp_aff_enable_clicks_custom_field') != '')
				{
					echo '<td><strong>'.$wp_aff_clicks->campaign_id.'</strong></td>';
				}
				echo '<td><strong>'.$wp_aff_clicks->referralurl.'</strong></td>';
				echo '</tr>';					
			}	
		}
		else
		{
			echo '<tr> <td colspan="6">'.__('No Click Data Found.', 'wp_affiliate').'</td> </tr>';
		}		
		echo '</tbody></table>';	
}

function show_last_clickthroughs()
{
    echo '<div id="message" class="updated fade"><p><strong>';
    echo 'Displaying 20 Recent Clicks Data';
    echo '</strong></p></div>';	
    
	echo '
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('Affiliate ID', 'wp_affiliate').'</th>
	<th scope="col">'.__('Date', 'wp_affiliate').'</th>
	<th scope="col">'.__('Time', 'wp_affiliate').'</th>
	<th scope="col">'.__('IP Address', 'wp_affiliate').'</th>';
	if(get_option('wp_aff_enable_clicks_custom_field') != '')
	{
		echo '<th scope="col">'.__('Custom Value', 'wp_affiliate').'</th>';
	}
	echo '<th scope="col">'.__('Referral URL', 'wp_affiliate').'</th>
	<th scope="col"></th>
	</tr></thead>
	<tbody>';

	global $wpdb;
	$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
	$wp_aff_clicks_db = $wpdb->get_results("SELECT * FROM $affiliates_clickthroughs_table_name ORDER BY date DESC LIMIT 20", OBJECT);

	if ($wp_aff_clicks_db)
	{
		foreach ($wp_aff_clicks_db as $wp_aff_clicks_db)
		{
			echo '<tr>';
			echo '<td>'.$wp_aff_clicks_db->refid.'</td>';
			echo '<td><strong>'.$wp_aff_clicks_db->date.'</strong></td>';
			echo '<td><strong>'.$wp_aff_clicks_db->time.'</strong></td>';
			echo '<td><strong>'.$wp_aff_clicks_db->ipaddress.'</strong></td>';
			if(get_option('wp_aff_enable_clicks_custom_field') != '')
			{
				echo '<td><strong>'.$wp_aff_clicks_db->campaign_id.'</strong></td>';
			}			
			echo '<td><strong>'.$wp_aff_clicks_db->referralurl.'</strong></td>';
			echo '</tr>';
		}
	}
	else
	{
		echo '<tr> <td colspan="8">'.__('No Click Data Found.', 'wp_affiliate').'</td> </tr>';
	}

	echo '</tbody>
	</table>';	
}
?>
