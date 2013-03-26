<?php

require_once('../../../../../wp-load.php');
$upload_dir = wp_upload_dir();
$file_names = '';
$upload_path = $upload_dir['path'];
$upload_path = str_replace('\\', '/', $upload_path);

foreach ($_FILES["images"]["error"] as $key => $error) {
	if ($error == UPLOAD_ERR_OK) {
		$name = $_FILES["images"]["name"][$key];
		move_uploaded_file($_FILES["images"]["tmp_name"][$key], $upload_path . '/' . $_FILES['images']['name'][$key]);
	}
	$file_names .= $_FILES['images']['name'][$key];
}

echo json_encode(array('message' => '<h4>Successfully Uploaded Images</h4>', 'file_name' => $file_names));
