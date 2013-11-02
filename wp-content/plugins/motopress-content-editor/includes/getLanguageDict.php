<?php
require_once 'settings.php';

function motopressCEGetLanguageDict() {
    global $motopressCESettings;
    $lang = isset($motopressCESettings['lang']) ? $motopressCESettings['lang'] : 'en.json';

    $contents = json_decode(file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $lang));
    return $contents->lang;
}