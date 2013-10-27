<?php
add_action ("plugins_loaded", "wp_aff_3rd_party_handle_plugins_loaded_hook");
function wp_aff_3rd_party_handle_plugins_loaded_hook()
{
	wp_aff_check_clickbank_transaction();
}

/*** s2Member Integration ***/
if (defined ("WS_PLUGIN__S2MEMBER_VERSION")){
	add_action ("ws_plugin__s2member_before_sc_paypal_button_after_shortcode_atts", "wp_aff_s2member_integration");
	add_action ("ws_plugin__s2member_pro_before_sc_paypal_form_after_shortcode_atts", "wp_aff_s2member_integration");
	add_action ("ws_plugin__s2member_pro_before_sc_authnet_form_after_shortcode_atts", "wp_aff_s2member_integration");	
	add_action ("plugins_loaded", "wp_aff_s2member_specify_post_payment_notification_url");	
}
function wp_aff_s2member_integration ($vars = array ())
{
	$cookie_value = $_SESSION["ap_id"];
	if(empty($cookie_value)){
		$cookie_value = esc_html ($_COOKIE["ap_id"]);
	}
	if(!empty($cookie_value)){
    	$vars["__refs"]["attr"]["custom"] .= "|" . $cookie_value;
	}
}
function wp_aff_s2member_specify_post_payment_notification_url()
{
	$urls = &$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["payment_notification_urls"];
    $secret_key = get_option('wp_aff_secret_word_for_post');
    $wp_aff_payment_notification_url = WP_AFF_PLATFORM_URL.'/api/post.php?secret='.$secret_key.'&ap_id=%%cv1%%&sale_amt=%%amount%%&buyer_email=%%payer_email%%';
    $pos = strpos($urls, $wp_aff_payment_notification_url);    
    if ($pos === false) {
    	$urls = trim ($urls . "\n" . $wp_aff_payment_notification_url);
    }

	$specific_post_page_urls = &$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_sale_notification_urls"];    
    $pos2 = strpos($specific_post_page_urls, $wp_aff_payment_notification_url);    
    if ($pos2 === false) {
    	$specific_post_page_urls = trim ($specific_post_page_urls . "\n" . $wp_aff_payment_notification_url);
    }    
}
/*** End s2Member integration ***/
    
/*** WP-eCommerce Integration ***/
function wpsc_submit_checkout_handler($args)
{
	wp_affiliate_log_debug("WPSC Integration - wpsc_submit_checkout_handler(). Saving purchase log ID.",true);
	global $wpdb;
	$aff_relations_tbl = WP_AFF_RELATIONS_TBL_NAME;
	$purchase_log_id = $args['purchase_log_id'];
	$referrer = wp_affiliate_get_referrer();
    $clientdate = (date ("Y-m-d"));
	$clienttime	= (date ("H:i:s"));
	$clientip = $_SERVER['REMOTE_ADDR'];		
	$updatedb = "INSERT INTO $aff_relations_tbl (unique_ref,refid,reference,date,time,ipaddress,additional_info) VALUES ('$purchase_log_id','$referrer','wp_ecommerce','$clientdate','$clienttime','$clientip','')";        		
	$results = $wpdb->query($updatedb);       		
}
add_action('wpsc_submit_checkout','wpsc_submit_checkout_handler');//Alternative hook - wpsc_pre_submit_gateway

function wpsc_transaction_result_cart_item_handler($order_details)
{
	wp_affiliate_log_debug("WPSC Integration - wpsc_transaction_result_cart_item_handler()",true);
	$purchase_log = $order_details['purchase_log'];	
	$sale_amount = $purchase_log['totalprice'];
	$txn_id = $purchase_log['id'];
	$cart_item = $order_details['cart_item'];
	$item_id = $cart_item['prodid'];
	$buyer_email = wpsc_get_buyers_email($purchase_log['id']);
	$shipping = $purchase_log['base_shipping'];
	$sale_amount = $sale_amount - $shipping;
	$referrer = wp_affiliate_get_referrer();
	if(empty($referrer)){
		$referrer = wp_aff_retrieve_id_from_relations_tbl($purchase_log['id']);
	}
	wp_affiliate_log_debug("WPSC Integration - debug data: ".$referrer."|".$txn_id."|".$sale_amount."|".$buyer_email,true);

	global $wpdb;
	$aff_sales_table = WP_AFF_SALES_TBL_NAME;
	$resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
	if($resultset)
	{
		//Commission for this transaction has already been awarded so no need to do anything.
	}
	else
	{			
		if (!empty($referrer))
		{
		    wp_aff_award_commission($referrer,$sale_amount,$txn_id,$item_id,$buyer_email);
		}else{//Not an affiliate conversion		    
		    wp_affiliate_log_debug("WPSC Integration - referrer data (Affiliate ID) is empty so this is not an affiliate sale",true);
		}
	}	
}
add_action('wpsc_transaction_result_cart_item','wpsc_transaction_result_cart_item_handler');
/*** End WP-eCommerce ***/

/*** WooCommerce plugin integration ***/
add_action('woocommerce_thankyou', 'wp_aff_handle_woocommerce_payment');
//add_action('woocommerce_checkout_order_processed','wp_aff_handle_woocommerce_payment');	
function wp_aff_handle_woocommerce_payment($order_id)
{
	$order = new WC_Order($order_id);
	$total = $order->order_total;
	$shipping = $order->get_shipping();
	$sale_amount = $total - $shipping;
	$txn_id = $order_id;
	$item_id = "";
	$buyer_email = $order->billing_email;
	$referrer = $_COOKIE['ap_id'];
	$ip_address = $_SERVER['REMOTE_ADDR'];
	if(empty($referrer)){
		$referrer = wp_aff_get_referrer_id_from_ip_address($ip_address);
	}		

	if(!empty($referrer)){
		$debug_data = "Commission tracking debug data from the WooCommerce plugin:".$referrer."|".$sale_amount."|".$buyer_email."|".$txn_id;
		wp_affiliate_log_debug($debug_data,true);		
		wp_aff_award_commission_unique($referrer,$sale_amount,$txn_id,$item_id,$buyer_email);
	}
	else{
		wp_affiliate_log_debug("WooCommerce Affiliate integration - This is not an affiliate referred sale!",true);
	}
}
/*** End WWooCommerce integration ***/

/*** WPMU DEV Pro site/supporter plugin integration ***/
add_action('supporter_payment_processed','wp_aff_handle_pro_sites_payment',10,4);
function wp_aff_handle_pro_sites_payment($arg1,$arg2,$arg3,$arg4)
{
	$referrer = $_COOKIE['ap_id'];
	$sale_amt = $arg2;
	$debug_data = "Commission tracking debug data from the pro-sites/supporter plugin:".$referrer."|".$arg1."|".$arg2."|".$arg3."|".$arg4;
	wp_affiliate_log_debug($debug_data,true);
	wp_aff_award_commission($referrer,$sale_amt,"","","");
}

/*** Clickbank commission award ***/
function wp_aff_check_clickbank_transaction()
{	
	if(WP_AFFILIATE_ENABLE_CLICKBANK_INTEGRATION == '1'){
		if(isset($_REQUEST['cname']) && isset($_REQUEST['cprice']))
		{
			$aff_id = wp_affiliate_get_referrer();
			if(!empty($aff_id))
			{
				$sale_amt = strip_tags($_REQUEST['cprice']);
				$txn_id = strip_tags($_REQUEST['cbreceipt']);
				$item_id = strip_tags($_REQUEST['item']);
				$buyer_email = strip_tags($_REQUEST['cemail']);	
				$debug_data = "Commission tracking debug data from ClickBank transaction:".$aff_id."|".$sale_amt."|".$buyer_email."|".$txn_id."|".$item_id;
				wp_affiliate_log_debug($debug_data,true);
				wp_aff_award_commission_unique($aff_id,$sale_amt,$txn_id,$item_id,$buyer_email);	
			}
		}
	}
}

/*** Contact Form 7 Lead Capture ***/
add_action('wpcf7_before_send_mail','wp_aff_wpcf7_lead_capture');
function wp_aff_wpcf7_lead_capture( $cf7 )
{
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	if($wp_aff_platform_config->getValue('wp_aff_enable_wpcf7_lead_capture') === '1')
	{
		wp_affiliate_log_debug("Contact Form 7 lead capture feature is enabled. Checking details...",true);
		$reference = $cf7->id;
		//Check form exclusion list
		$form_exclusion = $wp_aff_platform_config->getValue('wp_aff_wp_cf7_form_exclusion_list');
		if(!empty($form_exclusion)){
		    $form_exclusion_list = explode(",",$form_exclusion);
		    foreach ($form_exclusion_list as $form_id){
		        if($reference == trim($form_id)){
		            wp_affiliate_log_debug("You have excluded this contact form (ID: ".$reference." ) from the lead capture pool. So no lead will be captured for this submission.",true);	
		            return;
		        }
		    }
		}
					
		$buyer_email = $cf7->posted_data["your-email"];
		$buyer_name = $cf7->posted_data["your-name"];
		$aff_id = wp_affiliate_get_referrer();			
	    $clientdate = (date ("Y-m-d"));
	    $clienttime = (date ("H:i:s"));
	    $ipaddress = $_SERVER['REMOTE_ADDR'];
		$debug_data = "Contact Form 7 lead capture data. Name: ".$buyer_name." | Email: ".$buyer_email." | Affiliate ID: ".$aff_id." | Contact Form Reference ID: ".$reference;
		wp_affiliate_log_debug($debug_data,true);	
		if(!empty($aff_id)){	
			//Capture the lead
			wp_aff_capture_lead_data_in_leads_table($buyer_email, $buyer_name, $aff_id, $reference, $clientdate, $clienttime, $ipaddress);
			//Add the referrer ID in the email body
			$cf7->posted_data["your-message"] = $cf7->posted_data["your-message"] . "\n\nReferrer ID: ".$aff_id;
		}
		else{
			wp_affiliate_log_debug("Contact Form 7 lead capture result: This is not an affiliate referral.",true);
		}
	}
}

//function wp_affiliate_shopperpress_track_commission_handler($sp_order_details)
//{
//	$total_amt = $sp_order_details['order_total'];
//	$shipping_amt = $sp_order_details['order_shipping'];
//	$sale_amt = $total_amt - $shipping_amt;
//	$txn_id = $sp_order_details['order_id'];
//	//$item_id = $order_details['item_id'];
//	$buyer_email = $sp_order_details['order_email'];    	
//	$order_details = array("sale_amt" =>$sale_amt, "txn_id"=>$txn_id, "buyer_email"=>$buyer_email,"item_id"=>"");
//	wp_affiliate_log_debug("Invoking shopperpress checkout commisison tracking...",true);
//    wp_aff_write_debug_array($order_details,true);
//	do_action('wp_affiliate_process_cart_commission',$order_details);
//}

/*** Gravity Forms Lead Capture ***/
function wp_aff_gf_post_submission_handler($entry, $form)
{
	wp_affiliate_log_debug("GF integration (Lead capture) - form submitted. Checking if affiliate lead needs to be captured...",true);
	$aff_id = $_COOKIE['ap_id'];
	if(empty($aff_id)){
		wp_affiliate_log_debug("GF integration (Lead capture) - affiliate ID is not present. This user was not sent by an affiliate.",true);
		return;
	}	
	$lead_capture_enabled = false;
	$pay_per_lead_enabled = false;
	foreach($form['fields'] as $field){
		if($field['inputName'] == "wpap-lead-email"){
			$email_field_id = $field['id'];
			$lead_capture_enabled = true;
		}
		if($field['inputName'] == "wpap-gf-commission"){
			$comm_hidden_field_id = $field['id'];
			$pay_per_lead_enabled = true;		
		}
	}
	$reference = $form['id'];
	$lead_email = $entry[$email_field_id];	
	wp_affiliate_log_debug("GF integration (Lead capture) - Debug data: ".$lead_email."|".$aff_id."|".$reference,true);

	$clientdate = (date ("Y-m-d"));
	$clienttime = (date ("H:i:s"));			
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	global $wpdb;
	$affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;	

	if($pay_per_lead_enabled){//Award appropriate commission for this lead		
		$commission_amt = $entry[$comm_hidden_field_id];
		wp_affiliate_log_debug("GF integration (Pay Per Lead) - Pay per lead option is enabled on this form. Commisison amount to award: ".$commission_amt,true);
		$fields = array();
		$fields['refid'] = $aff_id;
		$fields['payment'] = $commission_amt;
		$fields['sale_amount'] = "00.00";
		$fields['txn_id'] = uniqid();
		$fields['item_id'] = $reference;
		$fields['buyer_email'] = $lead_email;		
		wp_aff_add_commission_amt_directly($fields);
		wp_affiliate_log_debug("GF integration (Pay Per Lead) - Commission awarded! Commission amount: ".$commission_amt,true);
	}
	
	if(!$lead_capture_enabled){
		wp_affiliate_log_debug("GF integration (Lead capture) - lead capture is not enabled for this Gravity Form as it is missing the 'wpap-lead-email' parameter name in the email field!",true);
		return;
	}

	$updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress) VALUES ('$lead_email','$aff_id','$reference','$clientdate','$clienttime','$ipaddress')";
	$results = $wpdb->query($updatedb);
	wp_affiliate_log_debug("GF integration (Lead capture) - lead successfully captured in the leads table.",true);
}
add_action("gform_post_submission", "wp_aff_gf_post_submission_handler", 10, 2);

/*** Gravity Forms PayPal addon ***/
add_filter('gform_paypal_query', 'wp_aff_gf_update_paypal_query', 10, 3);
function wp_aff_gf_update_paypal_query($query_string, $form, $entry){	
	$aff_id = wp_affiliate_get_referrer();
	wp_affiliate_log_debug("GF integration... adding ap_id meta data to entry with value:".$aff_id,true);
	gform_update_meta($entry['id'], 'ap_id', $aff_id);
	return '&' . $query_string;
}
add_action('gform_paypal_post_ipn','wp_aff_gf_track_affiliate_commission',10,4);
function wp_aff_gf_track_affiliate_commission($ipn_data, $entry, $config, $cancel)
{
	wp_affiliate_log_debug("GF integration - received IPN notification. Checking details...",true);
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	if($wp_aff_platform_config->getValue('wp_aff_enable_gf_paypal')!='1'){
		wp_affiliate_log_debug("GF PayPal AddOn tracking feature is disabled! No commission will be awarded for this sale.",true);
		return;
	}
	$referrer = gform_get_meta($entry['id'], 'ap_id');
	if(!empty($referrer)){
		$sale_amount = $ipn_data['mc_gross'];
		$txn_id = $ipn_data['txn_id'];
		$item_id = $entry['id'];
		$buyer_email = $ipn_data['payer_email'];
		$clientip = $entry['ip'];
		$buyer_name = $ipn_data['first_name']." ".$ipn_data['last_name'];		

		$debug_data = "GF integration - Commission tracking debug data from PayPal transaction:".$referrer."|".$sale_amount."|".$buyer_email."|".$txn_id."|".$item_id."|".$buyer_name;
		wp_affiliate_log_debug($debug_data,true);	
		wp_aff_award_commission_unique($referrer,$sale_amount,$txn_id,$item_id,$buyer_email,$clientip,'',$buyer_name);
	}
}

/*********************************************/
/*** 3rd Party Integration Helper function ***/
/*********************************************/
function wp_aff_get_referrer_id_from_ip_address($ip_address)
{
	global $wpdb;
	$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
    $resultset = $wpdb->get_row("SELECT * FROM $affiliates_clickthroughs_table_name WHERE ipaddress = '$ip_address'", OBJECT);
	if($resultset)
	{
		return $resultset->refid;
	}    		
	else
	{
		return "";
	}
}
function wp_aff_retrieve_id_from_relations_tbl($unique_ref)
{
	wp_affiliate_log_debug("Trying to retrieve Affiliate ID from relations table for Unique Ref: ".$unique_ref,true);	
	global $wpdb;
	$aff_relations_tbl = WP_AFF_RELATIONS_TBL_NAME;	
	$resultset = $wpdb->get_row("SELECT * FROM $aff_relations_tbl WHERE unique_ref = '$unique_ref'", OBJECT);
	if($resultset){
		return $resultset->refid;
	}
	return "";
}
function wp_aff_check_commission_awarded_for_txn_id($txn_id)
{
	global $wpdb;
	$aff_sales_table = WP_AFF_SALES_TBL_NAME;
	$resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
	if($resultset){
		return true;
	}
	else{
		return false;
	}	
}
function wp_aff_check_if_buyer_is_referrer($referrer_id,$buyer_email)
{
	global $wpdb;
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$referrer_id'", OBJECT);
	if($result){
		if($result->email == $buyer_email || $result->paypalemail == $buyer_email){
			return true;
		} 
	} 
	return false;   	
}
function wp_aff_get_referrer_from_leads_table_for_buyer($buyer_email)
{
	global $wpdb;
	$affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
	$result = $wpdb->get_row("SELECT * FROM $affiliates_leads_table_name where buyer_email='$buyer_email'", OBJECT);
	if($result){
		$ref_id = $result->refid;
		return $ref_id;
	} 
	return "";   	
}
function wp_aff_capture_lead_data_in_leads_table($buyer_email, $buyer_name, $aff_id, $reference, $clientdate, $clienttime, $ipaddress)
{
    global $wpdb;
    $affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
    if(version_compare(WP_AFFILIATE_PLATFORM_DB_VERSION,'4.2', '>')){//if current DB version is greater than 4.2
    	wp_affiliate_log_debug("Capturing lead with the name. Name: ".$buyer_name,true);
    	$updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress,buyer_name) VALUES ('$buyer_email','$aff_id','$reference','$clientdate','$clienttime','$ipaddress','$buyer_name')";	
    }
	else{    	
    	$updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress) VALUES ('$buyer_email','$aff_id','$reference','$clientdate','$clienttime','$ipaddress')";
	}  
    $results = $wpdb->query($updatedb);
    wp_affiliate_log_debug("Lead captured in the leads database table. Lead email: ".$buyer_email,true);	
}
?>