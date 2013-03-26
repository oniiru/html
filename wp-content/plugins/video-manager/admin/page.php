<?php
/*
 * Creating Admin Video page
 */

class IonVideoPage extends IonVideoPageTemplates {

	/**
	 * Page variables
	 */
	var $page_name = 'video-manager';
	var $setting_page_name = 'video-manager-setting';
	var $edit_dir = 'edit-directory';
	var $delete_dir = 'delete-directory';
	var $delete_vid = 'delete-vid';

	/**
	 * Nonce variables
	 */
	var $add_video_directory_nonce = '44985';
	var $external_vid_nonce = '2222';
	var $video_list_nonce = '8888';
	var $added_video_list_nonce = '11111';
	var $popup_setting_nonce = '19840125';
	var $sign_url_nonce = '19850803';

	function IonVideoPage() {
		add_action('admin_menu', array(&$this, 'create_video_pages'));
		add_action('admin_enqueue_scripts', array(&$this, 'register_scripts'));
		add_action('admin_print_styles', array(&$this, 'register_styles'));
	}

	function create_video_pages() {
		$page = add_menu_page('Manage Video', 'Video Manager', 'administrator', $this->page_name, array(&$this, 'index_page'), '', 25);
		add_submenu_page($this->page_name, 'Overview page', 'Overview', 'administrator', $this->page_name, array(&$this, 'index_page'));
		add_submenu_page($this->page_name, 'Settings Page', 'Settings', 'administrator', $this->setting_page_name, array(&$this, 'setting_page'));
		add_action('admin_print_styles-' . $page, array(&$this, 'register_scripts'));
	}

	function index_page() {
		echo '<div class="wrap">';
		if (($_GET['page'] == $this->page_name) && isset($_GET['pageAction'])) {
			switch ($_GET['pageAction']) {
				case 'edit-directory' :
					if (isset($_GET['did']) && is_numeric($_GET['did'])) :
						$this->edit_directory();
					else :
						$this->ion_overview_page();
					endif;
					break;
				case 'delete-directory' :
					if (isset($_GET['did']) && is_numeric($_GET['did'])) :
						global $wpdb;
						$v_directory = get_option('v_directory_');
						if ($wpdb->get_var("SELECT COUNT(*) FROM $v_directory WHERE id = '" . $_GET['did'] . "'")) :
							if ($wpdb->query("DELETE FROM $v_directory WHERE id = '" . $_GET['did'] . "'")) {
								$this->show_message('Video directory successfully deleted.');
							} else {
								$this->show_message('Execution failed. Please try again.', 1);
							}
						endif;
						$this->ion_overview_page();
					endif;
					break;
				default :
					$this->ion_overview_page();
			}
		} else {
			$this->ion_overview_page();
		}
		echo '</div>';
	}

	function setting_page() {
		$filepath = admin_url() . 'admin.php?page=' . $this->setting_page_name;
		?>
		<div class="wrap">
			<?php $this->header_page('Video Manager Setting'); ?>
			<form id="popup-setting_form" name="popup-setting-form" method="post" action="<?php echo $filepath; ?>">
				<div class="video-manager-list-column">
					<?php $this->popup_content_setting(); ?>
				</div><!-- #video-list-column -->
				<div class="video-manager-list-column">
					<?php $this->sign_url_setting(); ?>
				</div><!-- #video-list-column -->
				<div class="video-manager-list-column">
					<?php $this->member_tab_setting(); ?>
				</div><!-- #video-list-column -->
			</form>
		</div>
		<?php
	}

	function ion_overview_page() {
		$this->header_page('Video Manager');
		$this->video_directory();
		echo '<br />';
		$this->add_new_directory();
	}

	function edit_directory() {
		global $wpdb, $filepath, $all_roles, $products;

		$filepath = admin_url() . 'admin.php?page=' . $this->page_name . '&pageAction=' . $this->edit_dir . '&did=' . $_GET['did'];
		$this->header_page('Add/Edit Video List');
		$get_roles = $wpdb->prefix . 'user_roles';
		$all_roles = get_option($get_roles);
		# Chargify
		if (class_exists('ion_chargify')) {
			$chargify = new ion_chargify();
			$products = $chargify->products();
		}
		?>
		<div id="video-list-column">
			<?php $this->external_add(); ?>
		</div><!-- #video-list-column -->
		<?php $this->added_videos(); ?>
		<?php
	}

	# Not used
	/* function overview_notice() {
	  echo '<div id="poststuff">';
	  echo '<div id="video-directory" class="postbox">';
	  echo '<h3 class="hndle">Sample 1</h3>';
	  echo '<div class="video-directory-info inside">';
	  echo 'sup';
	  echo '</div>';
	  echo '</div>';
	  echo '</div>';
	  } */

	function header_page($page = '') {
		?>
		<div class="icon32" id="icon-themes"><br></div>
		<h2 class="nav-tab-wrapper">
			<a href="#" class="nav-tab"><?php echo $page ?></a>
		</h2>
		<?php
		// Submit function
		$this->process_submission();
		//echo $wpdb->num_queries;
	}
	
	/**
	 * Adjusts all the embed codes to have the textarea resolution of FullHD (just in case ;))
	 * @param text $code
	 * @return text
	 */
	public static function adjustVideoSize($code){
		$code = preg_replace("/height\=(\"|\')[0-9]+(\"|\')/", 'height="1080"', stripslashes($code));
		$code = preg_replace("/width\=(\"|\')[0-9]+(\"|\')/", 'width="1920"', $code);
		return addslashes($code);
	}

	function video_directory() {
		global $wpdb;
		$v_directory = get_option('v_directory_');
		$get_lists = $wpdb->get_results("SELECT * FROM " . $v_directory . " ORDER BY id ASC");
		?>
		<script type="text/javascript">
			ZeroClipboard.setMoviePath( '<?php echo $GLOBALS['url_path'] . '/display/zeroclipboard/'; ?>ZeroClipboard.swf' );
		</script>
		<table id="video-directory" class="widefat">
			<thead>
				<tr>
					<th style="line-height:20px;" scope="col">Video Directory</th>
					<th style="line-height:20px;" scope="col">Video Count</th>
					<th style="line-height:20px;" scope="col">Short code</th>
					<th style="line-height:20px;" scope="col">Date Created</th>
					<th style="line-height:20px;" scope="col">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($get_lists as $list) { ?>
					<tr>
						<td>
							<a href="<?php echo admin_url() . 'admin.php?page=' . $this->page_name . '&pageAction=' . $this->edit_dir . '&did=' . $list->id; ?>">
								<?php echo stripcslashes($list->directory_name); ?>
							</a>
						</td>
						<td><?php echo $list->video_count; ?></td>
						<td><input id="get-copy-id-<?php echo $list->id; ?>" type="text" style="width: 30%;" value='[videodirectory id="<?php echo $list->id; ?>"]' readonly="readonly"> <a id="d_clip_button_<?php echo $list->id; ?>" style="position:relative">Copy to Clipboard</a>
							<script type="text/javascript">
								var clip_<?php echo $list->id; ?> = new ZeroClipboard.Client();
								clip_<?php echo $list->id; ?>.setText( '[videodirectory id="<?php echo $list->id; ?>"]' );
								clip_<?php echo $list->id; ?>.glue( 'd_clip_button_<?php echo $list->id; ?>' );
							</script>
						</td>
						<td><?php echo date('m/d/y', strtotime($list->date_created)) ?></td>
						<td><a href="<?php echo admin_url() . 'admin.php?page=' . $this->page_name . '&pageAction=' . $this->edit_dir . '&did=' . $list->id; ?>">Edit</a> | <a onclick="return confirm('All videos hook to this directory will be deleted as well. Proceed?')" href="<?php echo admin_url() . 'admin.php?page=' . $this->page_name . '&pageAction=' . $this->delete_dir . '&did=' . $list->id; ?>">Delete</a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}

	function add_new_directory() {
		$filepath = admin_url() . 'admin.php?page=' . $this->page_name;
		?>
		<h2>Add New Video Directory</h2>
		<br />
		<form id="video-directory_form" name="video-directory" method="post" accept-charset="utf-8" action="<?php echo $filepath; ?>">
			<?php wp_nonce_field($this->add_video_directory_nonce); ?>
			<table>
				<tbody>
					<tr valign="top">
						<td  style="width:20%;"><input type="text" size="40" name="name_of_directory"></td>
						<td><input type="submit" value="Add directory" id="add_newdir_btn" name="add_dir" class="button-primary"></td>
					</tr>
				</tbody>
			</table>
			<i>( Allowed characters for file and folder names are: a-z, A-Z, 0-9, -, _ )</i>
			<input name="key" type="hidden" value="new_directory" />
		</form>
		<?php
	}

	function process_submission() {
		if (!empty($_POST) && isset($_POST['key'])) {
			switch ($_POST['key']) {
				/**
				 * Process submitted form to an a new directory
				 *
				 * @since 1.0.0
				 */
				case 'new_directory':
					if (check_admin_referer($this->add_video_directory_nonce)) :
						global $wpdb;

						$table_ = get_option('v_directory_');
						$var = $_POST['name_of_directory'];
						if ($wpdb->get_var("SELECT COUNT(*) FROM $table_ WHERE 'directory_name' = '$var'")) :
							return $this->show_message("An <b>ERROR</b> has occured: Directory '<b>" . $var . "</b>' already exist.", 1);
						else :
							if ($wpdb->insert($table_, array(
													'directory_name' => $var,
													'video_count' => 0,
													'date_created' => date('Y-m-d'),
													'video_order' => ''
											))) {
								$this->show_message('New Directory Added');
							}
						endif;
					endif;
					break;
				case 'update_video_list' :
					if (check_admin_referer($this->video_list_nonce)) :
						if (($_POST['list_action'] != -1) && ($_POST['list_action'] != -1)) {
							switch ($_POST['list_action']) {
								case 'add-selected' :
									global $wpdb;
									$table_ = get_option('v_listings_');
									$table2_ = get_option('v_directory_');
									$video_count = 0;

									if (is_array($_POST['list'])) {
										$video_order = $wpdb->get_results("SELECT video_order, video_count FROM $table2_ WHERE id = '" . $_POST['did'] . "'", ARRAY_A);
										$video_order = unserialize($video_order[0]['video_order']);
										$old_count = $video_order[0]['video_count'];
										foreach ($_POST['list'] as $list) {
											$stack = array();
											$video_name = $_POST['video_name'][$list];
											$options = $_POST['video'][$list];
											$options['media_id'] = $list;
											$stack[0] = $options;
											$stack = serialize($stack);

											if ($wpdb->insert($table_, array('dir_id' => $_POST['did'], 'video_name' => $video_name, 'options' => $stack))) {
												$video_order[] = $wpdb->insert_id;
											}
											$video_count++;
										}
										$new_count = $video_count + $old_count;
										$video_order = serialize($video_order);
										if ($wpdb->update($table2_, array('video_order' => $video_order, 'video_count' => $new_count), array('id' => $_POST['did']))) {
											$this->show_message('Video added to the directory');
										} else {
											echo $wpdb->last_query;
										}
									}
									break;
							}
						} else {
							$this->show_message('No action taken.');
						}
					endif;
					break;

				/**
				 * Process and add submitted form for embed links
				 *
				 * Upon submission it will verify the nonce and input fields, if no error, proceed creating the array values, else, return fail.
				 * Verifies if dir_id has already exist and has existing value, if that is the case it will prepend the submitted value to an
				 * existing mysql value. If not, do an insert query.
				 *
				 * @since 1.0.0
				 * @uses $wpdb
				 * @return bool To verify that the query is successful or it has failed.
				 */
				case 'add_external_video_link' :
					if (check_admin_referer($this->external_vid_nonce)) :
						global $wpdb;

						$stack = array();
						$stack_ = array();

						if (empty($_POST['video']['name'])) {
							return $this->show_message('An <b>ERROR</b> has occured: Label should not be empty.', 1);
						}

						if (empty($_POST['video']['duration']['hours']) || empty($_POST['video']['duration']['minutes']) || empty($_POST['video']['duration']['seconds'])) {
							return $this->show_message('An <b>ERROR</b> has occured: Duration should not be empty.', 1);
						} elseif (!ctype_digit($_POST['video']['duration']['hours']) || !ctype_digit($_POST['video']['duration']['minutes']) || !ctype_digit($_POST['video']['duration']['seconds'])) {
							return $this->show_message('An <b>ERROR</b> has occured: Duration should be an integer.', 1);
						}

						// New version
						array_push($stack_, array('duration' => $_POST['video']['duration'], 'options' => $_POST['video']['options']));
						// End
						// New version
						$table_ = get_option('v_listings_');
						$table2_ = get_option('v_directory_');
						// End
						// New version
						$stack_ = serialize($stack_);
						$vid_counter_ = count($stack_);

						if ($wpdb->insert($table_, array('dir_id' => $_POST['did'], 'video_name' => $_POST['video']['name'], 'embed_value' => esc_textarea($this->adjustVideoSize($_POST['video']['embed'])), 'options' => $stack_))) {
							$the_id = $wpdb->insert_id;
							$old_order = $wpdb->get_results("SELECT video_order FROM $table2_ WHERE id = '" . $_POST['did'] . "'", ARRAY_A);
							$old_order = unserialize($old_order[0]['video_order']);
							if (is_array($old_order)) :
								$old_order[] = $the_id;
								$new_order = $old_order;
								$order = serialize($new_order);
							else :
								$new_order = array();
								$new_order[] = $the_id;
							endif;
							$order = serialize($new_order);
							$vid_counter_ = count($new_order);
							if ($wpdb->update($table2_, array('video_count' => $vid_counter_, 'video_order' => $order), array('id' => $_POST['did']))) {
								$this->show_message('Video added to the directory');
							} else {
								$this->show_message('An error has occured. Please try again.', 1);
							}
						} else {
							$this->show_message('An error has occured. Please try again.', 1);
						}
					endif; # check_admin_referer
					break;
				case 'update_added_video_list' :
					if (check_admin_referer($this->added_video_list_nonce)) :
						global $wpdb;
						$stack = array();
						if (empty($_POST['update-directory-name'])) {
							return $this->show_message('Update failed: Directory name should not be empty.', 1);
						}
						$table1_ = get_option('v_listings_');
						$table2_ = get_option('v_directory_');
						$dir_name = $_POST['update-directory-name'];
						$popup_id = $_POST['popup_id'];
						if (!empty($popup_id)) {
							$wpdb->update($table2_, array('popup_id' => $popup_id), array('id' => $_POST['did']));
						}
						if (!empty($_POST['video'])) {
							$value = $_POST['video'];
							$order = $_POST['order'];
							// New version
							if ($wpdb->get_var("SELECT COUNT(*) FROM $table2_ WHERE directory_name = '$dir_name' AND id != '" . $_POST['did'] . "'")) :
								return $this->show_message("An <b>ERROR</b> has occured: Directory '<b>" . $dir_name . "</b>' already exist.", 1);
							else :
								$vid_counter_ = 0;
								foreach ($value as $val) {
									if (empty($val['name']) || empty($val['duration']['hours']) || empty($val['duration']['minutes']) || empty($val['duration']['seconds']) || empty($val['width']) || empty($val['height'])) {
										return $this->show_message('Update failed: One of the added video must not have an empty field.', 1);
									} elseif (!ctype_digit($val['width']) || !ctype_digit($val['height'])) {
										return $this->show_message('Update failed: Width and Height should be an integer.', 1);
									} elseif (!ctype_digit($val['duration']['hours']) || !ctype_digit($val['duration']['minutes']) || !ctype_digit($val['duration']['seconds'])) {
										return $this->show_message('Update failed: Duration should be an integer.', 1);
									}
								}
								foreach ($value as $val) {
									$stack_ = array();
									if (!empty($val['media_id']) && !empty($val['width']) && !empty($val['height'])) {
										array_unshift($stack_, array('media_id' => $val['media_id'], 'width' => $val['width'], 'height' => $val['height'], 'duration' => $val['duration'], 'options' => $val['options']));
									} else {
										array_unshift($stack_, array('duration' => $val['duration'], 'options' => $val['options']));
									}
									$stack_ = serialize($stack_);
									$wpdb->update($table1_, array('video_name' => $val['name'], 'embed_value' => esc_textarea($this->adjustVideoSize($val['value'])), 'options' => $stack_), array('ID' => $order[0]));
									array_shift($order);
									$vid_counter_++;
								}
								$new_order = $_POST['order'];
								$new_order = serialize($new_order);
								$wpdb->update($table2_, array('directory_name' => $dir_name, 'video_count' => $vid_counter_, 'video_order' => $new_order), array('id' => $_POST['did']));
								if (is_array($_POST['for_deletion'])) {
									$delete = implode(',', $_POST['for_deletion']);
									$wpdb->query("DELETE FROM $table1_ WHERE ID IN (" . $delete . ")");
								}
								$this->show_message('Video updated to the directory');
							endif;
							// End
						} else {
							if ($wpdb->update($table2_, array('directory_name' => $dir_name, 'video_count' => 0, 'video_order' => NULL), array('id' => $_POST['did']))) {
								if (is_array($_POST['for_deletion'])) {
									$delete = implode(',', $_POST['for_deletion']);
									$wpdb->query("DELETE FROM $table1_ WHERE ID IN (" . $delete . ")");
									$this->show_message('Directory have been emptied.');
								}
							}
						}
					endif;
					break;
				case 'update_options_page' :
					if (check_admin_referer($this->option_page_nonce)) :
						if (update_option('ion_video_options', $_POST['video'])) {
							$this->show_message('Options updated.');
						}
					endif;
					break;
				case 'popup_setting':
					if (check_admin_referer($this->popup_setting_nonce)) :
						$popup_data = $_POST['popup'];
						if ($popup_data['submit'] == 'Save Changes') {
							$content = $popup_data['content'];
							$action = $popup_data['action'];
							$button_color = $popup_data['button_color'];
							$button_text = $popup_data['button_text'];
							$user_type = $popup_data['user_type'];
							$sign_in = $popup_data['sign_in'];
							$sign_up = $popup_data['sign_up'];
							$popup_id = $popup_data['popup_id'];
							$google_analytic = isset($popup_data['add_google_analytics']) ? 'on' : '';
							
							$member_tab_title = $popup_data['member_tab_title'];
							$member_tab_text = $popup_data['member_tab_text'];
							update_option('member_tab_settings', array('title' => $member_tab_title, 'text' => $member_tab_text));

							if ($action != 'none' && ( $sign_in == '' || $sign_up == '')) {
								$this->show_message('An <b>ERROR</b> has occured: Sigin In or Up URL is empty.');
							} elseif ($content == '') {
								$this->show_message('An <b>ERROR</b> has occured: Content is empty.');
							} elseif ($button_text == '') {
								$this->show_message('An <b>ERROR</b> has occured: Button text is empty.');
							} else {
								global $wpdb;

								$table = $wpdb->prefix . 'video_popup';

								$popup_options = array(
										'content' => stripcslashes($content)
										, 'action' => $action
										, 'button_color' => $button_color
										, 'button_text' => $button_text
										, 'user_type' => $user_type
										, 'sign_in' => $sign_in
										, 'sign_up' => $sign_up
										, 'add_google_analytics' => $google_analytic
								);
								$popup_options = serialize($popup_options);
								if ($wpdb->get_var("SELECT COUNT(*) FROM $table WHERE id='$popup_id'")) :
									$wpdb->update($table, array('popup_options' => $popup_options, 'date_created' => date('Y-m-d')), array('id' => $popup_id), array('%s', '%s'));
								else :
									$wpdb->insert($table, array('popup_options' => $popup_options, 'date_created' => date('Y-m-d')), array('%s', '%s'));
								endif;
								$this->show_message('Popup Options updated.');
							}
						} else if ($popup_data['submit'] == 'Add new popup') {
							global $wpdb;

							$table = $wpdb->prefix . 'video_popup';
							$popup_settings = get_option('v_popup_default_settings_');
							unset($popup_settings['popup_id']);
							$wpdb->insert($table, array('popup_options' => serialize($popup_settings), 'date_created' => date('Y-m-d')));
							$popup_settings['popup_id'] = $wpdb->insert_id;
							update_option('v_popup_default_settings_', $popup_settings);
							$_POST['popup']['popup_id'] = $wpdb->insert_id;
							$this->show_message('Created new popup.');
						} else if ($popup_data['submit'] == 'Delete popup') {

							$popup_ids = $this->get_popup_ids();

							if (sizeof($popup_ids) <= 1) {
								$this->show_message('You can\'t delete this popup.');
							} else {
								global $wpdb;

								$table = $wpdb->prefix . 'video_popup';
								$table_dir = get_option('v_directory_');

								$wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id = %d", $popup_data['popup_id']));
								$wpdb->update($table_dir, array('popup_id' => 0), array('popup_id' => $popup_data['popup_id']));
								$this->show_message('Deleted popup-' . $popup_data['popup_id'] . '.');
								$popup_ids = $this->get_popup_ids();
								$popup_settings = get_option('v_popup_default_settings_');
								$popup_settings['popup_id'] = $popup_ids[count($popup_ids) - 1]['id'];
								$_POST['popup']['popup_id'] = $popup_ids[count($popup_ids) - 1]['id'];
								update_option('v_popup_default_settings_', $popup_settings);
							}
						}
					endif;
					break;
			}
		}
	}

	function show_message($msg, $type = 0) {
		if (!empty($msg)) :
			switch ($type) {
				case 0:
				case 'message':
					echo '<div class="updated fade"><p>' . $msg . '</p></div>' . "\n";
					break;
				case 1:
				case 'error':
					echo '<div class="error fade"><p>' . $msg . '</p></div>' . "\n";
					break;
			}
		else :
			return;
		endif;
	}

	function authenticate_($variable, $data = array()) {
		global $wpdb;

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $variable)) { // Check for Special Characters
			return $this->show_message('An <b>ERROR</b> has occured: Special characters are not allowed.', 1);
		}

		if (is_array($data) && !empty($data)) {
			if ($wpdb->get_var("SELECT COUNT(*) FROM $data[0] WHERE $data[1] = '$variable'")) :
				return $this->show_message("An <b>ERROR</b> has occured: Directory '<b>" . $variable . "</b>' already exist.", 1);
			else :
				if ($wpdb->insert($data[0], array(
										'directory_name' => $variable,
										'video_count' => 0,
										'date_created' => date('Y-m-d')
								))) {
					$this->show_message('New Directory Added');
				}
			endif;
		}
	}

	function register_scripts() {
		wp_register_script('ion-admin', $GLOBALS['url_path'] . '/admin/js/ion-admin.js', array('jquery'), '1.0.0');
		wp_enqueue_script('ion-admin');
		wp_register_script('zeroclip', $GLOBALS['url_path'] . '/display/zeroclipboard/ZeroClipboard.js', array('jquery'), '1.0.0');
		wp_enqueue_script('zeroclip');
		wp_enqueue_script('jquery-ui-sortable');
	}

	function register_styles() {
		wp_enqueue_style('ion-admin-css', $GLOBALS['url_path'] . '/admin/css/ion-admin.css', false, '1.0.0', 'screen');
		wp_enqueue_style('ion-admin-css');
		wp_enqueue_style('ion-front-css', $GLOBALS['url_path'] . '/css/video-directory.css', false, '1.0.0', 'screen');
		wp_enqueue_style('ion-front-css');
	}

}

new IonVideoPage();