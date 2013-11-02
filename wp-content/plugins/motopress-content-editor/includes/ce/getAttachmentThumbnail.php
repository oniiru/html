<?php
function motopressCEGetAttachmentThumbnail() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';

    $motopressCELang = motopressCEGetLanguageDict();

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int) trim($_POST['id']);
        $attachment = get_post($id);
        if (!empty($attachment) && $attachment->post_type === 'attachment') {
            if (wp_attachment_is_image($id)) {
                $attachmentImageSrc = wp_get_attachment_image_src($id, 'medium');
                if (isset($attachmentImageSrc[0]) && !empty($attachmentImageSrc[0])) {
                    echo $attachmentImageSrc[0];
                } else {
                    motopressCESetError($motopressCELang->CEAttachmentImageSrc);
                }
            } else {
                motopressCESetError($motopressCELang->CEAttachmentNotImage);
            }
        } else {
            motopressCESetError($motopressCELang->CEAttachmentEmpty);
        }
    } else {
        motopressCESetError($motopressCELang->CEAttachmentThumbnailError);
    }
    exit;
}