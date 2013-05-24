<?php

//**** This file needs to be included from a file that has access to "wp-load.php" ****
include_once('wp_aff_debug_handler.php');

function wp_aff_aweber_new_signup_user($full_target_list_name,$fname,$lname,$email_to_subscribe)
{
	wp_affiliate_log_debug("Attempting to signup the user via AWeber API",true);
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	$wp_aff_aweber_access_keys = $wp_aff_platform_config->getValue('wp_aff_aweber_access_keys');
	if(empty($wp_aff_aweber_access_keys['consumer_key'])){
		wp_affiliate_log_debug("Missing AWeber access keys! You need to first make a conntect before you can use this API",false);
		return;		
	}
	//wp_eStore_write_debug_array($wp_aff_aweber_access_keys,true);
	if (!class_exists('AWeberAPI')){//TODO - change the class name to "WP_AFF_AWeberAPI" to avoid conflict with others
		include_once('lib/auto-responder/aweber_api/aweber_api.php');
		wp_affiliate_log_debug("AWeber API library inclusion succeeded.",true);
	}else{
		wp_affiliate_log_debug("AWeber API library is already included from another plugin.",true);
	}
	
	$aweber = new AWeberAPI($wp_aff_aweber_access_keys['consumer_key'], $wp_aff_aweber_access_keys['consumer_secret']);
	$account = $aweber->getAccount($wp_aff_aweber_access_keys['access_key'], $wp_aff_aweber_access_keys['access_secret']);//Get Aweber account
	$account_id = $account->id;
	$mylists = $account->lists;
	wp_affiliate_log_debug("AWeber account retrieved. Account ID: ".$account_id,true);
	
	$target_list_name = str_replace("@aweber.com", "", $full_target_list_name);
	wp_affiliate_log_debug("Attempting to signup the user to the AWeber list: ".$target_list_name,true);
	$list_name_found = false;
	foreach ($mylists as $list) {
		if($list->name == $target_list_name){
			$list_name_found = true;
			try {//Create a subscriber			    
			    $params = array(
			        'email' => $email_to_subscribe,
			        'name' => $fname.' '.$lname,
			    );
			    $subscribers = $list->subscribers;
			    $new_subscriber = $subscribers->create($params);
			    wp_affiliate_log_debug("User with email address " .$email_to_subscribe. " was added to the AWeber list: ".$target_list_name,true);
			}catch (Exception $exc) {
				wp_affiliate_log_debug("Failed to complete the AWeber signup! Error Details Below.",false);
				wp_eStore_write_debug_array($exc,true);
			}    
		}
	}
	if(!$list_name_found){
		wp_affiliate_log_debug("Error! Could not find the AWeber list (".$full_target_list_name.") in your AWeber Account! Please double check your list name value for typo.",false);
	}
}

function wp_aff_send_aweber_mail($list_name,$from_address,$cust_name,$cust_email){
    $subject = "Aweber Automatic Sign up email";
    $body    = "\n\nThis is an automatic email that is sent to AWeber for member signup purpose\n".
               "\nEmail: ".$cust_email.
               "\nName: ".$cust_name;

	$headers = 'From: '.$from_address . "\r\n";
    wp_mail($list_name, $subject, $body, $headers);
    wp_affiliate_log_debug ("Sent the Aweber signup email to: ".$list_name,true);
}

function wp_aff_get_chimp_api_new()
{
	include_once('lib/auto-responder/wp_aff_MCAPI.class.php');
    $api_key = get_option('wp_aff_chimp_api_key');
    if(!empty($api_key))
    {
    	wp_affiliate_log_debug("Creating a new MailChimp API object using the API Key specified in the settings",true);
        $api = new WP_AFF_MCAPI($api_key);
    }
    else
    {
    	wp_affiliate_log_debug("Error! You did not specify your MailChimp API key in the autoresponder settings. MailChimp signup will fail.",false);
        $api = "";//
    }
    return $api;
}

function wp_aff_mailchimp_subscribe($api,$target_list_name,$fname,$lname,$email_to_subscribe)
{
	wp_affiliate_log_debug("MailChimp target list name: ".$target_list_name,true);	
    $all_lists = $api->lists();
    //wp_aff_write_debug_array($all_lists,true);
    $lists_data = $all_lists['data'];
    $found_match = false;
    foreach ($lists_data AS $list) 
    {
    	wp_affiliate_log_debug("Checking list name : ".$list['name'],true);	
        if (strtolower($list['name']) == strtolower($target_list_name)){
        	$found_match = true;
            $list_id = $list['id'];
            wp_affiliate_log_debug("Found a match for the list name on MailChimp. List ID :".$list_id,true);
        }
    }
    if(!$found_match){
    	wp_affiliate_log_debug("Could not find a list name in your MailChimp account that matches with the target list name: ".$target_list_name,false);
    	return;
    }    
    //echo "<br />List ID: ".$list_id;
    $signup_date_field_name = get_option('wp_aff_signup_date_field_name');
    if(empty($signup_date_field_name)){
    	$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'INTERESTS'=>'');
    }
    else{
    	$todays_date = date ("Y-m-d");
    	$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'INTERESTS'=>'', $signup_date_field_name => $todays_date);
    }
    
    if(get_option('wp_aff_mailchimp_disable_double_optin')!='')
    {
    	wp_affiliate_log_debug("Subscribing to MailChimp without double opt-in... Name: ".$fname." ".$lname." Email: ".$email_to_subscribe,true); 
    	//listSubscribe doc at http://apidocs.mailchimp.com/1.2/listsubscribe.func.php
    	$retval = $api->listSubscribe($list_id, $email_to_subscribe, $merge_vars, "html", false, false, true, true);
    }
    else//do the default subscription with basic values
    {
    	wp_affiliate_log_debug("Subscribing to MailChimp... Name: ".$fname." ".$lname." Email: ".$email_to_subscribe,true); 
    	$retval = $api->listSubscribe($list_id, $email_to_subscribe, $merge_vars );
    }
	if ($api->errorCode){
		wp_affiliate_log_debug ("Unable to load listSubscribe()!",false);
		wp_affiliate_log_debug ("\tCode=".$api->errorCode,false);
		wp_affiliate_log_debug ("\tMsg=".$api->errorMessage,false);
	} 
	else
	{
		wp_affiliate_log_debug("MailChimp Signup was successful.",true);
	}
    return $retval;
}

function wp_aff_getResponse_subscribe($campaign_name,$fname,$lname,$email_to_subscribe)
{
	wp_affiliate_log_debug('Attempting to call GetResponse API for list signup...',true);	 
	// your API key
	// available at http://www.getresponse.com/my_api_key.html
	$api_key = get_option('wp_aff_getResponse_api_key');
	
	// API 2.x URL
	$api_url = 'http://api2.getresponse.com';
	
	$customer_name = $fname." ".$lname;
	
	wp_affiliate_log_debug('API Key:'.$api_key.', Customer name:'.$customer_name,true);	 
	
	include_once('lib/auto-responder/wp_aff_jsonRPCClient.php');
	// initialize JSON-RPC client
	$client = new WP_AFF_jsonRPCClient($api_url);
	wp_affiliate_log_debug('created the WP_AFF_jsonRPCClient object',true);
	$result = NULL;
	
	wp_affiliate_log_debug('Attempting to retrieve campaigns for '.$campaign_name,true);
	// get CAMPAIGN_ID of the specified campaign (e.g. 'sample_marketing')
	try {
	    $result = $client->get_campaigns(
	        $api_key,
	        array (
	            # find by name literally
	            'name' => array ( 'EQUALS' => $campaign_name )
	        )
	    );
	}
	catch (Exception $e) {
		wp_affiliate_log_debug('There was an error trying to retrieve campaign names from your GetResponse account: '.$campaign_name,false);
		wp_aff_write_debug_array($e->getMessage(),false);
	    return;
	}

	wp_affiliate_log_debug('Retrieved campaigns for: '.$campaign_name,true);
	# uncomment this line to preview data structure
	# print_r($result);
	
	# since there can be only one campaign of this name
	# first key is the CAMPAIGN_ID you need
	$CAMPAIGN_ID = array_pop(array_keys($result));	
	wp_affiliate_log_debug("Attempting GetResponse add contact operation for campaign ID: ".$CAMPAIGN_ID." Name: ".$customer_name." Email: ".$email_to_subscribe,false);
	
	if(empty($CAMPAIGN_ID))
	{
		wp_affiliate_log_debug("Could not retrieve campaign ID. Please double check your GetResponse Campaign Name:".$campaign_name,false);
	}
	else
	{
	# add contact to 'sample_marketing' campaign
	//try {
	    $result = $client->add_contact(
	        $api_key,
	        array (
	            'campaign'  => $CAMPAIGN_ID,
	            'name'      => $customer_name,
	            'email'     => $email_to_subscribe,
	        	'cycle_day' => '0'
	        )
	    );
//	}
//	catch (Exception $e) {
//	    # check for communication and response errors
//	    wp_affiliate_log_debug($e->getMessage(),false);
//	}
	}
	# uncomment this line to preview data structure
	# print_r($result);	
	//print("Contact added\n");
	wp_affiliate_log_debug("GetResponse contact added... result:".$result,false);
	return true;
}

function wp_aff_global_autoresponder_signup($firstname,$lastname,$emailaddress)
{
	wp_affiliate_log_debug('===> Performing autoresponder signup if specified. <===',true);
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	if (get_option('wp_aff_enable_aweber_int') == '1')
    {
    	$from_email = get_option('wp_aff_senders_email_address');
    	$aweber_list = get_option('wp_aff_aweber_list_name');
        $cust_name = $firstname .' '. $lastname;
        wp_affiliate_log_debug('AWeber list to signup to:'.$aweber_list,true);
        if($wp_aff_platform_config->getValue('wp_aff_use_new_aweber_integration') == '1'){
        	wp_aff_aweber_new_signup_user($aweber_list,$firstname,$lastname,$emailaddress);
        }
        else{        
        	wp_aff_send_aweber_mail($aweber_list,$from_email,$cust_name,$emailaddress);
        	wp_affiliate_log_debug('AWeber list signup from email address value:'.$from_email,true);
        }        
        wp_affiliate_log_debug('AWeber signup performed for affiliate:'.$emailaddress,true);
    }
    if (get_option('wp_aff_use_mailchimp') == '1')
    {
    	wp_affiliate_log_debug('Mailchimp integration is being used.',true);	 
        $api = wp_aff_get_chimp_api_new();
        $target_list_name = get_option('wp_aff_chimp_list_name');
        $retval = wp_aff_mailchimp_subscribe($api,$target_list_name,$firstname,$lastname,$emailaddress);
        wp_affiliate_log_debug('MailChimp global list signup operation performed. Return value is: '.$retval,true);
    }
    if(get_option('wp_aff_use_getResponse') == '1')
    {
    	wp_affiliate_log_debug('GetResponse integration is being used.',true);	 
	    $campaign_name = get_option('wp_aff_getResponse_campaign_name');
	    $retval = wp_aff_getResponse_subscribe($campaign_name,$firstname,$lastname,$emailaddress);
	    wp_affiliate_log_debug('GetResponse campaign to signup to:'.$campaign_name,true);
	    wp_affiliate_log_debug('GetResponse global list signup operation performed. Return value is: '.$retval,true);	      	
    }
    wp_affiliate_log_debug('===> End of autoresponder signup <===',true);	
}
?>