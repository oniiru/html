<?php
include_once('../../../../wp-load.php');
include_once('../wp_aff_includes.php');

$wp_aff_debug_enabled = false;
if(get_option('wp_aff_enable_debug') == '1'){
	$wp_aff_debug_enabled = true;
}
$sandbox = get_option('wp_aff_sandbox_mode');

$wp_aff_ipn_debug_log = "ipn_handle_debug.log"; // Debug log file name
$error_msg='';

class wp_aff_paypal_ipn_handler {

   var $last_error;                 // holds the last error encountered
   var $ipn_log;                    // bool: log IPN results to text file?
   var $ipn_log_file;               // filename of the IPN log
   var $ipn_response;               // holds the IPN response from paypal
   var $ipn_data = array();         // array contains the POST values for IPN
   var $fields = array();           // array holds the fields to submit to paypal
   var $sandbox_mode = false;

   	function wp_aff_paypal_ipn_handler()
   	{
        $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
      	$this->last_error = '';
      	$this->ipn_log_file = 'ipn_handle_debug.log';
      	$this->ipn_response = '';
    }

   	function wp_aff_validate_and_award_commission()
	{
		// Check Product Name , Price , Currency , Receivers email ,
		global $error_msg;

 		// Read the IPN and validate

    	$payment_status = $this->ipn_data['payment_status'];
    	if (!empty($payment_status))
    	{
	        if ($payment_status != "Completed" && $payment_status != "Processed" && $payment_status != "Refunded")
	        {
                $error_msg .= 'Funds have not been cleared yet. commission will be awarded when the fund clears!';
		    	$this->debug_log($error_msg,false);
    		    return false;
	        }
    	}

		$transaction_type = $this->ipn_data['txn_type'];
		$transaction_id = $this->ipn_data['txn_id'];
		$transaction_subject = $this->ipn_data['transaction_subject'];
		$gross_total = $this->ipn_data['mc_gross'];
		if ($gross_total < 0)
		{
			// This is a refund or reversal
			$this->debug_log('This is a refund. Refund amount: '.$gross_total,true);
			$parent_txn_id = $this->ipn_data['parent_txn_id'];
			wp_aff_handle_refund($parent_txn_id);				
			$this->debug_log('Calling Automatic Commission Reversal API. Parent transaction ID: '.$parent_txn_id,true);
			return true;
		}
		
		if ($transaction_type == "cart")
		{
			$this->debug_log('Transaction Type: Shopping Cart',true);
			// Cart Items
			$num_cart_items = $this->ipn_data['num_cart_items'];
			$this->debug_log('Number of Cart Items: '.$num_cart_items,true);

			$i = 1;
			$cart_items = array();
			while($i < $num_cart_items+1)
			{
				$item_number = $this->ipn_data['item_number' . $i];
				$item_name = $this->ipn_data['item_name' . $i];
				$quantity = $this->ipn_data['quantity' . $i];
				$mc_gross = $this->ipn_data['mc_gross_' . $i];
				$mc_currency = $this->ipn_data['mc_currency'];

				$current_item = array(
									   'item_number' => $item_number,
									   'item_name' => $item_name,
									   'quantity' => $quantity,
									   'mc_gross' => $mc_gross,
									   'mc_currency' => $mc_currency,
									  );

				array_push($cart_items, $current_item);
				$i++;
			}
		}
		else if (($transaction_type == "subscr_signup"))
		{
            $this->debug_log('Subscription signup IPN received... nothing to do here(handled by the subscription IPN handler if implemented)',true);
			// Code to handle the signup IPN for subscription

			return true;
		}
		else if (($transaction_type == "subscr_cancel") || ($transaction_type == "subscr_eot") || ($transaction_type == "subscr_failed"))
		{
			$this->debug_log('Subscription cancellation IPN received... nothing to do here(handled by the subscription IPN handler if implemented)',true);
			return true;
		}
		else
		{
			$cart_items = array();
			$this->debug_log('Transaction Type: Buy Now/Subscribe',true);
			$item_number = $this->ipn_data['item_number'];
			$item_name = $this->ipn_data['item_name'];
			$quantity = $this->ipn_data['quantity'];
			$mc_gross = $this->ipn_data['mc_gross'];
			$mc_currency = $this->ipn_data['mc_currency'];

			$current_item = array(
									   'item_number' => $item_number,
									   'item_name' => $item_name,
									   'quantity' => $quantity,
									   'mc_gross' => $mc_gross,
									   'mc_currency' => $mc_currency,
									  );

			array_push($cart_items, $current_item);
		}

		$this->debug_log('Updating Affiliate Database Table with Sales Data if Using the WP Affiliate Platform Plugin.',true);
		if (function_exists('wp_aff_platform_install'))
		{
			$this->debug_log('WP Affiliate Platform is installed, registering sale...',true);
			$referrer = $this->ipn_data['custom'];//$customvariables['ap_id'];
			$total_tax = $this->ipn_data['tax'];
			if(empty($total_tax)){$total_tax = 0;}
			$total_shipping = 0;
			if(!empty($this->ipn_data['shipping'])){
				$total_shipping = $this->ipn_data['shipping'];
			}else if (!empty($this->ipn_data['mc_shipping'])){
				$total_shipping = $this->ipn_data['mc_shipping'];
			}
			$gross_sale_amt = $this->ipn_data['mc_gross'];	
			$this->debug_log('Gross sale amount: '.$gross_sale_amt.' Tax: '.$total_tax.' Shipping: '.$total_shipping,true);
			$sale_amount = $gross_sale_amt - $total_shipping - $total_tax;
			
			$txn_id = $this->ipn_data['txn_id'];
			$item_id = $this->ipn_data['item_number'];
			$buyer_email = $this->ipn_data['payer_email'];
			$buyer_name = $this->ipn_data['first_name'] . " " .$this->ipn_data['last_name'];
			if(!empty($referrer))
			{
				wp_aff_award_commission($referrer,$sale_amount,$txn_id,$item_id,$buyer_email,'','',$buyer_name);
				$aff_details_debug = "Referrer: ".$referrer." Sale Amt: ".$sale_amount." Buyer Email: ".$buyer_email." Txn ID: ".$txn_id;
			}
			else
			{
				$aff_details_debug = "Referrer value is empty! No commission will be awarded. Sale Amt: ".$sale_amount." Buyer Email: ".$buyer_email." Txn ID: ".$txn_id;
			}
			$this->debug_log('Aff Details=> '.$aff_details_debug,true);
		}

        //wp_mail($this->ipn_data['payer_email'], $subject, $body, $headers);

        // Do Post operation and cleanup
        //award commission here if u like
        return true;
    }

    function wp_aff_validate_ipn()
    {
      // parse the paypal URL
      $url_parsed=parse_url($this->paypal_url);

      // generate the post string from the _POST vars aswell as load the _POST vars into an arry
      $post_string = '';
      foreach ($_POST as $field=>$value) {
         $this->ipn_data["$field"] = $value;
         $post_string .= $field.'='.urlencode(stripslashes($value)).'&';
      }

      $this->post_string = $post_string;
      $this->debug_log('Post string : '. $this->post_string,true);

      $post_string.="cmd=_notify-validate"; // append ipn command

      // open the connection to paypal
      if($this->sandbox_mode){//connect to PayPal sandbox
	      $uri = 'ssl://'.$url_parsed['host'];
	      $port = '443';         	
	      $fp = fsockopen($uri,$port,$err_num,$err_str,30);
      }
      else{//connect to live PayPal site using standard approach
      	$fp = fsockopen($url_parsed['host'],"80",$err_num,$err_str,30);
      }      
      //$fp = fsockopen($url_parsed['host'],"80",$err_num,$err_str,30);
      
      if(!$fp)
      {
         // could not open the connection.  If loggin is on, the error message
         // will be in the log.
         $this->debug_log('Connection to '.$url_parsed['host']." failed.fsockopen error no. $errnum: $errstr",false);
         return false;

      }
      else
      {
         // Post the data back to paypal
         fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
         fputs($fp, "Host: $url_parsed[host]\r\n");
         fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
         fputs($fp, "Content-length: ".strlen($post_string)."\r\n");
         fputs($fp, "Connection: close\r\n\r\n");
         fputs($fp, $post_string . "\r\n\r\n");

         // loop through the response from the server and append to variable
         while(!feof($fp)) {
            $this->ipn_response .= fgets($fp, 1024);
         }

         fclose($fp); // close connection

         $this->debug_log('Connection to '.$url_parsed['host'].' successfuly completed.',true);
      }

      if (eregi("VERIFIED",$this->ipn_response))
      {
         // Valid IPN transaction.
         $this->debug_log('IPN successfully verified.',true);
         return true;

      }
      else
      {
         // Invalid IPN transaction.  Check the log for details.
         $this->debug_log('IPN validation failed.',false);
         return false;
      }
   }

   function log_ipn_results($success)
   {
      if (!$this->ipn_log) return;  // is logging turned off?

      // Timestamp
      $text = '['.date('m/d/Y g:i A').'] - ';

      // Success or failure being logged?
      if ($success) $text .= "SUCCESS!\n";
      else $text .= 'FAIL: '.$this->last_error."\n";

      // Log the POST variables
      $text .= "IPN POST Vars from Paypal:\n";
      foreach ($this->ipn_data as $key=>$value) {
         $text .= "$key=$value, ";
      }

      // Log the response from the paypal server
      $text .= "\nIPN Response from Paypal Server:\n ".$this->ipn_response;

      // Write to log
      $fp=fopen($this->ipn_log_file,'a');
      fwrite($fp, $text . "\n\n");

      fclose($fp);  // close file
   }

   function debug_log($message,$success,$end=false)
   {

   	  if (!$this->ipn_log) return;  // is logging turned off?

      // Timestamp
      $text = '['.date('m/d/Y g:i A').'] - '.(($success)?'SUCCESS :':'FAILURE :').$message. "\n";

      if ($end) {
      	$text .= "\n------------------------------------------------------------------\n\n";
      }

      // Write to log
      $fp=fopen($this->ipn_log_file,'a');
      fwrite($fp, $text );
      fclose($fp);  // close file
   }
}

// Start of IPN handling (script execution)

$ipn_handler_instance = new wp_aff_paypal_ipn_handler();

if ($wp_aff_debug_enabled)
{
	echo 'Debug is enabled. Check the '.$wp_aff_ipn_debug_log.' file for debug output.';
	$ipn_handler_instance->ipn_log = true;
	$ipn_handler_instance->ipn_log_file = $wp_aff_ipn_debug_log;
	
	if(empty($_POST))
	{
		$ipn_handler_instance->debug_log('This debug line was generated because you entered the URL of the ipn handling script in the browser.',true,true);
		exit;
	}	
}

if ($sandbox) // Enable sandbox testing
{
	$ipn_handler_instance->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	$ipn_handler_instance->sandbox_mode = true;
}

$ipn_handler_instance->debug_log('Paypal Class Initiated by '.$_SERVER['REMOTE_ADDR'],true);

// Validate the IPN
if ($ipn_handler_instance->wp_aff_validate_ipn())
{
	$ipn_handler_instance->debug_log('Creating product Information to send.',true);

      if(!$ipn_handler_instance->wp_aff_validate_and_award_commission())
      {
          $ipn_handler_instance->debug_log('IPN product validation failed.',false);
      }
}
$ipn_handler_instance->debug_log('Paypal class finished.',true,true);

?>
