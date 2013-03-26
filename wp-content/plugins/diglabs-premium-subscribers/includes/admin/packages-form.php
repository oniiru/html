<?php

if( $_POST[ 'cmd-delete' ] ) {

	// Delete the checked subscriptions
	//
	$indices = $_POST['package_ids'];
	
	if( !is_null( $indices ) ) {

		$all_ok = true;
		foreach( $indices as $package_id ) {

			$ok = DlPs_Package::DeletePackage( $package_id );
			$all_ok = $all_ok && $ok;
		}
		
		if( $all_ok ) {
			echo "<div class='updated'><p><strong>Subscription(s) deleted.</strong></p></div>";
		} else {
			echo "<div class='error'><p><strong>Something went wrong.</strong></p></div>";
		}

	}

} else if( $_POST[ 'cmd-add' ] ) {

	// Adding a new subscription
	//

	// Validation
	//
	$errors = array();
	if( empty( $_POST[ 'name' ] ) ) {
		$errors[] = "Please supply a <strong>Name</strong>.";
	}
	if( empty( $_POST[ 'plan_ids' ] ) ) {
		$errors[] = "Please select at least one <strong>Plan</strong>.";
	}
	$plan_ids = explode(",", $_POST[ 'plan_ids'] );
	if( count($plan_ids) == 0 ) {
		$errors[] = "Please select at least one <strong>Plan</strong>.";
	}
	// Ensure all the plans exist.
	foreach( $plan_ids as $id ){
		if( DlPs_Plan::Get( $id ) == null ) {
			$errors[] = "The pland ID = " . $id . " does not exist.";
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

		// Add the subscription
		//
		$package = new DlPs_Package();
		$package->name = $_POST['name'];
		$package->plan_ids = $plan_ids;


		if( DlPs_Package::AddPackage( $package ) ){
			echo "<div class='updated'><p><strong>Subscription added.</strong></p></div>";
		} else {
			echo "<div class='error'><p><strong>Failed to add subscription. Be sure name is unique.</strong></p></div>";			
		}

	}
	
}

// Get fresh data for rendering
//
$packages = DlPs_Package::All();

// Fetch the plan information
//
foreach( $packages as $package ) {

	$plans = array();
	foreach( $package->plan_ids as $plan_id ) {

		$plan = DlPs_Plan::Get( $plan_id );
		if( !is_null( $plan ) ) {
			$temp = "<strong>" . $plan->level . "</strong> $" . $plan->amount . " / " . $plan->period . " / ";
			if( $plan->is_recurring ){
				$temp .= "recurring";
			} else {
				$temp .= "not recurring";
			}
			$temp .= " [ID=" . $plan->id . "]";
			$plans[] = $temp;
		}
	}
	$package->plans = $plans;
}

$plans = DlPs_Plan::All();

?>

<h3>Existing Packages</h3>
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<table class="widefat fixed">
		<col width="5" />
		<col width="10" />
		<col width="20" />
		<col width="80" />
		<thead>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Id</th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Sorted Plans</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column"></th>
				<th class="manage-column column-columnname">Id</th>
				<th class="manage-column column-columnname">Name</th>
				<th class="manage-column column-columnname">Sorted Plans</th>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach( $packages as $index => $package ) { ?>
			<tr <?php if($index%2==0){echo "class='alternate'";} ?> >
				<th class="check-column" scope="row"><input type="checkbox" name="package_ids[]" value="<?php echo $package->id; ?>" /></th>
				<td class="column-columnname"><?php echo $package->id; ?></td>
				<td class="column-columnname"><?php echo $package->name; ?></td>
				<td class="column-columnname">
					<?php foreach( $package->plans as $plan)
						echo "$plan<br />";
					?>
				</td>
			</tr>
			<?php } ?>
		</tbody>

	</table>
	<p><input class="button-primary" name="cmd-delete" type="submit" value="Delete Checked" /></p>
</form>

<h3>Create A Package</h3>
<div id='container'>
	<div id='plans'>
		<h5>Existing Plans</h5>
		<ul>
		<?php foreach( $plans as $plan ) {
			echo "<li><a href='#' class='plan' data-id='" . $plan->id . "'>";
			echo "<strong>" . $plan->id . " - " . $plan->level . "</strong><br />";
			echo "<small>$" . $plan->amount . '/' . $plan->period . '/';
			if( $plan->is_recurring ) {
				echo "recurring</small><br />";
			} else {
				echo "not recurring</small><br />";
			}
			echo "<small>" . $plan->description . "</small>";
			echo "</a></li>";
		}?>
		</ul>
	</div>
	<div id='form'>
		<h5>Build A Package</h5>
		<form method="post" action="">

			<table class="form-table">

				<tr valign="top">
					<td><label for="name">Name</label></td>
					<td><input maxlength="45" size="25" name="name" type="text" /></td>
					<td><span>The package name.</span></td>
				</tr>

				<tr valign="top">
					<td><label for="plan"> Sorted Plan ID(s)</label></td>
					<td>
						<span><em>Click plans in the list to the left.<br />Drag and drop to sort</em></span>
						<input type='hidden' name='plan_ids' id='plan_ids' />
						<ul id='plan_list'>
						</ul>
					</td>
					<td><span>A sorted list of plans in the package.<br /></span></td>
				</tr>

			</table>
			<p><input class="button-primary" name="cmd-add" type="submit" value="Add Package" /></p>
		</form>
	</div>
</div>
<script type='text/javascript'>
(function($){
	$(document).ready(function(){
		$('#plans a').click(function(evt){
			evt.preventDefault();
			var id = $(this).data('id');
			var html = $(this).html();
			$('#plan_list').append('<li data-id=' + id + ' class="ui-state-default"><span class="delete">[delete]</span>' + html + '</li>');
			
			updateIdList();
		});

		$('#plan_list').sortable({
			update: function(event, ui) {
				updateIdList();
			}
		});
		$('#plan_list').disableSelection();

		$('#plan_list li span.delete').live('click', function(evt){
			var li = $(this).closest('li');
			li.fadeOut('fast', function(){
				$(this).remove();
				updateIdList();
			});
		});

		function updateIdList() {
			var ids = '';
			$('#plan_list li').each(function(){
				if( ids.length > 0 ){
					ids += ',';
				}
				ids += $(this).data('id');
			})
			$('#plan_ids').val(ids);			
		}
	});
})(jQuery);
</script>
