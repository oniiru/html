<?php
require_once dirname(__FILE__) . '/class-payperdownload.php';
$ppd = new PayPerDownload();

add_shortcode('stripe_pay_per_download', 'stripe_pay_per_download');
function stripe_pay_per_download($atts, $content = null) {
	global $ppd;
	
	extract(shortcode_atts(array(
		"id"			=> null
	), $atts));
	
	if(is_null($id)) {
		return "<p>ID cannot be null</p>";
	}

	$product = $ppd->get_product($id);
	if(is_null($product)) {
		return "<p>ID does not exist.</p>";
	}
	$name = $product['name'];
	return "<input name='product' value='$name' type='hidden' /><input name='dlppd_product' value='$id' type='hidden' />";
}

?>