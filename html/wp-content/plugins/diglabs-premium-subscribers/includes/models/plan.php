<?php

class DlPs_Plan {

	private static $option_key = 'dl_ps_plans';
	private static $plans;

	public $id;
	public $level_id;
	public $name;
	public $amount;
	public $period;
	public $stripe_plan_id;
	public $is_active = true;
	public $subscribers_count = 0;

	public function Save() {

		$plans = array();
		$current_plans = self::All();
		foreach( $current_plans as $plan ) {

			if( $plan->id == $this->id ) {

				$plans[] = $this;
			} else {

				$plans[] = $plan;
			}
		}

		self::Update( $plans );
	}

	public function ExtendDate( $ts ) {

		$ts_now = mktime();
		$ts1 = is_null( $ts ) ? $ts_now : $ts;
		if( $ts1 < $ts_now ) {
			$ts1 = $ts_now;
		}

		$date1 = date("Y-m-d", $ts1);

		$offset = " +" . $this->period;
		$result = strtotime( date('Y-m-d', strtotime($date1)) . $offset);
		return $result;
	}

	public function ToString() {

		$result = $this->name . ' ($' . $this->amount . ' / ' . $this->period . ' / ';
		if( !is_null( $this->stripe_plan_id ) ) {
			$result .= 'recurring';
		} else {
			$result .= 'non-recurring';
		}
		return $result;
	}

	// *********** STATIC FUNCTIONS ************ //
	//
	public static function Clear() {
		
		$plans = self::All();
		foreach( $plans as $plan ) {
		
			DlPs_Subscribers::Clear( $plan->id );
		}
		
		delete_option( self::$option_key );
	}
	
	public static function All() {

		if( isset( self::$plans ) ) {

			// Already fetched from the DB
			//
			return self::$plans;
		}

		// Need to fetch from the DB
		//
		$plans = get_option( self::$option_key );
		if(!is_array( $plans )) {
			$plans = array();
		}
		self::$plans = $plans;

		return self::$plans;
	}

	public static function Update( $plans ) {

		usort( $plans, array(__CLASS__, 'ComparePlans') );

		self::$plans = $plans;
		update_option( self::$option_key, self::$plans );
	}
	
	private static function ComparePlans( $a, $b ) {
	
		return strcmp( $a->level_id, $b->level_id );
	}

	public static function Get( $id ) {
		$plans = self::All();

		foreach( $plans as $plan ) {
			if( $plan->id == $id ) {
				return $plan;
			}
		}
		return null;
	}

	public static function AddPlan( &$plan ) {

		// Ensure this is a DlPs_Plan class
		//
		if(!is_a( $plan, 'DlPs_Plan') ) {
			return false;
		}

		$plans = self::All();
		if( is_null( $plan->id ) ) {
			
			// Find an open ID...just go with the max ID.
			//
			$max_existing_id = 0;
			foreach( $plans as $this_plan ) {

				if( $this_plan->id > $max_existing_id ) {

					$max_existing_id = $this_plan->id;
				}
			}
			$plan->id = $max_existing_id + 1;
		}

		// Add this to the current plans and save
		//
		$plans[] = $plan;
		self::Update( $plans );

		return true;	
	}

	public static function DeletePlan( $id ) {

		$plans = array();
		$current_plans = self::All();
		foreach( $current_plans as $plan ) {

			if( $plan->id != $id ) {

				$plans[] = $plan;
			} else {

				// Do not allow the deletion of plans that have
				//	subscribers.
				//
				if( $plan->subscribers_count > 0 ) {
					return false;
				}
			}
		}

		self::Update( $plans );

		if( count( $plans ) == count( $current_plans ) ) {
			return false;
		}
		return true;
	}

	public static function GetStripePlans( ) {

		require_once DLPS_PLUGIN_PATH . "/../diglabs-stripe-payments/stripe-php-1.6.1/lib/Stripe.php";
		require_once DLPS_PLUGIN_PATH . "/../diglabs-stripe-payments/includes/class.settings.php";
	
		try {

			$settings = new StripeSettings();
			Stripe::setApiKey( $settings->getSecretKey() );
			$result = Stripe_Plan::all();
			return $result->data;

		} catch( Exception $e ) {

			return null;
		}

		return null;
	}
}

?>