<?php

/**
 * Class template to display the video directory
 * @since 1.0.0
 */
class IonVideoDirectoryDisplay {

	function show_directory($attr) {
		global $wpdb;

		$table1 = get_option('v_listings_');
		$table2 = get_option('v_directory_');

		$v_id = $attr['id'];
		$duration = $attr['duration'] == 'on' ? true : false;
		?>
		<div class="wrap">
			<?php
			$v_id = explode(',', $v_id);
			foreach ($v_id as $v) {
				$show_ = $wpdb->get_results("SELECT directory_name, video_order, video_count, popup_id FROM $table2 WHERE id IN (" . $v . ")");
				foreach ($show_ as $chapter) {
					if ($chapter->video_count > 0) :
						$c_order = unserialize($chapter->video_order);
						$j_order = $c_order;
						$c = implode(',', $c_order);
						$popup_id = (int) $chapter->popup_id;

						if ($popup_id == 0) {
							$popup_id = IonVideoPageTemplates::get_default_popup_id();
						}
						if ($popup_id == 0) {
							$popup_settings = get_option('v_popup_default_settings_');
						} else {
							$sql = 'SELECT * FROM ' . $wpdb->prefix . 'video_popup WHERE id="' . $popup_id . '"';
							$popup_settings = $wpdb->get_results($sql, ARRAY_A);
							$popup_settings = unserialize($popup_settings[0]['popup_options']);
						}

						$google_analytic = $popup_settings['add_google_analytics'];
						$user_type = $popup_settings['user_type'];

						$show_options = $wpdb->get_results("SELECT * FROM $table1 WHERE id IN (" . $c . ")", ARRAY_A);
						/**
						 * Calculate the total duration for one directory
						 * @since 1.0.0
						 * @source http://stackoverflow.com/questions/3172332/convert-seconds-to-hourminutesecond#answer-3172368
						 */
						$total_duration = '';

						foreach ($show_options as $x) {
							$show = unserialize($x['options']);
							$show = $show[0];
							$total_duration += floor($show['duration']['hours'] * 3600) + floor($show['duration']['minutes'] * 60) + floor($show['duration']['seconds']);
						}

						$hours = floor($total_duration / 3600);
						$minutes = floor(($total_duration / 60) % 60);
						$seconds = $total_duration % 60;
						/**
						 * Ends here.
						 */
						?>
						<div class="postbox">
							<h3 class="directory-name">
								<span class="icon-before"></span>
								<span class="directorynamefix"><?php echo stripslashes($chapter->directory_name); ?></span>
								<?php if ($duration) : ?>
									<span style="float:right;"><?php echo $hours . 'hr ' . str_pad($minutes, 2, 0, STR_PAD_LEFT) . 'm ' . str_pad($seconds, 2, 0, STR_PAD_LEFT) . 's'; ?></span>
								<?php endif; ?>
							</h3>
							<div class="inside">
								<ul class="video-list">
									<?php
									if ($chapter->video_count > 0) :
										foreach ($show_options as $j) {
											$list = $wpdb->get_results("SELECT * FROM $table1 WHERE ID = '$j_order[0]'", ARRAY_A);
											array_shift($j_order);
											$list = $list[0];
											?>
											<li class="<?php if ($duration) echo 'show_duration'; ?>">
												<?php
												global $ion_auth_users;
												$o = unserialize($list['options']);

												$o = $o[0];
												if ($ion_auth_users->all($o['options'])) {
													if ($google_analytic == 'on') {
														?>
														<a onclick="trackpersonclick()" class="<?php echo (!empty($o['media_id']) ? 'show_jw_player' : 'show_iframe'); ?>" href="#<?php echo sanitize_title($list['video_name']); ?>-<?php echo $list['ID']; ?><?php echo (!empty($o['media_id']) ? '_wrapper' : ''); ?>" title="<?php echo stripslashes($list['video_name']); ?>"><?php echo stripslashes($list['video_name']); ?></a><?php if ($duration) : ?><span class="video-duration"><?php if ($o['duration']['hours'] !== '000') : echo abs($o['duration']['hours']); ?>hr<?php endif; ?> <?php echo $o['duration']['minutes']; ?>m <?php echo $o['duration']['seconds']; ?>s</span><?php endif; ?>
														<?php
													} else {
														?>
														<a class="<?php echo (!empty($o['media_id']) ? 'show_jw_player' : 'show_iframe'); ?>" href="#<?php echo sanitize_title($list['video_name']); ?>-<?php echo $list['ID']; ?><?php echo (!empty($o['media_id']) ? '_wrapper' : ''); ?>" title="<?php echo stripslashes($list['video_name']); ?>"><?php echo stripslashes($list['video_name']); ?></a><?php if ($duration) : ?><span class="video-duration"><?php if ($o['duration']['hours'] !== '000') : echo abs($o['duration']['hours']); ?>hr<?php endif; ?> <?php echo $o['duration']['minutes']; ?>m <?php echo $o['duration']['seconds']; ?>s</span><?php endif; ?>
														<?php
													}
												} else {
													if ($google_analytic == 'on') {
														?>
														<a title="<?php echo stripslashes($list['video_name']); ?>" class="login-pop noaccess" onclick="trackclicktry()" href="#-<?php echo $popup_id; ?>"><?php echo $list['video_name']; ?></a><?php if ($duration) : ?><span class="video-duration"><?php if ($o['duration']['hours'] !== '000') : echo abs($list['duration']['hours']); ?>hr<?php endif; ?> <?php echo $o['duration']['minutes']; ?>m <?php echo $o['duration']['seconds']; ?>s</span><?php endif; ?>
														<?php
													} else {
														?>
														<a class="login-pop noaccess" title="<?php echo stripslashes($list['video_name']); ?>" href="#-<?php echo $popup_id; ?>"><?php echo $list['video_name']; ?></a><?php if ($duration) : ?><span class="video-duration"><?php if ($o['duration']['hours'] !== '000') : echo abs($list['duration']['hours']); ?>hr<?php endif; ?> <?php echo $o['duration']['minutes']; ?>m <?php echo $o['duration']['seconds']; ?>s</span><?php endif; ?>
														<?php
													}
												}
												?>
											</li>
											<?php
										}
									endif;
									?>
								</ul><!-- .video-list -->
								
										<script type="text/javascript">
										function trackpersonclick(){
											<?php if (is_user_logged_in()) { ?>
										mixpanel.identify('<?php global $current_user; get_currentuserinfo(); echo $current_user->ID; ?>'); 											<?php } ?>
														mixpanel.people.increment({
														    "Videos Watched": 1,
														    "<?php the_title(); ?> - Videos Watched": 1,
														});
														
														
										};
										
										function trackclicktry(){
											<?php if (is_user_logged_in()) { ?>
											
										mixpanel.identify('<?php global $current_user; get_currentuserinfo(); echo $current_user->ID; ?>');
										<?php } ?>
														mixpanel.people.increment({
														    "Tried to watch <?php the_title(); ?> video": 1,
														});
					
										};
										
										
										
										var pagetitle = '<?php the_title(); ?>';
									</script>
								
								<div class="iframe">
									<?php
									if ($chapter->video_count > 0) :
										foreach ($show_options as $list) {
											$o = unserialize($list['options']);
											$o = $o[0];
											?>
											<div id="<?php echo sanitize_title($list['video_name']); ?>-<?php echo $list['ID']; ?>" class="ion-css">
												<?php
												if (empty($o['media_id'])) :
													echo stripslashes(html_entity_decode(( $list['embed_value'])));
												else :
													$video = wp_get_attachment_url($o['media_id']);
													?>
													<script type="text/javascript">
														jwplayer("<?php echo sanitize_title($list['video_name']); ?>-<?php echo $list['ID']; ?>").setup({
															flashplayer: "<?php echo $GLOBALS['url_path'] . '/display/mediaplayer-5.8/'; ?>player.swf",
															width: <?php echo $o['width']; ?>,
															height: <?php echo $o['height']; ?>,
															file: "<?php echo $video; ?>"
														});
													</script>
												<?php
												endif;
												?>
											</div>
											<?php
										}
									endif;
									?>
								</div>
							</div><!-- .inside -->
						</div><!-- .postbox -->
						<div id="no-access-content-<?php echo $popup_id; ?>" class="hide">
							<?php echo $this->get_popup_content($popup_id); ?>
						</div>
						<input id="user_type_<?php echo $popup_id; ?>" type="hidden" value="<?php echo $user_type; ?>" />
						<?php
					else :
						?>
						<div class="postbox">
							<h3 class="directory-name"><span><?php echo stripslashes($chapter->directory_name); ?></span></h3>
							<div class="inside">
								<ul class="video-list">
									<li>No videos available.</li>
								</ul>
							</div>
						</div><!-- empty .postbox -->
					<?php
					endif;
				}
			}
			?>
		</div><!-- .wrap -->
		<input type="hidden" id="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>"/>
		<input type="hidden" id="user-type" value="<?php echo $popup_settings['user-type']; ?>" />
		<?php
	}

	function container_tabs($list_header = array(), $content_list = array()) {
		$i = 0;
		$j = 0;
		?>
		<div class="ion_tabs">
			<ul>
				<?php foreach ($list_header as $list) { ?>
					<li><a href="#tabs-<?php echo $i++; ?>"><?php echo $list; ?></a></li>
				<?php } ?>
			</ul>
			<?php foreach ($content_list as $container) { ?>
				<div id="tabs-<?php echo $j++; ?>"><?php echo do_shortcode($container); ?><br class="clear" /></div>
			<?php } ?>
		</div>
		<?php
	}

	function container_tabs_($list_header = array(), $content_list = '') {
		?>
		<div class="ion_tabs">
			<ul>
				<?php foreach ($list_header as $id => $name) { ?>
					<li><a href="#<?php echo $id; ?>"><?php echo $name; ?></a></li>
				<?php } ?>
			</ul>
			<?php
			echo do_shortcode($content_list);
			?>
		</div>
		<?php
	}

	function tabs($d = array(), $content = '') {
		if ($d['require']) {
			if (!is_user_logged_in()) {
				$tab_settings = get_option('member_tab_settings');
//				$content = $d['require'];
				?>
				<div id="<?php echo $d['id']; ?>">
					<p class="noaccesstab"><?php echo $tab_settings['title']; ?></p>
					<p><?php echo $tab_settings['text']; ?></p>
					<br class="clear" />
				</div>
			<?php } else { ?>
				<div id="<?php echo $d['id']; ?>"><?php echo do_shortcode($content); ?><br class="clear" /></div>
			<?php }
		} else {
			?>
			<div id="<?php echo $d['id']; ?>"><?php echo do_shortcode($content); ?><br class="clear" /></div>
			<?php
		}
	}

	function video_subscribebar($attr) {
		$sign_up = IonVideoPageTemplates::get_sign_up_url();
		if (!is_user_logged_in()) {
			?>
			<div id="buttonNoLgin">
				<p><?php echo $attr['text']; ?></p><a href="<?php echo $sign_up; ?>" class="videosignupbtn btnposition btn btn-danger" title="subscribe"><?php echo $attr['btntext']; ?></a>
			</div>
			<?php
		}
		 if(pmpro_hasMembershipLevel('1')) 
								{ ?>  
									<div id="buttonNoLgin">
										<p>Access our entire training library.</p><a href="<?php echo $sign_up; ?>" class="btnposition btn btn-danger video	" title="subscribe">Upgrade Now</a>
									</div>
									<?php }
									
	}

	function video_email($attr) {
		if (!is_user_logged_in()) {
			?>
			<div id="buttonNoLgin">
				<input type="hidden" id="user_type" name="user_type" value="<?php echo $attr['usertype']; ?>" />
				<p><?php echo $attr['text']; ?></p>
				<input type="text" name="user_email" id="user_email" />
				<a href="javascript:void(0);" class="subscribevids emailvids" title="subscribe"><?php echo $attr['btntext']; ?></a>
			</div>
			<?php
		}
	}

	static function get_popup_content($popup_id = '0') {
		global $wpdb;

		$popup_id = (int) $popup_id;
		if ($popup_id == 0) {
			$popup_settings = get_option('v_popup_default_settings_');
		} else {
			$sql = 'SELECT * FROM ' . $wpdb->prefix . 'video_popup WHERE id="' . $popup_id . '"';
			$popup_settings = $wpdb->get_results($sql, ARRAY_A);
			$popup_settings = unserialize($popup_settings[0]['popup_options']);
		}


		$popup_content = $popup_settings['content'];
		$popup_content = str_replace('[color]', $popup_settings['button_color'], $popup_content);
		$popup_content = str_replace('[button text]', $popup_settings['button_text'], $popup_content);
		$popup_content = str_replace('[signin url]', $popup_settings['sign_in'], $popup_content);
		$popup_content = str_replace('[signup url]', $popup_settings['sign_up'], $popup_content);

		return $popup_content;
	}

}

//$ion_video_directory_display = &new IonVideoDirectoryDisplay();
?>