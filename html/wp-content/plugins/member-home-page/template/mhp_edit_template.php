<?php
/**
 * Holds admin edit template class
 * @author Anton Matiyenko (amatiyenko@gmail.com)
 */

/**
 * Include base class
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mhp_template.php');

/**
 * Edit page class
 */
class MHP_Edit_Template extends MHP_Template {

	/**
	 * Displays settings editing form
	 * @param array $data
	 */
	static function display($data) {
		?>
		<div class="wrap">
			<div id="icon-edit" class="icon32"><br /></div>
			<h2><?php echo __('Edit Member Homepage settings', 'member_home_page'); ?></h2>
			<form action="" method="post" enctype="multipart/form-data">
				<div id="post-body">
					<div id="post-body-content" class="has-sidebar-content">
						<div id="titlediv">
							<div id="titlewrap">
								<label for="mhp_day_of_the_week"><?php echo __('"Office Hours" this week:', 'member_home_page') ?></label>
								<select name="mhp_day_of_the_week">
									<?php echo parent::options($data['mhp_day_of_the_week'], $data['days']) ?>
								</select>
								<select name="mhp_time_starts">
									<?php echo parent::options($data['mhp_time_starts'], $data['hours']) ?>
								</select>
								<select name="mhp_time_ends">
									<?php echo parent::options($data['mhp_time_ends'], $data['hours']) ?>
								</select>
								<?php echo parent::errorMessage('time', $data); ?>
							</div>
							<br class="clear" />
							<div id="titlewrap">
								<label for="mhp_signup_url"><?php echo __('Sign Up URL:', 'member_home_page') ?></label>
								<input type="text" name="mhp_signup_url" style="width: 348px;" value="<?php echo $data['mhp_signup_url'] ?>"/>
								<?php echo parent::errorMessage('url', $data); ?>
							</div>
							<br class="clear" />
							<div id="titlewrap">
								<label for="mhp_promo_page_1_id"><?php echo __('Promo Page No.1', 'member_home_page') ?></label>
								<select name="mhp_promo_page_1_id" style="width: 348px;">
									<?php echo parent::options($data['mhp_promo_page_1_id'], $data['pages'], 'keys') ?>
								</select>
								<?php echo parent::errorMessage('url', $data); ?>
							</div>
							<br class="clear" />
							<div id="titlewrap">
								<label for="mhp_promo_page_2_id"><?php echo __('Promo Page No.2', 'member_home_page') ?></label>
								<select name="mhp_promo_page_2_id" style="width: 348px;">
									<?php echo parent::options($data['mhp_promo_page_2_id'], $data['pages'], 'keys') ?>
								</select>
								<?php echo parent::errorMessage('url', $data); ?>
							</div>
							<br class="clear" />
							<input type="submit" value="<?php echo __('Save Settings', 'member_home_page'); ?>" />
						</div>
						<br class="clear" />
					</div>
				</div>
				<br class="clear" />
			</form>
		</div>
		<br class="clear" />
		<?php
	}
}
?>
