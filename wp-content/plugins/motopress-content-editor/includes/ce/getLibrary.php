<?php
if (!isset($motopressCERequirements)) $motopressCERequirements = new MPCERequirements();
if (!isset($motopressCELang)) $motopressCELang = motopressCEGetLanguageDict();

function motopressCEGetLibrary() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/Library.php';

    global $motopressCELang;
    global $motopressCESettings;
    $errors = array();

    $motopressCELibrary = new MPCELibrary();
    $json = $motopressCELibrary->toJson();

    if ($json) {
        echo $json;
    } else {
        $errors[] = $motopressCELang->CELibraryError;
    }

    if (!empty($errors)) {
        if ($motopressCESettings['debug']) {
            print_r($errors);
        } else {
            motopressCESetError($motopressCELang->CELibraryError);
        }
    }

    exit;
}