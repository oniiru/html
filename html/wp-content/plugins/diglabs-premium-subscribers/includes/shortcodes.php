<?php

add_shortcode('diglabs_premium_register', 'diglabs_premium_register');
function diglabs_premium_register($atts, $content = null) {
	global $wpError;


	$html = <<<HTML
<div id="dlps_register" class="dlps_container">
	<p class="framed info">Registration is <strong>free</strong>! Use the form below to register.</p>
HTML;

	// Add any messages.
	//
	if( !empty( $wpError ) ) {
		$html .= "<p class='framed error'>" . $wpError . "</p>";
	}

	$login_url = DlPs_Options::GetLoginUrl();
	
	$html .= <<<HTML
	<form action="" method="post">
		<input type="hidden" name="dlps_register_posted" value="1" />
		<p><label>Username</label><input class="input" type="text" name="user" id="user" /></p>
		<p><label>Email</label><input class="input" type="text" name="email" id="email" /></p>
		<p><label>Password</label><input class="input" type="password" name="pass1" id="pass1" /></p>
		<p><label>Confirm Password</label><input class="input" type="password" name="pass2" id="pass2" /></p>
		<p class="submit"><input class="button-primary" type="submit" name="register" id="register" value="Register" /></p>
		<p>Already a member? <a href="$login_url">Login</a>
	</form>
</div>
HTML;


	return $html;
}

add_shortcode('diglabs_premium_login', 'diglabs_premium_login');
function diglabs_premium_login($atts, $content = null) {
	global $wpError;


	$html = <<<HTML
<div id="dlps_login" class="dlps_container">
HTML;

	// Add any messages.
	//
	if( !empty( $wpError ) ) {
		$html .= "<p class='framed error'>" . $wpError . "</p>";
	}

	$register_url = DlPs_Options::GetRegisterUrl();
	
	$html .= <<<HTML
	<form action="" method="post">
		<input type="hidden" name="dlps_login_posted" value="1" />
		<p><label>Username</label><input class="input" type="text" name="user" id="user" /></p>
		<p><label>Password</label><input class="input" type="password" name="pass" id="pass1" /></p>
		<p class="remember"><input type="checkbox" checked="checked" name="remember" id="remember" /> <label>Remember me!</label></p>
		<p class="submit"><input class="button-primary" type="submit" name="login" id="login" value="Login" /></p>
		<p>Not a member? <a href="$register_url">Register</a>
	</form>
</div>
HTML;


	return $html;
}

add_shortcode( 'diglabs_premium_subscribe', 'diglabs_premium_subscribe' );
function diglabs_premium_subscribe( $atts, $content = null ) {

	extract(shortcode_atts(array(
		"id"		=> -1
	), $atts));


	// Section header
	$html = "<h3 class='stripe-payment-form-section'>Subscription Options</h3>";

	// Add a marker for processing
	$html .= "<input type='hidden' name='dlps_subscriber' value='1' />";

	// Level selection
	$levels = DlPs_Level::All();
	foreach( $levels as $level ) {

		$level->plans = array();
		foreach( $level->plan_ids as $plan_id ) {

			$plan = DlPs_Plan::Get( $plan_id );
			if( !is_null( $plan) && $plan->is_active ) {

				$level->plans[] = $plan;
			}
		}
	}
	$html .= "<div class='stripe-payment-form-row'><label>Level</label><select id='level_id' name='level_id'>";
	foreach( $levels as $level ) {

		$html .= "<option value='" . $level->id . "'";
		$html .= " data-plans='" . json_encode( $level->plans ) . "'";
		$html .= " data-desc='" . $level->description . "'>";
		$html .= $level->name;
		$html .= "</option>";
	}
	$html .= "</select><span class='error'></span></div>";

	// Level description
	$html .= "<div class='stripe-payment-form-row'><label>Description</label><input id='level_desc' type='text' disabled='disabled' />";
	$html .= "<span class='error'></span></div>";		

	// Plan selection
	$html .= "<div class='stripe-payment-form-row'><label>Plan</label><select id='plan_id' name='plan_id'>";
	$html .= "</select><span class='error'></span></div>";	

	// Amount
	$html .= "<div class='stripe-payment-form-row'><label>Amount</label><input type='text' disabled='disabled' class='amountShown disabled' />";
	$html .= "<span class='error'></span></div>";
	$html .= "<input type='hidden' name='amount' class='amount' />";


	return $html;
}


































add_shortcode( 'diglabs_premium_package', 'diglabs_premium_package' );
function diglabs_premium_package($atts, $content = null) {
	global $wpError;

	extract(shortcode_atts(array(
		"id"		=> -1
	), $atts));

	if( $id == -1 ) {
		return "<p class='framed errors'>No package ID was specified</p>";
	}
	$package = DlPs_Package::Get( $id );
	if( is_null( $package ) ) {
		return "<p class='framed errors'>No package exists with ID=$id</p>";
	}
	$plans = array();
	foreach( $package->plan_ids as $plan_id ) {

		$plan = DlPs_Plan::Get( $plan_id );
		if( !is_null( $plan ) ) {
			$plans[] = $plan;
		}
	}
	// Add any messages.
	//
	if( !empty( $wpError ) ) {
		$html .= "<p class='framed error'>" . $wpError . "</p>";
	}

	$html .= <<<HTML
<h3 class="stripe-payment-form-section">Plan Selection</h3>
<div class="stripe-payment-form-row">
<input type="hidden" class="amount" size="20" name="amount" value="0" />
<label>Amount (USD $)</label>
<input type="text" size="20" disabled="disabled" class="disabled amountShown" value="$value" />
<span class="error"></span>
</div>
<div class="stripe-payment-form-row">
<label>Plan Options</label>
<select id='dl_ps_plans' name='dl_ps_plan'>
HTML;
	
	foreach( $plans as $plan ) {
		$html .= "<option value='$plan->id' data-amount='$plan->amount'>$plan->level [$ $plan->amount / $plan->period / ";
		if( $plan->is_recurring ) {
			$html .= "recurring";
		} else {
			$html .= "non-recurring";
		}
		$html .= "] - $plan->description</option>";
	}

	$html .= "</select></div>";
	return $html;
}

add_shortcode( 'diglabs_premium_my_account', 'diglabs_premium_my_account' );
function diglabs_premium_my_account( $atts, $content = null ) {
	global $wpError;

	extract(shortcode_atts(array(
		"package_id"		=> -1
	), $atts));

	if( $package_id == -1 ) {
		return "<p class='framed errors'>No package ID was specified</p>";
	}
	$package = DlPs_Package::Get( $package_id );
	if( is_null( $package ) ) {
		return "<p class='framed errors'>No package exists with ID=$package_id</p>";
	}
	$plans = array();
	foreach( $package->plan_ids as $plan_id ) {

		$plan = DlPs_Plan::Get( $plan_id );
		if( !is_null( $plan ) ) {
			$plans[] = $plan;
		}
	}

	// Ensure we have a wordpress user logged in.
	//
	if( !is_user_logged_in() ) {
		$login_url = DlPs_Options::GetLoginUrl();
		$reg_url = DlPs_Options::GetRegisterUrl();
		return <<<HTML
<p>Please <a href="$login_url">login</a> or <a href="$reg_url">register</a>.</p>
HTML;
	}

	// Get the user's information.
	//
	$wp_user = wp_get_current_user();
	$user = new DlPs_User( $wp_user );

/*
	// Can't be changed directly
	public $user_login;
	public $expiration;

	// Personal info Form
	public $first_name; 
	public $last_name;
	public $user_email;

	// Payment info form
	public $stripe_id;

	// Subscription form
	public $plan_id;
	public $plan;


*/

	// Main container
	//
	$html = <<<HTML
<div id="dlps_myadmin" class="dlps_container">'
HTML;

	// Add any messages.
	//
	if( !empty( $wpError ) ) {
		$html .= "<p class='framed error'>" . $wpError . "</p>";
	}

	// Header
	//
	$md5 = md5( $user->user_email );
	$gravatar_src = "http://www.gravatar.com/avatar/$md5?s=80";
	$exp = date( "F j, Y", $user->expiration );
	$html .= <<<HTML
	<div class="section">
		<div class="gravatar">
			<img src="$gravatar_src" />
		</div>
		<div class="username">$user->user_login</div>
		<div class="expiration"><small>expiration:</small> $exp</div>
	</div>
HTML;

	// Personal info
	//
	$html .= <<<HTML
	<div class="section">
		<h4>Personal Information<span class='edit'>Edit</span></h4>
		<div class="form">
			<form method="post" action="" class="confirm">
				<input type="hidden" name="dl_ps_myadmin_personal" value="1" />
				<dl>
					<dt><label for="fname">First Name:</label></dt>
					<dd><input name="fname" type="text" value="$user->first_name" />
					<dt><label for="lname">Last Name:</label></dt>
					<dd><input name="lname" type="text" value="$user->last_name" />
					<dt><label for="email">Email:</label></dt>
					<dd><input name="email" type="text" value="$user->user_email" />
				</dl>
				<p><input class="button-primary" name="cmd-update-peronal" type="submit" value="Update" /></p>
			</form>
		</div>
		<div class="container">
			<dl>
				<dt>First Name:</dt>
				<dd>$user->first_name</dd>
				<dt>Last Name:</dt>
				<dd>$user->last_name</dd>
				<dt>Email:</dt>
				<dd>$user->user_email</dd>
			</dl>
		</div>
	</div>
HTML;

	// Subscription info
	//
	$recurring = $user->plan->is_recurring ? 'yes' : 'no';
	$html .= <<<HTML
	<div class="section">
		<h4>Subscription Information<span class='edit'>Edit</span></h4>
		<div class="form">
			<form method="post" action="" class="confirm">
				<input type="hidden" name="dl_ps_myadmin_plan" value="1" />
				<dl>
					<dt><label for="plan_id">Plan Options:</label></dt>
					<dd>
						<select name="plan_id">
HTML;

	if( is_null($user->plan_id) ) {
		$html .= "<option value='-1' selected='selected'>None</option>";
	} else {
		$html .= "<option value='-1'>None</option>";
	}

	foreach( $plans as $plan ) {
		$html .= "<option value='" . $plan->id . "'";
		if( $user->plan_id == $plan->id) {
			$html .= " selected='selected'";
		}
		$html .= ">";
		$html .= $plan->level . ' $' . $plan->amount . ' / ' . $plan->period . ' / ';
		if($plan->is_recurring){
			$html .= 'recurring';
		} else {
			$html .= 'non-recurring';
		}
		$html .= ' [ID=' . $plan->id . ']';
		$html .= "</option>";
	}

	$html .= <<<HTML
						</select>
					</dd>
				</dl>
				<p><input class="button-primary" name="cmd-update-peronal" type="submit" value="Update" /></p>
			</form>
		</div>
		<div class="container">
			<dl>
				<dt>Level:</dt>
				<dd>{$user->plan->level}</dd>
				<dt>Cost:</dt>
				<dd>{$user->plan->amount}</dd>
				<dt>Period:</dt>
				<dd>{$user->plan->period}</dd>
				<dt>Recurring?:</dt>
				<dd>$recurring</dd>
			</dl>
		</div>
	</div>
HTML;

	// Payment info
	//
	
	$html .= <<<HTML
	<div class="section">
		<h4>Payment Information<span class='edit'>Edit</span></h4>
		<div class="container">
			<dl>
				<dt>Stripe ID:</dt>
				<dd>$user->stripe_id</dd>
			</dl>
		</div>
	</div>
HTML;

	// End the main container
	$html .= <<<HTML
</div>
HTML;

	return $html;
}

/*
add_shortcode('diglabs_premium_subscription', 'diglabs_premium_subscription');
function diglabs_premium_subscription($atts, $content = null) {
	global $ppd;
	
	extract(shortcode_atts(array(
		"id"			=> null
	), $atts));
	
	if(is_null($id)) {
		return "<p>ID cannot be null</p>";
	}

	$sub = DlPs_Options::GetSubscription( $id );
	if( $sub == false ) {
		return "<p>ID does not exist.</p>";
	}
	$sub_id = $sub['id'];
	return "<input name='dlps_subscription' value='$sub_id' type='hidden' />";
}*/

?>