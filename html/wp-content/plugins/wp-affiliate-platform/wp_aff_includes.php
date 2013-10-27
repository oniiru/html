<?php
$aff_tx_msg = '';
$aff_error_msg = ''; 

/*** Start of deprecated code (need to be deleted) ***/
function wp_aff_display_transaction_result()
{
	global $aff_tx_msg,$aff_error_msg;   
	$output .= '<h4>The commission award result</h4>'; 
    if (empty($aff_error_msg))
    {
    	$output .= '<div STYLE="word-wrap: break-word">';    	
    	$output .= $aff_tx_msg.'<br />';
    	$output .= '</div>';
    }
    else
    {
    	$output .= '<br />'.$aff_error_msg.'<br />';
    }    
    return $output;	
}
/******************************/
/*** End of deprecated code ***/
/******************************/

function wp_aff_process_PDT_payment_data($keyarray)
{
    $gross_total = $keyarray['mc_gross'];	
    $sale_amount = $keyarray['mc_gross'] - $keyarray['mc_shipping'] - $keyarray['mc_handling'];	    
    $txn_id = $keyarray['txn_id'];
    $item_id = $keyarray['item_number'];
    $buyer_email = $keyarray['payer_email'];
	$referrer = wp_affiliate_get_referrer(); 
	$buyer_name = $keyarray['first_name'] . " " .$keyarray['last_name'];
	wp_aff_award_commission($referrer,$sale_amount,$txn_id,$item_id,$buyer_email,'','',$buyer_name);
}

function wp_aff_prepare_data()
{
    $gross_total = $_POST['mc_gross'];	
    $sale_amount = $_POST['mc_gross'] - $_POST['mc_shipping'] - $_POST['mc_handling'];	    
    $txn_id = $_POST['txn_id'];
    $item_id = '';
    $buyer_email = $_POST['payer_email'];
	$referrer = wp_affiliate_get_referrer();  
	wp_aff_award_commission($referrer,$sale_amount,$txn_id,$item_id,$buyer_email);
}

function wp_aff_add_unique_commission_amt_directly($fields)
{
	global $wpdb;
	$aff_sales_table = WP_AFF_SALES_TBL_NAME;
	$txn_id = $fields['txn_id'];
	wp_affiliate_log_debug("wp_aff_add_unique_commission_amt_directly() - Txn ID: ".$txn_id,true);
	$resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
	if($resultset){//Commission for this transaction has already been awarded so no need to do anything.
		wp_affiliate_log_debug("Commission for this transaction has already been awarded so no need to do anything. Transaction ID:".$txn_id,true);
	}
	else{
		wp_affiliate_log_debug("Calling add direct commission function.",true);
		wp_aff_add_commission_amt_directly($fields);
	}
}

function wp_aff_add_commission_amt_directly($fields)
{
	global $wpdb;
	$inTable = WP_AFF_SALES_TBL_NAME;

	if(!isset($fields['date'])){$fields['date'] = (date ("Y-m-d"));}
	if(!isset($fields['time'])){$fields['time'] = (date ("H:i:s"));}
	if(!isset($fields['ipaddress'])){$fields['ipaddress'] = $_SERVER['REMOTE_ADDR'];}

	$fieldss = '';
	$valuess = '';
	$first = true;
	foreach($fields as $field=>$value)
	{
		if($first){$first = false;}
		else{
            $fieldss .= ' , ';
            $valuess .= ' , ';
		}
		$fieldss .= " $field ";
		$valuess .= " '" .$wpdb->escape($value)."' ";
	}
	$query .= " INSERT INTO $inTable ($fieldss) VALUES ($valuess)";
	$results = $wpdb->query($query);
	return $results;
}

function wp_aff_award_commission_unique($referrer,$sale_amount,$txn_id,$item_id,$buyer_email,$clientip='',$comm_rate='',$buyer_name='')
{
	global $wpdb;
	$aff_sales_table = WP_AFF_SALES_TBL_NAME;
	wp_affiliate_log_debug("wp_aff_award_commission_unique() - Txn ID: ".$txn_id,true);
	$resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
	if($resultset){
		//Commission for this transaction has already been awarded so no need to do anything.
		wp_affiliate_log_debug("Commission for this transaction has already been awarded so no need to do anything. Transaction ID:".$txn_id,true);
	}
	else{
		wp_affiliate_log_debug("Calling process commission function.",true);
		wp_aff_award_commission($referrer,$sale_amount,$txn_id,$item_id,$buyer_email,$clientip,$comm_rate,$buyer_name);
	}	
}

function wp_aff_award_commission($referrer,$sale_amount,$txn_id,$item_id,$buyer_email,$clientip='',$comm_rate='',$buyer_name='')
{
	global $aff_tx_msg,$aff_error_msg;
	$commission_award_result = "";
	$debug_data = "";
	$debug_data .= "Referrer: ".$referrer. ", Sale Amount: ".$sale_amount.", Transaction ID: ".$txn_id;
	$debug_data .= "Item ID: ".$item_id. ", Buyer Email: ".$buyer_email.", Custom Commission Rate: ".$comm_rate;
	$aff_tx_msg = $debug_data;
	wp_affiliate_log_debug($debug_data,true);
    $clientdate = (date ("Y-m-d"));
	$clienttime	= (date ("H:i:s"));
	if(empty($clientip))
	{
		$clientip = $_SERVER['REMOTE_ADDR'];
	}	
    if(empty($txn_id))
    {
        $txn_id = uniqid();
    }
	if (!empty($referrer))
	{
		global $wpdb;
		$affiliates_table_name = $wpdb->prefix . "affiliates_tbl";
		$aff_sales_table = $wpdb->prefix . "affiliates_sales_tbl";
		$wp_aff_affiliates_db = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$referrer'", OBJECT);
		if(empty($comm_rate)){		
			$commission_level = $wp_aff_affiliates_db->commissionlevel;
		}
		else{
			$commission_level = $comm_rate;
		}
		
		if (get_option('wp_aff_use_fixed_commission'))
		{
            $commission_amount = $commission_level;
        }
        else
        {
		    $commission_amount = ($commission_level * $sale_amount)/100;
        }
		    
		if(WP_AFFILIATE_NO_COMMISSION_FOR_SELF_PURCHASE == '1'){
			if(!empty($buyer_email)){
				if(wp_aff_check_if_buyer_is_referrer($referrer,$buyer_email)){
					wp_affiliate_log_debug('The buyer ('.$buyer_email.') is the referrer ('.$referrer.') so this sale is NOT ELIGIBLE for generating any commission.',true);
					return true;
				}
			}
			else{
				 wp_affiliate_log_debug("Buyer email data is missing from the request so the plugin cannot verify the WP_AFFILIATE_NO_COMMISSION_FOR_SELF_PURCHASE option",true);
			}
		}

		$commission_amount = round($commission_amount,2);
        $c_id='';
        if(!empty($buyer_name)){
			$updatedb = "INSERT INTO $aff_sales_table (refid,date,time,browser,ipaddress,payment,sale_amount,txn_id,item_id,buyer_email,campaign_id,buyer_name) VALUES ('$referrer','$clientdate','$clienttime','','$clientip','$commission_amount','$sale_amount','$txn_id','$item_id','$buyer_email','$c_id','$buyer_name')";        	
        }
        else{
       		$updatedb = "INSERT INTO $aff_sales_table (refid,date,time,browser,ipaddress,payment,sale_amount,txn_id,item_id,buyer_email,campaign_id) VALUES ('$referrer','$clientdate','$clienttime','','$clientip','$commission_amount','$sale_amount','$txn_id','$item_id','$buyer_email','$c_id')";
        }		
       	$results = $wpdb->query($updatedb);	
		if(!$results)
		{
			$aff_tx_msg .= "<br />The database update query failed for table: ".$aff_sales_table;
			wp_affiliate_log_debug("The database update query failed for table: ".$aff_sales_table,false);
		}		
		else
		{		
			$aff_tx_msg .= '<br />The sale has been registered in the WP Affiliate Platform Database for referrer: '.$referrer.' with amount: '.$commission_amount;
			wp_affiliate_log_debug('The sale has been registered in the WP Affiliate Platform Database for referrer: '.$referrer.' with amount: '.$commission_amount,true);
		}
		wp_aff_send_commission_notification($wp_aff_affiliates_db->email,$txn_id);
		
		// 2nd tier commission
		$commission_award_result = wp_aff_award_second_tier_commission($wp_aff_affiliates_db,$sale_amount,$txn_id,$item_id,$buyer_email);
	}
	return $commission_award_result;		
}

function wp_aff_award_second_tier_commission($wp_aff_affiliates_db,$sale_amount,$txn_id,$item_id,$buyer_email)
{
	global $aff_tx_msg;
    $clientdate = (date ("Y-m-d"));
	$clienttime	= (date ("H:i:s"));	
		
		if (get_option('wp_aff_use_2tier') && !empty($wp_aff_affiliates_db->referrer))
		{
			$aff_tx_msg .= '<br />Using tier model';
			wp_affiliate_log_debug("Using tier model",true);
			$award_tier_commission = true;	
			$duration = get_option('wp_aff_2nd_tier_duration');		
			if(!empty($duration))
			{
				$join_date = $wp_aff_affiliates_db->date;
				$days_since_joined = round((strtotime(date("Y-m-d")) - strtotime($join_date) ) / (60 * 60 * 24));
				
				if ($days_since_joined > $duration)
				{
					$aff_tx_msg .= '<br />Tier commission award duration expried';
					wp_affiliate_log_debug("Tier commission award duration expried! No tier commission will be awarded for this sale.",true);
					$award_tier_commission = false;
				}
			}				
			if ($award_tier_commission)
			{
				if(!empty($wp_aff_affiliates_db->sec_tier_commissionlevel)){
					$second_tier_commission_level = $wp_aff_affiliates_db->sec_tier_commissionlevel;
					wp_affiliate_log_debug("Using the affiliate specific 2nd tier commission for this referral. 2nd tier commission level: ".$second_tier_commission_level,true);
				}
				else{
					$second_tier_commission_level = get_option('wp_aff_2nd_tier_commission_level');
					wp_affiliate_log_debug("Using global 2nd tier commission for this referral. 2nd tier commission level: ".$second_tier_commission_level,true);
				}
				if (get_option('wp_aff_use_fixed_commission'))
				{
                    $commission_amount = $second_tier_commission_level;
                }
                else
                {
				    $commission_amount = round(($second_tier_commission_level * $sale_amount)/100,2);
                }
				global $wpdb;
				$aff_sales_table = $wpdb->prefix . "affiliates_sales_tbl";				
				$updatedb = "INSERT INTO $aff_sales_table (refid,date,time,browser,ipaddress,payment,sale_amount,txn_id,item_id,buyer_email) VALUES ('$wp_aff_affiliates_db->referrer','$clientdate','$clienttime','','','$commission_amount','$sale_amount','$txn_id','$item_id','$buyer_email')";
				$results = $wpdb->query($updatedb);	
				$aff_tx_msg .= '<br />Tier commission awarded to: '.$wp_aff_affiliates_db->referrer.'. Commission amount: '.$commission_amount;
				wp_affiliate_log_debug('Tier commission awarded to: '.$wp_aff_affiliates_db->referrer.'. Commission amount: '.$commission_amount,true);	
			}			
		}	
		return $aff_tx_msg;
}

function wp_aff_handle_refund($unique_txn_id)
{
    $allow_refund = get_option('wp_aff_commission_reversal');
    if ($allow_refund)
    {
    	wp_affiliate_log_debug("Handling refund...",true);
        global $wpdb;
        $sales_table = $wpdb->prefix . "affiliates_sales_tbl";
        $wp_aff_sales = $wpdb->get_results("SELECT * FROM $sales_table WHERE txn_id = '$unique_txn_id'", OBJECT);
        
        if ($wp_aff_sales)
        {
        	foreach ($wp_aff_sales as $wp_aff_sales)
        	{
        		if($wp_aff_sales->payment > 0)
        		{
					$referrer = $wp_aff_sales->refid;
					$clientdate = (date ("Y-m-d"));
		        	$clienttime	= (date ("H:i:s"));
					$commission_amount = "-".$wp_aff_sales->payment;
					$sale_amount = "-".$wp_aff_sales->sale_amount;
					$txn_id = $unique_txn_id;
			        $item_id = $wp_aff_sales->item_id;
			    	$buyer_email = $wp_aff_sales->buyer_email;
		
			      	$updatedb = "INSERT INTO $sales_table (refid,date,time,browser,ipaddress,payment,sale_amount,txn_id,item_id,buyer_email) VALUES ('$referrer','$clientdate','$clienttime','','','$commission_amount','$sale_amount','$txn_id','$item_id','$buyer_email')";
					$results = $wpdb->query($updatedb);
					wp_affiliate_log_debug("Commission refunded for transaction ID:".$txn_id,true);
        		}
        	}
        }
    }
}
?>
