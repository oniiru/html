<?php

if( $_POST[ 'cmd-delete' ] ) {

	// Delete the checked subscriptions
	//
	$ids = $_POST['ids'];

	if(!is_null($ids)) {

		$all_ok = true;
		foreach($ids as $id) {

			$ok = DlPs_Level::DeleteLevel( $id );

			$all_ok = $all_ok && $ok;
		}
		
		if( $all_ok ) {

			echo "<div class='updated'><p><strong>Plan(s) deleted.</strong></p></div>";
		} else {

			echo "<div class='error'><p><strong>Something went wrong.</strong></p></div>";
		}
	}

} else if( $_POST[ 'cmd-add' ] ) {

	// Adding a new level
	//

	// Validation
	//
	$errors = array();
	if( empty( $_POST[ 'name' ] ) ) {
		$errors[] = "Please supply a <strong>Level</strong>.";
	}
	if( empty( $_POST[ 'description' ] ) ) {
		$errors[] = "Please supply a <strong>Description</strong>.";
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

		// Add the level
		//
		$level = new DlPs_Level();
		$level->name = trim( $_POST[ 'name' ] );
		$level->description = trim( $_POST[ 'description' ] );


		if( !DlPs_Level::AddLevel( $level ) ) {
			echo "<div class='error'><p><strong>Failed to add level.</strong></p></div>";			
		} else {
			echo "<div class='updated'><p><strong>Level added.</strong></p></div>";
		}

	}
	
}

// Get fresh data for rendering
//
$levels = DlPs_Level::All();
?>

<h3>Existing Levels</h3>
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<table class="widefat fixed">
		<thead>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Description</th>
				<th class="manage-column column-columnname">Plan Count</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Description</th>
				<th class="manage-column column-columnname">Plan Count</th>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach( $levels as $index => $level ) { ?>
			<tr <?php if( $index % 2 == 0 ) { echo "class='alternate'"; } ?> >
				<?php if( count( $level->plan_ids ) == 0 ) { ?>
					<th class="check-column" scope="row"><input type="checkbox" name="ids[]" value="<?php echo $level->id; ?>" /></th>
				<?php } else { ?>
					<th class="check-column" scope="row">&nbsp;</th>
				<?php } ?>
				<td class="column-columnname"><?php echo $level->name; ?></td>
				<td class="column-columnname"><?php echo $level->description; ?></td>
				<td class="column-columnname"><?php echo count( $level->plan_ids ); ?></td>
			</tr>
			<?php } ?>
		</tbody>

	</table>
	<p><input class="button-primary" name="cmd-delete" type="submit" value="Delete Checked" /></p>
</form>

<h3>Add A New Level</h3>
<form method="post" action="">

	<table class="form-table">

		<tr valign="top">
			<td><label for="name">Name</label></td>
			<td><input maxlength="45" size="25" name="name" type="text" /></td>
			<td><span>The name for the level. Each level should have a unique name.</span></td>
		</tr>

		<tr valign="top">
			<td><label for="description">Description</label></td>
			<td><input maxlength="255" size="25" name="description" type="text" /></td>
			<td><span>A short description of the level.</span></td>
		</tr>

	</table>
	<p><input class="button-primary" name="cmd-add" type="submit" value="Add Level" /></p>
</form>