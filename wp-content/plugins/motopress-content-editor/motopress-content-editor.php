<?php
/*
Plugin Name: MotoPress Content Editor
Plugin URI: http://www.getmotopress.com/
Description: MotoPress content builder makes the process of post editing easy and fast. Thanks to drag and drop functionality it's possible to manage your article, add different content elements, replace, edit them and see the ready to be published result right in the editor area.
Version: 1.1.3
Author: MotoPress
Author URI: http://www.getmotopress.com/
License: Copyright MotoPress. The Regular License grants you a non-exclusive non-transferable permission to use Content Editor in one single WordPress website. Distribution of the source files is not permitted.
*/

require_once 'includes/Requirements.php';
require_once 'includes/settings.php';

add_action('wp_head', 'motopressCEWpHead', 0);
add_filter('the_content', 'motopressCEContentWrapper');

if (isset($_GET['motopress-ce']) && $_GET['motopress-ce'] == 1) {
    add_filter('show_admin_bar', '__return_false');
}
remove_filter('the_content', 'wpautop');
add_filter('the_content', 'wpautop', 11);

function motopressCEContentWrapper($content) {
    return '<div class="motopress-content-wrapper">' . $content . '</div>';
}
function motopressCEWpHead() {
    global $motopressCESettings;

    wp_register_style('mpce-bootstrap-grid', plugin_dir_url(__FILE__).'bootstrap/bootstrap-grid.min.css', null, $motopressCESettings['plugin_version']);
    wp_enqueue_style('mpce-bootstrap-grid');

    wp_register_style('mpce-theme', plugin_dir_url(__FILE__) . 'includes/css/theme.css', null, $motopressCESettings['plugin_version']);
    wp_enqueue_style('mpce-theme');

    wp_register_style('mpce-flexslider', plugin_dir_url(__FILE__).'flexslider/flexslider.min.css', null, $motopressCESettings['plugin_version']);

    if (!wp_script_is('jquery')) {
        wp_enqueue_script('jquery');
    }

    wp_register_script('mpce-flexslider', plugin_dir_url(__FILE__).'flexslider/jquery.flexslider-min.js', array('jquery'), $motopressCESettings['plugin_version']);

    //wp_register_script('mpce-theme', plugin_dir_url(__FILE__).'includes/js/theme.js', array('jquery'), $motopressCESettings['plugin_version']);
    //wp_enqueue_script('mpce-theme');

    if (isset($_GET['motopress-ce']) && $_GET['motopress-ce'] == 1) {
        global $wp_scripts;
        $migrate = false;
        if (version_compare($wp_scripts->registered['jquery']->ver, MPCERequirements::MIN_JQUERY_VER, '<')) {
            $wpjQueryVer = motopressCEGetWPScriptVer('jQuery');
            wp_deregister_script('jquery');
            wp_register_script('jquery', includes_url() . 'js/jquery/jquery.js', array(), $wpjQueryVer);
            wp_enqueue_script('jquery');

            if (version_compare($wpjQueryVer, '1.9.0', '>')) {
                if (wp_script_is('jquery-migrate', 'registered')) {
                    wp_enqueue_script('jquery-migrate', array('jquery'));
                    $migrate = true;
                }
            }
        }

        wp_register_script('mpce-no-conflict', plugin_dir_url(__FILE__).'includes/js/noConflict.js', array('jquery'), $motopressCESettings['plugin_version']);
        wp_enqueue_script('mpce-no-conflict');
        $jQueryOffset = array_search('jquery', $wp_scripts->queue) + 1;
        $index = ($migrate) ? array_search('jquery-migrate', $wp_scripts->queue) : array_search('mpce-no-conflict', $wp_scripts->queue);
        $length = $index - $jQueryOffset;
        $slice = array_splice($wp_scripts->queue, $jQueryOffset, $length);
        $wp_scripts->queue = array_merge($wp_scripts->queue, $slice);

        wp_register_script('mpce-tinymce', plugin_dir_url(__FILE__).'tinymce/tinymce.min.js', null, $motopressCESettings['plugin_version']);
        wp_enqueue_script('mpce-tinymce');

        wp_enqueue_style('mpce-flexslider');
        wp_enqueue_script('mpce-flexslider');
    }
}

require_once 'includes/ce/Shortcode.php';
$shortcode = new MPCEShortcode();
$shortcode->register();

if (!is_admin()) return;

require_once 'includes/getLanguageDict.php';
require_once 'contentEditor.php';
require_once 'motopressOptions.php';
//require_once 'includes/settings.php';
require_once 'includes/Flash.php';
require_once 'includes/AutoUpdate.php';

add_action('admin_init', 'motopressCEInit');
add_action('admin_menu', 'motopressCEMenu', 11);

function motopressCEInit() {
    global $motopressCESettings;

    wp_register_style('mpce-style', plugin_dir_url(__FILE__) . 'includes/css/style.css', null, $motopressCESettings['plugin_version']);
    wp_register_script('mpce-detect-browser', plugin_dir_url(__FILE__).'mp/core/detectBrowser/detectBrowser.js', null, $motopressCESettings['plugin_version']);

    wp_enqueue_script('mpce-detect-browser');

    new MPCEAutoUpdate($motopressCESettings['plugin_version'], $motopressCESettings['update_path'], $motopressCESettings['plugin_name'].'/'.$motopressCESettings['plugin_name'].'.php');
    add_action('in_plugin_update_message-'.$motopressCESettings['plugin_name'].'/'.$motopressCESettings['plugin_name'].'.php', 'motopressCEAddUpgradeMessageLink', 20, 2);

    motopressCERegisterHtmlAttributes();
}

function motopressCEAddUpgradeMessageLink($plugin_data, $r) {
    global $motopressCELang;
    echo ' ' . strtr($motopressCELang->CEDownloadMessage, array('%link%' => $r->url));
}

function motopressCERegisterHtmlAttributes() {
    global $allowedposttags;

    if (isset($allowedposttags['div']) && is_array($allowedposttags['div'])) {
        $attributes = array_fill_keys(array_values(MPCEShortcode::$attributes), true);
        $allowedposttags['div'] = array_merge($allowedposttags['div'], $attributes);
    }
}

add_filter('tiny_mce_before_init', 'motopressCERegisterTinyMCEHtmlAttributes', 10, 1);

function motopressCERegisterTinyMCEHtmlAttributes($options) {
    global $motopressCESettings;

    if (!isset($options['extended_valid_elements'])) {
        $options['extended_valid_elements'] = '';
    }

    $attributes = implode('|', array_values(MPCEShortcode::$attributes));
    $options['extended_valid_elements'] .= ',div[' . $attributes . ']';

    return $options;
}

function motopressCEMenu() {
    global $motopressCELang;
    $motopressCELang = motopressCEGetLanguageDict();
    global $motopressCERequirements;
    $motopressCERequirements = new MPCERequirements();
    global $motopressCEIsjQueryVer;
    $motopressCEIsjQueryVer = motopressCECheckjQueryVer();

    $mainMenuSlug = 'motopress';

    $mainMenuExists = has_action('admin_menu', 'motopressMenu');
    if (!$mainMenuExists) {
        $mainPage = add_menu_page('MotoPress', 'MotoPress', 'read', $mainMenuSlug, 'motopressCE', plugin_dir_url(__FILE__) . 'favicon.ico');
    } else {
        $optionsHookname = get_plugin_page_hookname('motopress_options', $mainMenuSlug);
        remove_action($optionsHookname, 'motopressOptions');
        remove_submenu_page('motopress', 'motopress_options');
    }
    $mainPage = add_submenu_page($mainMenuSlug, $motopressCELang->CE, $motopressCELang->CE, 'read', $mainMenuExists ? 'motopress_content_editor' : 'motopress', 'motopressCE');
    $optionsPage = add_submenu_page($mainMenuSlug, $motopressCELang->motopressOptions, $motopressCELang->motopressOptions, 'manage_options', 'motopress_options', 'motopressCEOptions');

    add_action('load-'.$optionsPage,'motopressCESettingsSave');
    add_action('admin_print_scripts-post.php', 'motopressCEAddTools');
    add_action('admin_print_scripts-post-new.php', 'motopressCEAddTools');
    add_action('admin_print_styles-' . $mainPage, 'motopressCEAdminStylesAndScripts');
    add_action('admin_print_styles-' . $optionsPage, 'motopressCEAdminStylesAndScripts');
}

function motopressCEAdminStylesAndScripts() {
    wp_enqueue_style('mpce-style');
}

function motopressCE() {
    motopressCEShowWelcomeScreen();
}

function motopressCEShowWelcomeScreen() {
    global $motopressCERequirements;
    global $motopressCESettings;
    global $motopressCELang;
    echo '<div class="motopress-title-page">';
    echo '<img id="motopress-logo" src="'.plugin_dir_url(__FILE__).'images/logo-large.png?ver='.$motopressCESettings['plugin_version'].'" />';
    echo '<p class="motopress-description">' . $motopressCELang->motopressDescription . '</p>';

    global $motopressCEIsjQueryVer;
    if (!$motopressCEIsjQueryVer) {
        MPCEFlash::setFlash(strtr($motopressCELang->jQueryVerNotSupported, array('%minjQueryVer%' => MPCERequirements::MIN_JQUERY_VER, '%minjQueryUIVer%' => MPCERequirements::MIN_JQUERYUI_VER)), 'error');
    }

    echo '<p><div class="alert alert-error" id="motopress-browser-support-msg" style="display:none;">'.$motopressCELang->browserNotSupported.'</div></p>';

    echo '<div class="motopress-block"><p class="motopress-title">'.$motopressCELang->CE.'</p>';
    echo '<p class="motopress-sub-description">'.$motopressCELang->CEDescription.'</p>';
    echo '<a href="'.admin_url('post-new.php?post_type=page').'" target="_self" id="motopress-ce-link"><img id="motopress-ce" src="'.plugin_dir_url(__FILE__).'images/ce/ce.png?ver='.$motopressCESettings['plugin_version'].'" /></a></div>';

	?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            if (Browser.IE || Browser.Opera) {
//                $('#motopress-layout-editor-btn').hide();
                $('.motopress-block #motopress-ce-link')
                    .attr('href', 'javascript:void(0);')
                    .css({ cursor: 'default' });
                $('#motopress-browser-support-msg').show();
            }
        });
    </script>
    <?php
}

function motopressCEInstall() {
    add_option('motopress-language', 'en.json');
    add_option('motopress-ce-options', array('post', 'page'));
}
register_activation_hook(__FILE__, 'motopressCEInstall');

function motopressCECheckjQueryVer() {
    $jQueryVer = motopressCEGetWPScriptVer('jQuery');
    $jQueryUIVer = motopressCEGetWPScriptVer('jQueryUI');

    return (version_compare($jQueryVer, MPCERequirements::MIN_JQUERY_VER, '>=') && version_compare($jQueryUIVer, MPCERequirements::MIN_JQUERYUI_VER, '>=')) ? true : false;
}

function motopressCEGetWPScriptVer($script) {
    $path = ABSPATH . WPINC;
    $ver = false;
    switch ($script) {
        case 'jQuery':
            $path .= '/js/jquery/jquery.js';
            break;
        case 'jQueryUI':
            $path .= '/js/jquery/ui/jquery.ui.core.min.js';
            break;
    }
    if (is_file($path)) {
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if ($content) {
                $pattern = '/v((\d+\.{1}){1}(\d+){1}(\.{1}\d+)?)/is';
                preg_match($pattern, $content, $matches);
                if (!empty($matches[1])) {
                    $ver = $matches[1];
                }
            }
        }
    }
    return $ver;
}