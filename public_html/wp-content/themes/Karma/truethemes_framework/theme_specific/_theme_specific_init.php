<?php
/*
* Loads theme specific file.
* @since version 2.6
*
*/

//site options
require_once(TEMPLATEPATH . '/truethemes_framework/theme_specific/site-option.php');

//admin functions
require_once(TEMPLATEPATH . '/truethemes_framework/theme_specific/admin-functions.php');

//theme functions
require_once(TEMPLATEPATH . '/truethemes_framework/theme_specific/functions.php');

//writes panel
require_once(TEMPLATEPATH . '/truethemes_framework/theme_specific/write-panels.php');

//Javascript Loader
require_once(TEMPLATEPATH . '/truethemes_framework/theme_specific/javascript.php');

//update notifier
require_once(TEMPLATEPATH . '/truethemes_framework/theme_specific/update-notifier.php');


?>