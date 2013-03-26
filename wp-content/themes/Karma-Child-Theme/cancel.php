<?php
/* Template Name: cancel */
?>

<?php get_header(); ?>
</div><!-- header-area --></div><!-- end rays --></div><!-- end header-holder --></div><!-- end header -->
<?php
truethemes_before_main_hook();
// action hook, see truethemes_framework/global/hooks.php
?>

<div id="main">
	<div class="main-area" style="padding-top: 0px !important;">	<div class="main-holder">
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
							<a class="yeslink" onCLick="return confirm('This will perminantly delete your account and immediately cancel billing. Are you sure you want to proceed?')" href="<?php echo pmpro_url("cancel", "?confirm=true") ?>">Yes, cancel my account</a>
							|
							<a class="nolink" href="<?php echo pmpro_url("account") ?>">No, keep my account</a>
						</p>
					<?php } else { ?>
						<p>Click here to <a href="<?php echo get_home_url() ?>">go to the home page</a>.</p>
					<?php } ?>
				</div>
			</div><!-- end content -->
		</div><!-- end main-holder -->
	</div><!-- main-area --><?php get_footer(); ?>