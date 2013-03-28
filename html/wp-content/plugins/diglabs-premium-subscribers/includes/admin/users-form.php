<?php

$url = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
$user_to_edit = null;
$levels = null;
if( isset($_REQUEST[ 'userid' ]) ) {

	$user_to_edit = DlPs_User::Get( $_REQUEST[ 'userid' ] );
	if( !is_null( $user_to_edit ) ) {

		$levels = DlPs_Level::All();
		foreach( $levels as $level ) {

			$level->plans = array();
			foreach( $level->plan_ids as $plan_id ) {

				$plan = DlPs_Plan::Get( $plan_id );
				if( !is_null( $plan) ) {

					$level->plans[] = $plan;
				}
			}
		}
	}
}	

if( $_POST[ 'cmd-update' ] ){

	// Update the users with their new subscriptions
	//
	$errors = array();

	// Only admins can do this.
	//
	if( current_user_can( 'manage_options' ) ) {

		if( !is_null( $user_to_edit ) ) {

			// Process expiration date.
			//
			if( !empty( $_POST[ 'expiration' ] ) ) {

				$user_to_edit->expiration = strtotime( $_POST[ 'expiration' ] );
			} else {

				$errors[] = 'Please supply a <strong>Expiration Date</strong>.';
			}

			// Process the new subscription level.
			//
			if( is_numeric( $_POST[ 'level_id' ] ) ) {

				$level_id = intval( $_POST[ 'level_id' ] );

				if( $level_id == -1 ) {

					$user_to_edit->level_id = null;

				} else {

					$level = DlPs_Level::Get( $level_id );
					if( !is_null( $level ) ) {

						$user_to_edit->level_id = $level_id;
					} else {

						$errors[] = 'Please select a valid <strong>Level</strong>.';
					}
				}
			} else {

				$errors[] = 'Please select a valid <strong>Level</strong>.';
			}

			// Process the stripe ID.
			//
			$user_to_edit->stripe_id = $_POST[ 'stripe_id' ]; 


			if( count( $errors ) == 0 ) {

				// Still no errors...date the user.
				//
				$user_to_edit->Update();

				echo "<div class='updated'><p><strong>User(s) updated.</strong></p></div>";
			} else {

				// There were errors.
				//
				echo "<div class='error'>";
				echo "<p>Please correct the following errors:</p>";

				foreach($errors as $error) {
					echo "<p>$error</p>";
				}

				echo "</div>";
			}

		} else {

			$errors[] = 'Not a valid <em>user to edit</em>';
		}


	} else {

		$errors[] = 'Login as an <strong>Admin</strong> first.';
	}
}

// Get fresh data for rendering
//
$options = DlPs_Options::Get();
$users = DlPs_User::All();

$level_names = array();
$levels = DlPs_Level::All();
foreach( $levels as $level ) {
	$level_names[ $level->id ] = $level->name;
}

$plan_names = array();
$plan_amounts = array();
$plans = DlPs_Plan::All();
foreach( $plans as $plan ) {
	$name = $plan->ToString();
	$plan_names[ $plan->id ] = $name;
	$plan_amounts[ $plan->id ] = $plan->amount;
}

$stripe_url = get_bloginfo( 'url' ) . "/wp-admin/admin.php?page=diglabs_stripe_payments&tab=&p=0&c=";
?>


<h3>Manage User Subscriptions</h3>
<table class="widefat fixed">
	<thead>
		<tr>
			<th id="columnname" class="manage-column column-columnname" scope="col">Username</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Expiration</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Level</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Plan</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Stripe ID</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th id="columnname" class="manage-column column-columnname" scope="col">Username</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Expiration</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Level</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Plan</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Stripe ID</th>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach( $users as $index => $user ) { ?>
		<tr <?php if($index%2==0){echo "class='alternate'";} ?> >
			<td class="column-columnname"><a href="<?php echo $url . '&userid=' . $user->wp_user->ID; ?>"><?php echo $user->user_login; ?></a></td>
			<td class="column-columnname"><?php echo $user->user_email; ?></td>
			<td class="column-columnname"><?php echo date('F j, Y', $user->expiration); ?></td>
			
			<?php if( !empty( $user->level_id ) ) { ?>
				<td class="column-columnname"><?php echo $level_names[ $user->level_id ]; ?></td>
			<?php } else { ?>
				<td class="column-columnname">-</td>
			<?php } ?>

			<?php if( !empty( $user->plan_id ) && isset( $plan_names[ $user->plan_id ] ) ) { ?>
				<td class="column-columnname"><?php echo $plan_names[ $user->plan_id ]; ?></td>
				<td class="column-columnname">$ <?php echo number_format( $plan_amounts[ $user->plan_id ], 2); ?></td>
			<?php } else { ?>
				<td class="column-columnname">-</td>
				<td class="column-columnname">-</td>
			<?php } ?>
			
			<?php if( !empty( $user->stripe_id ) ) { ?>
				<td class="column-columnname"><a href="<?php echo $stripe_url . $user->stripe_id; ?>"><?php echo $user->stripe_id; ?></a></td>
			<?php } else { ?>
				<td class="column-columnname">-</td>
			<?php } ?>

		</tr>
		<?php } ?>
	</tbody>

</table>
<h3>Edit User</h3>
<?php if( !is_null( $user_to_edit ) ) { ?>

<form method="post" action="">

	<table class="form-table">

		<tr valign="top">
			<td><label for="username">Username</label></td>
			<td><input maxlength="45" size="25" type="text" disabled=disabled value="<?php echo $user_to_edit->user_login; ?>" /></td>
			<td><span>Not editable here. Use the Wordpress <strong>Users</strong> admin panel.</span></td>
		</tr>

		<tr valign="top">
			<td><label for="email">Email</label></td>
			<td><input maxlength="255" size="25" type="text" disabled=disabled value="<?php echo $user_to_edit->user_email; ?>" /></td>
			<td><span>Not editable here. Use the Wordpress <strong>Users</strong> admin panel.</span></td>
		</tr>

		<tr valign="top">
			<td><label for="expiration">Expiration</label></td>
			<td><input id="expiration" maxlength="10" size="25" name="expiration" class="datepicker" type="text" value="<?php echo date('F j, Y', $user_to_edit->expiration); ?>" /></td>
			<td><span>The expiration date. A user will not be billed again until they are expired.</span></td>
		</tr>

		<tr valign="top">
			<td><label for="level">Level</label></td>
			<td>
				<select id='level_id' name="level_id">
				<?php 
				if( is_null($user_to_edit->level_id) ) {
					echo "<option value='-1' selected='selected'>None</option>";
				} else {
					echo "<option value='-1'>None</option>";
				}
				foreach($levels as $level) {
					echo "<option value='" . $level->id . "'";
					
					if( $user_to_edit->level_id == $level->id) {
						echo " selected='selected'";
					}

					echo ">";
					echo $level->name;
					echo "</option>";
				} ?>
				</select><br />
				current: <strong><?php echo is_null($user_to_edit->level) ? "None" : $user_to_edit->level->name; ?></strong>
			</td>
			<td><span>The subscription level for this user.</span></td>
		</tr>

		<tr valign="top">
			<td><label for="stripe_id">Stripe ID</label></td>
			<td><input maxlength="50" size="25" name="stripe_id" type="text" value="<?php echo $user_to_edit->stripe_id; ?>" /></td>
			<td><span>The user's Stripe ID.</span></td>
		</tr>

	</table>
	<p><input class="button-primary" name="cmd-update" type="submit" value="Update User" /></p>
</form>

<?php } else { ?>
	<p><em>Click the <strong>Username</strong> link of a user to begin editing.</em></p>
<?php } ?>