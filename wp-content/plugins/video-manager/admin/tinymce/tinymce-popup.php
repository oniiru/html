<?php
require_once('../../../../../wp-load.php');

if (!is_user_logged_in() || !current_user_can('edit_posts'))
	die('You are not allowed to call this page directly.');

global $wpdb;

$get_roles = $wpdb->prefix . 'user_roles';
$all_roles = get_option($get_roles);

$v_directory = get_option('v_directory_');
$directories = $wpdb->get_results("SELECT * FROM " . $v_directory . " ORDER BY id ASC");
$upload_dir = wp_upload_dir();

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Video Directory</title>
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
		<script language="javascript" type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/jquery/jquery.js"></script>
		<script language="javascript" type="text/javascript">
			var ajax_url = '<?php echo plugins_url('', __FILE__); ?>';
			var upload_url = '<?php echo $upload_dir['url']; ?>';
		</script>
		<script language="javascript" type="text/javascript" src="<?php echo plugins_url('/js/video_tinymce.js', __FILE__); ?>"></script>
		<link href='<?php echo plugins_url('/css/video_tinymce.css', __FILE__); ?>' rel='stylesheet' type='text/css'>
			<base target="_self" />
	</head>
	<body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');">
		<form name="video-directory-picker" action="#">
			<div id="directory_video_container">
				<div id="tab_buttons_controller">
					<div id="use_tab" class="section">
						<h4>Use Tabs?</h4>
						<select id="sel_use_tab">
							<option value="no">No</option>
							<option value="yes">Yes</option>
						</select>
					</div>
					<div id="popup_action_div" class="section">
						<h4>Add subscription bar?</h4>
						<select id="popup_action" onchange="show_subscription(this);">
							<option value="none">None</option>
							<option value="subscribebar">Sign up Button</option>
							<option value="email">Email Field</option>
						</select>
					</div>
					<div id="button_setting">
						<div id="button_setting_note">
							<label class="note">Text(recommended max: 75 characters : </label><br class="clear"/>
							<input type="text" id="subscript_txt" name="subscript_txt" value="Get immediate access to the entire library!"/>
						</div>
						<div id="button_setting_select">
							<label>Button Color</label><br class="clear" />
							<select id="button_color" name="button_color">
								<option value="blue">Blue</option>
								<option value="green">Green</option>
								<option value="red">Red</option>
								<option value="magenta">Magenta</option>
								<option value="yellow">Yellow</option>
								<option value="orange">Orange</option>
							</select>
						</div>
						<div id="button_setting_input">
							<label>Button Text</label><br class="clear"/>
							<input type="text" name="button_text" id="button_text" value="Become a Member »" />
						</div>
						<div id="user_roles">
							<label>User Type</label><br class="clear" />
							<select id="user_type" name="user_type">
								<?php foreach ($all_roles as $role_key => $role) { ?>
									<option value="<?php echo $role_key; ?>" <?php if ($role_key == $popup_settings['user_type']) echo 'selected="selected"'; ?>><?php echo $role['name']; ?></option>
								<?php } ?>	
							</select>
						</div>
					</div>
					<div class="clear"></div>
				</div> <!-- End buttons tab controller -->
				<div id="no_use_tab_container">
					<hr class="clear"/>
					<div class="video_directory">
						<h4>Pick video directory</h4>
						<label class="note">Note: you can pick multiple directory. (Ctrl/Shift + select an item)</label>
						<?php
						if ($directories) :
							?>
							<table>
								<tr>
									<td>
										<select size="10" multiple="multiple" class="video_directory_list">
											<?php foreach ($directories as $list) { ?>
												<option value="<?php echo $list->id; ?>"><?php echo $list->directory_name ?></option>
											<?php } ?>
										</select>
									</td>
									<td>
										<input class="add_dir mceButton" type="button" value="Add >>" onclick="add_directory(this);" />
										<input class="remove_dir mceButton" type="button" value="<< Remove" onclick="remove_directory(this);" />
									</td>
									<td>
										<select class="added_video_directory_list" name="added-video-directory-list[]" size="10" multiple="multiple"></select>
									</td>
									<td>
										<input type="button" class="up_item mceButton" value="Up" onclick="up_item(this);" />
										<input type="button" class="down_item mceButton" value="Down" onclick="down_item(this);" />
									</td>
								</tr>
							</table>
						<?php endif; ?>
						<div class="show_duration">
							<h4>Show video durations?
								<input type="checkbox" id="chk_show_duration_default" class="chk_show_duration" name="chk_show_duration" />
								<label id="lbl_show_duration" for="chk_show_duration_default">Yes</label>
							</h4>
						</div>
					</div>
					<div style="float:right">
						<input class="insert_select mceButton" value="Insert" type="button" onclick="insert_select(this);" />
					</div>	
					<div class="clear"></div>
					<!--					<div style="float:left">
											<input class="mceButton" value="Cancel" type="button" onClick="tinyMCEPopup.close();"/>
										</div>
										<br clear="all" />	
										<hr class="clear"/>
										<div id="miscellaneous">
											<h4>Miscellaneous</h4>
											<div>&nbsp;</div>
											<div style="float:left; margin: 0 3px;">
												<input class="mceButton" value="Create Tabs" type="button" onClick="insertTabs();"/>
											</div>
											<br clear="all" />
										</div> Miscellaneous functions -->
				</div><!-- No use tabs -->
				<div id="use_tab_wrapper">
					<div id="use_tab_container"></div>
					<div id="use_tab_bottom">
						<hr/>
						<span class="add_tab">Add Tab</span>
						<h4>Posting will convert this data to short codes, and will only be editable in that format. Make sure to double check your work.</h4>
						<input type="button" class="add_directory mceButton" value="Add Directory" />
					</div>
					<div class="clear"></div>
				</div>
				<div id="tmp_use_tab_controller">
					<h4 classs="tab_title"></h4>
					<label>Title : </label><br/>
					<input type="text" class="txt_tab_title title" /><br/>
					<label>Add element : </label><br/>
					<select class='add_element'>
						<option value="video_directory">Video Directory</option>
						<option value="file_download">File Download</option>
						<option value="description">Description</option>
					</select>
				</div>
				<div id="tmp_use_tab_content">
					<div id="video_directory">
						<h4>Pick video directory</h4>
						<label class="note">Note: you can pick multiple directory. (Ctrl/Shift + select an item)</label>
						<?php
						if ($directories) :
							?>
							<table>
								<tr>
									<td>
										<select size="10" multiple="multiple" class="video_directory_list">
											<?php foreach ($directories as $list) { ?>
												<option value="<?php echo $list->id; ?>"><?php echo $list->directory_name ?></option>
											<?php } ?>
										</select>
									</td>
									<td>
										<input class="add_dir mceButton" type="button" value="Add >>" onclick="add_directory(this);" />
										<input class="remove_dir mceButton" type="button" value="<< Remove" onclick="remove_directory(this);" />
									</td>
									<td>
										<select class="added_video_directory_list" name="added-video-directory-list[]" size="10" multiple="multiple"></select>
									</td>
									<td>
										<input type="button" class="up_item mceButton" value="Up" onclick="up_item(this);" />
										<input type="button" class="down_item mceButton" value="Down" onclick="down_item(this);" />
									</td>
								</tr>
							</table>
						<?php endif; ?>
						<div class="show_duration">
							<h4>Show video durations?
								<input type="checkbox" id="chk_show_duration_default" class="chk_show_duration" name="chk_show_duration" />
								<label id="lbl_show_duration" for="chk_show_duration_default">Yes</label>
							</h4>
						</div>
					</div>
					<div id="file_download">
						<input type="checkbox" class="chk_allow_member" name="chk_allow_member" />
						<label class="lbl_allow_member">Allow only members to access tab</label><br/>
						<label>File Download Text:</label><br/>
						<textarea class="txtra_download_text"></textarea><br/>
						<label>Download Text</label>
						<input type="text" class="txt_download_title title" /><br/>
						<label>Download URL:</label>
						<input type="text" class="txt_download_url title" />
						<form method="post" enctype="multipart/form-data" class="frm_file_upload"  action="<?php echo plugins_url('/upload.php', __FILE__); ?>">
							<input type="file" name="images" class="file_names" />
							<button type="button" id="btn" onclick="file_upload(this);">Upload Files!</button>
						</form>
						<div class="response"></div>
					</div>
					<div id="description">
						<input type="checkbox" class="chk_allow_member" name="chk_allow_member" />
						<label class="lbl_allow_member">Allow only members to access tab</label><br/>
						<label>Description:</label><br/>
						<textarea class="txtra_description_text"></textarea><br/>
					</div>	
				</div>
			</div><!-- Video Directory Container -->
		</form>
	</body>
</html>