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
						<h2>Membership Cancellation</h2>
						<p style="margin-bottom:20px">We're sorry to see you go! If you are sure you want to cancel your membership, please take 3 seconds to let us know how we can improve. It would really mean a lot.</p>
						<?php echo do_shortcode('[gravityform id="6" name="Cancellation Form" title="false" description="false"]' ) ?>

						<p class="cancelrawr">
							|
							<a class="nolink btn btn-info" href="<?php echo pmpro_url("account") ?>">Wait! Keep my account</a>
						</p>
					<?php } else { ?>
						<p>Click here to <a href="<?php echo get_home_url() ?>">go to the home page</a>.</p>
					<?php } ?>
				</div>
			</div><!-- end content -->
			
			
	
			<script type="text/javascript">
			function trackcancel(){
			mixpanel.identify('<?php global $current_user; get_currentuserinfo(); echo $current_user->ID; ?>');
			mixpanel.people.set({
			    "membership_status": "CANCELLED", 
				"cancellation_date": "<?php echo date('Y-m-dTH:i:s'); ?>",	
			});
			mixpanel.track('Cancelled');
	
						return confirm('This will perminantly delete your account and immediately cancel billing. Are you sure you want to proceed?');
			};
			</script>
			
			
		
<?php get_footer(); ?>