<?php

class DlPs_Package {

	private static $option_key = 'dl_ps_packages';
	private static $packages;

	public $id;
	public $name;
	public $plan_ids = array();



	// *********** STATIC FUNCTIONS ************ //
	//
	public static function All() {

		if( isset( self::$packages ) ) {

			// Already fetched from the DB
			//
			return self::$packages;
		}

		// Need to fetch from the DB
		//
		$packages = get_option( self::$option_key );
		if(!is_array( $packages )) {
			$packages = array();
		}
		return $packages;
	}

	public static function Get( $id ) {
		$packages = self::All();

		foreach( $packages as $package ) {
			if( $package->id == $id ) {
				return $package;
			}
		}
		return null;
	}

	public static function Update( $packages ) {

		usort( $packages, array( __CLASS__, 'ComparePackage' ) );

		self::$packages = $packages;
		update_option( self::$option_key, self::$packages );
	}

	private static function ComparePackage($a, $b){
	
		return strcmp( $a->name, $b->name );
	}
	
	public static function AddPackage( $package ) {

		// Ensure this is a DlPs_Package class
		//
		if(!is_a( $package, 'DlPs_Package') ) {
			return false;
		}

		$packages = self::All();
		if( is_null( $package->id ) ) {
		
			// Find an open ID...just go with the max ID.
			//
			$max_existing_id = 0;
			foreach( $packages as $this_package ) {

				if( $this_package->id > $max_existing_id ) {

					$max_existing_id = $this_package->id;
				}
			}
			$package->id = $max_existing_id + 1;
		}

		// Add this to the current package and save
		//
		$packages[] = $package;
		self::Update( $packages );

		return true;	
	}

	public static function DeletePackage( $id ) {

		$packages = array();
		$current_packages = self::All();
		foreach( $current_packages as $package ) {

			if( $package->id != $id ) {

				$packages[] = $package;
			}
		}

		self::Update( $packages );

		if( count( $packages ) == count( $current_packages ) ) {
			return false;
		}
		return true;
	}

	public static function UpdatePackage( $package ) {

		if( !DeletePackage( $package->id ) ) {

			return false;
		}

		return AddPackage( $package );
	}
}

?>