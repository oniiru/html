<?php
	$settings = new StripeSettings();
	
	if($_POST) {
		// Form data sent
		$settings->setLivePublicKey($_POST['live_public_key']);
		$settings->setLiveSecretKey($_POST['live_secret_key']);

		$settings->setTestPublicKey($_POST['test_public_key']);
		$settings->setTestSecretKey($_POST['test_secret_key']);
		
		$settings->setWebHookUrl($_POST['webhook_url']);
		
		$settings->setIsLive($_POST['is_live_keys']);
		$settings->setCurrencySymbol($_POST['currency_symbol']);
		
		?>
		
		<div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
		
		<?php
	} 
?>


<div id="stripe-payments-admin-wrap" class="wrap">
	<h2>Stripe Payments - Options</h2>
	
	<form name="stripe_payment_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<p class="info">Log into <a href="http://stripe.com" target="_blank">stripe.com</a> to access your keys and determine the 3-letter ISO code for currency.</p>
		<h4>Live Keys</h4>
		<p>These keys are configured to a real account and <strong>will</strong> result in actual credit card charges.</p>
		<ul>
			<li>
				<label for="live_public_key">Publishable Key:</label>
				<input type="text" name="live_public_key" value="<?php echo $settings->livePublicKey; ?>" />
			</li>
			<li>
				<label for="live_secret_key">Secret Key:</label>
				<input type="text" name="live_secret_key" value="<?php echo $settings->liveSecretKey; ?>" />
			</li>
		</ul>
		<h4>Test Keys</h4>
		<p>These keys are configured to a test account and <strong>will not</strong> result in actual credit card charges.</p>
		<ul>
			<li>
				<label for="test_public_key">Publishable Key:</label>
				<input type="text" name="test_public_key" value="<?php echo $settings->testPublicKey; ?>" />
			</li>
			<li>
				<label for="test_secret_key">Secret Key:</label>
				<input type="text" name="test_secret_key" value="<?php echo $settings->testSecretKey; ?>" />
			</li>
		</ul>
		<h4>Web Hook URL</h4>
		<p>The following URL defines the callback that Stripe uses payment notifications.</p>
		<ul>
			<li>
				<label for="webhook_url">Webhook URL:</label>
				<input type="text" name="webhook_url" value="<?php echo $settings->webHookUrl; ?>" />
				<span>(e.g. payment-webhook) After setting this here, configure <a href='http://stripe.com' target='_blank'>Stripe</a> to use this web hook.</span>
			</li>
		</ul>
		<h4>Other</h4>
		<p>The following provide other options used by this plugin.</p>
		<ul>
			<li>
				<label for="is_live">Use Live Keys?:</label>
				<input type="checkbox" name="is_live_keys" <?php if($settings->isLive){echo 'checked=checked';} ?> />
				<span>Global setting. Leave unchecked for testing. Check when you are ready to <strong>go live</strong>. Individual forms can be set to use the test keys by the <code>test=true</code> attribute.</span>
			</li>
			<li>
				<label for="currency_symbol">Currency Symbol:</label>
				<input type="text" name="currency_symbol" value="<?php echo $settings->currencySymbol; ?>" />
				<span>Visit <a href="http://stripe.com">stripe.com</a> to determine the appropriate 3-letter ISO code. (e.g. usd)</span>
			</li>
		</ul>

		<p class="submit">
			<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Options'); ?>" />
		</p>
	</form>
</div>