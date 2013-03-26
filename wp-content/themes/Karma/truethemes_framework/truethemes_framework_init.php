<?php

// Define file directories
define('TRUETHEMES_FUNCTIONS', TEMPLATEPATH . '/truethemes_framework');
define('TRUETHEMES_GLOBAL', TEMPLATEPATH . '/truethemes_framework/global');
define('TRUETHEMES_ADMIN', TEMPLATEPATH . '/truethemes_framework/admin');
define('TRUETHEMES_EXTENDED', TEMPLATEPATH . '/truethemes_framework/extended');
define('TRUETHEMES_CONTENT', TEMPLATEPATH . '/truethemes_framework/content');
define('TRUETHEMES_JS', get_template_directory_uri() . '/truethemes_framework/js');
define('TRUETHEMES_FRAMEWORK', get_template_directory_uri() . '/truethemes_framework');
define('TRUETHEMES_CSS', get_template_directory_uri() . '/css/');
define('TRUETHEMES_HOME', get_template_directory_uri());
define('TRUETHEMES', TEMPLATEPATH . '/truethemes_framework/truethemes');
define('TIMTHUMB_SCRIPT',get_template_directory_uri()."/truethemes_framework/extended/timthumb/timthumb.php");
define('TIMTHUMB_SCRIPT_MULTISITE',get_template_directory_uri()."/truethemes_framework/extended/timthumb/timthumb.php");


// Load theme specific init file.
require_once(TEMPLATEPATH . '/truethemes_framework/theme_specific/_theme_specific_init.php');


// Load global elements
require_once(TRUETHEMES_GLOBAL . '/shortcodes.php');
require_once(TRUETHEMES_GLOBAL . '/shortcodes-old.php');
require_once(TRUETHEMES_GLOBAL . '/widgets.php');
require_once(TRUETHEMES_GLOBAL . '/sidebars.php');
require_once(TRUETHEMES_GLOBAL . '/theme_functions.php');
require_once(TRUETHEMES_GLOBAL . '/basic.php');
require_once(TRUETHEMES_GLOBAL . '/nav-output.php');
require_once(TRUETHEMES_GLOBAL . '/hooks.php');


// Load TrueThemes functions
require_once(TRUETHEMES . '/upgrade/init.php');
require_once(TRUETHEMES . '/wysiwyg/wysiwyg.php');
require_once(TRUETHEMES . '/image-thumbs.php');
$ka_formbuilder = get_option('ka_formbuilder');
if ($ka_formbuilder == "true"){require_once(TRUETHEMES . '/contact-form/truethemes-contact-form.php');}


// Load admin
require_once(TRUETHEMES_ADMIN . '/admin-functions.php');
require_once(TRUETHEMES_ADMIN . '/admin-interface.php');


// Load extended functionality
require_once(TRUETHEMES_EXTENDED . '/pricing-tables/pricing.php');
require_once(TRUETHEMES_EXTENDED . '/multiple_sidebars.php');
require_once(TRUETHEMES_EXTENDED . '/breadcrumbs.php');
require_once(TRUETHEMES_EXTENDED . '/3d-tag-cloud/wp-cumulus.php');
require_once(TRUETHEMES_EXTENDED . '/twitter/latest-tweets.php');
require_once(TRUETHEMES_EXTENDED . '/page_linking.php');
if(!function_exists('wp_pagenavi')){require_once(TRUETHEMES_EXTENDED . '/wp-pagenavi.php');}


// Load SEO module
global $ttso;
$seo_module = '';
$seo_module = $ttso->ka_seo_module;

//check user setting at site options general settings.
if ($seo_module == "true"){
//require all seo module files and "activate" seo module.
require_once(TRUETHEMES_EXTENDED. '/seo-module/seo_module.php');
	$aioseop_options = get_option('aioseop_options');
	if($aioseop_options['aiosp_enabled']==0){
	$aioseop_options['aiosp_enabled'] = 1;
	update_option('aioseop_options',$aioseop_options);
	}
}else{
    //user has "disable" seo module,
    //we do not include seo module files, but just show an empty seo settings page,
    //so that user do not encounter WordPress "permissions" error, 
    //and the seo settings page is always there.
	$aioseop_options = get_option('aioseop_options');
	$aioseop_options['aiosp_enabled'] = 0;
	update_option('aioseop_options',$aioseop_options);
    add_action('admin_menu','truethemes_add_empty_seo_settings_page');
}

/**
* Do not move this function!
* Load empty SEO Setting Page!
* this gets load when user disables SEO Module! so that there is no WordPress Permission error when user clicks on "SEO Settings" menu!
* @since version 2.6
**/
function truethemes_add_empty_seo_settings_page(){
	add_theme_page('SEO settings','SEO settings','manage_options','seo_settings','truethemes_empty_seo_settings_page');
}

/**
* Do not move this function!
* Empty SEO settings page!
* for use in function truethemes_add_empty_seo_settings_page()
* @since version 2.6
**/
function truethemes_empty_seo_settings_page(){
?>
<div class="wrap">
<div style='padding:8px 10px 15px 15px'>	
<div id="icon-options-general" class="icon32"></div>
<h2><?php _e('SEO Settings', 'truethemes_localize') ?></h2>
</div>
<?php
	$aioseop_options = get_option('aioseop_options');
	if($aioseop_options['aiosp_enabled'] == 0){
			echo "<div id=\"message\" class=\"updated fade\"style='width:765px!important;margin:10px 0px 0px 0px;'><p>The SEO Module is currently disabled. To enable this Module, please go to <a href='".admin_url('admin.php?page=siteoptions')."'>Appearance &gt; Site Options &gt; General Settings</a>.</p></div>";
	}

}


?>