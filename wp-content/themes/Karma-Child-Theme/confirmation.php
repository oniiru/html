<?php
/* Template Name: confirmation */
?>

<?php get_header(); ?>
</div><!-- header-area --></div><!-- end rays --></div><!-- end header-holder --></div><!-- end header -->
<?php
truethemes_before_main_hook();
// action hook, see truethemes_framework/global/hooks.php
?>

<div id="main">
    <div class="main-area" style="padding-top: 0px !important;">
        <div class="main-holder">
            <div id="content" class="content_full_width confirmationh9">
                   <?php
global $wpdb, $current_user, $pmpro_invoice, $pmpro_msg, $pmpro_msgt, $pmpro_currency_symbol;

if ($pmpro_msg) {
	?>
	<div class="pmpro_message <?php echo $pmpro_msgt ?>"><?php echo $pmpro_msg ?></div>
	<?php
}

$confirmation_message = "<h9><b>Thanks for signing up! Your " . $current_user->membership_level->name . " is now active.</b></h9>";

//confirmation message for this level
$level_message = $wpdb->get_var("SELECT l.confirmation FROM $wpdb->pmpro_membership_levels l LEFT JOIN $wpdb->pmpro_memberships_users mu ON l.id = mu.membership_id WHERE mu.user_id = '" . $current_user->ID . "' LIMIT 1");
if (!empty($level_message))
	$confirmation_message .= "\n" . stripslashes($level_message) . "\n";
?>


			

<?php if ($pmpro_invoice) { ?>
	<?php
	$pmpro_invoice->getUser();
	$pmpro_invoice->getMembershipLevel();

	$confirmation_message .= "<p>A welcome email has been sent to <strong>" . $pmpro_invoice->user->user_email . "</strong>. The details of your membership account and a receipt for your initial membership invoice are below. If you are ready to get started click on one of the topics below.</p>";
	$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, $pmpro_invoice);

	echo apply_filters("the_content", $confirmation_message);
	?>
<?php
	if(pmpro_hasMembershipLevel(array(2,3,4,5,6,7,8)))
	{
	?>
			<div class="confirmationpaid">
			<a href="/training/intro-to-solidworks"><div class="confcontenttiles6"></div></a>	
			<a href="/training/parts"><div class="confcontenttiles1"></div></a>
			<a href="/training/drawings"><div class="confcontenttiles2"></div></a>	
			<a href="/training/assemblies"><div class="confcontenttiles3"></div></a>	
			<a href="/training/sheet-metal"><div class="confcontenttiles4"></div></a>
			<a href="/training/surfacing"><div class="confcontenttiles5"></div></a>	
			<a href="/training/photoview-360"><div class="confcontenttiles7	"></div></a>	
			<br>
			<div class="confcontenttitle"><a href="/training/intro-to-solidworks">Intro</a></div>
			<div class="confcontenttitle"><a href="/training/parts">Part Modeling</a></div>
			<div class="confcontenttitle"><a href="/training/drawings">Drawing</a></div>
			<div class="confcontenttitle"><a href="/training/assemblies">Assembly</a></div>
			<div class="confcontenttitle"><a href="/training/sheet-metal">Sheet Metal</a></div>
			<div class="confcontenttitle"><a href="/training/surfacing">Surfacing</a></div>
			<div class="confcontenttitle"><a href="/training/photoview-360">Photoview 360</a></div>

			</div>
			<?php
	}
?>



	<h3>Invoice #<?php echo $pmpro_invoice->code ?> on <?php echo date("F j, Y", $pmpro_invoice->timestamp) ?></h3>
	<a class="pmpro_a-print" href="javascript:window.print()">Print</a>
	<ul> 
		<li><strong>Account:</strong> <?php echo $pmpro_invoice->user->display_name ?> (<?php echo $pmpro_invoice->user->user_email ?>)</li>
		<li><strong>Membership Level:</strong> <?php echo $current_user->membership_level->name ?></li>
		<?php if ($current_user->membership_level->enddate) { ?>
			<li><strong>Membership Expires:</strong> <?php echo date("n/j/Y", $current_user->membership_level->enddate) ?></li>
		<?php } ?>
		<?php if ($pmpro_invoice->getDiscountCode()) { ?>
			<li><strong>Discount Code:</strong> <?php echo $pmpro_invoice->discount_code->code ?></li>
		<?php } ?>
	</ul>

	<table id="pmpro_confirmation_table" class="pmpro_invoice" width="100%" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<th>Billing Address</th>
				<th>Payment Method</th>
				<th>Membership Level</th>
				<th>Total Billed</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php echo $pmpro_invoice->billing->name ?><br />
					<?php echo $pmpro_invoice->billing->street ?><br />
					<?php if ($pmpro_invoice->billing->city && $pmpro_invoice->billing->state) { ?>
						<?php echo $pmpro_invoice->billing->city ?>, <?php echo $pmpro_invoice->billing->state ?> <?php echo $pmpro_invoice->billing->zip ?> <?php echo $pmpro_invoice->billing->country ?><br />
					<?php } ?>
					<?php echo formatPhone($pmpro_invoice->billing->phone) ?>
				</td>
				<td>
					<?php if ($pmpro_invoice->accountnumber) { ?>
						<?php echo $pmpro_invoice->cardtype ?> ending in <?php echo last4($pmpro_invoice->accountnumber) ?><br />
						<small>Expiration: <?php echo $pmpro_invoice->expirationmonth ?>/<?php echo $pmpro_invoice->expirationyear ?></small>
					<?php } elseif ($pmpro_invoice->payment_type) { ?>
						<?php echo $pmpro_invoice->payment_type ?>
					<?php } ?>
				</td>
				<td><?php echo $pmpro_invoice->membership_level->name ?></td>
				<td><?php if ($pmpro_invoice->total) echo $pmpro_currency_symbol . number_format($pmpro_invoice->total, 2); else echo "---"; ?></td>
			</tr>
		</tbody>
	</table>
	<?php
} else {
	$confirmation_message .= "<p>A welcome email has been sent to <strong>" . $current_user->user_email . "</strong>. You now have access to the first three hours of our training. Click on one of the buttons below to get started.</p>";

	$confirmation_message = apply_filters("pmpro_confirmation_message", $confirmation_message, false);

	echo $confirmation_message;

//	if ($current_user->membership_level->initial_payment <= 0 && $current_user->membership_level->billing_amount <= 0 && $current_user->membership_level->trial_amount <= 0){
//		pmpro_changeMembershipLevel(false, $current_user->ID);
//	}
	?>
	
	<ul>
		<li><strong>Account:</strong> <?php echo $current_user->display_name ?> (<?php echo $current_user->user_email ?>)</li>
		<li><strong>Membership Level:</strong> <?php echo $current_user->membership_level->name ?></li>
	</ul>
	<?php
	if(pmpro_hasMembershipLevel('1'))
	{
	?>
	<div class="confirmationfree">
	<a href="http://solidwize.com/training/intro-to-solidworks" title="Intro to SolidWorks"><img width="275" height="150" src="http://solidwize.com/solidwize/wp-content/uploads/2011/11/intro.png"></a>
	<a href="http://solidwize.com/training/parts" title="Part Modeling"><img width="275" height="150" src="http://solidwize.com/solidwize/wp-content/uploads/2011/11/parts.png"></a>
	<br>
	<div class="conffreetitle1"><a href="/training/intro-to-solidworks">Intro to SolidWorks</a></div>
	<div class="conffreetitle"><a href="/training/parts">Part Modeling</a></div>
</div>
	
<?php
	} }
?>
<?php
	if(pmpro_hasMembershipLevel('1'))
	{
	?>
	<p align="center" style="margin:20px 0 5px 0;"><a href="http://solidwize.com">View Complete Training Package &raquo;</a></p>
	<p align="center" style="margin:0 0 20px 0;"><a href="<?php echo pmpro_url("account") ?>">View Your Account Page &raquo;</a></p>

<?php
	} 
?>
<?php
	if(pmpro_hasMembershipLevel(array(2,3,4,5,6,7,8)))
	{
	?>
<p align="center" style="margin:20px 0;"><a href="<?php echo pmpro_url("account") ?>">View Your Account Page &raquo;</a></p>

<?php
	} 
?>


            </div><!-- end content -->
        </div><!-- end main-holder -->
    </div><!-- main-area --><?php get_footer(); ?>