<?php 

class DlPs_Options {

	private static $option_key = 'dl_ps_options';
	private static $prefix = 'dlps_';
	private static $post_meta_key = 'dlps_levels';


	// ----------------------------------------
	//
	// Generic Helpers
	//
	//
	public static function Clear() {
	
		delete_option( self::$option_key );
	}
	
	public static function Get() {

		$options = get_option( self::$option_key );
		if(!is_array( $options )) {
			$options = array();
		}
		if( is_null( $options[ 'no_access_html' ] ) ) {
			$options[ 'no_access_html' ] = "Access to this content is for subscribers. Please <a href='#'>subscribe</a> or <a href='#'>login</a>.";
		}
		if( is_null( $options[ 'show_lock_icons' ] ) ) {
			$options[ 'show_lock_icons' ] = true;
		}

		return $options;
	}

	public static function Update( $options ) {
		update_option( self::$option_key, $options );
	}

	// ----------------------------------------
	//
	// Misc Options
	//
	//
	public static function IsLockIconVisible() {
		$options = self::Get();
		return $options[ 'show_lock_icons' ];
	}
	public static function SetIsLockIconVisible( $val ) {
		$options = self::Get();
		$options[ 'show_lock_icons' ] = $val;
		self::Update( $options );
	}
	public static function PostMetaKey() {
		return self::$post_meta_key;
	}


	// ----------------------------------------
	//
	// URL Options
	//
	//
	public static function GetRegisterUrl() {
		$options = self::Get();
		return $options[ 'register_url' ];
	}
	public static function UpdateRegisterUrl( $url ) {
		$options = self::Get();
		$options[ 'register_url' ] = $url;
		self::Update( $options );
	}
	public static function GetLoginUrl() {
		$options = self::Get();
		return $options[ 'login_url' ];
	}
	public static function UpdateLoginUrl( $url ) {
		$options = self::Get();
		$options[ 'login_url' ] = $url;
		self::Update( $options );
	}
	public static function GetSubscribeUrl() {
		$options = self::Get();
		return $options[ 'subscribe_url' ];
	}
	public static function UpdateSubscribeUrl( $url ) {
		$options = self::Get();
		$options[ 'subscribe_url' ] = $url;
		self::Update( $options );
	}


	// ----------------------------------------
	//
	// HTML / DOM Options
	//
	//
	public static function GetNoAccessHtml($swap=true) {
		$options = self::Get();
		$html = $options[ 'no_access_html' ];

		if( $swap ) {
			$login_url = self::GetLoginUrl();
			$register_url = self::GetRegisterUrl();
			$subscribe_url = self::GetSubscribeUrl();

			$html = str_replace( "{login_url}", $login_url, $html );
			$html = str_replace( "{register_url}", $register_url, $html );
			$html = str_replace( "{subscribe_url}", $subscribe_url, $html );
		}

		return $html;
	}
	public static function UpdateNoAccessHtml( $html ) {
		$options = self::Get();
		$options[ 'no_access_html' ] = $html;
		self::Update( $options );
	}

}

?>