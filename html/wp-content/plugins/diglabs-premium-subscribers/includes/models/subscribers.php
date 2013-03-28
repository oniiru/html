<?php


class DlPs_Subscribers {

	private static $option_key = 'dl_ps_subs_';
	private static $subs = array();


	// *********** STATIC FUNCTIONS ************ //
	//
	public static function Clear($plan_id){
	
		$key = self::Key( $plan_id );
		delete_option( $key );
	}
	
	public static function Key( $plan_id ) {

		return self::$option_key . $plan_id;
	}

	public static function All( $plan_id ) {

		$key = self::Key( $plan_id );
		if( isset( self::$subs[ $key] ) ) {

			// Already fetched from the DB
			//
			return self::$subs[ $key ];
		}

		// Need to fetch from the DB
		//
		$subs = get_option( $key );
		if(!is_array( $subs )) {
			$subs = array();
		}
		self::$subs[ $key ] = $subs;

		return self::$subs[ $key ];
	}

	public static function Update( $plan_id, $subs ) {

		$key = self::Key( $plan_id );
		self::$subs[ $key ] = $subs;
		update_option( $key, $subs );
	}

	public static function CancelSubscription( &$user ) {

		// Ensure this is a DlPs_User class
		//
		if(!is_a( $user, 'DlPs_User') ) {

			return false;
		}

		if( is_null( $user->plan_id ) ) {

			// Not currently on a plan...nothing more to do.
			//

			return true;
		}

		$old_plan = DlPs_Plan::Get( $user->plan_id );
		if( !is_null( $old_plan ) ) {

			// Manage Stripe subscriptions first.
			//
			if( !is_null( $old_plan->stripe_plan_id ) ) {

				// Cancel the stripe subscription.
				//
				if( !self::StripeCancel( $user->stripe_id ) ) {

					return false;
				}
			}

			// Remove the user from this plan.
			//
			self::RemoveUser( $old_plan, $user->id );
		} else {

			// Shouldn't end up here. Somehow the plan
			//	got deleted when it had subscribers. That
			//	should not happen.

			// TODO: what to do here?
		}

		// Update the user.
		//
		$user->plan_id = null;
		$user->plan = null;
		return true;
	}

	public static function UpdateSubscription( &$user, $new_plan ) {

		// Ensure this is a DlPs_User class
		//
		if(!is_a( $user, 'DlPs_User') ) {

			return false;
		}
		if(!is_a( $new_plan, 'DlPs_Plan') ) {

			return false;
		}

		if( !is_null( $user->plan_id ) ) {

			// Transferring to a different plan
			//
			$old_plan = DlPs_Plan::Get( $user->plan_id );
			if( !is_null( $old_plan ) ) {

				// Manage Stripe subscriptions first.
				//
				if( !is_null( $new_plan->stripe_plan_id ) ) {

					// Transferring to another stripe plan. It doesn't
					//	matter if the old plan was a stripe plan. The
					//	following call handles both situation.
					//
					if( !self::StripeUpdate( $user->stripe_id, $new_plan->stripe_plan_id, $old_plan->stripe_plan_id ) ) {

						return false;
					}
				} else if( !is_null( $old_plan->stripe_plan_id ) ){

					// Transferring to a non-stripe plan but old plan was
					//	a stripe plan.  Need to cancel the old stripe plan.
					//
					if( !self::StripeCancel( $user->stripe_id ) ) {

						return false;
					}
				}

				// Remove this user from their current plan
				//
				self::RemoveUser( $old_plan, $user->id );
			} else {

				// Shouldn't end up here. Somehow the plan
				//	got deleted when it had subscribers. That
				//	should not happen.

				// TODO: what to do here?
			}

		} else {

			// This is a new plan
			//
			if( !is_null( $new_plan->stripe_plan_id ) ) {

				// Create a new stripe plan
				//
				if( !self::StripeUpdate( $user->stripe_id, $new_plan->stripe_plan_id ) ) {

					return false;
				}
			}
		}

		// Add this user to a new plan
		//
		$user->plan_id = $new_plan->id;
		$user->plan = $new_plan;
		return self::AddUser( $new_plan, $user->id );
	}

	private static function StripeCancel( $stripe_id ) {

		if( is_null( $stripe_id ) ) {

			return false;
		}

		require_once DLPS_PLUGIN_PATH . "/../diglabs-stripe-payments/stripe-php-1.6.1/lib/Stripe.php";
		require_once DLPS_PLUGIN_PATH . "/../diglabs-stripe-payments/includes/class.settings.php";
	
		try {

			$settings = new StripeSettings();
			Stripe::setApiKey( $settings->getSecretKey() );
			$cu = Stripe_Customer::retrieve( $stripe_id );
			$result = $cu->CancelSubscription();
		} catch( Exception $e ) {

			return false;
		}

		return true;
	}

	private static function StripeUpdate( $stripe_id, $new_plan_id, $old_plan_id = null) {
	
		if( is_null( $stripe_id ) || is_null( $new_plan_id ) ) {

			return false;
		}

		// Transferring to a stripe plan
		//	Load up the stripe features.

		// Need to use the stripe API to cancel the subscription
		require_once DLPS_PLUGIN_PATH . "/../diglabs-stripe-payments/stripe-php-1.6.1/lib/Stripe.php";
		require_once DLPS_PLUGIN_PATH . "/../diglabs-stripe-payments/includes/class.settings.php";
	
		try {

			$settings = new StripeSettings();
			Stripe::setApiKey( $settings->getSecretKey() );
			$cu = Stripe_Customer::retrieve( $stripe_id );
		} catch( Exception $e ) {
			return false;
		}

		// Create the stripe arguments.
		//
		$args = array( "plan" => $new_plan_id );
		if( !is_null( $old_plan_id ) ) {

			// Transferring from a stripe plan...prorate
			//
			$args[ "prorate" ] = true;
		}

		try {

			$cu->UpdateSubscription( $args );
		} catch ( Exception $e ) {
			return false;
		}

		return true;
	}

	private static function AddUser( &$plan, $user_id ) {

		$subs = self::All( $plan->id );
		if( !in_array( $user_id, $subs ) ) {

			$subs[] = $user_id;
			self::Update( $plan->id, $subs );

			$plan->subscribers_count = count( $subs );
			$plan->Save();

			return true;
		} else {

			return true;
		}

		return false;
	}

	private static function RemoveUser( &$plan, $user_id ) {

		$subs = self::All( $plan->id );
		$new_subs = array();
		foreach( $subs as $sub ) {
		
			if( $sub != $user_id ) {
			
				$new_subs[] = $sub;
			}
		}
		
		self::Update( $plan->id, $new_subs );

		$plan->subscribers_count = count( $new_subs );
		$plan->Save();

		return true;
	}

}

?>