<?php
function motopressCEGetWpSettings() {
    require_once dirname(__FILE__).'/verifyNonce.php';
    require_once dirname(__FILE__).'/settings.php';
    require_once dirname(__FILE__).'/access.php';

    global $motopressCESettings;
    echo json_encode($motopressCESettings);
    exit;
}