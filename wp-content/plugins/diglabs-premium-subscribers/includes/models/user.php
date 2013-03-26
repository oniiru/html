<?php

class DlPs_User {

	private static $meta_key = 'dl_ps_meta';
	
	private $meta;

	// WP members (simple pass through)
	public $wp_user;
	public $ID;
	public $id;
	public $first_name;
	public $last_name;
	public $user_email;
	public $user_login;

	// Plugin data
	public $stripe_id;
	public $card_last4;
	public $card_type;
	public $expiration;
	public $level_id;
	public $plan_id;

	public $level;
	public $plan;

	public function __construct( $wp_user ) {

		// WP members (pass through)
		$this->wp_user 		= $wp_user;
		$this->ID 			= $wp_user->ID;
		$this->id			= $this->ID;
		$this->first_name 	= $wp_user->first_name;
		$this->last_name 	= $wp_user->last_name;
		$this->user_email 	= $wp_user->user_email;
		$this->user_login 	= $wp_user->user_login;

		// Get this plugin's meta
		$this->GetMeta();

		$this->stripe_id 	= $this->meta[ 'stripe_id' ];
		$this->card_last4 	= $this->meta[ 'card_last4' ];
		$this->card_type 	= $this->meta[ 'card_type' ];
		$this->level_id		= $this->meta[ 'level_id' ];
		$this->level 		= DlPs_Level::Get( $this->level_id );
		$this->plan_id 		= $this->meta[ 'plan_id' ];
		$this->plan 		= DlPs_Plan::Get( $this->plan_id );

		if( isset( $this->meta[ 'expiration' ] ) ) {
			$this->expiration = $this->meta[ 'expiration' ];
		} else {
			$this->expiration = mktime(0, 0, 0, 1, 1, 2000);
		}
	}

	public function UpdatePlan( $new_plan ) {

		// Ensure this is a DlPs_Plan class
		//
		if(!is_a( $new_plan, 'DlPs_Plan') ) {
			return false;
		}

		if( !is_null( $user->plan_id ) ) {

			// Already on a plan....cancel this subscription first.
			//

		}
	}

	private function CancelSubscription() {

		$prev_plan_id = $this->plan_id;
		DlPs_Plan::RemoveUser( $prev_plan_id, $this->ID );

		$this->plan_id = null;
		$this->plan = null;
		
		$this->Update();

		return true;
	}

	public function ChangePlan( $plan_id ) {

		$prev_plan_id = $this->plan_id;
		DlPs_Plan::RemoveUser( $prev_plan_id, $this->ID );

		// Make sure we have a valid plan.
		//
		$plan = DlPs_Plan::Get( $plan_id );
		if( is_null( $plan ) ) {

			return false;
		} 

		$this->plan_id = $plan_id;
		$this->plan = $plan;

		DlPs_Plan::AddUser( $plan_id, $this->ID );

		$this->Update();

		return true;
	}
	
	private function GetMeta() {

		$this->meta = get_user_meta( $this->wp_user->ID, self::$meta_key, true );
		
		if( !is_array( $this->meta ) ) {
		
			// Set this user as expired.
			//
			$this->meta = array();
		}
	}

	public function Update() {

		$this->meta[ 'stripe_id' ] 	= $this->stripe_id;
		$this->meta[ 'card_last4' ] = $this->card_last4;
		$this->meta[ 'card_type' ] 	= $this->card_type;
		$this->meta[ 'expiration' ] = $this->expiration;
		$this->meta[ 'plan_id' ] 	= $this->plan_id;
		$this->meta[ 'level_id' ]	= $this->level_id;

		update_user_meta( $this->wp_user->ID, self::$meta_key, $this->meta );		
	}
	
	public function ClearMeta() {
	
		delete_user_meta( $this->wp_user->ID, self::$meta_key );
	}

	// *********** STATIC FUNCTIONS ************ //
	//
	public static function Get( $id ) {
		$wp_user = get_user_by( 'id', $id );
		if( is_null( $wp_user ) ) {
			return null;
		}
		return new DlPs_User( $wp_user );
	}
	
	public static function GetByStripeId( $stripe_id ) {
	
		$users = self::All();
		foreach( $users as $user ) {
		
			if( $user->stripe_id == $stripe_id ) {
			
				return $user;
			}
		}
		
		return null;
	}

	public static function All() {

		$users = array();

		$wp_users = get_users();
		foreach( $wp_users as $wp_user ) {
			$user = new DlPs_User( $wp_user );
			$users[] = $user;
		}

		return $users;
	}

	public static function Delete( $user_id ) {

		delete_user_meta( $user_id, self::$meta_key );
	}

	public static function Create( $user, $pass, $email ) {

		$return = "";
		if( !empty( $user ) && 
			!empty( $email ) &&
			!empty( $pass )) {

			// We don't want duplicate users.
			//
			if( username_exists( $user ) ) {
				
				$return = "This <strong>username</strong> is taken. Please choose another.";
			
			} elseif( email_exists( $email ) ) {
			
				$return = "This <strong>email</strong> is taken. Please choose another.";
			
			} else {

				wp_create_user( $user, $pass, $email );

				$data = get_userdatabylogin( $user );

				$return = $data->ID;
			}
		} else {

			$return = "<strong>Username</strong>, <strong>password</strong> and <strong>email</strong> are all required.";
		
		}

		return $return;
	}
}

?>