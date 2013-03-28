<?php

require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.settings.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/stripe-php-1.6.1/lib/Stripe.php';

class StripeButtonHelper {
	private $amount;
	private $text;
	private $description;
	private $isLive;
	
	public function __construct($amount = 0.0, $text = "Submit", $description = "") {
		$this->amount = $amount;
		$this->text = $text;
		$this->description = $description;
		$this->isLive = true;
	}
	
	public function setAmount($amount) {
		$this->amount = $amount;
	}
	
	public function setText($text) {
		$this->text = $text;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function useTestKeys() {
		$this->isLive = false;
	}
	
	public function render($url = null) {
		
		echo $this->html($url);
	}
	
	public function html($url = null) {
		
		$html = "";
		
		$settings = new StripeSettings();
		Stripe::setApiKey($settings->getSecretKey());
		$paymentUrl = $url==null ? $settings->getPaymentUrl() : $url;
		
		$html .= "<!--Stripe.com wordpress plugin (http://diglabs.com) -->\n";
		$html .= "<form class='stripe-button-form' action='$paymentUrl' method='post' target='_blank'>\n";
		$html .= "<input name='amount' type='hidden' value='{$this->amount}' />\n";
		$html .= "<input type='submit' value='{$this->text}' />\n";
		$html .= "</form>\n";
		
		return $html;
	}
}

?>
