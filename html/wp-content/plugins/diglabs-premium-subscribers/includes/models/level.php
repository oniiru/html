<?php

class DlPs_Level {

	private static $option_key = 'dl_ps_levels';
	private static $levels;

	public $id;
	public $name;
	public $description;
	public $plan_ids = array();


	// *********** STATIC FUNCTIONS ************ //
	//
	public static function ClearAll() {
	
		// Levels
		self::Clear();
		
		// Plans & subscriptions
		DlPs_Plan::Clear();
		
		// Post meta
		$key = DlPs_Options::PostMetaKey();
		$the_query = new WP_Query( 'post_type=any' );
		while ( $the_query->have_posts() ) : $the_query->the_post();
		
			delete_post_meta( $the_query->post->ID, $key );
		endwhile;
		wp_reset_postdata();
		
		// User meta
		$users = DlPs_User::All();
		foreach( $users as $user ) {
			
			$user->ClearMeta();
		}
	}
	public static function Clear() {
			
		delete_option( self::$option_key );
		
	}
	
	public static function All() {
		
		if( isset( self::$levels ) ) {

			// Already fetched from the DB
			//
			return self::$levels;
		}

		// Need to fetch from the DB
		//
		$levels = get_option( self::$option_key );
		if(!is_array( $levels )) {
			$levels = array();
		}
		self::$levels = $levels;

		return self::$levels;
	}

	public static function Update( $levels ) {

		self::$levels = $levels;
		update_option( self::$option_key, self::$levels );
	}

	public static function Get( $id ) {
		$levels = self::All();

		foreach( $levels as $level ) {
			if( $level->id == $id ) {
				return $level;
			}
		}
		return null;
	}

	public static function AddLevel( $level ) {

		// Ensure this is a DlPs_Level class
		//
		if(!is_a( $level, 'DlPs_Level') ) {
			return false;
		}

		$levels = self::All();
		if( is_null( $level->id ) ) {
			
			// Find an open ID...just go with the max ID.
			//
			$max_existing_id = 0;
			foreach( $levels as $this_plan ) {

				if( $this_plan->id > $max_existing_id ) {

					$max_existing_id = $this_plan->id;
				}
			}
			$level->id = $max_existing_id + 1;
		}

		// Add this to the current levels and save
		//
		$levels[] = $level;
		self::Update( $levels );

		return true;	
	}

	public static function DeleteLevel( $id ) {

		$levels = array();
		$current_levels = self::All();
		foreach( $current_levels as $level ) {

			if( $level->id != $id ) {

				$levels[] = $level;
			} else {

				// Do not allow the deletion of levels
				//	that have plans.
				//
				if( count( $level->plan_ids ) > 0 ) {
					return false;
				}
			}
		}

		self::Update( $levels );

		if( count( $levels ) == count( $current_levels ) ) {
			return false;
		}
		return true;
	}

	public static function AddPlan( $level_id, $plan_id ) {

		$levels = self::All();

		foreach( $levels as $level ) {
			if( $level->id == $level_id ) {
				
				if( !in_array( $plan_id, $level->plan_ids ) ) {

					$level->plan_ids[] = $plan_id;

					self::Update( $levels );

					return true;
				}
			}
		}
		return false;
	}

	public static function DeletePlan( $level_id, $plan_id ) {

		$levels = self::All();

		foreach( $levels as $level ) {
			if( $level->id == $level_id ) {

				$after = array();
				foreach( $level->plan_ids as $this_plan_id ) {
					if( $plan_id != $this_plan_id ) {

						$after[] = $this_plan_id;
					}
				}
				if( count( $after ) < count( $level->plan_ids ) ) {

					$level->plan_ids = $after;
					self::Update( $levels );

					return true;
				}

				return false;
			}
		}
		return false;
	}

	public static function RemovePlan( $level_id, $plan_id ) {

		$levels = self::All();

		foreach( $levels as $level ) {
			if( $level->id == $level_id ) {

				$after = array();
				foreach( $level->plan_ids as $this_plan_id ) {
					if( $plan_id != $this_plan_id ) {

						$after[] = $this_plan_id;
					}
				}
				if( count( $after ) < count( $level->plan_ids ) ) {

					$level->plan_ids = $after;
					self::Update( $levels );

					return true;
				}

				return false;
			}
		}
		return false;
	}
}

?>