<?php

// Form short code
add_shortcode('stripe_form', 'stripe_form');
function stripe_form($atts, $content = null) {
	extract(shortcode_atts(array(
		"plans"			=> array(),
		"description" 	=> '',
		"label" 		=> 'Choose plan: ',
		"button" 		=> 'Check Out',
		"test"			=> false
	), $atts));
	
	$plans = explode(",", $plans);	
	$form = new StripeFormHelper($plans, $description, $label, $button);
	if($test){
		$form->useTestKeys();
	}
	return $form->html();
}

// Button short code
add_shortcode('stripe_button', 'stripe_button');
function stripe_button($atts, $content = null) {
	extract(shortcode_atts(array(
		"amount"		=> 10.00,
		"text"			=> "Donate $10.00",
		"description"	=> "A donate $10 button",
		"test"			=> false
	), $atts));
	
	$button = new StripeButtonHelper($amount, $text, $description);
	if($test){
		$button->useTestKeys();
	}
	return $button->html();
}

// Custom form short codes
add_shortcode('stripe_form_begin', 'stripe_form_begin');
function stripe_form_begin($atts, $content = null) {
	extract(shortcode_atts(array(
		"test"			=> false,
		"description"	=> null
	), $atts));
	
	$result = StripeCustomForm::htmlFormBegin($test);
	if(!is_null($description)) {
		$result .= StripeCustomForm::htmlDescription($description);
	}
	$ignore = array("amount", "plan");
	foreach( $_REQUEST as $name => $value ) {
		if( !in_array( $name, $ignore ) ) {

			$result .= "<input type='hidden' name='$name' value='$value' />";
		}
	}

	return $result;
}
add_shortcode('stripe_form_end', 'stripe_form_end');
function stripe_form_end($atts, $content = null) {
	return StripeCustomForm::htmlFormEnd();
}
add_shortcode('stripe_form_subscription', 'stripe_form_subscription');
function stripe_form_subscription($atts, $content = null) {
	extract(shortcode_atts(array(
		"plan_id"		=> null
	), $atts));
	if(!is_null($plan_id)) {
		return StripeCustomForm::htmlSubscriptionInfo($plan_id);
	}
	return "";
}
add_shortcode('stripe_form_amount', 'stripe_form_amount');
function stripe_form_amount($atts, $content = null) {
	extract(shortcode_atts(array(
		"amount"		=> null
	), $atts));
	if($amount == null && $_REQUEST['amount']) {
		$amount = $_REQUEST['amount'];
	}
	return StripeCustomForm::htmlAmountInfo($amount);
}
add_shortcode('stripe_form_plan_info', 'stripe_form_plan_info');
function stripe_form_plan_info($atts, $content = null) {
	extract(shortcode_atts(array(
		"plan"		=> null
	), $atts));
	if($plan == null && $_REQUEST['plan']) {
		$plan = $_REQUEST['plan'];
	}
	if($plan == null) {
		return stripe_form_amount($atts, $content);
	}
	return StripeCustomForm::htmlPlanInfo($plan);
}
add_shortcode('stripe_form_billing_info', 'stripe_form_billing_info');
function stripe_form_billing_info($atts, $content = null) {
	extract(shortcode_atts(array(
		"short"		=> false
	), $atts));
	
	if($short) {
		return StripeCustomForm::htmlBillingInfoShort();
	}
	return StripeCustomForm::htmlBillingInfo();
}
add_shortcode( 'stripe_form_account', 'stripe_form_account' );
function stripe_form_account($atts, $content = null) {
	return <<<HTML
<h3 class="stripe-payment-form-section">Login Information</h3>
<div class="stripe-payment-form-row">
<label for="uname">Username</label>
<input type="text" id="uname" name="uname" class="required" />
</div>
<div class="stripe-payment-form-row">
<label for="pword1">Password</label>
<input type="password" id="pword1" name="pword1" class="required" />
</div>
<div class="stripe-payment-form-row">
<label for="pword2">Password Confirmation</label>
<input type="password" id="pword2" name="pword2" class="required" />
<span class="error"></span>
</div>
HTML;
}
add_shortcode( 'stripe_form_coupon', 'stripe_form_coupon' );
function stripe_form_coupon( $atts, $content = null ) {
	extract(shortcode_atts(array(
		"code"		=> null
	), $atts));
	
	if( !is_null( $code ) ) {
		return <<<HTML
<input type='hidden' name='coupon' value='$code' />
HTML;
	}
	return <<<HTML
<div class="stripe-payment-form-row">
<label for="coupon">Coupon</label>
<input type="text" id="coupon" name="coupon" />
<span class="error"></span>
</div>
HTML;
}
add_shortcode('stripe_form_payment_info', 'stripe_form_payment_info');
function stripe_form_payment_info($atts, $content = null) {
	extract(shortcode_atts(array(
	), $atts));
	
	return StripeCustomForm::htmlPaymentInfo();
}
add_shortcode('stripe_form_section_header', 'stripe_form_section_header');
function stripe_form_section_header($atts, $content = null) {
	extract(shortcode_atts(array(
		"title"		=> null
	), $atts));
	
	if(title){
		return StripeCustomForm::htmlSectionHeader($title);
	} else {
		return "no title";
	}
}
add_shortcode('stripe_form_section_row', 'stripe_form_section_row');
function stripe_form_section_row($atts, $content = null) {
	extract(shortcode_atts(array(
		"label"		=> '',
		"input"		=> ''
	), $atts));
	return StripeCustomForm::htmlSectionRow($label, $input);
}
add_shortcode('stripe_form_receipt', 'stripe_form_receipt');
function stripe_form_receipt($atts, $content = null) {
	extract(shortcode_atts(array(
	), $atts));
	return StripeCustomForm::htmlReceipt($content);
}
add_shortcode('stripe_form_standard_amount', 'stripe_form_standard_amount');
function stripe_form_standard_amount($atts, $content = null) {
	extract(shortcode_atts(array(
		"amount"		=> null,
		"short"			=> false
	), $atts));

	if($amount == null && $_REQUEST['amount']) {
		$amount = $_REQUEST['amount'];
	}
	return StripeCustomForm::htmlStandardAmount($amount, $short);
}
add_shortcode('stripe_form_standard_plan', 'stripe_form_standard_plan');
function stripe_form_standard_plan($atts, $content = null) {
	extract(shortcode_atts(array(
		"plan"			=> null,
		"short"			=> false
	), $atts));
	if($plan == null && $_REQUEST['plan']) {
		$plan = $_REQUEST['plan'];
	}
	if($plan == null) {
		return StripeCustomForm::htmlStandardAmount(null, $short);
	}
	return StripeCustomForm::htmlStandardPlan($plan, $short);
}

?>