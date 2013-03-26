<?php

class EventProcessor implements iStripePaymentNotification {
	// Flag to enable emails using the stripe.com webhook tests.
	//	Stripe.com generates fake events, but they don't have real
	//	data backing them up. So calls to fetch invoices or customers
	//	throw exceptions. Setting this flag to true, allows these
	//	exceptions to be ignored.
	private $testing = false;

	public function process($data) {
		$body = @file_get_contents('php://input');
		$json = json_decode($body);
		
		// Set the api keys based upon the post data (insecure)
		$settings = new StripeSettings();
		if($json->livemode==true) {
			Stripe::setApiKey($settings->liveSecretKey);
		} else {
			Stripe::setApiKey($settings->testSecretKey);
		}
				
		// For better security retrieve the event from stripe.com
		$event = $json;
		try {
			$event = Stripe_Event::retrieve($json->id);
		} catch (Exception $e) {
			if(!$this->testing) {
				return;
			}
		}
			
		// Reset the api keys based upon the data received from stripe.com (secure)
		if($event->livemode==true) {
			Stripe::setApiKey($settings->liveSecretKey);
		} else {
			Stripe::setApiKey($settings->testSecretKey);
		}
		
		// collect event data
		$type = $event->type;
		
		// Todo:Clean this section up a bit...
		$subject = null;
		$title = null;
		$msg = null;
		if($type=='charge.succeeded') {
		
			$subject = "Payment Received";
			$title = "Payment Received";
			$msg = 'Thank you for your payment. This email is your receipt and includes important information. If you feel this transaction is in error, please respond to this email with the details.';
			
		} else if($type=='invoice.payment_failed'){
		
			$subject = "Payment Failed";
			$title = "Payment Failed";
			$msg = 'We recently tried to bill your credit card for a recurring payment. That attempt failed. Could it be that you have a new credit card? Please contact us so we can update our records. We appreciate your continued support. Thanks!';
			
		} else {
		
			// We don't process any other types.
			//
			return;
			
		}
				
		$card = $event->data->object->card;
		$description = null;
		if( !is_null( $event->data->object->description ) ) {
			$description = json_decode($event->data->object->description);
		}
		
		// A collection of data emailed to the customer
		$data = array();
		
		// charge information
		$data['Amount'] = "$".number_format($event->data->object->amount/100, 2);
		$data['Name'] = $card->name;
		$data['Card Type'] = $card->type;
		$data['Card Last 4'] = $card->last4;
		$data['Invoice Id'] = $event->data->object->id;

		$plan_ids = array();

		if(!is_null($event->data->object->invoice)) {
			// This is an invoice
			try{
				$invoice = Stripe_Invoice::retrieve($event->data->object->invoice);
				$event->invoice = $invoice;
				
				$lines = $invoice->lines;
				$subscriptions = $lines->subscriptions;
				$count = count($subscriptions);
				foreach($subscriptions as $i=>$subscription) {
					$index = $i + 1;
					$prefix = $count==1 ? "" : "$index: ";
					$data[$prefix."Plan"] = $subscription->plan->id;
					$data[$prefix."Interval"] = $subscription->plan->interval;

					$plan_ids[] = $subscription->plan->id;
				}
			} catch (Exception $e) {
				if(!$this->testing) {
					return;
				}
			}
		}
		
		$to = null;
		if(!is_null($event->data->object->customer)) {
			try{
				$customer = Stripe_Customer::retrieve($event->data->object->customer);
				$event->customer = $customer;
				$to = $customer->email;

				if( is_null( $description ) ) {
					$description = json_decode( $customer->description );
				}
				
			} catch (Exception $e) {
				if(!$this->testing) {
					return;
				}
			}
		}
		
		if(!is_null($description->email)) {
			$data['Email'] = $description->email;
		}
		if(!is_null($description->product)) {
			$data['Product'] = $description->product;
		}
		if(!is_null($description->subscription)) {
			$data['Subscription'] = $description->subscription;
		}

		// Allow for others to filter the data shown in the email.
		//
		$data = apply_filters('stripe_payment_data_filter', $data);

		// Send out a confirmation email
		//	
		$email = new StripeEmailHelper();
		$email->sendReceipt($to, $subject, $title, $msg, $data);
		
		// Raise the notification to anyone who has registered for this action
		//
		$diglabs = array( "email" => $data, "plan_ids" => $plan_ids );
		$event->diglabs = $diglabs;
		do_action( 'stripe_payment_notification', $event );



/*		$message .= "EVENT:\r\n".json_encode($event)."\r\n\r\n";
		$message .= "DESCRIPTION:\r\n".json_encode($description)."\r\n\r\n";
		$message .= "DATA:\r\n".json_encode($data)."\r\n\r\n";

				
		$to = 'bob.cravens@diglabs.com';
		$subject = 'stripe test';
		mail($to, $subject, $message);*/

	}
}

?>