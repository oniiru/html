<?php

if(!class_exists('DlPsAdminMenus')) {

	define( 'DLPS_ADMIN_PAGE', 'diglabs_premium_subscriber' );

	class DlPsAdminMenus {
		function configure() {
			if(is_plugin_active("diglabs-premium-subscribers/diglabs-premium-subscribers.php")) {

				// Make sure the main menu exists
				//
				if(!$this->find_my_menu_item('diglabs')) {

					add_menu_page(
							"Dig Labs", 
							"Dig Labs", 
							"manage_options", 
							"diglabs", 
							array($this, 'display_general_info'), 
							'http://diglabs.com/images/beaker-icon.png'
							);
				}
				
				// Add the submenu for this plugin
				//
				add_submenu_page(
							'diglabs', 
							'Dig Labs', 
							'Premium Subscribers', 
							'manage_options', 
							DLPS_ADMIN_PAGE, 
							array($this, 'display_my_admin')
							);
			}
		}
		
		// Callback for rendering the main menu
		//
		function display_general_info() {
			echo "<img src='http://diglabs.com/images/Dig-Labs.png' alt='logo' />";
			echo "<h3>Professional Website and Software Development</h3>";
			echo "<p>For more information about Dig Labs, visit our website <a href='http://diglabs.com'>http://diglabs.com</a>.</p>";
		}
		
		// Callback for rendering this plugin's admin menu
		//
		function display_my_admin() {
			include( 'admin-form.php' );
		}
		
		// Helper function to determine if a menu exists.
		//
		function find_my_menu_item($handle, $sub = false) {
			if(!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
			  return false;
			}
			global $menu, $submenu;
			$check_menu = $sub ? $submenu : $menu;
			if(empty($check_menu)) {
			  return false;
			}
			foreach($check_menu as $k => $item) {
			  if($sub) {
			    foreach($item as $sm) {
			      if($handle == $sm[2]) {
			        return true;
			      }
			    }
			  } 
			  else {
			    if($handle == $item[2]) {
			      return true;
			    }
			  }
			}
			return false;
		}
	}
	
	$dl_premimum_subscriber_admin_menus = new DlPsAdminMenus();
	add_action('admin_menu', array($dl_premimum_subscriber_admin_menus, 'configure'));
}

add_action('admin_head-index.php', 'dlps_load_admin_assets');
add_action('admin_head', 'dlps_admin_register_head');
function dlps_admin_register_head() {

	if( !isset( $_REQUEST['page'] ) || $_REQUEST['page'] == 'diglabs_premium_subscriber' ) {

		dlps_load_admin_assets();
	}
}
function dlps_load_admin_assets() {

	// Styles
	//
	wp_register_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
	wp_enqueue_style( 'jquery-style' );
	wp_register_style( 'dlps-admin-style', DLPS_PLUGIN_URL.'/css/admin.css', true);
	wp_enqueue_style( 'dlps-admin-style' );

	// Scripts
	//
	wp_register_script( 'google-jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'google-jquery-ui' );	
	wp_register_script( 'dlps-admin-js', DLPS_PLUGIN_URL.'/js/admin.js', array( 'jquery' ) );
	wp_enqueue_script( 'dlps-admin-js' );	
	wp_register_script( 'dlps-plans-js', DLPS_PLUGIN_URL.'/js/plans.js', array( 'jquery' ) );
	wp_enqueue_script( 'dlps-plans-js' );	
}

?>