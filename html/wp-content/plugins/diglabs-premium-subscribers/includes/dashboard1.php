<?php


// Hide the 'Profile' menu from subscribers.
//
add_action( 'admin_head', 'dlps_hide_menu', 0 );
function dlps_hide_menu() {
	global $current_user, $menu, $wphd_user_capability, $wp_db_version;

	if( current_user_can( 'subscriber') ) {

		unset( $menu[ 70 ] );
	}
}

// Handle any dashboard form posts.
//
add_action( 'init', 'dlps_dashboard_handler', 0 );
function dlps_dashboard_handler() {

	if( isset( $_POST['dl_ps_myadmin_personal'] ) && 
		! empty( $_POST['dl_ps_myadmin_personal'] ) ) {

		// Updating user personal information.
		//
		if( is_user_logged_in() ) {

			$wp_user = wp_get_current_user();

			wp_update_user( array(
				'ID'			=> $wp_user->ID,
				'first_name'	=> wp_kses_post( $_POST[ 'fname' ] ),
				'last_name'		=> wp_kses_post( $_POST[ 'lname' ] ),
				'user_email'	=> wp_kses_post( $_POST[ 'email' ] )
				));
		}
	}

	if( isset( $_POST['dl_ps_myadmin_plan'] ) && 
		! empty( $_POST['dl_ps_myadmin_plan'] ) &&
		isset( $_POST[ 'cmd-cancel-recurring' ] ) && 
		!empty( $_POST[ 'cmd-cancel-recurring' ] ) ) {

		// Updating user subscription plan information.
		//
		if( is_user_logged_in() ) {

			$wp_user = wp_get_current_user();
			$user = new DlPs_User( $wp_user );

			// Cancel the current subscription.
			//
			if( DlPs_Subscribers::CancelSubscription( $user ) ) {

				$user->Update();
				echo "<div class='info'>Your subscription has been cancelled. You will be able to access content until expired.</div>";
			} else {

				echo "<div class='error'>Failed to cancel the subscription. Ensure you have a valid credit card on file.</div>";
			}

		}
	}

	if( isset( $_POST['dl_ps_myadmin_payment'] ) && 
		! empty( $_POST['dl_ps_myadmin_payment'] ) ) {

		// Updating user personal information.
		//
		if( is_user_logged_in() ) {

			// This requires the stripe payments plugin to be activated.
			//
			$stripe_plugin = 'diglabs-stripe-payments/diglabs-stripe-payments.php';
			if( !function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			if( !is_plugin_active( $stripe_plugin ) ) {
				
				echo "<div class='error'>This requires the <a href='http://diglabs.com/stripe/'>Dig Labs Stripe Plugin</a> to be activated.</div>";
				return;
			} else {

				// Load the stripe API library.
				//
				$path = DLPS_PLUGIN_PATH . "/../diglabs-stripe-payments/stripe-php-1.6.1/lib/Stripe.php";
				require_once ( $path );
			}

			$wp_user = wp_get_current_user();
			$user = new DlPs_User( $wp_user );

			$settings = new StripeSettings();
			Stripe::setApiKey($settings->getSecretKey());

			$cu 		= Stripe_Customer::retrieve( $user->stripe_id );
			$cu->card 	= $_POST[ 'token' ];
			$response 	= $cu->save();


			$user->card_type 	= $response->active_card->type;
			$user->card_last4 	= $response->active_card->last4;
			$user->Update();
		}
	}
}

// Add the dashboard widget.
//
add_action( 'wp_dashboard_setup', 'dlps_dashboard1' );
function dlps_dashboard1() {
	
	wp_add_dashboard_widget( 
		'dashboard_custom_feed', 
		'My Premium Subscriptions', 
		'dlps_dashboard1_display' );

}
function dlps_dashboard1_display() {

	global $wpError;

	// Get the user's information.
	//
	$wp_user = wp_get_current_user();
	$user = new DlPs_User( $wp_user );

	$level_names = array();
	$levels = DlPs_Level::All();
	foreach( $levels as $level ) {
		$level_names[ $level->id ] = $level->name;
		$level->plans = array();
		foreach( $level->plan_ids as $plan_id ) {

			$plan = DlPs_Plan::Get( $plan_id );
			if( !is_null( $plan) ) {

				$level->plans[] = $plan;
			}
		}
	}

	$plan_names = array();
	$plan_amounts = array();
	$plans = DlPs_Plan::All();
	foreach( $plans as $plan ) {
		$name = $plan->ToString();
		$plan_names[ $plan->id ] = $name;
		$plan_amounts[ $plan->id ] = $plan->amount;
	}

	$md5 = md5( $user->user_email );
	$gravatar_src = "http://www.gravatar.com/avatar/$md5?s=80";
	$exp = date( "F j, Y", $user->expiration );
	$recurring = $user->plan->is_recurring ? 'yes' : 'no';


	$is_stripe_ok = false;
	$pubkey = '';
	if( class_exists( 'StripeSettings' ) ) {

		$is_stripe_ok = true;
		$settings = new StripeSettings();
		$pubkey = $settings->getPublicKey();
	}
?>

<div id="dlps_myadmin" class="dlps_wrap">
<?php 
	if( !empty( $wpError ) ) { 
		echo "<p class='framed error'>" . $wpError . "</p>";
	}
?>
	<div class="section">
		<div class="gravatar">
			<img src="<?php echo $gravatar_src; ?>" />
		</div>
		<div class="username"><?php echo $user->user_login; ?></div>
		<div class="expiration"><small>expiration:</small> <?php echo $exp; ?></div>
	</div>
	<div class="section">
		<h4>Personal Information<span class='edit'>Edit</span></h4>
		<div class="form">
			<form method="post" action="" class="confirm">
				<input type="hidden" name="dl_ps_myadmin_personal" value="1" />
				<dl>
					<dt><label for="fname">First Name:</label></dt>
					<dd><input name="fname" type="text" value="<?php echo $user->first_name; ?>" /></dd>
					<dt><label for="lname">Last Name:</label></dt>
					<dd><input name="lname" type="text" value="<?php echo $user->last_name; ?>" /></dd>
					<dt><label for="email">Email:</label></dt>
					<dd><input name="email" type="text" value="<?php echo $user->user_email; ?>" /></dd>
				</dl>
				<p><input class="button-primary" name="cmd-update-personal" type="submit" value="Update" /></p>
			</form>
		</div>
		<div class="container">
			<dl>
				<dt>First Name:</dt>
				<dd><?php echo $user->first_name; ?></dd>
				<dt>Last Name:</dt>
				<dd><?php echo $user->last_name; ?></dd>
				<dt>Email:</dt>
				<dd><?php echo $user->user_email; ?></dd>
			</dl>
		</div>
	</div>
	<div class="section">
	<?php if( !empty( $user->plan->stripe_plan_id ) ) { ?>
		<h4>Subscription Information<span class='edit'>Edit</span></h4>
		<div class="form">
			<form method="post" action="" class="confirm">
				<input type="hidden" name="dl_ps_myadmin_plan" value="1" />
				<p><input class="button-primary" name="cmd-cancel-recurring" type="submit" value="Cancel Recurring" /></p>
			</form>
		</div>
	<?php } else { ?>
		<h4>Subscription Information</h4>
	<?php } ?>
		<div class="container">
			<dl>
				<dt>Expiration:</dt>
				<dd><?php echo date("F j, Y", $user->expiration) ?></dd>
				<dt>Level:</dt>
				<dd><?php echo empty($user->level->name) ? '-' : $user->level->name; ?></dd>
				<dt>Plan:</dt>
				<dd><?php echo empty($user->plan->name) ? '-' : $user->plan->name; ?></dd>
				<dt>Cost:</dt>
				<dd><?php echo empty($user->plan->amount) ? '-' : $user->plan->amount; ?></dd>
				<dt>Period:</dt>
				<dd><?php echo empty($user->plan->period) ? '-' : $user->plan->period; ?></dd>
				<dt>Type:</dt>
				<dd><?php echo empty($user->plan->stripe_plan_id) ? 'non-recurring' : 'recurring'; ?></dd>
			</dl>
		</div>
	</div>
	<?php if($is_stripe_ok) { ?>
	<script type='text/javascript' src='https://js.stripe.com/v1/'></script>
	<script type='text/javascript' src='<?php echo DLPS_PLUGIN_URL; ?>/js/stripe.js'></script>
	<div class="section">
		<h4>Billing Information<span class='edit'>Edit</span></h4>
		<div class="form">
			<form id="dlps_stripe" method="post" action="">
				<div id="dlps_validation_error">This is an error.</div>
				<input class="pubkey" type="hidden" name="pubkey" value="<?php echo $pubkey; ?>" />
				<input type="hidden" name="dl_ps_myadmin_payment" value="1" />
				<dl>
					<dt><label >Name On Card:</label></dt>
					<dd><input class="cname" type="text" value="" /></dd>
					<dt><label >Card Number:</label></dt>
					<dd><input class="card" class="card" type="text" value="" /></dd>
					<dt><label >CVC:</label></dt>
					<dd><input class="cvc" class="cvc" type="text" value="" /></dd>
					<dt><label >Expiration:</label></dt>
					<dd>
						<select id="exp_month"></select>
						<select id="exp_year"></select>
					</dd>
				</dl>
				<p><input class="button-primary" name="cmd-update-peronal" type="submit" value="Update" /></p>
				<div id="dlps_progress"></div>
			</form>
		</div>
		<div class="container">
			<dl>
				<dt>Stripe ID:</dt>
				<dd><?php echo empty($user->stripe_id) ? '-' : $user->stripe_id; ?></dd>
			</dl>
			<dl>
				<dt>Card Type:</dt>
				<dd><?php echo empty($user->card_type) ? '-' : $user->card_type; ?></dd>
			</dl>
			<dl>
				<dt>Card Last 4:</dt>
				<dd><?php echo empty($user->card_last4) ? '-' : $user->card_last4; ?></dd>
			</dl>
		</div>
	</div>
	<?php } ?>
</div>

<?php

}