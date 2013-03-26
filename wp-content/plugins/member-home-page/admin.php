<?php
/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @author Anton Matiyenko (amatiyenko@gmail.com)
 */

/**
 * Member homepage admin panel class
 */
class MHPAdmin{
	
	/**
	 * Runs required backend init operations
	 */
	public function init(){
		//Add an item to the admin menu
		add_object_page(__('Member Homepage', 'member_home_page'), __('Member Homepage', 'member_home_page'), 8, 'member-home-page-manage', array('MHPAdmin','process'), '/wp-content/plugins/paid-memberships-pro/images/menu_users.png', 12);
	}
	
	/**
	 * Performs required operations to save/retrive data into/from database to render admin settings form
	 */
	public function process() {
		/**
		 * Include file containing HTML for the form
		 */
		require_once(MHP_PATH . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'mhp_edit_template.php');
		$data = array();
		/**
		 * Form was submitted => process it's data
		 */
		if (!empty($_POST)) {
			$data = $_POST;
			//Save settings
			/**
			 * Call incoming $_POST data validation
			 */
			if($errors=self::validateInput()){
				/**
				 * Assign errors if any detected
				 */
				$data['errors'] = $errors;
			} else {
				/**
				 * Store the submitted data
				 */
				//Day
				update_mhp_option('mhp_day_of_the_week', mysql_real_escape_string($_POST['mhp_day_of_the_week']));
				//Time starts
				update_mhp_option('mhp_time_starts', mysql_real_escape_string($_POST['mhp_time_starts']));
				//Time ends
				update_mhp_option('mhp_time_ends', mysql_real_escape_string($_POST['mhp_time_ends']));
				//Signup URL
				update_mhp_option('mhp_signup_url', mysql_real_escape_string($_POST['mhp_signup_url']));
				//Featured page No.1
				update_mhp_option('mhp_promo_page_1_id', mysql_real_escape_string($_POST['mhp_promo_page_1_id']));
				//Featured page No.2
				update_mhp_option('mhp_promo_page_2_id', mysql_real_escape_string($_POST['mhp_promo_page_2_id']));
				/**
				 * ToDo: Change the way update_mhp_option() is used, so all the data was first set and then all stored at once
				 */
			}
		} else {
			/**
			 * Retrieve associated options values from the DB and assign them to the 
			 * $data array being passed into the HTML form
			 */
			$data['mhp_day_of_the_week'] = get_mhp_option('mhp_day_of_the_week');
			$data['mhp_time_starts'] = get_mhp_option('mhp_time_starts');
			$data['mhp_time_ends'] = get_mhp_option('mhp_time_ends');
			$data['mhp_signup_url'] = get_mhp_option('mhp_signup_url');
			$data['mhp_promo_page_1_id'] = get_mhp_option('mhp_promo_page_1_id');
			$data['mhp_promo_page_2_id'] = get_mhp_option('mhp_promo_page_2_id');
		}
		/**
		 * Fetch and generate options data
		 */
		$data['hours'] = array('1:00 AM', '2:00 AM', '3:00 AM', '4:00 AM', '5:00 AM', '6:00 AM',
			'7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 AM',
			'1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM',
			'7:00 PM', '8:00 PM', '9:00 PM', '10:00 PM', '11:00 PM', '12:00 PM');
		$data['days'] = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		/**
		 * Fetch list of appropriate pages
		 */
		$data['pages'] = get_pages(array('child_of' => MHP_TRAINING_PARENT_PAGE));
		/**
		 * Create pages data array appropriate for the options builder inside HTML
		 */
		foreach($data['pages'] as $k => $page){
			$data['pages'][$page->ID] = $page->post_title . ' (' . get_permalink($page->ID) . ')';
			unset($data['pages'][$k]);
		}
		/**
		 * Render the HTML form
		 */
		MHP_Edit_Template::display($data);
	}
	
	/**
	 * Validates incoming $_POST data.
	 * Applied before data is stored
	 */
	private function validateInput(){
		$errors = array();
		$today = date('Y-m-d');
		//Validate if the starting time is prior ending time - to avoid very common mismatch
		if(strtotime($today . ' ' . $_POST['mhp_time_starts'])>strtotime($today . ' ' . $_POST['mhp_time_ends'])) {
			$errors['time'] = __('Wrong time selection.', 'member_home_page');
		}
		//See if the URL is specified
		if(empty($_POST['mhp_signup_url'])) {
			$errors['url'] = __('URL should not be empty.', 'member_home_page');
		}
		return $errors;
	}
}
?>
