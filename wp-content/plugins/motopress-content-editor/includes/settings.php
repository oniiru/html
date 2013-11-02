<?php
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$motopressCESettings = array();
$motopressCESettings['debug'] = false;
$motopressCESettings['demo'] = false;
$motopressCESettings['admin_url'] = get_admin_url();
$motopressCESettings['plugin_root'] = WP_PLUGIN_DIR;
$motopressCESettings['plugin_root_url'] = WP_PLUGIN_URL;
$motopressCESettings['plugin_name'] = 'motopress-content-editor';
$pluginData = get_plugin_data($motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/'.$motopressCESettings['plugin_name'].'.php', false, false);
$motopressCESettings['plugin_version'] = $pluginData['Version'];
$motopressCESettings['update_path'] = 'http://www.getmotopress.com/motopress-content-editor-update/?time='.time();

$theme = wp_get_theme();
$motopressCESettings['theme_root'] = $theme->get_theme_root();
$motopressCESettings['theme_root_url'] = get_theme_root_uri();
$motopressCESettings['current_theme'] = $theme->get_stylesheet();
$motopressCESettings['parent_theme'] = ($theme->parent()) ? $theme->parent()->get_stylesheet() : $theme->get_stylesheet();

$motopressCESettings['theme_static_root'] = $motopressCESettings['theme_root'] . '/' . $motopressCESettings['current_theme'] . '/static';
$motopressCESettings['theme_static_root_url'] = $motopressCESettings['theme_root_url'] . '/' . $motopressCESettings['current_theme'] . '/static';

$motopressCESettings['theme_wrapper_root'] = $motopressCESettings['theme_root'] . '/' . $motopressCESettings['current_theme'] . '/wrapper';
$motopressCESettings['theme_wrapper_root_url'] = $motopressCESettings['theme_root_url'] . '/' . $motopressCESettings['current_theme'] . '/wrapper';

$motopressCESettings['theme_loop_root'] = $motopressCESettings['theme_root'] . '/' . $motopressCESettings['current_theme'] . '/loop';
$motopressCESettings['parent_theme_loop_root'] = $motopressCESettings['theme_root'] . '/' . $motopressCESettings['parent_theme'] . '/loop';
$motopressCESettings['theme_loop_root_url'] = $motopressCESettings['theme_root_url'] . '/'.$motopressCESettings['current_theme'] . '/loop';

$motopressCESettings['lang'] = get_option('motopress-language') ? get_option('motopress-language') : 'en.json';

$motopressCESettings['load_scripts_url'] = $motopressCESettings['admin_url'] . 'load-scripts.php?c=0&load=jquery-ui-core,jquery-ui-widget,jquery-ui-mouse,jquery-ui-position,jquery-ui-draggable,jquery-ui-droppable,jquery-ui-resizable,jquery-ui-button,jquery-ui-dialog';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
$wpIncludesUrl = str_replace($protocol.'://'.$_SERVER['HTTP_HOST'], '', includes_url());
$motopressCESettings['motopress_localize'] = array(
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'wpJQueryUrl' => $wpIncludesUrl . 'js/jquery/',
    'wpCssUrl' => $wpIncludesUrl . 'css/',
    'pluginVersion' => $motopressCESettings['plugin_version'],
    'pluginVersionParam' => '?ver=' . $motopressCESettings['plugin_version']
);