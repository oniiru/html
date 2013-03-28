<?php

$url = '?page=' . DLPS_ADMIN_PAGE . '&tab=plans';

// Only admins can do this.
//
if( current_user_can( 'manage_options' ) ) {

	if( $_GET[ 'deactivate' ] ) {

		$id = $_GET[ 'deactivate' ];
		$plan = DlPs_Plan::Get( $id );
		if( is_null( $plan ) ) {
			
			echo "<div class='error'><p><strong>Plan with id=$id does not exist.</strong></p></div>";
		} else {

			$plan->is_active = false;
			$plan->Save();
			echo "<div class='updated'><p><strong>Plan deactivated.</strong></p></div>";
		}
	} else if( $_GET[ 'activate' ] ) {

		$id = $_GET[ 'activate' ];
		$plan = DlPs_Plan::Get( $id );
		if( is_null( $plan ) ) {
			
			echo "<div class='error'><p><strong>Plan with id=$id does not exist.</strong></p></div>";
		} else {

			$plan->is_active = true;
			$plan->Save();
			echo "<div class='updated'><p><strong>Plan activated.</strong></p></div>";
		}
	}

	if( $_POST[ 'cmd-delete' ] ) {

		// Delete the checked subscriptions
		//
		$ids = $_POST['ids'];

		if(!is_null($ids)) {

			$all_ok = true;
			foreach($ids as $id) {

				$plan = DlPs_Plan::Get( $id );
				if( is_null( $plan ) ) {

					$all_ok = false;
					echo "<div class='error'><p><strong>Plan with id=$id does not exist.</strong></p></div>";
					continue;
				}

				if( !DlPs_Level::RemovePlan( $plan->level_id, $id ) ) {

					$all_ok = false;
					echo "<div class='error'><p><strong>Failed to delete Plan (id=$id) from Level (id=$plan->level_id).</strong></p></div>";
					//continue;
				}

				$ok = DlPs_Plan::DeletePlan( $id );

				$all_ok = $all_ok && $ok;
			}
			
			if( $all_ok ) {

				echo "<div class='updated'><p><strong>Plan(s) deleted.</strong></p></div>";
			} else {

				echo "<div class='error'><p><strong>Something went wrong.</strong></p></div>";
			}
		}

	} else if( $_POST[ 'cmd-add' ] ) {

		// Adding a new subscription
		//

		// Validation
		//
		$plan = new DlPs_Plan();
		$errors = array();
		
		// Level ID
		if( empty( $_POST[ 'level_id' ] ) ) {

			$errors[] = "Please supply a <strong>Level</strong>.";
		}
		$plan->level_id = intval( $_POST[ 'level_id' ] );

		// Name
		if( empty( $_POST[ 'name' ] ) ) {

			$errors[] = "Please supply a <strong>Description</strong>.";
		}
		$plan->name = trim( $_POST[ 'name' ] );

		// Type
		if( $_POST[ 'type' ] == 'single' ) {

			// Single payment plan
			$plan->stripe_plan_id = null;

			// Amount
			if( !is_numeric( $_POST[ 'amount' ] ) || 
				floatval( $_POST[ 'amount' ] ) < 0.0 ) {

				$errors[] = "Please supply a positive number for the <strong>Amount</strong>.";
			}
			$plan->amount = floatval( intval( $_POST[ 'amount' ] * 100 ) / 100 );

			// Period
			if( !is_numeric( $_POST[ 'length' ] ) || 
				intval( $_POST[ 'length' ] ) < 0 ) {

				$errors[] = "Please supply a positive number for the <strong>Period Length</strong>.";
			}
			$plan->period = intval( $_POST[ 'length' ] ) . ' ' . $_POST[ 'unit' ];

		} else {

			// Recurring plan
			if( empty( $_POST[ 'stripe_plan_id' ] ) ) {

				$errors[] = "Please supply a <strong>Stripe Plan ID</strong>.";
			}
			$stripe_plan_id = trim( $_POST[ 'stripe_plan_id' ] );
			try {

				require_once( ABSPATH . '/wp-content/plugins/diglabs-stripe-payments/stripe-php-1.6.1/lib/Stripe.php' );
				require_once( ABSPATH . '/wp-content/plugins/diglabs-stripe-payments/includes/class.settings.php' );

				$stripeSettings = new StripeSettings();
				$secretKey = $stripeSettings->getSecretKey();
				Stripe::setApiKey( $secretKey );

				$stripe_plan = Stripe_Plan::retrieve( $stripe_plan_id );

				$plan->stripe_plan_id = $stripe_plan_id;
				$plan->amount = $stripe_plan->amount/100.0;
				$plan->period = "1 " . $stripe_plan->interval;
			} catch ( Exception $e ) {

				$errors[] = "The <strong>Stripe Plan ID</strong> is not valid.";
			}
		}

		if( count( $errors ) ) {

			// There were errors.
			//
			echo "<div class='error'>";
			echo "<p>Please correct the following errors:</p>";

			foreach($errors as $error) {
				echo "<p>$error</p>";
			}

			echo "</div>";
		} else {

			// Add the plan
			//
			if( !DlPs_Plan::AddPlan( $plan ) ) {

				echo "<div class='error'><p><strong>Failed to add plan.</strong></p></div>";			
			} else {

				if( !DlPs_Level::AddPlan( $plan->level_id, $plan->id ) ) {

					echo "<div class='error'><p><strong>Failed to add Plan (id=$plan->id) to Level (id=$plan->level_id).</strong></p></div>";			
				} else {
					
					echo "<div class='updated'><p><strong>Plan added.</strong></p></div>";
				}
			}
		}
		
	}
}

// Get fresh data for rendering
//
$plans = DlPs_Plan::All();
$levels = DlPs_Level::All();

foreach( $plans as $plan ) {

	foreach( $levels as $level ) {

		if( $level->id == $plan->level_id ) {

			$plan->level = $level->name;
			break;
		}
	}
}

$stripe_plan_ids = array();
$stripe_plans = DlPs_Plan::GetStripePlans();
foreach($stripe_plans as $stripe_plan){

	$stripe_plan_ids[] = $stripe_plan->id;
}
?>

<h3>Existing Plans</h3>
<form method="post" action="<?php echo $url; ?>">
	<table class="widefat fixed">
		<thead>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Subscribers</th>
				<th class="manage-column column-columnname">Level</th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Amount</th>
				<th class="manage-column column-columnname">Period</th>
				<th class="manage-column column-columnname">Stripe Plan</th>
				<th class="manage-column column-columnname">Actions</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Subscribers</th>
				<th class="manage-column column-columnname">Level</th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Amount</th>
				<th class="manage-column column-columnname">Period</th>
				<th class="manage-column column-columnname">Stripe Plan</th>
				<th class="manage-column column-columnname">Actions</th>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach( $plans as $index => $plan ) { 
				$classes = $index % 2 == 0 ? 'alternate' : '';
				if( !$plan->is_active ) {
					$classes .= ' deactive';
				}
			?>
			<tr <?php echo "class='$classes'"; ?> >
				<?php if( $plan->subscribers_count == 0 ) { ?>
				<th class="check-column" scope="row"><input type="checkbox" name="ids[]" value="<?php echo $plan->id; ?>" /></th>
				<?php } else { ?>
				<th class="check-column" scope="row">&nbsp;</th>
				<?php } ?>
				<td class="column-columnname"><?php echo $plan->subscribers_count; ?></td>
				<td class="column-columnname"><?php echo $plan->level; ?></td>
				<td class="column-columnname"><?php echo $plan->name; ?></td>
				<td class="column-columnname">$ <?php echo number_format( $plan->amount, 2); ?></td>
				<td class="column-columnname"><?php echo $plan->period; ?></td>
				<td class="column-columnname"><?php echo $plan->stripe_plan_id; ?></td>
				<td class="column-columnname actions">
					<?php if( $plan->is_active ) { ?>
						<a href='<?php echo $url . "&deactivate=$plan->id"; ?>'>[deactivate]</a>
					<?php } else { ?>
						<a href='<?php echo $url . "&activate=$plan->id"; ?>'>[activate]</a>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>

	</table>
	<p><input class="button-primary" name="cmd-delete" type="submit" value="Delete Checked" /></p>
</form>

<h3>Add A New Plan</h3>
<form method="post" action="<?php echo $url; ?>">

	<table class="form-table">

		<tr valign="top">
			<td><label for="level_id">Level</label></td>
			<td>
				<select name="level_id">
				<?php foreach( $levels as $level ) {

					echo "<option value='$level->id'>$level->name</option>";
				} ?>
				</select>
			</td>
			<td><span>The level for the plan. Levels can have multiple plans but different price points &amp; periods.</span></td>
		</tr>

		<tr valign="top">
			<td><label for="name">Name</label></td>
			<td><input maxlength="255" size="25" name="name" type="text" /></td>
			<td><span>A short name for the plan.</span></td>
		</tr>

		<tr valign="top">
			<td><label for="type">Type</label></td>
			<td>
				<input id='single' type="radio" name="type" value="single" checked="checked" /> Single
				<input id='recurring' type="radio" name="type" value="recurring" /> Recurring
			</td>
			<td><span>The payment type. Either single or recurring payments.</span></td>
		</tr>

		<tr class="single" valign="top">
			<td><label for="amount">Amount ($)</label></td>
			<td><input maxlength="10" size="25" name="amount" type="text" /></td>
			<td><span>The amount the plan costs in U.S. dollars.</span></td>
		</tr>

		<tr class="single" valign="top">
			<td><label for="length">Period</label></td>
			<td>
				<input maxlength="10" size="5" name="length" type="text" />
				<select name="unit">
					<option value="hours">Hour(s)</option> 
					<option value="days" />Day(s)</option>
					<option value="months" />Month(s)</option>
					<option value="years" />Year(s)</option>
				</select>
			</td>
			<td><span>The subscription length.</span></td>
		</tr>


		<tr class="recurring" valign="top">
			<td><label for="stripe_plan_id">Stripe ID</label></td>
			<td>
				<select name="stripe_plan_id">
				<?php foreach( $stripe_plan_ids as $stripe_plan_id ) {
					echo "<option value='$stripe_plan_id'>$stripe_plan_id</option>";
				}?>
				</select>
			</td>
			<td><span>The <a href="http://stripe.com">Stripe</a> plan ID.</span></td>
		</tr>

	</table>
	<p><input class="button-primary" name="cmd-add" type="submit" value="Add Plan" /></p>
</form>