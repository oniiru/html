<?php

$project_mb = new WPAlchemy_MetaBox(array
(
	'id' => '_project_meta',
	'title' => 'Project Options',
	'types' => array('project_views'), // added only for pages and to custom post type "events"
	'context' => 'normal', // same as above, defaults to "normal"
	'priority' => 'high', // same as above, defaults to "high"
	'template' => get_stylesheet_directory() . '/metaboxes/project-meta.php'
));

/* eof */