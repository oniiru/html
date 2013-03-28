<?php

require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.settings.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/stripe-php-1.6.1/lib/Stripe.php';

class StripeCustomForm {

	// Start a form
	public static function renderFormBegin($test = false) {
		echo StripeCustomForm::htmlBeginForm($test);
	}
	public static function htmlFormBegin($test = false) {
        wp_enqueue_script('stripe_payment_plugin', STRIPE_PAYMENTS_PLUGIN_URL . '/js/stripe.js', array('jquery'), '1.5.19', true);
		wp_enqueue_script('stripe', 'https://js.stripe.com/v1/', array('jquery'), '1.5.19', true);
		
		echo '<script type="text/javascript">var stripe_blog_url="'.get_site_url().'";</script>';

		$settings = new StripeSettings();
		$pubkey = null;
		if($test) {
			Stripe::setApiKey($settings->testSecretKey);
			$pubkey = $settings->testPublicKey;
		} else {
			Stripe::setApiKey($settings->getSecretKey());
			$pubkey = $settings->getPublicKey();
		}
		$paymentUrl = $settings->getPaymentUrl();

		return <<<HTML
<div class="stripe-form-wrap">
<form action="$paymentUrl" method="post" class="stripe-payment-form">
<input class="pubkey" type="hidden" name="pubkey" value="$pubkey" />
HTML;
	}
	
	// End a form
	public static function renderFormEnd() {
		echo StripeCustomForm::htmlEndForm();
	}
	public static function htmlFormEnd() {
		return <<<HTML
<div class="stripe-payment-form-row-submit">
<button class="stripe-payment-form-submit" type="submit" class="button">Submit Payment</button>
</div>
<div class="stripe-payment-form-row-progress">
<span class="message"></span>
</div>
</form>
</div>
HTML;
	}
	
	// Add a hidden description field
	public static function renderDescription($description) {
		echo StripeCustomForm::htmlDescription($description);
	}
	public static function htmlDescription($description) {
		return <<<HTML
<input class="description" type="hidden" name="description" value="$description" />
HTML;
	}
	
	public static function renderReceipt() {
		echo StripeCustomForm::htmlReceipt();
	}
	public static function htmlReceipt($content = null) {
		if($content == null || strlen(trim($content))==0) {
			$content = StripeCustomForm::receiptDefault();		
		}
		return <<<HTML
<div class="stripe-payment-receipt">$content</div>
HTML;
	}
	private static function receiptDefault() {
		return <<<HTML
<p><strong>Thank You, {fname} {lname}</strong></p>
<p><strong>$ {amount} is making its way to our bank account.</strong></p>
<p>A receipt has been sent to <strong>{email}</strong>.</p>
<p>Transaction ID: {id}</p>
HTML;
	}
	
	// Add a payment amount section
	public static function renderAmountInfo($amount = null) {
		echo StripeCustomForm::htmlAmountInfo();
	}
	public static function htmlAmountInfo($amount = null) {
		$disabled = $amount==null ? '' : 'disabled="disabled"';
		$value = $amount==null ? '' : number_format($amount, 2);
		$value_in_cents = $value*100;
		return <<<HTML
<h3 class="stripe-payment-form-section">Amount</h3>
<div class="stripe-payment-form-row">
<input type="hidden" class="amount" size="20" name="amount" value="$value_in_cents" />
<label>Amount (USD $)</label>
<input type="text" size="20" $disabled class="disabled amountShown required" value="$value" />
<span class="error"></span>
</div>
HTML;
	}
	
	// Add a plan info section
	public static function renderPlanInfo($plan = null) {
		echo StripeCustomForm::htmlPlanInfo($plan);
	}
	public static function htmlPlanInfo($plan = null) {
		$planInfo = Stripe_Plan::retrieve($plan);
		$amount = $planInfo->amount;
		$planName = $planInfo->name;
		$interval = $planInfo->interval;
		$disabled = $amount==null ? '' : 'disabled="disabled"';
		$amountShown = $amount==null ? '' : number_format($amount/100, 2);
		return <<<HTML
<h3 class="stripe-payment-form-section">Plan Information</h3>
<input class="plan" type="hidden" name="plan" value="$plan" />
<input class="amount" type="hidden" name="amount" value="$amount" />
<input class="interval" type="hidden" name="interval" value="$interval" />
<div class="stripe-payment-form-row">
<label>Plan Name</label>
<input type="text" size="20" name="planName" disabled="disabled" class="planName disabled required" value="$planName" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Amount (USD $)</label>
<input type="text" size="20" name="cardAmount" disabled="disabled" class="cardAmount disabled amount required" value="$amountShown" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Every</label>
<input type="text" size="20" name="planInterval" disabled="disabled" class="planName disabled required" value="$interval" />
<span class="error"></span>
</div>
HTML;
	}
	
	// Add a subscription info section
	public static function renderSubscriptionInfo($plan = null) {
		echo StripeCustomForm::htmlSubscriptionInfo($plan);
	}
	public static function htmlSubscriptionInfo($plan = null) {
		$planInfo = Stripe_Plan::retrieve($plan);
		$amount = $planInfo->amount/100;
		$planName = $planInfo->name;
		$interval = $planInfo->interval;
		$disabled = $amount==null ? '' : 'disabled="disabled"';
		$value = $amount==null ? '' : number_format($amount, 2);
		return <<<HTML
<h3 class="stripe-payment-form-section">Subscription Information</h3>
<input type='hidden' name='subscription' value='$plan' />
<input class="plan" type="hidden" name="plan" value="$plan" />
<input class="amount" type="hidden" name="amount" value="$amount" />
<input class="interval" type="hidden" name="interval" value="$interval" />
<div class="stripe-payment-form-row">
<label>Plan Name</label>
<input type="text" size="20" name="planName" disabled="disabled" class="planName disabled required" value="$planName" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Amount (USD $)</label>
<input type="text" size="20" name="cardAmount" disabled="disabled" class="cardAmount disabled amount required" value="$amount" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Every</label>
<input type="text" size="20" name="planName" disabled="disabled" class="planName disabled required" value="$interval" />
<span class="error"></span>
</div>
HTML;
	}
	
	// Add a billing info section
	public static function renderBillingInfo() {
		echo StripeCustomForm::htmlBillingInfo();
	}
	public static function htmlBillingInfo() {
	return <<<HTML
<h3 class="stripe-payment-form-section">Billing Information</h3>
<div class="stripe-payment-form-row">
<label>First Name</label>
<input type="text" size="20" name="fname" class="fname required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Last Name</label>
<input type="text" size="20" name="lname" class="lname required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Address 1</label>
<input type="text" size="20" name="address1" class="address1 required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Address 2</label>
<input type="text" size="20" name="address2" class="address2" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>City</label>
<input type="text" size="20" name="city" class="city required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>State/Province</label>
<input type="text" size="20" name="state" class="state required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Zip/Postal Code</label>
<input type="text" size="20" name="zip" class="zip required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Country</label>
<input type="text" size="20" name="country" class="country required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Email Address</label>
<input type="text" size="20" name="email" class="email required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Phone</label>
<input type="text" size="20" name="phone" />
<span class="error"></span>
</div>		
HTML;
	}
		
	public static function renderBillingInfoShort() {
		echo StripeCustomForm::htmlBillingInfoShort();
	}
	public static function htmlBillingInfoShort() {
		return <<<HTML
<h3 class="stripe-payment-form-section">Billing Information</h3>
<div class="stripe-payment-form-row">
<label>First Name</label>
<input type="text" size="20" name="fname" class="fname required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Last Name</label>
<input type="text" size="20" name="lname" class="lname required" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Email Address</label>
<input type="text" size="20" name="email" class="email email required" />
<span class="error"></span>
</div>
HTML;
	}
	
	public static function renderPaymentInfo() {
		echo StripeCustomForm::htmlPaymentInfo();
	}
	public static function htmlPaymentInfo() {
		$imgUrl = STRIPE_PAYMENTS_PLUGIN_URL.'/images/types.png';
		return <<<HTML
<h3 class="stripe-payment-form-section">Payment Information</h3>
<div class="stripe-payment-form-row">
<img src="$imgUrl" alt="cc types" />
</div>
<div class="stripe-payment-form-row">
<label>Card Number</label>
<input type="text" size="20" class="cardNumber number required stripe-sensitive" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>CVC</label>
<input type="text" size="4" class="cardCvc number required stripe-sensitive" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Expiration</label>
<select class="cardExpiryMonth required card-expiry-month stripe-sensitive"></select>
&nbsp;/&nbsp;
<select class="cardExpiryYear required card-expiry-year stripe-sensitive"></select>
</div>
HTML;
	}
	
	public static function renderSectionHeader($title) {
		echo StripeCustomForm::htmlSelectionHeader($title);
	}
	public static function htmlSectionHeader($title) {
		return <<<HTML
<h3 class='stripe-payment-form-section'>$title</h3>
HTML;
	}
	
	public static function renderSectionRow($label, $input) {
		echo StripeCustomForm::htmlSectionRow($label, $input);
	}
	public static function htmlSectionRow($label, $input) {
		return <<<HTML
<div class='stripe-payment-form-row'>
<label>$label</label>
$input
<span class='error'></span>
</div>
HTML;
	}
	
	public static function renderSection($title, $rows) {
		echo StripeCustomForm::htmlSection($title, $row);
	}
	public static function htmlSection($title, $rows) {
		$html = StripeCustomForm::htmlSectionHeader($title);
		foreach($rows as $row) {
			$label = $row['label'];
			$input = $row['input'];
			$html .= StripeCustomForm::htmlSectionRow($label, $input);
		}
		return $html;
	}
			
	public static function renderStandardAmount($amount = null, $short = false) {
		echo StripeCustomForm::renderStandardAmount($amount, $short);
	}
	public static function htmlStandardAmount($amount = null, $short = false) {
		$html = StripeCustomForm::htmlAmountInfo($amount);
		if($short){
			$html .= StripeCustomForm::htmlBillingInfoShort();
		} else {
			$html .= StripeCustomForm::htmlBillingInfo();
		}
		$html .= StripeCustomForm::htmlPaymentInfo();
		
		return $html;
	}
			
	public static function renderStandardPlan($plan = null, $short = false) {
		echo StripeCustomForm::renderStandardPlan($plan, $short);
	}
	public static function htmlStandardPlan($plan = null, $short = false) {
		// If no plan is specified, fallback to an open ended amount form
		if($plan == null) {
			return Stripe::renderStandardAmount(null, $short);
		}
		$html = StripeCustomForm::htmlPlanInfo($plan);
		if($short){
			$html .= StripeCustomForm::htmlBillingInfoShort();
		} else {
			$html .= StripeCustomForm::htmlBillingInfo();
		}
		$html .= StripeCustomForm::htmlPaymentInfo();
		
		return $html;
	}
}

?>