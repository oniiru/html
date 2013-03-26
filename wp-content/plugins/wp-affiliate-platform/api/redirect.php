<?php
include_once('../../../../wp-load.php');

if(isset($_GET['url'])&&isset($_GET['ap_id']))
{
	$referrer_id=trim(strip_tags($_GET['ap_id']));
	$url=trim(strip_tags($_GET['url']));

	if(strlen($referrer_id) > 0 )
	{
		$campaign_id = strip_tags($_GET['c_id']);
		record_click($referrer_id,$campaign_id);				
		//record_click($referrer_id);
	}
	header('Location: ' . $url);
	exit;
}
?>