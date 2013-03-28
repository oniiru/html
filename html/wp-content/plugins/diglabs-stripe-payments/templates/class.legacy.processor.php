<?php

class LegacyProcessor implements iStripePaymentNotification {

	public function process($data) {
		$json = str_replace('\"', '"', $json);
		$json = json_decode($json);
		
		$settings = new StripeSettings();
		if($json->livemode==true) {
			Stripe::setApiKey($settings->liveSecretKey);
		} else {
			Stripe::setApiKey($settings->testSecretKey);
		}
		
		$customer = null;
		if(isset($json->customer)){
			$id = $json->customer;
			try {
				$customer = Stripe_Customer::retrieve($id);
			} catch (Exception $e) {
				// Probably not a valid customer.
			}
		}
		switch ($json->event) {
			case "recurring_payment_failed":
				$this->processRecurringPaymentFailed($json, $customer);
				break;
			case "invoice_ready":
				$this->processInvoiceReady($json, $customer);
				break;
			case "recurring_payment_succeeded":
				$this->processRecurringPaymentSucceeded($json, $customer);
				break;
			case "subscription_trial_ending":
				$this->processSubscriptionTrialEnding($json, $customer);
				break;
			case "subscription_final_payment_attempt_failed":
				$this->processSubscriptionFinalPaymentAttemptFailed($json, $customer);
				break;
			case "ping":
				var_dump($json);
				break;
			default:
				header("HTTP/1.0 404 Not Found");
				break;
		}
	}
	
	private function processRecurringPaymentFailed($json, $customer) {
	/*
	When a customer has a subscription, Stripe attempts to charge his credit card each month. If this charge fails, Stripe notifies you of this failure. This hook can be used to automatically change the state of the customer's account, e.g. to display a banner reminding him to update his payment information.
	
	EXAMPLE NOTIFICATION:
	{
	  "customer":"cus_RTW3KxBMCknuhB",
	  "livemode": true,
	  "event": "recurring_payment_failed",
	  "attempt": 2,
	  "invoice": {
	    "attempted": true,
	    "charge": "ch_sUmNHkMiag",
	    "closed": false,
	    "customer": "cus_RTW3KxBMCknuhB",
	    "date": 1305525584,
	    "id": "in_jN6A1g8N76",
	    "object": "invoice",
	    "paid": true,
	    "period_end": 1305525584,
	    "period_start": 1305525584,
	    "subtotal": 2000,
	    "total": 2000,
	    "lines": {
	      "subscriptions": [
	        {
	          "period": {
	            "start": 1305525584,
	            "end": 1308203984
	          },
	          "plan": {
	            "object": "plan",
	            "name": "Premium plan",
	            "id": "premium",
	            "interval": "month",
	            "amount": 2000
	          },
	          "amount": 2000
	        }
	      ]
	    }
	  },
	  "payment": {
	    "time": 1297887533,
	    "card": {
	      "type": "Visa",
	      "last4": "4242"
	    },
	    "success": false
	  }
	}	
	*/
		if(function_exists('stripe_webhook_recurring_failed')) {
			stripe_webhook_recurring_failed($json, $customer);
		} else {
			$to = $customer->email;
			$subject = 'Payment Failed';
			$title = 'Payment Failed';
			$msg = 'We recently tried to bill your credit card for a recurring payment. That attempt failed. Could it be that you have a new credit card? Please contact us so we can update our records. We appreciate your continued support. Thanks!';
			
			$data = array();
			$data['Amount'] = "$".number_format($json->invoice->total/100, 2);
			$data['Email'] = $customer->email;
			$data['Customer Id'] = $customer->id;
			$data['Invoice Id'] = $json->invoice->id;
			$data['Card Type'] = $json->payment->card->type;
			$data['Card Last 4'] = $json->payment->card->last4;
			if($json->invoice->lines->subscriptions) {
				$data['Plan'] = $json->invoice->lines->subscriptions[0]->plan->name;
				$data['Interval'] = $json->invoice->lines->subscriptions[0]->plan->interval;
			}
			$email = new StripeEmailHelper();
			$email->sendReceipt($to, $subject, $title, $msg, $data);
		}
	}
	
	private function processInvoiceReady($json, $customer) {
	/*
	Each month, when Stripe has calculated the next charge for a customer, Stripe sends a notification informing you of the amount to be charged as well as the individual lines that make up the invoice (e.g. a subscription renewal, individiual invoice items added to the invoice during the usage period, etc.). At that time you have the opportunity to add invoice items to the invoice by responding to the webhook.
	
	You can also add invoice items to the bill separately from the webhook response, but only until the invoice freezes and attempts to collect payment, which happens ~1 hour after the invoice_ready webhook is sent.
	
	The invoice ready notification is very useful for implementing usage-based billing as part of your pricing scheme. For example, if we send you a notification for the usage period between Mar 1 and April 1, you can tally bandwidth usage during this period of time and send us a response with the total additional usage fees to apply to the invoice.
	
	Use the invoice's period_start and period_end parameters to calculate any usage-based billing for the usage period the invoice covers. Note that the subscription renewal line on the invoice has a period for the renewed subscription. Because subscriptions are pre-billed, the usage period does not align with the subscription's current period (the period through which it's paid).
	
	EXAMPLE NOTIFICATION
	{
	  "customer":"cus_RTW3KxBMCknuhB",
	  "event":"invoice_ready",
	  "invoice": {
	    "total": 1500,
	    "subtotal": 3000,
	    "lines": {
	      "invoiceitems": [
	        {
	          "id": "ii_N17xcRJUtn",
	          "amount": 1000,
	          "date": 1303586118,
	          "currency": "usd",
	          "description": "One-time setup fee"
	        }
	      ],
	      "subscriptions": [
	        {
	          "amount": 2000,
	          "period": {
	            "start": 1304588585,
	            "end": 1307266985
	          },
	          "plan": {
	            "amount": 2000,
	            "interval": "month",
	            "object": "plan",
	            "id": "premium"
	          }
	        }
	      ]
	    },
	    "object": "invoice",
	    "discount": {
	      "coupon": {
	        "id": "50OFF",
	        "livemode": false,
	        "percent_off": 50,
	        "object": "coupon"
	      },
	      "start": 1304588585
	    },
	    "date": 1304588585,
	    "period_start": 1304588585,
	    "id": "in_jN6A1g8N76",
	    "period_end": 1304588585
	  }
	  
	}
	
	If you want to add charges or credits to the invoice before Stripe attempts to collect payment for it, you can simply respond with a JSON hash that includes an invoiceitems parameter.
	
	EXAMPLE RESPONSE:
	{
	    "invoiceitems": [
	        {
	            "amount": 1000,
	            "currency": "usd",
	            "description": "Usage charge"
	        },
	        {
	            "amount": -500,
	            "currency": "usd",
	            "description": "Credit for being a loyal customer"
	        }
	    ]
	}
	*/
		if(function_exists('stripe_webhook_invoice_ready')) {
			stripe_webhook_invoice_ready($json, $customer);
		} else {
		}
	}
	
	private function processRecurringPaymentSucceeded($json, $customer) {
	/*
	When a customer has a subscription, Stripe attempts to charge her credit card each month. When this charge succeeds as expected, Stripe sends a notification with the details of the successful charge. You can use this webhook for performing actions such as emailing your customer with an invoice.
	
	EXAMPLE NOTIFICATION:
	{
	  "customer":"cus_RTW3KxBMCknuhB",
	  "livemode": true,
	  "event":"recurring_payment_succeeded",
	  "invoice": {
	    "total": 2000,
	    "subtotal": 2000,
	    "lines": {
	      "subscriptions": [
	      {
	        "amount": 2000,
	        "period": {
	          "start": 1304588585,
	          "end": 1307266985
	        },
	        "plan": {
	          "amount": 2000,
	          "interval": "month",
	          "object": "plan",
	          "id": "premium",
	          "name": "Premium plan"
	        }
	      }
	      ]
	    },
	    "object": "invoice",
	    "date": 1304588585,
	    "period_start": 1304588585,
	    "id": "in_jN6A1g8N76",
	    "period_end": 1304588585
	  },
	  "payment": {
	    "time": 1297887533,
	    "card":
	    {
	      "type": "Visa",
	      "last4": "4242"
	    },
	    "success": true
	  }
	}
	*/
		if(function_exists('stripe_webhook_recurring_succeeded')) {
			stripe_webhook_recurring_succeeded($json, $customer);
		} else {
			$to = $customer->email;
			$subject = 'Payment Received';
			$title = 'Payment Received';
			$msg = 'Thank you for your payment. This email is your receipt and includes important information. If you feel this transaction is in error, please respond to this email with the details.';
			
			$data = array();
			$data['Amount'] = "$".number_format($json->invoice->total/100, 2);
			$data['Email'] = $customer->email;
			$data['Customer Id'] = $customer->id;
			$data['Invoice Id'] = $json->invoice->id;
			$data['Card Type'] = $json->payment->card->type;
			$data['Card Last 4'] = $json->payment->card->last4;
			if($json->invoice->lines->subscriptions) {
				$data['Plan'] = $json->invoice->lines->subscriptions[0]->plan->name;
				$data['Interval'] = $json->invoice->lines->subscriptions[0]->plan->interval;
			}
			$email = new StripeEmailHelper();
			$email->sendReceipt($to, $subject, $title, $msg, $data);
		}
	}
	
	private function processSubscriptionTrialEnding($json, $customer) {
	/*
	If a customer is subscribed to a plan with a free trial, Stripe sends a webhook notifying you 3 days before the trial is about to end and the card is about to be charged for the first time. This gives you the opportunity to email the customer or take some other action.
	
	EXAMPLE NOTIFICATION:
	{
	  "customer":"cus_RTW3KxBMCknuhB",
	  "event":"subscription_trial_ending",
	  "subscription":
	  {
	    "trial_start": 1304627445,
	    "trial_end": 1307305845,
	    "plan": {
	      "trial_period_days": 31,
	      "amount": 2999,
	      "interval": "month",
	      "id": "silver",
	      "name": "Silver"
	    }
	  }
	}
	*/
		if(function_exists('stripe_webhook_trial_ending')) {
			stripe_webhook_trial_ending($json, $customer);
		} else {
		}
	}
	
	private function processSubscriptionFinalPaymentAttemptFailed($json, $customer) {
	/*
	Stripe automatically handles failed payments for you. We retry a failed payment up to 3 times, and if we still can't collect payment, we take a final action that you specify. By default we'll cancel the subscription for you and stop attempting to invoice or charge the customer, but if you choose we can merely mark the subscription unpaid and continue to invoice but not attempt payment. In either case, we'll notify you when the maximum failed payment attempts have been reached so that you know when a subscription has been canceled or marked unpaid.
	
	EXAMPLE NOTIFICATION:
	{
	  "customer":"cus_RTW3KxBMCknuhB",
	  "event":"subscription_final_payment_attempt_failed",
	  "subscription": {
	    "status": "canceled",
	    "start": 1304585542,
	    "plan": {
	      "amount": 2000,
	      "interval": "month",
	      "object": "plan",
	      "id": "silver"
	    },
	    "canceled_at": 1304585552,
	    "ended_at": 1304585552,
	    "object": "subscription",
	    "current_period_end": 1307263942,
	    "id": "sub_kP4M63kFrb",
	    "current_period_start": 1304585542
	  }
	}
	
	*/
		if(function_exists('stripe_webhook_recurring_final_failed')) {
			stripe_webhook_recurring_final_failed($json, $customer);
		} else {
			$to = $customer->email;
			$to = 'bob.cravens@diglabs.com';
			$subject = 'Final Payment Failed';
			$title = 'Final Payment Failed';
			$msg = 'We recently tried to bill your credit card for a recurring payment. That attempt failed. We are canceling your payment. If however, you wish to continue your support, please contact us so we can update our records. Thanks!';
			
			$data = array();
			$data['Email'] = $customer->email;
			$data['Customer Id'] = $customer->id;
			$data['Invoice Id'] = $json->subscription->id;
			$email = new StripeEmailHelper();
			$email->sendReceipt($to, $subject, $title, $msg, $data);
		}
	}
}

?>