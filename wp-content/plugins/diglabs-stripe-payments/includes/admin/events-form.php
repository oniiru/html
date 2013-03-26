<?php

require_once 'pagination.php';

// Load the official Stripe PHP bootstrap file
//
require_once STRIPE_PAYMENTS_PLUGIN_DIR.'/stripe-php-1.6.1/lib/Stripe.php';

// Get the API key from the settings.
//
$settings = new StripeSettings();
$secretKey = $settings->getSecretKey();
Stripe::setApiKey( $secretKey );

// Get the count / offset arguements.
//
$page = intval( $_REQUEST[ 'p' ] );
$count = 50;
$offset = $page * $count;

// Fetch the event data from Stripe
//
$total_events = 0;
$events = array();
try {
	$args = array(
			'count'		=> $count,
			'offset'	=> $offset
		);
	$all = Stripe_Event::all( $args );
	$events = $all->data;
	$total_events = $all->count;

} catch (Exception $e) {
	
	echo "<div class='error'>Configure the Stripe API keys in the <strong>Setup</strong> tab.</div>";
}


// Calculate the number of pages.
//
$tot_pages = ceil( $total_events / $count );

// Render pagination on top of the form.
//
kriesi_pagination($page, $tot_pages, 2);
$tab = $_REQUEST['tab'];
?>

<table class="widefat fixed">
	<thead>
		<tr>
			<th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Timestamp</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Type</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Pending</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th id="columnname" class="manage-column column-columnname" scope="col">Id</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Timestamp</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Type</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Amount</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Is Live</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Pending</th>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach( $events as $index => $event ) { ?>
		<tr <?php if($index%2==0){echo "class='alternate'";} ?> >
			<td class="column-columnname"><?php echo $event->id; ?></a></td>
			<td class="column-columnname"><?php echo date( 'F j Y g:i a', $event->created ); ?></td>
			<td class="column-columnname"><?php echo $event->type; ?></td>
			<td class="column-columnname"><?php echo "$".number_format( $event->data->object->amount/100, 2); ?></td>
			<td class="column-columnname"><?php echo $event->livemode ? 'Yes' : 'No'; ?></td>
			<td class="column-columnname"><?php echo $event->pending_webhooks; ?></td>
		</tr>
		<?php } ?>
	</tbody>

</table>
<?php
// Render pagination on bottom of the form
//
kriesi_pagination($page, $tot_pages, 2);
?>