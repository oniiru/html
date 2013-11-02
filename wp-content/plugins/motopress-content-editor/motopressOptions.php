<?php

function motopressCEOptions() {
    global $motopressCELang;

    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        add_settings_error(
            'motopressSettings',
            esc_attr('settings_updated'),
            $motopressCELang->OptMsgUpdated,
            'updated'
        );
    }

    echo '<div class="wrap">';
    echo '<div class="icon32" id="icon-options-general"><br></div>';
    echo '<h2>'.$motopressCELang->motopressOptions.'</h2>';
    settings_errors('motopressSettings', false);
    echo '<form actoin="options.php" method="POST">';
//    settings_fields('motopressOptionsFields');
    do_settings_sections('motopress_options');
    echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="'.$motopressCELang->save.'" /></p>';
    echo '</form>';
    echo '</div>';
}

add_action('admin_init', 'motopressCEInitOptions');
function motopressCEInitOptions() {
    global $motopressCELang;

    register_setting('motopressLanguageOptionsFields', 'motopressLanguageOptions');
    add_settings_section('motopressLanguageOptionsFields', '', 'motopressCELanguageOptionsSecTxt', 'motopress_options');
    add_settings_field('motopressLangeuageOptions', $motopressCELang->language, 'motopressCELanguageSettings', 'motopress_options', 'motopressLanguageOptionsFields');

//    register_setting('motopressCEOptionsFields', 'motopressCEOptions'/*, 'plugin_options_validate'*/);
    register_setting('motopressCEOptionsFields', 'motopressContentEditorOptions'/*, 'plugin_options_validate'*/);
    add_settings_section('motopressCEOptionsFields', '', 'motopressCEOptionsSecTxt', 'motopress_options');
    add_settings_field('motopressContentType', $motopressCELang->CEOptContentTypes, 'motopressCEContentTypeSettings', 'motopress_options', 'motopressCEOptionsFields');
}


function motopressCELanguageOptionsSecTxt() {}
function motopressCELanguageSettings() {
    $curLang = get_option('motopress-language');
    $languageFileList = glob(plugin_dir_path(__FILE__) . 'lang/*.json');
    echo '<select class="motopress-language" name="language" id="language">';
    foreach ($languageFileList as $path) {
        $file = basename($path);
        $fileContents = file_get_contents($path);
        $fileContentsJSON = json_decode($fileContents);
        $languageName = $fileContentsJSON->{'name'};
        $selected = ($file == $curLang) ? ' selected' : '';
        echo '<option value="'.$file.'"'.$selected.'>' . $languageName . '</option>';
    }
    echo '</select>';
    echo '<br/><br/><br/>';
}

function motopressCEOptionsSecTxt() {}
function motopressCEContentTypeSettings() {
    $postTypes = get_post_types(array('public' => true));
    $excludePostTypes = array('attachment' => 'attachment');
    $postTypes = array_diff_assoc($postTypes, $excludePostTypes);
    $checkedPostTypes = get_option('motopress-ce-options');
    if (!$checkedPostTypes) $checkedPostTypes = array();

    foreach ($postTypes as $key => $val) {
        if (post_type_supports($key, 'editor')) {
            $checked = '';
            if (in_array($key, $checkedPostTypes)) {
                $checked = 'checked="checked"';
            }
            echo '<label><input type="checkbox" name="post_types[]" value="'.$key.'" '.$checked.' /> '.$val.'</label><br/>';
        }
    }
}

function motopressCESettingsSave() {
    if (!empty($_POST)) {
        global $motopressCESettings;

        // Language
        if (isset($_POST['language']) && !empty($_POST['language'])) {
            $language = $_POST['language'];
            update_option('motopress-language', $language);
            $motopressCESettings['lang'] = $language;
        }

        // Post types
        $postTypes = array();
        if (isset($_POST['post_types']) and count($_POST['post_types']) > 0) {
            $postTypes = $_POST['post_types'];
        }
        update_option('motopress-ce-options', $postTypes);
        wp_redirect($_SERVER['PHP_SELF'].'?page='.$_GET['page'].'&settings-updated=true');
    }
}