<?php
include_once('helper_func.php');
include_once('wp_aff_includes1.php');

$affiliates_leads_table_name = $wpdb->prefix . "affiliates_leads_tbl";

function aff_top_leads_menu()
{
	echo wp_aff_misc_admin_css();
	echo '<div class="wrap"><h2>WP Affiliate Platform - Manage Leads</h2>';
	echo '<div class="wp_affiliate_grey_box">';
	echo '<p>Please read the lead capture documentation before using this feature</p>';
	echo '&raquo; <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=215" target="_blank">Capturing Lead Using Contact Form 7 Plugin</a><br /><br />';
	echo '&raquo; <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=385" target="_blank">Capturing Lead Using Gravity Forms Plugin</a><br /><br />';
	echo '</div>';
	echo '<div id="poststuff"><div id="post-body">';	
		
	aff_leads_menu();
	  
   	echo '</div></div>';
	echo '</div>';
}

function aff_leads_menu()
{
  	//echo "<h2>Overall Leads Data</h2>";  	   
	global $affiliates_leads_table_name;
	global $wpdb;	

	if(isset($_POST['Award']))
	{		
		?>			
		<div class="postbox">
		<div style="border:1px solid #CC0000;">
		<h3 style="background: #FFDED9; border-bottom:1px solid #CC0000;"><label for="title">Award Commission (Finalize the commission awarding data below)</label></h3>
		<div class="inside">	
		Please enter the sale Amount and hit the "Award Commission" button. Commission will be calculated based on this sale amount and awarded to the appropriate affiliate.	
		<form name="award_comm" method=post action="admin.php?page=manage_leads"> 
		<br />Sale Amount: <input type="text" name="sale_amt" value=" "> <br />
		<input type="hidden" name="lead_id" value="<?php echo $_POST['lead_id']; ?>">
		<br /><input type="submit" value="Award Commission" name="award_commission">
		</form> 
		</div></div></div>		
		<?php
	}
    if(isset($_POST['award_commission']))
    {
    	$sale_amt = $_POST['sale_amt'];
    	$lead_id = $_POST['lead_id'];
		$aff_leads = $wpdb->get_row("SELECT * FROM $affiliates_leads_table_name where lead_id='$lead_id'", OBJECT);  		
    	wp_aff_award_commission($aff_leads->refid,$sale_amt,'',$aff_leads->reference,$aff_leads->buyer_email,$aff_leads->ipaddress,'',$aff_leads->buyer_name);
        echo '<div id="message" class="updated fade"><p><strong>';
	    echo "Commission awarded to referrer: ".$aff_leads->refid;
	    echo '</strong></p></div>';    	
    }

	wp_aff_add_leads_data();
		
	if(!aff_detect_ie())
	{
    	aff_handle_date_form();    	
	}
	else
	{
		aff_handle_date_form_in_ie();
	}
        
	if(isset($_POST['Delete']))
    {
        if(wp_aff_delete_leads_data($_POST['lead_id']))
        {
            $message = "Record successfully deleted";
        }
        else
        {
            $message = "Could not delete the entry. Please check and make sure the Lead ID field is unique and has a value";
        }
        echo '<div id="message" class="updated fade"><p><strong>';
	    echo $message;
	    echo '</strong></p></div>';
    }
    
	if (isset($_POST['Submit']))
	{
		if (!empty($_POST['refid']))
		{
			$referrer = $_POST['refid'];
			if (empty($_POST['date']))
	        	$clientdate = (date ("Y-m-d"));
	        else
	        	$clientdate = $_POST['date'];
	        	
			if (empty($_POST['time']))
	        	$clienttime	= (date ("H:i:s"));	
	        else
	        	$clienttime = $_POST['time'];  
       
	    	$buyer_email = $_POST['buyer_email'];
	    	$buyer_name = $_POST['buyer_name'];
	    	$reference = $_POST['reference'];
	    	$ipaddress = $_POST['ipaddress'];
	      	$updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress,buyer_name) VALUES ('$buyer_email','$referrer','$reference','$clientdate','$clienttime','$ipaddress','$buyer_name')";
			$results = $wpdb->query($updatedb);				
		}
	}
		
	$msg = '';
    if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        $msg .= '<div class="wp_affiliate_yellow_box"><p><strong>';
        $msg .= 'Displaying Leads Data Between '.$start_date.' and '. $end_date;
        $msg .= '</strong></p></div>';		        	
		$curr_date = (date ("Y-m-d"));				
		$wp_aff_leads = $wpdb->get_results("SELECT * FROM $affiliates_leads_table_name WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	}
	if ($msg == '')
	{
	    $msg .= '<div class="wp_affiliate_yellow_box"><p><strong>';
	    $msg .= 'Displaying 20 Recent Leads Below';
	    $msg .= '</strong></p></div>';	   			
		$wp_aff_leads = $wpdb->get_results("SELECT * FROM $affiliates_leads_table_name ORDER BY date DESC LIMIT 20", OBJECT);	    	
	}
	wp_aff_display_leads_data($wp_aff_leads,$msg);
}

function wp_aff_display_leads_data($wp_aff_leads,$msg)
{  
	echo $msg;   
	echo '
		<table class="widefat">
		<thead><tr>
		<th scope="col">'.__('Lead ID', 'wp_affiliate').'</th>
		<th scope="col">'.__('Email', 'wp_affiliate').'</th>
		<th scope="col">'.__('Name', 'wp_affiliate').'</th>
		<th scope="col">'.__('Referrer ID', 'wp_affiliate').'</th>
		<th scope="col">'.__('Reference', 'wp_affiliate').'</th>
		<th scope="col">'.__('Date', 'wp_affiliate').'</th>
		<th scope="col">'.__('Time', 'wp_affiliate').'</th>
		<th scope="col">'.__('IP Address', 'wp_affiliate').'</th>
		<th scope="col">'.__('Award Commission', 'wp_affiliate').'</th>
        <th scope="col">'.__('Delete Entry', 'wp_affiliate').'</th>
		</tr></thead>
		<tbody>';
			
	if ($wp_aff_leads)
	{
		foreach ($wp_aff_leads as $wp_aff_leads)
		{
			echo '<tr>';
			echo '<td><strong>'.$wp_aff_leads->lead_id.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->buyer_email.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->buyer_name.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->refid.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->reference.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->date.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->time.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->ipaddress.'</strong></td>';
			
			echo "<td>";
			?>	
			<form name="convert" method=post action="admin.php?page=manage_leads"> 
			<input type="hidden" name="lead_id" value="<?php echo $wp_aff_leads->lead_id; ?>">
			<input type="submit" value="Award" name="Award">
			</form> 			
			<?php			
			echo "</td>";
			
            echo "<td>";
			echo "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";
            echo "<input type=\"hidden\" name=\"lead_id\" value=".$wp_aff_leads->lead_id." />";
            echo "<input type=\"submit\" value=\"Delete\" name=\"Delete\">";
            echo "</form>";
            echo "</td>";

			echo '</tr>';					
		}	
	}
	else
	{
		echo '<tr> <td colspan="9">'.__('No Leads Data Found.', 'wp_affiliate').'</td> </tr>';
	}		
	echo '</tbody></table>';
}

function wp_aff_add_leads_data()
{
	?>
	<div class="postbox">
	<h3><label for="title">Add a Lead Manually</label></h3>
	<div class="inside">

	<form method="post" action="">
	<table width="960">
    
	<thead><tr>
	<th align="left"><strong>Email</strong></th>
	<th align="left"><strong>Name</strong></th>
	<th align="left"><strong>Referrer ID</strong></th>
	<th align="left"><strong>Reference</strong></th>
	<th align="left"><strong>Date (yyyy-mm-dd)</strong></th>
	<th align="left"><strong>Time (hh:mm:ss)</strong></th>
	<th align="left"><strong>IP Address</strong></th>
	</tr></thead>
	<tbody>
	
	<tr>
	<td width="160"><input name="buyer_email" type="text" id="buyer_email" value="" size="20" /></td>
	<td width="160"><input name="buyer_name" type="text" id="buyer_name" value="" size="20" /></td>
    <td width="160"><input name="refid" type="text" id="refid" value="" size="10" /></td>
    <td width="160"><input name="reference" type="text" id="reference" value="" size="4" /></td>    
    <td width="150"><input name="date" type="text" id="date" value="" size="10" /></td>
    <td width="160"><input name="time" type="text" id="time" value="" size="10" /></td>
    <td width="160"><input name="ipaddress" type="text" id="ipaddress" value="" size="12" /></td>
	<td>
	<p class="submit"><input type="submit" name="Submit" value="Add Lead" /> &nbsp; </p>
	</td></tr>	
		
	<tr><td colspan="7"><i>Tip: Leave the Date and Time field empty to use current Date and Time.</i></td></tr>	
	</tbody>
	</table>
	</form>
	</div></div>	
	<?php
}

function wp_aff_delete_leads_data($lead_id)
{
    global $wpdb;
    global $affiliates_leads_table_name;
    $updatedb = "DELETE FROM $affiliates_leads_table_name WHERE lead_id='$lead_id'";
    $results = $wpdb->query($updatedb);
    if($results>0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

?>