<?php
/* Template Name: cancel */
?>

<?php get_header(); ?>
			<div id="content" class="content_full_width">
				<div id="cancelmembership">
					<?php
					global $pmpro_msg, $pmpro_msgt, $pmpro_confirm;

					if ($pmpro_msg) {
						?>
						<div class="pmpro_message <?php echo $pmpro_msgt ?>"><?php echo $pmpro_msg ?></div>
						<?php
					}
					?>

					<?php if (!$pmpro_confirm) { ?>

						<p>Are you sure you want to cancel your membership?</p>

						<p>
							<a class="yeslink btn btn-danger" onCLick="return confirm('This will perminantly delete your account and immediately cancel billing. Are you sure you want to proceed?')" href="<?php echo pmpro_url("cancel", "?confirm=true") ?>">Yes, cancel my account</a>
							|
							<a class="nolink btn btn-info" href="<?php echo pmpro_url("account") ?>">No, keep my account</a>
						</p>
					<?php } else { ?>
						<p>Click here to <a href="<?php echo get_home_url() ?>">go to the home page</a>.</p>
					<?php } ?>
				</div>
			</div><!-- end content -->
<?php get_footer(); ?>