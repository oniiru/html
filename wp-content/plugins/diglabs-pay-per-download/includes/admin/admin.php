<?php 

if(!class_exists('DlPayPerDownloadAdminMenus')) {

	class DlPayPerDownloadAdminMenus {

		function configure() {
			if(is_plugin_active("diglabs-pay-per-download/diglabs-pay-per-download.php")) {

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
							'Pay Per Download', 
							'manage_options', 
							'diglabs_stripe_pay_per_download', 
							array($this, 'display_my_admin')
							);
			}
			
		}
		
		function display_general_info() {
			echo "<img src='http://diglabs.com/images/Dig-Labs.png' alt='logo' />";
			echo "<h3>Professional Website and Software Development</h3>";
			echo "<p>For more information about Dig Labs, visit our website <a href='http://diglabs.com'>http://diglabs.com</a>.</p>";
		}
				
		function display_my_admin() {
			include( 'admin_form.php' );
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
	
	$dl_ppd_admin_menus = new DlPayPerDownloadAdminMenus();
	add_action('admin_menu', array($dl_ppd_admin_menus, 'configure'));
}

add_action('admin_head', 'dlppd_admin_register_head');
function dlppd_admin_register_head() {
//	$url = STRIPE_PAYMENTS_PLUGIN_URL.'/admin.css';
//	echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}

?>