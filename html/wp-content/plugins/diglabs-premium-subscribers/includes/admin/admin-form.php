<?php

// Tab definitions.
//
$tabs = array(
		'users' 	=> 'Users',
		'plans'		=> 'Plans',
		'levels'	=> 'Levels',
		'misc'		=> 'Setup'
	);

// Get the currently selected tab
//
$tab = $_REQUEST['tab'];
if( !$tab ) {
	$tab = 'users';
}

?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2>Dig Labs Premium Subscriber</h2>


	<h2 class="nav-tab-wrapper">
	<?php
	foreach( $tabs as $key => $title ) {
		$active = ($key==$tab) ? 'nav-tab-active' : '';
		echo "<a class='nav-tab $active' href='?page=" . DLPS_ADMIN_PAGE . "&tab=$key'>$title</a>";
	}
	?>
	</h2>

	<?php require_once $tab . "-form.php"; ?>

</div>

