<?php

require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.settings.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/stripe-php-1.6.1/lib/Stripe.php';

class StripeFormHelper {
	private $plans;
	private $description;
	private $label;
	private $buttonText;
	private $isLive;
	private $hidden;
	
	public function __construct($plans = array(), $description = "", $label = "", $buttonText = "Submit") {
		$this->plans = $plans;
		$this->description = $description;
		$this->label = $label;
		$this->buttonText = $buttonText;
		$this->isLive = true;
		$this->hidden = array();
	}
	
	public function addPlan($plan) {
		$this->plans[] = $plan;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function setLabel($text) {
		$this->label = $text;
	}
	
	public function setButtonText($text) {
		$this->buttonText = $text;
	}
	
	public function addHidden($name, $value) {
		$this->hidden[] = array('name' => $name, 'value' => $value);
	}
	
	public function useTestKeys() {
		$this->isLive = false;
	}
	
	public function render($url = null) {
		echo $this->html($url);
	}
	
	public function html($url = null) {
		$html = "";
		if(count($this->plans) == 0) {
			$html .= "No plans specified.";
			return $html;
		}
		
		$settings = new StripeSettings();
		Stripe::setApiKey($settings->getSecretKey());
		$paymentUrl = $url==null ? $settings->getPaymentUrl() : $url;
				
		$html .= "<!--Stripe.com wordpress plugin (http://diglabs.com) -->\n";
		$html .= "<form class='stripe-option-form' action='$paymentUrl' method='post' target='_blank'>\n";
		foreach($this->hidden as $elem) {
			$name = $elem['name'];
			$val = $elem['value'];
			$html .= "<input type='hidden' name='$name' value='$val' />\n";
		}
		$html .= "{$this->label}\n";
		if(count($this->plans) == 1) {
			$plan = $this->plans[0];
			$planInfo = Stripe_Plan::retrieve($plan);
			$amount = $planInfo->amount/100;
			$html .= "<input name='plan' type='hidden' value='{$plan}' />\n";
			$html .= "<span class='stripe-amount'>$".number_format($amount,2)."</span>\n";
		} else {
			$html .= "<select name='plan'>\n";
			foreach($this->plans as $plan) {
			try {
				$planInfo = Stripe_Plan::retrieve($plan);
				$amount = $planInfo->amount/100;
				$html .= "<option value='$plan'>$$amount per month</option>\n";
			} catch (Exception $e) {
				// This plan does not exist.
				$html .= "<option value='unknown'>$plan not found at stripe.com</option>\n";
			}
			}
			$html .= "</select>\n";
		}
		$html .= "<input type='submit' value='{$this->buttonText}' />\n";
		$html .= "</form>\n";
		
		return $html;
	}
}


?>
