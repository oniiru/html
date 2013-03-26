<?php

class IonVideoPageTemplates {

	function added_videos() {
		global $wpdb, $filepath, $all_roles, $products;

		$popup_ids = $this->get_popup_ids();
		?>
		<div id="added-video-list-wrapper">
			<form id="added-video-list_form" method="POST" action="<?php echo $filepath; ?>">
				<?php wp_nonce_field($this->added_video_list_nonce); ?>
				<?php
				$table = get_option('v_directory_');
				$dir_name = $wpdb->get_var("SELECT directory_name FROM $table WHERE id ='" . $_GET['did'] . "'");
				?>
				<div id="added-video-header" class="ion-video-top-display">
					<div class="video-submit-box">
						<label for="directory-name" class="video-name-label howto open-label">
							<span>Video Directory</span>
							<input type="text" value="<?php echo stripslashes($dir_name); ?>" title="Edit video directory name here" class="directory-name" id="directory-name" name="update-directory-name">
						</label>
						<div class="publishing-action">
							<input type="submit" value="Update" class="button-primary" id="update_added_videos" name="update_added_videos">
						</div>
						<div class="clear"></div>
					</div><!-- .video-submit-box 1 -->
				</div><!-- #added-video-header -->
				<div id="added-video-list">
					<?php
					$table = get_option('v_listings_');
					$table2 = get_option('v_directory_');

					$get_ = $wpdb->get_results("SELECT video_order, popup_id FROM $table2 WHERE id = '" . $_GET['did'] . "'", ARRAY_A);
					$order = unserialize($get_[0]['video_order']);
					$popup_id = $get_[0]['popup_id'];
					if ($popup_id == 0) {
						$popup_id = $this->get_default_popup_id();
					}
					?>

					<ul id="videos-to-edit">
						<?php
						if (is_array($order)) :

							$i = 0;
							foreach ($order as $o) {
								$videos = $wpdb->get_results("SELECT * FROM $table WHERE ID IN (" . $o . ")", ARRAY_A);

								$video = $videos[0];
								$options = $video['options'];
								$options = unserialize($options);
								$options_ = $options[0];
								?>
								<li class="video-item">
									<dl class="video-item-bar">
										<dt class="video-item-handle"><span class="video-item"><?php echo stripslashes($video['video_name']); ?></span><a href="#" title="Edit a Video Item" class="video-edit">Edit a Video Item</a><input type="hidden" name="order[]" value="<?php echo $video['ID']; ?>"/></dt>
									</dl>
									<div class="video-item-settings">
										<label id="added-video-name-label" for="added-video-name-<?php echo $i; ?>" class="video-name-label howto open-label">
											<span>Video Name</span>
											<input type="text" value="<?php echo stripslashes($video['video_name']); ?>" class="directory-name howto" id="added-video-name-<?php echo $i; ?>" name="video[<?php echo $i; ?>][name]">
										</label>
										<?php if (empty($options_['media_id'])) : ?>
											<label id="added-video-embed-label" for="added-embed-<?php echo $i; ?>" class="video-name-label howto open-label">
												<span>Embed</span>
												<textarea id="added-video-embed-<?php echo $i; ?>" class="directory-name howto" name="video[<?php echo $i; ?>][value]" cols="" rows=""><?php echo stripslashes(html_entity_decode($video['embed_value'])); ?></textarea>
											</label>
											<input name="video[<?php echo $i; ?>][width]" type="hidden" value="300" />
											<input name="video[<?php echo $i; ?>][height]" type="hidden" value="200" />
										<?php else : ?>
											<div class="media-attachment">
												<span>Media Attachment ID:</span> <b><a href="<?php echo admin_url() . 'media.php?attachment_id=' . $options_['media_id'] . '&action=edit'; ?>"><?php echo $options_['media_id']; ?></a></b>
												<input name="video[<?php echo $i; ?>][media_id]" type="hidden" value="<?php echo $options_['media_id']; ?>" />
											</div>
											<br class="clear" />
											<div id="video-width-height-label" class="howto">
												<input name="video[<?php echo $i; ?>][width]" class="video-resize" type="text" id="video-width" value="<?php echo $options_['width']; ?>" maxlength="3" />
												<span>x</span>
												<input name="video[<?php echo $i; ?>][height]"  class="video-resize" type="text" id="video-height" value="<?php echo $options_['height']; ?>" maxlength="3" />
												<span>Width x Height</span>
											</div>
										<?php endif; ?>
										<br class="clear" />
										<div id="added-video-duration-label" class="howto"><span>Duration</span>
											<br class="clear" />
											<input name="video[<?php echo $i; ?>][duration][hours]" type="text" class="duration" id="added-video-duration-hour" value="<?php echo str_pad($options_['duration']['hours'], 3, 0, STR_PAD_LEFT); ?>" maxlength="3" /> <span>:</span> <input name="video[<?php echo $i; ?>][duration][minutes]" type="text" class="duration" id="added-video-duration-minute"  value="<?php echo str_pad($options_['duration']['minutes'], 2, 0, STR_PAD_LEFT); ?>" maxlength="2"/> <span>:</span> <input name="video[<?php echo $i; ?>][duration][seconds]" type="text" class="duration" id="added-duration-seconds" value="<?php echo str_pad($options_['duration']['seconds'], 2, 0, STR_PAD_LEFT); ?>" maxlength="2" /> <span>(hours : minutes : seconds)</span></div>
										<br class="clear" />
										<?php if ($products) : ?>
											<h4 class="howto">Chargify Access Settings</h4>
											<p>
												<?php $show_ = $options_; ?>
												<?php foreach ($products as $p) { ?>
													<input <?php if (!empty($show_['options']['chargify']) && in_array($p->getHandle(), $show_['options']['chargify'])) echo 'checked="checked"'; ?> id="added-chargify-option-<?php echo $i . '-' . $p->getHandle(); ?>" name="video[<?php echo $i; ?>][options][chargify][]" type="checkbox" value="<?php echo $p->getHandle(); ?>" /> <label for="added-chargify-option-<?php echo $i . '-' . $p->getHandle(); ?>"><?php echo $p->getName(); ?></label>
												<?php } ?>
											</p>
										<?php endif; ?>
										<h4 class="howto">Role</h4>
										<?php
										foreach ($all_roles as $role_key => $role) {
											if (!empty($options_['options']['roles']) && in_array($role_key, $options_['options']['roles']))
												$checked = 'checked="checked"';
											else
												$checked = '';
											?>
											<input <?php echo $checked; ?> id="added-role-option-<?php echo $i . '-' . $role_key ?>" name="video[<?php echo $i; ?>][options][roles][]" type="checkbox" value="<?php echo $role_key; ?>" />
											<label for="added-role-option-<?php echo $i . '-' . $role_key; ?>"><?php echo $role['name']; ?></label>
										<?php } ?>
										<a href="#" class="video-item-delete video-submitdelete" title="<?php echo $video['ID']; ?>">Remove</a>
										<div class="clear"></div>
									</div>
								</li>
								<?php
								$i++;
							} endif;
						?>
					</ul>
					<?php //endif; ?>
				</div><!-- #added-video-list -->
				<div id="added-video-footer" class="ion-video-bottom-display">
					<div class="video-submit-box">
						<span id="update-msg" class="howto"></span>
						<div class="popup_section">
							<span><b>No access popup : </b></span>
							<select id="select_popups" name="popup_id" class="left">
								<?php
								foreach ($popup_ids as $popup_item) {
									echo '<option value="' . $popup_item['id'] . '" ' . ($popup_id == $popup_item['id'] ? 'selected="selected"' : '') . '>Popup ' . $popup_item['id'] . '</option>';
								}
								?>
							</select>
						</div>
						<div class="publishing-action">
							<input type="submit" value="Update" class="button-primary" id="update_added_videos" name="update_added_videos">
						</div>
						<div class="clear"></div>
					</div><!-- .video-submit-box 2 -->
				</div><!-- #added-video-footer -->
				<div id="for-deletion"></div>
				<input name="key" type="hidden" value="update_added_video_list" />
				<input name="did" type="hidden" value="<?php echo $_GET['did']; ?>" />
			</form>
		</div><!-- #added-video-list-wrapper -->
		<?php
	}

	function external_add() {

		global $filepath, $all_roles, $products;
		?>
		<div id="external-add-wrapper">
			<form id="external-add_form" name="external-link-video" method="post" action="<?php echo $filepath; ?>">
				<?php wp_nonce_field($this->external_vid_nonce); ?>
				<div id="external-add" class="postbox">
					<h3 class="hndle"><span>External Add</span></h3>
					<div class="inside">
						<p class="howto">You can an external link or an embed link.</p>
						<label id="video-name-label" for="video-name" class="howto"><span>Label</span> <input id="video-name" class="howto" name="video[name]" type="text" /></label>
						<br class="clear" />
						<label id="video-embed-label" for="video-embed" class="howto"><span>Embed</span> <textarea id="video-embed" class="howto" name="video[embed]" cols="" rows=""></textarea></label>
						<br class="clear" />
						<div id="video-duration-label" class="howto"><span>Duration</span>
							<br class="clear" />
							<input name="video[duration][seconds]" type="text" class="duration" id="video-duration-seconds" value="00" maxlength="2" /> <span>(hours : minutes : seconds)</span>
							<input name="video[duration][minutes]" type="text" class="duration" id="video-duration-minute"  value="00" maxlength="2"/> <span>:</span>
							<input name="video[duration][hours]" type="text" class="duration" id="video-duration-hour" value="000" maxlength="3" /> <span>:</span>
						</div>
						<br class="clear" />
						<?php if ($products) : ?>
							<h4 class="howto">Chargify Access Settings</h4>
							<p>
								<?php foreach ($products as $p) { ?>
								<input id="chargify-option-ex-<?php echo $p->getHandle(); ?>" name="video[options][chargify][]" type="checkbox" checked="checked" value="<?php echo $p->getHandle(); ?>" /> <label for="chargify-option-ex-<?php echo $p->getHandle(); ?>"><?php echo $p->getName(); ?></label>
								<?php } ?>
							</p>
						<?php endif; ?>
						<h4 class="howto">Role</h4>
						<p>
							<?php foreach ($all_roles as $role_key => $role) { ?>
								<input id="role-option-ex-<?php echo $role_key; ?>" name="video[options][roles][]" type="checkbox" value="<?php echo $role_key; ?>" /> <label for="role-option-ex-<?php echo $role_key; ?>"><?php echo $role['name']; ?></label>
							<?php } ?>
						</p>
						<p class="button-controls">
							<span class="add-to-video-list">
								<input type="submit" id="add-external-link" name="add-external-link" value="Add Video" class="button-secondary submit-add-to-menu">
							</span>
						</p>
						<br class="clear" />
					</div>
				</div>
				<input name="key" type="hidden" value="add_external_video_link" />
				<input name="did" type="hidden" value="<?php echo $_GET['did']; ?>" />
			</form><!-- #external-add_form -->
		</div><!-- #external-add-wrapper -->
		<?php
	}

	function popup_content_setting() {
		global $wpdb;

		$default_settings = get_option('v_popup_default_settings_');
		$popup_settings = $this->get_popup_options();
		$popup_ids = $this->get_popup_ids();
		$get_roles = $wpdb->prefix . 'user_roles';
		$all_roles = get_option($get_roles);
		?>
		<div id="popup-setting-wrapper">
			<div id="hidden-default-settings">
				<textarea id="popup-default-content"><?php echo $default_settings['content']; ?></textarea>
				<textarea id="popup-default-signup-content"><?php echo $default_settings['signup_content']; ?></textarea>
				<textarea id="popup-default-email-content"><?php echo $default_settings['email_content']; ?></textarea>
			</div>
			<?php wp_nonce_field($this->popup_setting_nonce); ?>
			<div id="popup-setting-inside" class="postbox">
				<h3 class="hndle"><span>No Access Popup</span></h3>
				<div class="inside">
					<p class="howto">Bellow you can configure the pop-up visitors see when they don't have access to video content(HTML is allowed).</p>
					<div id="popup_add_section">
						<select id="select_popups" name="popup[popup_id]">
							<?php
							foreach ($popup_ids as $popup_item) {
								echo '<option value="' . $popup_item['id'] . '" ' . ($popup_settings['popup_id'] == $popup_item['id'] ? 'selected="selected"' : '') . '>Popup ' . $popup_item['id'] . '</option>';
							}
							?>
						</select>
						<input type="submit" id="add_new_popup" name="popup[submit]" value="Add new popup" class="button-primary submit-add-to-menu" />
						<input type="submit" id="delete_popup" name="popup[submit]" value="Delete popup" class="button-primary submit-add-to-menu" <?php if (count($popup_ids) <= 1) echo 'disabled="disabled'; ?> />
						<input type="hidden" id="switch_popup" name="popup[switch]" value="off" />
					</div>
					<div id="popup-content-div">
						<label id="popup-content-label" for="popup-content">
							<span>Popup Content</span>
							<textarea id="popup-content" class="howto" name="popup[content]" cols="" rows=""><?php echo stripcslashes($popup_settings['content']); ?></textarea>
						</label>
						<a id="popup-content-preview" href="#popuppreview">Preview</a>
					</div>
					<br class="clear" />
					<div id="popup-action-div">
						<span>Call to action</span>
						<br class="clear" />
						<select id="popup-action" name="popup[action]">
							<option value="none">None</option>
							<option value="sign" <?php echo ($popup_settings['action'] == 'sign' ? 'selected="selected"' : ''); ?>>Sign up Button</option>
							<option value="email" <?php echo ($popup_settings['action'] == 'email' ? 'selected="selected"' : ''); ?>>Email Field</option>
						</select>
					</div>
					<div id="popup-button-setting" <?php if ($popup_settings['action'] != 'none') echo 'style="display:block;"'; ?>>
						<div id="popup-button-setting-select">
							<span>Button Color</span>
							<br class="clear" />
							<select id="popup-button-color" name="popup[button_color]">
								<option value="blue">Blue</option>
								<option value="green"  <?php echo ($popup_settings['button_color'] == 'green' ? 'selected="selected"' : ''); ?>>Green</option>
								<option value="red"  <?php echo ($popup_settings['button_color'] == 'red' ? 'selected="selected"' : ''); ?>>Red</option>
								<option value="magenta"  <?php echo ($popup_settings['button_color'] == 'magenta' ? 'selected="selected"' : ''); ?>>Magenta</option>
								<option value="yellow"  <?php echo ($popup_settings['button_color'] == 'yellow' ? 'selected="selected"' : ''); ?>>Yellow</option>
								<option value="orange"  <?php echo ($popup_settings['button_color'] == 'orange' ? 'selected="selected"' : ''); ?>>Orange</option>
							</select>
						</div>
						<div id="popup-button-setting-input">
							<span>Button Text</span>
							<br class="clear" />
							<input type="text" name="popup[button_text]" id="popup-button-text" value="<?php echo $popup_settings['button_text']; ?>" />
						</div>
					</div>
					<br class="clear" />
					<div id="popup-user-roles" <?php if ($popup_settings['action'] == 'email') echo 'style="display:block;"'; ?>>
						<span>User Type : </span>
						<br class="clear" />
						<select id="popup-user-type" name="popup[user_type]">
							<?php foreach ($all_roles as $role_key => $role) { ?>
								<option value="<?php echo $role_key; ?>" <?php if ($role_key == $popup_settings['user_type']) echo 'selected="selected"'; ?>><?php echo $role['name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="button-controls">
						<input type="submit" id="popup-setting" name="popup[submit]" value="Save Changes" class="button-primary submit-add-to-menu">
						<a href="javascript:void(0);" id="popup-setting-restore">Restore default</a>
					</div>
					<br class="clear" />
				</div>
			</div>
			<input type="hidden" name="key" value="popup_setting" />
		</div><!-- #popup-setting-wrapper -->
		<div class="clear"></div>
		<div id="popuppreview" class="ion-css"></div>
		<?php
	}

	function sign_url_setting() {
		$popup_settings = $this->get_popup_options();
		?>
		<div id="sign-setting-wrapper">
			<div id="sign-setting-inside" class="postbox">
				<h3 class="hndle"><span>Settings</span></h3>
				<div class="inside">
					<div id="sign-content-div">
						<label class="label-list" for="sing-up">
							<span>Sign up URL</span>
							<input type="text" id="sign-up" name="popup[sign_up]" value="<?php echo $popup_settings['sign_up']; ?>" />
						</label>
						<label class="label-list" for="sing-in">
							<span>Sign in URL</span>
							<input type="text" id="sign-in" name="popup[sign_in]" value="<?php echo $popup_settings['sign_in']; ?>" />
						</label>
						<label class="label-check-list" for="add-google-analytics">
							<input type="checkbox" id="add-google-analytics" name="popup[add_google_analytics]" <?php echo $popup_settings['add_google_analytics'] == 'on' ? 'checked' : ''; ?> />
							<span>Track video plays with google analytics</span>
						</label>
					</div>
					<div class="button-controls">
						<input type="submit" id="popup-setting" name="popup[submit]" value="Save Changes" class="button-primary submit-add-to-menu">
					</div>
					<br class="clear" />
				</div>
			</div>
		</div><!-- #popup-setting-wrapper -->
		<?php
	}

	function member_tab_setting() {
		$tab_settings = get_option('member_tab_settings');
		?>
		<div id="member-setting-wrapper">
			<div id="member-setting-inside" class="postbox">
				<h3 class="hndle"><span>Member-only Tab Settings:</span></h3>
				<div class="inside">
					<div id="member-content-div">
						<label class="label-list" for="member_tab_title">
							<span>Title : </span>
							<input type="text" id="member_tab_title" name="popup[member_tab_title]" value="<?php echo $tab_settings['title']; ?>" />
						</label>
						<label class="label-list" id="lbl_member_tab_text">
							<span>Text :</span>
							<textarea id="member_tab_text" name="popup[member_tab_text]"><?php echo $tab_settings['text']; ?></textarea>
						</label>
					</div>
					<div class="button-controls">
						<input type="submit" id="popup-setting" name="popup[submit]" value="Save Changes" class="button-primary submit-add-to-menu">
					</div>
					<br class="clear" />
				</div>
			</div>
		</div><!-- #popup-setting-wrapper -->
		<?php
	}

	function get_popup_options() {
		global $wpdb;

		$default_settings = get_option('v_popup_default_settings_');
		if (isset($_POST['popup']) && $_POST['popup']['submit'] == 'Save Changes') {
			$post_data = $_POST['popup'];
		} else {
			if ($_POST['popup']['switch'] == 'on') {
				$popup_id = $_POST['popup']['popup_id'];
			} else {
				$popup_id = $default_settings['popup_id'];
			}
			$sql = 'SELECT * FROM ' . $wpdb->prefix . 'video_popup WHERE id="' . $popup_id . '"';
			$post_data = $wpdb->get_results($sql, ARRAY_A);
			$post_data = unserialize($post_data[0]['popup_options']);
			$post_data['popup_id'] = $popup_id;
		}
		return array_merge($default_settings, $post_data);
	}

	function get_popup_ids() {
		global $wpdb;

		$sql = 'SELECT id FROM ' . $wpdb->prefix . 'video_popup';
		$post_data = $wpdb->get_results($sql, ARRAY_A);

		return $post_data;
	}

	static function get_default_popup_id() {
		global $wpdb;

		$sql = 'SELECT id FROM ' . $wpdb->prefix . 'video_popup ORDER BY id';
		$post_data = $wpdb->get_results($sql, ARRAY_A);
		if (count($post_data) > 0) {
			return $post_data[0]['id'];
		} else {
			return 0;
		}
	}

	static function get_sign_up_url() {
		global $wpdb;

		$popup_id = self::get_default_popup_id();
		if ($popup_id == 0) {
			$popup_settings = get_option('v_popup_default_settings_');
		} else {
			$sql = 'SELECT * FROM ' . $wpdb->prefix . 'video_popup WHERE id="' . $popup_id . '"';
			$popup_settings = $wpdb->get_results($sql, ARRAY_A);
			$popup_settings = unserialize($popup_settings[0]['popup_options']);
		}

		return $popup_settings['sign_up'];
	}

}
?>