<?php

// Calculate the path to the folder that holds the uploaded files.
//
$path = dirname(__FILE__).'/../../files/';

if( $_POST[ 'fdelete' ] ) {

	// Delete the checked subscriptions
	//
	$indices = $_POST['indices'];
	if(!is_null($indices)) {

		// Get a copy of the files array as it existed when
		//	the indices were generated.
		//
		$options = DlPs_Options::Get();
		$all_ok = true;
		foreach($indices as $index) {

			// Get the file data
			//
			$file = $options[ 'files' ][ $index ];

			// Delete the file from disk.
			//
			$file_path = $path . $file['file'];
			if(file_exists($file_path)) {
				unlink($file_path);
			}

			// Delete the file data from the options.
			//
			$ok = DlPs_Options::DeleteFile( $file[ 'name' ] );

			$all_ok = $all_ok && $ok;
		}
		
		if( $all_ok ) {
			echo "<div class='updated'><p><strong>File(s) deleted.</strong></p></div>";
		} else {
			echo "<div class='error'><p><strong>Something went wrong.</strong></p></div>";
		}

	}

} else if( $_POST[ 'fadd' ] ) {

	// Adding a new subscription
	//

	// Validation
	//
	$errors = array();
	if( empty( $_POST[ 'name' ] ) ) {
		$errors[] = "Please supply a <strong>Name</strong>.";
	}
	if(empty($_POST['cost'])) {
		$errors[] = "Error: Please supply a cost.";
	}
	if(!is_numeric($_POST['cost'])) {
		$errors[] = "Error: The cost must be a valid number.";
	}
	if( $_FILES[ 'file' ][ 'error' ] > 0 ) {
		$errors[] = "Error: Invalid file. err=".$_FILES['file']['error'];
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

		// Get the validated form fields.
		//
		$name = $_POST['name'];
		$cost = floatval($_POST['cost']);
		$file = $_FILES['file']['name'];
		
		// Create the path to the final destination.
		//
		$file_path = $path . $file;

		if( file_exists( $file_path ) ) {

			// A file with this name already exists
			//
			echo "<div class='updated'><p><strong>";
			echo "Error: ".$_FILES['file']['name']." already exists. Delete first.";
			echo "</strong></p></div>";

		} else {

			// Move the file to the folder.
			//
			if(move_uploaded_file( $_FILES['file']['tmp_name'], $file_path)) {

				// Move succeeded. Add to the options.
				//
				if( DlPs_Options::AddFile( $name, $cost, $file ) ){

					echo "<div class='updated'><p><strong>File added.</strong></p></div>";

				} else {

					echo "<div class='error'><p><strong>Failed to add file. Be sure name is unique.</strong></p></div>";

				}
			
			} else {

				// Failed to move the file.
				//
				echo "<div class='updated'><p><strong>Error: Failed to move the uploaded file.</strong></p></div>";
			}
		}
	}
}

// Get fresh data for rendering
//
$options = DlPs_Options::Get();
$users = DlPs_User::All();

?>

<h3>Existing Paid Access Files</h3>
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<table class="widefat fixed">
		<thead>
			<tr>
				<th class="manage-column column-cb check-column" scope="col"></th>
				<th class="manage-column column-columnname" scope="col">Name</th>
				<th class="manage-column column-columnname" scope="col">Cost</th>
				<th class="manage-column column-columnname" scope="col">File Name</th>
				<th class="manage-column column-columnname" scope="col">Payment Form Short Code</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column" scope="col"></th>
				<th class="manage-column column-columnname" scope="col">Name</th>
				<th class="manage-column column-columnname" scope="col">Cost</th>
				<th class="manage-column column-columnname" scope="col">File Name</th>
				<th class="manage-column column-columnname" scope="col">Payment Form Short Code</th>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach( $options[ 'files' ] as $file ) { ?>
			<tr <?php if($index%2==0){echo "class='alternate'";} ?> >
				<th class="check-column" scope="row"><input type="checkbox" name="indices[]" value="<?php echo $index; ?>" /></th>
				<td class="column-columnname"><?php echo $file['name']; ?></td>
				<td class="column-columnname">$ <?php echo number_format( $file['cost'], 2 ); ?></td>
				<td class="column-columnname"><?php echo $file['file']; ?></td>
				<td class="column-columnname"><?php echo DlPs_Gateway::Instance()->FileShortCode( $file ); ?></td>

			</tr>
			<?php } ?>
		</tbody>

	</table>
	<p><input class="button-primary" name="fdelete" type="submit" value="Delete Checked" /></p>
</form>

<h3>Add A Paid Access File</h3>
<form method="post" action="" enctype="multipart/form-data">

	<table class="form-table">
		<legend>Add File</legend>

		<tr valign="top">
			<th scope="row"><label for="name">Name</label></th>
			<td><input maxlength="45" size="25" name="name" type="text" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="cost">Cost</label></th>
			<td><input maxlength="45" size="25" name="cost" type="text" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="file">File</label></th>
			<td><input maxlength="45" size="25" name="file" type="file" /></td>
		</tr>

	</table>
	<p><input class="button-primary" name="fadd" type="submit" value="Add File" /></p>
</form>
