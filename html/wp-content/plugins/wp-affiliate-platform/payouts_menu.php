<?php

global $referrers;
global $payouts;

$affiliates_table = $wpdb->prefix . "affiliates_tbl";
$sales_table = $wpdb->prefix . "affiliates_sales_tbl";
$payouts_table = $wpdb->prefix . "affiliates_payouts_tbl";
	
function manage_payouts_menu()
{
	echo '<div class="wrap"><h2>WP Affiliate Platform - Manage Payouts</h2>';	
	echo '<div id="poststuff"><div id="post-body">'; 
		
	global $wpdb;
	global $affiliates_table,$payouts_table;
			
	$date = (date ("Y-m-d"));
	$time =	(date ("H:i:s"));
    $m = date('m');
    $y = date('Y');
	$start_date = $y.'-'.$m.'-01';
	$end_date = $date;
	
    if (isset($_POST['generate_payouts_report']))
    {   	
    	update_option('wp_aff_min_payouts', ((string)$_POST["min_payout_balance"]));
    	$min_payout = get_option('wp_aff_min_payouts');
    			
		if ($min_payout == '')
		{
			echo '<div id="message" class="error"><p>'.__('Please Enter a Minimum Payout Value.', 'wp_affiliate').'</p></div>'; 
		}
		else
		{
			echo generate_payouts_report($min_payout);
			echo "<br />";
			echo '<div id="message" class="updated fade"><p>Payouts Report Generated</p></div>';
		}		       
    }
    if(isset($_POST['generate_payouts_report_upto_date']))
    {
    	update_option('wp_aff_min_payouts', ((string)$_POST["min_payout_balance"]));
    	$min_payout = get_option('wp_aff_min_payouts');   
    	$enddate = $_POST["end_date"]; 	
    	$validDate = true;
    	if(!wp_aff_platform_is_date_valid($enddate))    	
		{
			$message .= "End Date is invalid. Please enter date in yyyy-mm-dd formate (eg. 2010-12-30)<br />";
			$validDate = false;
		}
		if($validDate)
		{
			update_option('wp_aff_payouts_enddate', $enddate);
			if ($min_payout == '')
			{
				echo '<div id="message" class="error"><p>'.__('Please Enter a Minimum Payout Value.', 'wp_affiliate').'</p></div>'; 
			}
			else
			{
				echo wp_aff_generate_payouts_report_upto_date($min_payout,$enddate);
				echo "<br />";
				$message = 'Payouts Report Generated';
			}
		}
		echo '<div id="message" class="updated fade">'.$message.'</div>';	
    }
    if (isset($_POST['generate_payouts_report_by_date']))
    {   	
    	update_option('wp_aff_min_payouts', ((string)$_POST["min_payout_balance"]));
    	$min_payout = get_option('wp_aff_min_payouts');
    	
    	$validDate = true;
    	$startdate = $_POST["start_date"];
    	$enddate = $_POST["end_date"];
		
		if(!wp_aff_platform_is_date_valid($startdate))    	
		{
			$message .= "Start Date is invalid. Please enter date in yyyy-mm-dd formate (eg. 2010-12-25)<br />";
			$validDate = false;
		}
    	if(!wp_aff_platform_is_date_valid($enddate))    	
		{
			$message .= "End Date is invalid. Please enter date in yyyy-mm-dd formate (eg. 2010-12-30)<br />";
			$validDate = false;
		}			
		if($validDate)
		{
			update_option('wp_aff_payouts_startdate', $startdate);
			update_option('wp_aff_payouts_enddate', $enddate);
			if ($min_payout == '')
			{
				echo '<div id="message" class="updated fade"><p>'.__('Please Enter a Minimum Payout Value.', 'wp_affiliate').'</p></div>'; 
			}
			else
			{
				echo generate_payouts_report_by_date($min_payout,$startdate,$enddate);
				echo "<br />";
				$message = 'Payouts Report Generated';
			}
		}
		echo '<div id="message" class="updated fade">'.$message.'</div>';		       
    }    
    if (isset($_POST['generate_mass_pay_file']))
    {    
		echo create_mass_pay_file();
		echo "<br />";			
    }
	if (isset($_POST['mark_as_paid']))
	{
		$min_payout = get_option('wp_aff_min_payouts');//(string)$_POST["min_payout_balance"];
		if ($min_payout != '')
		{
			echo '<div id="message" class="updated fade"><p>'.__('Do you really want to mark all the outstanding payments as paid? This action cannot be undone.', 'wp_affiliate').' <a href="admin.php?page=manage_payouts&label_paid=true">'.__('Yes', 'wp_affiliate').'</a> &nbsp; <a href="admin.php?page=manage_payouts">'.__('No!', 'wp_affiliate').'</a></p></div>'; 
		}
		else
		{
			echo '<div id="message" class="error"><p>'.__('Please Enter a Minimum Payout Value.', 'wp_affiliate').'</p></div>';
		}
	}

	if (isset($_GET['label_paid']))
	{	
		echo label_as_paid();
	}
	if (isset($_POST['search_aff']))
	{
	    echo '<div id="message" class="updated fade"><p><strong>';
	    echo 'Displaying Affiliate\'s Payment Data';
	    echo '</strong></p></div>';	  		
		$search_term = (string)$_POST["wp_aff_referrer_search"];
		//update_option('wp_aff_payouts_referrer_search', (string)$_POST["wp_aff_referrer_search"]);

		//$wp_aff_affiliates_db = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$search_term'", OBJECT);
		$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table WHERE refid like '%".$search_term."%' OR firstname like '%".$search_term."%' OR email like '%".$search_term."%'", OBJECT);
	    echo wp_aff_payouts_report_for_individual($wp_aff_affiliates_db);
	}
    if(isset($_POST['mark_paid']))
    {
    	$ref_id = $_POST['ref_id'];
    	$pay_amt = $_POST['pay_amt'];
    	$message = 'Payment marked as paid';
    	if($pay_amt>0)
    	{
			$updatedb = "INSERT INTO $payouts_table VALUES ('$ref_id', '$date', '$time', '$pay_amt')";
			$results = $wpdb->query($updatedb);	    	
    	}
    	else
    	{
    		$message = 'Payment amount need to be more than zero to be able to mark it as paid';
    	}
        echo '<div id="message" class="updated fade"><p><strong>';
	    echo $message;
	    echo '</strong></p></div>';
    }
	?>
	
	<strong>There are four ways you can pay your affiliates:</strong>
	<br /><i>&raquo; Payout all the affiliates by their outstanding commission amount to date (Option A)</i>
	<br /><i>&raquo; Payout all the outstanding affiliate commission upto a date (Option B)</i>
	<br /><i>&raquo; Payout all the affiliate commissions by date range (Option C)</i>
	<br /><i>&raquo; Payout an individual affiliate commission (Option D)</i>
	<br /><br />
	
	<div class="postbox">
	<h3><label for="title">Option A: Affiliate Mass Payout by Outstanding Amount</label></h3>
	<div class="inside">
		
    <form method="post" action="">    

    <strong>Step 1:</strong> Minimum Payout Balance:
    <input name="min_payout_balance" type="text" size="5" value="<?php echo get_option('wp_aff_min_payouts'); ?>" />
    <input type="submit" name="generate_payouts_report" value="<?php _e('Generate Report'); ?> &raquo;" />
    <br /><i>Enter the minimum payout balance in the text box and hit "Generate Report" to get a list of all the affiliate earnings that need to be paid.</i><br />
    <br />
    </form>

	<form method="post" action="">  
    <strong>Step 2:</strong> <input type="submit" name="generate_mass_pay_file" value="<?php _e('Create Payment Report File'); ?> &raquo;" />
    <br /><i>Use this to generate a PayPal mass payment and a payment report CSV file. The mass payment file can be used in paypal to pay all your affiliates in one click. If you have never used PayPal MassPay then watch <a href="http://www.tipsandtricks-hq.com/?p=1934" target="_blank">This Video Tutorial</a></i><br />
    <br />
    </form>

	<form method="post" action="">  
    <strong>Step 3:</strong> <input type="submit" name="mark_as_paid" value="<?php _e('Mark as Paid'); ?> &raquo;" />
    <br /><i>After you have generated the payout report and paid all the affiliates their outstanding balance, use this button to mark all the payments as paid.</i><br />
    <br />
        
    </form>
    </div></div>
    
	<div class="postbox">
	<h3><label for="title">Option B: Affiliate Mass Payout by Outstanding Amount Upto a Date</label></h3>
	<div class="inside">
		
    <form method="post" action="">
    <strong>Step 1:</strong> Select a cutoff date (report willl be generated based on all outstanding commission upto this date)           
    <br /><br />
    Cutoff Date (yyyy-mm-dd):
    <input type="text" id="end_date" name="end_date" value="<?php echo get_option('wp_aff_payouts_enddate'); ?>" size="12">
	<br />	
    	    
    <strong>Step 2:</strong> Minimum Payout Balance: 
    <input name="min_payout_balance" type="text" size="5" value="<?php echo get_option('wp_aff_min_payouts'); ?>" />
    <input type="submit" name="generate_payouts_report_upto_date" value="<?php _e('Generate Report'); ?> &raquo;" />
    <br /><i>Enter the minimum payout balance in the text box and hit "Generate Report" to get a list of all the affiliate earnings that need to be paid.</i><br />
    <br />
    </form>

	<form method="post" action="">
    <strong>Step 3:</strong> <input type="submit" name="generate_mass_pay_file" value="<?php _e('Create Payment Report File'); ?> &raquo;" />
    <br /><i>Use this to generate a PayPal mass payment and a payment report CSV file. The mass payment file can be used in paypal to pay all your affiliates in one click. If you have never used PayPal MassPay then watch <a href="http://www.tipsandtricks-hq.com/?p=1934" target="_blank">This Video Tutorial</a></i><br />
    <br />
    </form>

	<form method="post" action="">
    <strong>Step 4:</strong> <input type="submit" name="mark_as_paid" value="<?php _e('Mark as Paid'); ?> &raquo;" />
    <br /><i>After you have generated the payout report and paid all the affiliates their outstanding balance, use this button to mark all the payments as paid.</i><br />
    <br />
        
    </form>
    </div></div>
        
	<div class="postbox">
	<h3><label for="title">Option C: Affiliate Mass Payout by Date Range</label></h3>
	<div class="inside">
		
    <form method="post" action="">    

    <strong>Step 1:</strong> Select a date range (yyyy-mm-dd) (report will be generated based on all the affiliate commission accumulated during this period)           
    <br /><br />
    Start Date: 
    <input type="text" id="start_date" name="start_date" value="<?php echo get_option('wp_aff_payouts_startdate'); ?>" size="12">
	
    End Date:
    <input type="text" id="end_date" name="end_date" value="<?php echo get_option('wp_aff_payouts_enddate'); ?>" size="12">
	<br />	
    	    
    <strong>Step 2:</strong> Minimum Payout Balance: 
    <input name="min_payout_balance" type="text" size="5" value="<?php echo get_option('wp_aff_min_payouts'); ?>" />
    <input type="submit" name="generate_payouts_report_by_date" value="<?php _e('Generate Report'); ?> &raquo;" />
    <br /><i>Enter the minimum payout balance in the text box and hit "Generate Report" to get a list of all the affiliate earnings that need to be paid.</i><br />
    <br />
    </form>

	<form method="post" action="">
    <strong>Step 3:</strong> <input type="submit" name="generate_mass_pay_file" value="<?php _e('Create Payment Report File'); ?> &raquo;" />
    <br /><i>Use this to generate a PayPal mass payment and a payment report CSV file. The mass payment file can be used in paypal to pay all your affiliates in one click. If you have never used PayPal MassPay then watch <a href="http://www.tipsandtricks-hq.com/?p=1934" target="_blank">This Video Tutorial</a></i><br />
    <br />
    </form>

	<form method="post" action="">
    <strong>Step 4:</strong> <input type="submit" name="mark_as_paid" value="<?php _e('Mark as Paid'); ?> &raquo;" />
    <br /><i>After you have generated the payout report and paid all the affiliates their outstanding balance, use this button to mark all the payments as paid.</i><br />
    <br />
        
    </form>
    </div></div>
        
	<div class="postbox">
	<h3><label for="title">Option D: Individual Affiliate Payout</label></h3>
	<div class="inside">
	<strong>Search for an Affiliate by Entering the Affiliate ID or First Name or Email address</strong> (Full or Partial)
	<br /><br />
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    
    <input name="wp_aff_referrer_search" type="text" size="35" value=""/>
    <div class="submit">
        <input type="submit" name="search_aff" value="<?php _e('Search'); ?> &raquo;" />
    </div>   
    </form> 	
	</div></div>    
    
    <?php
    echo '</div></div>';
    echo '</div>';
}

function generate_payouts_report($min_payout)
{
	$output .= '
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('Referrer ID', 'wp_affiliate').'</th>
	<th scope="col">'.__('Name', 'wp_affiliate').'</th>
	<th scope="col">'.__('PayPal Email', 'wp_affiliate').'</th>
	<th scope="col">'.__('Pending Amount', 'wp_affiliate').'</th>
	</tr></thead>
	<tbody>';
	
	$no_pending_payment = true;
	$counter = 0;
	global $referrers;
	global $payouts;
			
	global $wpdb;
	global $affiliates_table;
	global $sales_table;
	global $payouts_table;	
	
	$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table ORDER BY date", OBJECT);
	if ($wp_aff_affiliates_db)
	{
		foreach ($wp_aff_affiliates_db as $wp_aff_affiliates_db)
		{
			$processing_affs_refid = $wp_aff_affiliates_db->refid;			 
			$row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where refid = '".$processing_affs_refid."'", OBJECT);
			$total_earnings = $row->total;
			
			if ($total_earnings >= $min_payout)
			{
				$payouts_row = $wpdb->get_row("select SUM(payout_payment) AS total from $payouts_table where refid = '".$processing_affs_refid."'", OBJECT);								
				$total_payouts_payment = $payouts_row->total;
				$pending_payment = round(($total_earnings - $total_payouts_payment),2);

				if ($pending_payment >= $min_payout)
				{
					$affiliates_name = $wp_aff_affiliates_db->firstname." ".$wp_aff_affiliates_db->lastname;
					$output .= '<tr>';
					$output .= '<td>'.$processing_affs_refid.'</td>';
					$output .= '<td><strong>'.$affiliates_name.'</strong></td>';
					$output .= '<td><strong>'.$wp_aff_affiliates_db->paypalemail.'</strong></td>';
					$output .= '<td><strong>'.$pending_payment.'</strong></td>';
					$output .= '</tr>';	
					$no_pending_payment = false;
					$referrers[$counter] = $processing_affs_refid;
					$payouts[$counter] = $pending_payment;
					$counter++;			
				}
			}
		}
	}
	else
	{
		$output .= '<tr> <td colspan="4">'.__('No Affiliates Found in the Database.', 'wp_affiliate').'</td> </tr>';
	}
	if ($no_pending_payment)
	{
		$output .= '<tr> <td colspan="4">'.__('No Pending Payment Found.', 'wp_affiliate').'</td> </tr>';
	}
	$output .= '</tbody></table>';
	
	update_option('report_generated', true);
	update_option('wp_aff_platform_referrers', $referrers);
	update_option('wp_aff_platform_payouts', $payouts);
	
	return $output;
}

function create_mass_pay_file()
{
	$referrers = get_option('wp_aff_platform_referrers');
	$payouts = get_option('wp_aff_platform_payouts');
	$currency_code = get_option('wp_aff_currency');
	global $wpdb;
	global $affiliates_table;
	global $sales_table;
	global $payouts_table;

	if (empty($referrers))
	{
		$output = '<div id="message" class="updated fade"><p>There are no pending payment.</p></div>';
		return $output;
	}

	for ($i=0; $i<sizeof($referrers);$i++)	
	{
		$row = $wpdb->get_row("select * from $affiliates_table where refid = '$referrers[$i]'", OBJECT);
		if (!empty($row->paypalemail))
		{
			$output .= $row->paypalemail;
			$output .= "\t";
			$output .= round($payouts[$i],2);
			$output .= "\t";
			$output .= $currency_code;
			$output .= "\n";						
		}
		
	}
	$abs_file_path = WP_AFF_PLATFORM_PATH.'paypal_mass_pay.txt';//realpath(dirname(__FILE__)).'/paypal_mass_pay.txt';
	$File = $abs_file_path;//"paypal_mass_pay.txt";
	$Handle = fopen($File,'w') or die("can't open file named 'paypal_mass_pay.txt'");
	fwrite($Handle, $output);	
	fclose($Handle);
	
	//Create the CSV file with affiliate payout details
	if(WP_AFFILIATE_ENABLE_UTF_8_ENCODING === '1'){
		$separator = "\t";
	}
	else{
		$separator = ", ";
	}
	$csv_output = "";
	$csv_output.= "Commission Amount". $separator;
	$csv_output.= "Currency". $separator;
	$csv_output.= "Affiliate ID". $separator;
	$csv_output.= "First Name". $separator;
	$csv_output.= "Last Name". $separator;
	$csv_output.= "Email". $separator;
	$csv_output.= "Street". $separator;
	$csv_output.= "City". $separator;
	$csv_output.= "State". $separator;
	$csv_output.= "Postal Code". $separator;
	$csv_output.= "Country". $separator;
	$csv_output.= "Phone". $separator;
	$csv_output.= "Tax ID". $separator;
	$csv_output.= "Bank Account Details". $separator;
	$csv_output.= "\n";
	for ($i=0; $i<sizeof($referrers);$i++)	
	{
		$row = $wpdb->get_row("select * from $affiliates_table where refid = '$referrers[$i]'", OBJECT);
		
		$csv_output.= wp_aff_escape_csv_value(round($payouts[$i],2)). $separator;
		$csv_output.= wp_aff_escape_csv_value($currency_code). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->refid )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->firstname )). $separator;		
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->lastname )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->email )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->street )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->town )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->state )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->postcode )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->country )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->phone )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->tax_id )). $separator;
		$csv_output.= wp_aff_escape_csv_value(stripslashes($row->account_details )). $separator;
		$csv_output.= "\n";
	}		
	if(WP_AFFILIATE_ENABLE_UTF_8_ENCODING === '1'){
		$csv_output = chr(255).chr(254).mb_convert_encoding( $csv_output, 'UTF-16LE', 'UTF-8');
	}	
	
	$csv_file_abs_path = WP_AFF_PLATFORM_PATH.'affiliate_payout_report.csv';//realpath(dirname(__FILE__)).'/affiliate_payout_report.csv';
	$Handle = fopen($csv_file_abs_path,'w') or die("can't open file named 'affiliate_payout_report.csv'");	
	fwrite($Handle, $csv_output);	
	fclose($Handle);		
	
	$output = nl2br($output);
	if(empty($output)){
		$output .= '<div id="message" class="error"><p>Note: Please make sure that the PayPal email address field of the affiliates that are about to get paid via PayPal are not empty. PayPal Mass Pay do not work without PayPal email address. You can ignore this warning if you are going to pay your affiliates via other means (example, bank transfer, bank check).</p></div>';
	}
	else{
		$output .= '<div id="message" class="updated fade"><p>PayPal Mass Payout file created. Download the <a href="'.WP_AFF_PLATFORM_URL.'/paypal_mass_pay.txt">Mass Payout File</a> (Right click and choose "Save Link As"). You can use this file to make a PayPal mass payment and pay the commissions in one go.</p></div>';
	}
	
	//Show link for the CSV file
	$output .= '<div id="message" class="updated fade"><p>CSV file with outstanding affiliate commission details created. Download the <a href="'.WP_AFF_PLATFORM_URL.'/affiliate_payout_report.csv">Affiliate Payout Report File</a> (Right click and choose "Save Link As"). You can use this file to manually send money to your affiliates using bank transfer or bank cheque.</p></div>';
	
	return $output;
}

function label_as_paid()
{
	$date = (date ("Y-m-d"));
	$time =	(date ("H:i:s"));
	
	$referrers = get_option('wp_aff_platform_referrers');
	$payouts = get_option('wp_aff_platform_payouts');
	global $wpdb;
	global $payouts_table;

	if (sizeof($referrers) == 0)
	{
		$output = '<div id="message" class="updated fade"><p>There are no pending payment to mark.</p></div>';
		return $output;
	}
	for ($i=0; $i<sizeof($referrers);$i++)	
	{
		$updatedb = "INSERT INTO $payouts_table VALUES ('$referrers[$i]', '$date', '$time', '$payouts[$i]')";
		$results = $wpdb->query($updatedb);
	}
	$output = '<div id="message" class="updated fade"><p>Marked payments as paid</p></div>';
	return $output;
}

function wp_aff_payouts_report_for_individual($wp_aff_affiliates_db)
{
	$output .= '
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('Referrer ID', 'wp_affiliate').'</th>
	<th scope="col">'.__('Name', 'wp_affiliate').'</th>
	<th scope="col">'.__('PayPal Email', 'wp_affiliate').'</th>
	<th scope="col">'.__('Pending Amount', 'wp_affiliate').'</th>
	<th scope="col">'.__('Action', 'wp_affiliate').'</th>
	</tr></thead>
	<tbody>';
	
	//$no_pending_payment = true;
	$counter = 0;
	global $referrers;
	global $payouts;
			
	global $wpdb;
	global $affiliates_table;
	global $sales_table;
	global $payouts_table;	
	
	//$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table ORDER BY date", OBJECT);
	if ($wp_aff_affiliates_db)
	{
		foreach ($wp_aff_affiliates_db as $wp_aff_affiliates_db)
		{
			$processing_affs_refid = $wp_aff_affiliates_db->refid;			 
			$row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where refid = '".$processing_affs_refid."'", OBJECT);
			$total_earnings = $row->total;
			
			//if ($total_earnings >= $min_payout)
			//{
				$payouts_row = $wpdb->get_row("select SUM(payout_payment) AS total from $payouts_table where refid = '".$processing_affs_refid."'", OBJECT);								
				$total_payouts_payment = $payouts_row->total;
				$pending_payment = round(($total_earnings - $total_payouts_payment),2);

				//if ($pending_payment >= $min_payout)
				//{
					$affiliates_name = $wp_aff_affiliates_db->firstname." ".$wp_aff_affiliates_db->lastname;
					$output .= '<tr>';
					$output .= '<td>'.$processing_affs_refid.'</td>';
					$output .= '<td><strong>'.$affiliates_name.'</strong></td>';
					$output .= '<td><strong>'.$wp_aff_affiliates_db->paypalemail.'</strong></td>';
					$output .= '<td><strong>'.$pending_payment.'</strong></td>';
                       
					$output .= "<td>";
					$output .= "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you have paid the affiliate and want to mark this payment as paid?');\">";
                    $output .= "<input type=\"hidden\" name=\"ref_id\" value=".$processing_affs_refid." />";
                    $output .= "<input type=\"hidden\" name=\"pay_amt\" value=".$pending_payment." />";
                    $output .= "<input type=\"submit\" value=\"Mark as Paid\" name=\"mark_paid\">";
                    $output .= "</form>";
                    $output .= "</td>";
                        					
					$output .= '</tr>';	
					//$no_pending_payment = false;
					$referrers[$counter] = $processing_affs_refid;
					$payouts[$counter] = $pending_payment;
					$counter++;			
				//}
			//}
		}
	}
	else
	{
		$output .= '<tr> <td colspan="5">'.__('No Affiliates Found for that term.', 'wp_affiliate').'</td> </tr>';
	}

	$output .= '</tbody></table>';	
	return $output;
}

function wp_aff_generate_payouts_report_upto_date($min_payout,$enddate)
{
	$output .= '
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('Referrer ID', 'wp_affiliate').'</th>
	<th scope="col">'.__('Name', 'wp_affiliate').'</th>
	<th scope="col">'.__('PayPal Email', 'wp_affiliate').'</th>
	<th scope="col">'.__('Pending Amount', 'wp_affiliate').'</th>
	</tr></thead>
	<tbody>';
	
	$no_pending_payment = true;
	$counter = 0;
	global $referrers;
	global $payouts;
			
	global $wpdb;
	global $affiliates_table;
	global $sales_table;
	global $payouts_table;	
	
	$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table ORDER BY date", OBJECT);
	if ($wp_aff_affiliates_db)
	{
		foreach ($wp_aff_affiliates_db as $wp_aff_affiliates_db)
		{
			$processing_affs_refid = $wp_aff_affiliates_db->refid;			 
			$row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where refid = '".$processing_affs_refid."' AND date < '$enddate'", OBJECT);
			$total_earnings = $row->total;
			
			if ($total_earnings >= $min_payout)
			{
				$payouts_row = $wpdb->get_row("select SUM(payout_payment) AS total from $payouts_table where refid = '".$processing_affs_refid."'", OBJECT);								
				$total_payouts_payment = $payouts_row->total;
				$pending_payment = round(($total_earnings - $total_payouts_payment),2);

				if ($pending_payment >= $min_payout)
				{
					$affiliates_name = $wp_aff_affiliates_db->firstname." ".$wp_aff_affiliates_db->lastname;
					$output .= '<tr>';
					$output .= '<td>'.$processing_affs_refid.'</td>';
					$output .= '<td><strong>'.$affiliates_name.'</strong></td>';
					$output .= '<td><strong>'.$wp_aff_affiliates_db->paypalemail.'</strong></td>';
					$output .= '<td><strong>'.$pending_payment.'</strong></td>';
					$output .= '</tr>';	
					$no_pending_payment = false;
					$referrers[$counter] = $processing_affs_refid;
					$payouts[$counter] = $pending_payment;
					$counter++;			
				}
			}
		}
	}
	else
	{
		$output .= '<tr> <td colspan="4">'.__('No Affiliates Found in the Database.', 'wp_affiliate').'</td> </tr>';
	}
	if ($no_pending_payment)
	{
		$output .= '<tr> <td colspan="4">'.__('No Pending Payment Found.', 'wp_affiliate').'</td> </tr>';
	}
	$output .= '</tbody></table>';
	
	update_option('report_generated', true);
	update_option('wp_aff_platform_referrers', $referrers);
	update_option('wp_aff_platform_payouts', $payouts);
	
	return $output;
}

function generate_payouts_report_by_date($min_payout,$startdate,$enddate)
{
	$output .= '
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('Referrer ID', 'wp_affiliate').'</th>
	<th scope="col">'.__('Name', 'wp_affiliate').'</th>
	<th scope="col">'.__('PayPal Email', 'wp_affiliate').'</th>
	<th scope="col">'.__('Pending Amount', 'wp_affiliate').'</th>
	</tr></thead>
	<tbody>';
	
	$no_pending_payment = true;
	$counter = 0;
	global $referrers;
	global $payouts;
			
	global $wpdb;
	global $affiliates_table;
	global $sales_table;
	global $payouts_table;	
	
	$wp_aff_affiliates_db = $wpdb->get_results("SELECT * FROM $affiliates_table ORDER BY date", OBJECT);
	if ($wp_aff_affiliates_db)
	{
		foreach ($wp_aff_affiliates_db as $wp_aff_affiliates_db)
		{
			$processing_affs_refid = $wp_aff_affiliates_db->refid;			 
			$row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where refid = '".$processing_affs_refid."' AND date BETWEEN '$startdate' AND '$enddate'", OBJECT);
			$total_earnings = $row->total;
			
			if ($total_earnings >= $min_payout)
			{
				//$payouts_row = $wpdb->get_row("select SUM(payout_payment) AS total from $payouts_table where refid = '".$processing_affs_refid."'", OBJECT);								
				//$total_payouts_payment = $payouts_row->total;
				//$pending_payment = round(($total_earnings - $total_payouts_payment),2);
				$pending_payment = $total_earnings;

				if ($pending_payment >= $min_payout)
				{
					$affiliates_name = $wp_aff_affiliates_db->firstname." ".$wp_aff_affiliates_db->lastname;
					$output .= '<tr>';
					$output .= '<td>'.$processing_affs_refid.'</td>';
					$output .= '<td><strong>'.$affiliates_name.'</strong></td>';
					$output .= '<td><strong>'.$wp_aff_affiliates_db->paypalemail.'</strong></td>';
					$output .= '<td><strong>'.$pending_payment.'</strong></td>';
					$output .= '</tr>';	
					$no_pending_payment = false;
					$referrers[$counter] = $processing_affs_refid;
					$payouts[$counter] = $pending_payment;
					$counter++;			
				}
			}
		}
	}
	else
	{
		$output .= '<tr> <td colspan="4">'.__('No Affiliates Found in the Database.', 'wp_affiliate').'</td> </tr>';
	}
	if ($no_pending_payment)
	{
		$output .= '<tr> <td colspan="4">'.__('No Pending Payment Found.', 'wp_affiliate').'</td> </tr>';
	}
	$output .= '</tbody></table>';
	
	update_option('report_generated', true);
	update_option('wp_aff_platform_referrers', $referrers);
	update_option('wp_aff_platform_payouts', $payouts);
	
	return $output;
}

function wp_aff_platform_is_date_valid($date)
{
    	$arr=split("-",$date); // splitting the array
		$yy=$arr[0]; // first element of the array is year
		$mm=$arr[1]; // second element is month
		$dd=$arr[2]; // third element is day
		If(!checkdate($mm,$dd,$yy)){
			return false;
		}	
		else{
			return true;
		}
}
?>