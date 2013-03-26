<?php

if( $_POST[ 'sdelete' ] ) {

	// Delete the checked subscriptions
	//
	$indices = $_POST['subs'];
	if(!is_null($indices)) {

		$options = DlPs_Options::Get();
		$all_ok = true;
		foreach($indices as $index) {

			$sub = $options[ 'subscriptions' ][ $index ];

			$ok = DlPs_Options::DeleteSubscription( $sub[ 'name' ] );

			$all_ok = $all_ok && $ok;
		}
		
		if( $all_ok ) {
			echo "<div class='updated'><p><strong>Subscription(s) deleted.</strong></p></div>";
		} else {
			echo "<div class='error'><p><strong>Something went wrong.</strong></p></div>";
		}

	}

} else if( $_POST[ 'sadd' ] ) {

	// Adding a new subscription
	//

	// Validation
	//
	$errors = array();
	if( empty( $_POST[ 'sname' ] ) ) {
		$errors[] = "Please supply a <strong>Name</strong>.";
	}
	if( empty( $_POST[ 'splan' ] ) ) {
		$errors[] = "Please supply a <strong>Payment Plan ID</strong>.";
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

		// Add the subscription
		//
		if( DlPs_Options::AddSubscription( $_POST['sname'], $_POST['splan'] ) ){
			echo "<div class='updated'><p><strong>Subscription added.</strong></p></div>";
		} else {
			echo "<div class='error'><p><strong>Failed to add subscription. Be sure name is unique.</strong></p></div>";			
		}

	}
	
}

// Get fresh data for rendering
//
$options = DlPs_Options::Get();
$users = DlPs_User::All();

?>

<h3>Existing Subscription Plans</h3>
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<table class="widefat fixed">
		<thead>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Payment Plan ID</th>
				<th class="manage-column column-columnname">Payment Form Short Code</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Payment Plan ID</th>
				<th class="manage-column column-columnname">Payment Form Short Code</th>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach( $options['subscriptions'] as $index => $subs ) { ?>
			<tr <?php if($index%2==0){echo "class='alternate'";} ?> >
				<th class="check-column" scope="row"><input type="checkbox" name="subs[]" value="<?php echo $index; ?>" /></th>
				<td class="column-columnname"><?php echo $subs['name']; ?></td>
				<td class="column-columnname"><?php echo $subs['plan']; ?></td>
				<td class="column-columnname"><?php echo DlPs_Gateway::Instance()->SubscriptionShortCode( $subs ); ?></td>
			</tr>
			<?php } ?>
		</tbody>

	</table>
	<p><input class="button-primary" name="sdelete" type="submit" value="Delete Checked" /></p>
</form>

<h3>Create A New Subscription Plan</h3>
<form method="post" action="">

	<table class="form-table">
		<legend>Add Subscription</legend>

		<tr valign="top">
			<td><label for="name">Name</label></td>
			<td><input maxlength="45" size="25" name="sname" type="text" /></td>
			<td><span>The display name for the subscription</span></td>
		</tr>

		<tr valign="top">
			<td><label for="plan">Payment Plan ID(s)</label></td>
			<td><input maxlength="45" size="25" name="splan" type="text" /></td>
			<td><span>A comma separated list of payment plan IDs that provide access to this subscription.</span></td>
		</tr>

	</table>
	<p><input class="button-primary" name="sadd" type="submit" value="Add Subscription" /></p>
</form>