<?php

/* * ******************************************
 * **       THIS IS NOT A FREE PLUGIN       ***
 * ** DO NOT COPY ANY CODE FROM THIS PLUGIN ***
 * ******************************************* */
if (!isset($_SESSION)) { 
	session_start();
}

define('WP_AFF_DATE_CSS_URL', WP_AFF_PLATFORM_URL . '/lib/date/dhtmlxcalendar.css');
define('WP_AFF_COM_JS_URL', WP_AFF_PLATFORM_URL . '/lib/date/dhtmlxcommon.js');
define('WP_AFF_CAL_JS_URL', WP_AFF_PLATFORM_URL . '/lib/date/dhtmlxcalendar.js');
define('WP_AFF_DATE_IMG_URL', WP_AFF_PLATFORM_URL . '/lib/date/codebase/imgs/');

define('WP_AFF_CLICKS_TBL_NAME', $wpdb->prefix . "affiliates_clickthroughs_tbl");
define('WP_AFF_AFFILIATES_TBL_NAME', $wpdb->prefix . "affiliates_tbl");
define('WP_AFF_SALES_TBL_NAME', $wpdb->prefix . "affiliates_sales_tbl");
define('WP_AFF_PAYOUTS_TBL_NAME', $wpdb->prefix . "affiliates_payouts_tbl");
define('WP_AFF_BANNERS_TBL_NAME', $wpdb->prefix . "affiliates_banners_tbl");
define('WP_AFF_LEAD_CAPTURE_TBL_NAME', $wpdb->prefix . "affiliates_leads_tbl");
define('WP_AFF_RELATIONS_TBL_NAME', $wpdb->prefix . "affiliates_relations_tbl");

$aff_language = get_option('wp_aff_language');
if (!empty($aff_language))
	$language_file = "affiliates/lang/" . $aff_language;
else
	$language_file = "affiliates/lang/eng.php";
include_once($language_file);

include_once('wp_aff_advanced_configs.php');
include_once('wp_aff_db_access_class.php');
include_once('wp_aff_debug_handler.php');
include_once('wp_aff_utility_functions.php');
include_once('wp_aff_includes_3rd_party_integration.php');
include_once('wp_aff_includes.php');
include_once('wp_aff_includes2.php');
include_once('wp_affiliate_login_widget.php');
include_once('affiliate_platform_affiliate_view.php');

/* * * PDT Stuff for PayPal transaction commission awarding ** */
if (isset($_GET['tx']) && isset($_GET['amt'])) {
	wp_affiliate_log_debug("PayPal PDT detected - checking if commission need to be tracked...", true);
	$auth_token = get_option('wp_aff_pdt_identity_token');
	if (get_option('wp_aff_enable_3rd_party') != '' && !empty($auth_token)) {
		wp_affiliate_log_debug("Need to process commission for this sale...", true);
		//Process PDT to award commission
		$_SESSION['aff_tx_result_error_msg'] = "";
		$req = 'cmd=_notify-synch';
		$tx_token = strip_tags($_GET['tx']);
		$req .= "&tx=$tx_token&at=$auth_token";

		// post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		$sandbox_enabled = get_option('wp_aff_sandbox_mode');
		if ($sandbox_enabled != '') {
			wp_affiliate_log_debug("Sandbox mode is enabled", true);
			$host_url = 'www.sandbox.paypal.com';
			$uri = 'ssl://' . $host_url;
			$port = '443';
			$fp = fsockopen($uri, $port, $err_num, $err_str, 30);
		} else {
			$host_url = 'www.paypal.com';
			$fp = fsockopen($host_url, 80, $errno, $errstr, 30);
		}
		//$fp = fsockopen ($host_url, 80, $errno, $errstr, 30);
		// If possible, securely post back to paypal using HTTPS
		// Your PHP server will need to be SSL enabled
		// $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

		if (!$fp) {
			wp_affiliate_log_debug("Error! HTTP ERROR... could not establish a connection to PayPal for verification.", false);
			echo "<br />Error! HTTP ERROR... could not establish a connection to PayPal for verification!";
			return;
		} else {
			fputs($fp, $header . $req);
			// read the body data
			$res = '';
			$headerdone = false;
			while (!feof($fp)) {
				$line = fgets($fp, 1024);
				if (strcmp($line, "\r\n") == 0) {
					// read the header
					$headerdone = true;
				} else if ($headerdone) {
					// header has been read. now read the contents
					$res .= $line;
				}
			}
			// parse the data
			$lines = explode("\n", $res);
			$keyarray = array();
			if (strcmp($lines[0], "SUCCESS") == 0) {
				for ($i = 1; $i < count($lines); $i++) {
					list($key, $val) = explode("=", $lines[$i]);
					$keyarray[urldecode($key)] = urldecode($val);
				}
			} else if (strcmp($lines[0], "FAIL") == 0) {
				wp_affiliate_log_debug("Error! PDT verification failed! Could not verify the authenticity of the payment with PayPal!", false);
				echo "<br />Error! PDT verification failed! Could not verify the authenticity of the payment with PayPal!";
				return;
			}
		}
		fclose($fp);
		global $wpdb;
		$aff_sales_table = WP_AFF_SALES_TBL_NAME;
		$txn_id = $keyarray['txn_id'];
		$resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
		if ($resultset) {
			//Commission for this transaction has already been awarded so no need to do anything.
			wp_affiliate_log_debug("Commission for this transaction has already been awarded so no need to do anything. Transaction ID:" . $txn_id, true);
		} else {
			wp_affiliate_log_debug("Calling process PDT function.", true);
			wp_aff_process_PDT_payment_data($keyarray);
		}
	} else {
		wp_affiliate_log_debug("Nothing to do... 3rd party integration is disabled or the auth token is missing.", true);
	}
}

/* 3rd party cart commission award handler */

function wp_affiliate_process_cart_commission_handler($order_details) {
	$sale_amount = $order_details['sale_amt'];
	$txn_id = $order_details['txn_id'];
	$item_id = $order_details['item_id'];
	$buyer_email = $order_details['buyer_email'];
	$referrer = $order_details['referrer'];
	if (empty($referrer)) {
		$referrer = wp_affiliate_get_referrer();
	}
	global $wpdb;
	$aff_sales_table = WP_AFF_SALES_TBL_NAME;
	$resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
	if ($resultset) {
		//Commission for this transaction has already been awarded so no need to do anything.
	} else {
		$db_data = "Commission tracking debug data:" . $referrer . "|" . $sale_amount . "|" . $txn_id . "|" . $buyer_email;
		wp_affiliate_log_debug($db_data, true);

		if (!empty($referrer)) {
			wp_aff_award_commission($referrer, $sale_amount, $txn_id, $item_id, $buyer_email);
		} else {
			//Not an affiliate conversion
		}
	}
}

/* Affiliate View Option 2 POST Data processing */

function wp_aff_affiliate_view2_login_handler() {
	if (isset($_POST['wpAffDoLogin'])) {
		if ($_POST['userid'] != '' && $_POST['password'] != '') {
			// protection against script injection
			$userid = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['userid']);
			$password = $_POST['password'];
			include_once(ABSPATH . WPINC . '/class-phpass.php');
			$wp_hasher = new PasswordHash(8, TRUE);

			global $wpdb;
			$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
			$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$userid'", OBJECT);

			setcookie("cart_in_use", "true", time() + 21600, "/", COOKIE_DOMAIN); //set the cookie for W3 Total Cache

			if ($wp_hasher->CheckPassword($password, $result->pass)) {
				// this sets variables in the session
				$_SESSION['user_id'] = $userid;
				setcookie("user_id", $userid, time() + 60 * 60 * 6, "/", COOKIE_DOMAIN); //set cookie for 6 hours
				//set a cookie witout expiry until 60 days
				if (isset($_POST['remember'])) {
					setcookie("user_id", $_SESSION['user_id'], time() + 60 * 60 * 24 * 60, "/", COOKIE_DOMAIN);
				}

				$target_url = wp_aff_view_get_url_with_separator("members_only");
				header("Location: " . $target_url);
				exit;
			} else {
				$msg = urlencode("Invalid Login. Please try again with correct user name and password. ");
				$target_url = wp_aff_view_get_url_with_separator("login&msg=" . $msg);
				header("Location: " . $target_url);
				exit;
			}
		}
	}
}

function wp_aff_affiliate_view2_logout_handler() {
	if (isset($_GET['wp_affiliate_view']) && $_GET['wp_affiliate_view'] == 'logout') {
		/*		 * * Delete the cookies and unset the session data ** */
		$aff_id = $_SESSION['user_id'];
		unset($_SESSION['user_id']);
		setcookie("user_id", '', time() - 60 * 60 * 24 * 60, "/", COOKIE_DOMAIN);

		$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
		if ($wp_aff_platform_config->getValue('wp_aff_auto_logout_aff_account') == '1') {
			wp_clear_auth_cookie();
		}

		//Logout eMember account if using auto affiliate log-in option
		if (function_exists('wp_eMember_install')) {
			global $emember_config;
			$emember_config = Emember_Config::getInstance();
			$eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
			if ($eMember_auto_affiliate_account_login) {
				wp_emem_logout();
			}
		}

		$curr_page_after_logout = wp_aff_current_page_url();
		$logout_get_val_pos = strpos($curr_page_after_logout, "?wp_affiliate_view");
		if (empty($logout_get_val_pos)) {
			$logout_get_val_pos = strpos($curr_page_after_logout, "&wp_affiliate_view");
		}
		$redirect_page_after_logout = substr($curr_page_after_logout, 0, $logout_get_val_pos);
		echo '<meta http-equiv="refresh" content="0;url=' . $redirect_page_after_logout . '" />';
		exit;
	}
}

/* End of Affiliate View Option 2 POST Data processing */

function wp_aff_award_custom_commission_handler($atts) {
	$sale_amount = "0"; //Commission will be calcualted based off this amount
	$txn_id = "A unique transaction id"; //can be anything
	$item_id = "Id of this item for identification"; //can be anything
	$buyer_email = "email address of the buyer";


	if (!empty($_SESSION['ap_id'])) {
		$referrer = $_SESSION['ap_id'];
	} else if (isset($_COOKIE['ap_id'])) {
		$referrer = $_COOKIE['ap_id'];
	}

	if (!empty($referrer)) {
		wp_aff_award_commission($referrer, $sale_amount, $txn_id, $item_id, $buyer_email);
	} else {
		//Not an affiliate conversion
	}
	return "";
}

function wp_aff_login_handler($atts) {
	return aff_login_widget();
}

function wp_aff_login_onpage_version_handler($atts) {
	extract(shortcode_atts(array(
				'url' => '',
					), $atts));
	return aff_login_widget_onpage_version($url);
}

function aff_get_cookie_life_time() {
	$cookie_expiry = get_option('wp_aff_cookie_life');
	if (!empty($cookie_expiry)) {
		$cookie_life_time = time() + $cookie_expiry * 86400;
	} else {
		$cookie_life_time = time() + 30 * 86400;
	}
	return $cookie_life_time;
}

if (isset($_GET['ap_id'])) {
	/* Common stripping to avoid any type of hack */
	$referrer_id = trim(strip_tags($_GET['ap_id']));
	if (strlen($referrer_id) > 0) {
		$campaign_id = isset($_GET['c_id']) ? strip_tags($_GET['c_id']) : '';
		if (WP_AFFILIATE_DO_NOT_OVERRIDE_AFFILIATE_COOKIE == '1') {
			if (isset($_COOKIE['ap_id']) && $_COOKIE['ap_id'] != $referrer_id) {
				//Do not tract this click as the admin doesn't want to override
				wp_affiliate_log_debug("Not tracking this click since the admin has enabled WP_AFFILIATE_DO_NOT_OVERRIDE_AFFILIATE_COOKIE", true);
			} else {
				record_click($referrer_id, $campaign_id);
			}
		} else {
			record_click($referrer_id, $campaign_id);
		}
	}

	if (WP_AFFILIATE_AUTO_REDIRECT_TO_NOT_AFFILIATE_URL == '1') {
		wp_affiliate_log_debug("Redirecting to non affiliate URL since the admin has enabled WP_AFFILIATE_AUTO_REDIRECT_TO_NOT_AFFILIATE_URL", true);
		wp_aff_redirect_to_non_affiliate_url();
	}
}

function record_click($referrer_id, $campaign_id = '') {
	global $wpdb;
	$cookie_life_time = aff_get_cookie_life_time();

	$domain_url = $_SERVER['SERVER_NAME'];
	$cookie_domain = str_replace("www", "", $domain_url);
	setcookie('ap_id', $referrer_id, $cookie_life_time, "/", $cookie_domain);
	if (!empty($campaign_id)) {
		setcookie('c_id', $campaign_id, $cookie_life_time, "/", $cookie_domain);
	}
	if (function_exists('wp_cache_serve_cache_file')) {//WP Super cache workaround
		setcookie("comment_author_", "wp_affiliate", time() + 21600, "/", $cookie_domain);
	}

	$_SESSION['ap_id'] = $referrer_id;
	if (!empty($campaign_id)) {
		$_SESSION['c_id'] = $campaign_id;
	}

	$clientdate = (date("Y-m-d"));
	$clienttime = (date("H:i:s"));
	$clientbrowser = $_SERVER['HTTP_USER_AGENT'];
	$clientip = $_SERVER['REMOTE_ADDR'];
	$clienturl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$current_page = wp_aff_current_page_url();

	// Ignore bots and wordpress trackbacks
	if (strpos($clientbrowser, "WordPress") !== false || strpos($clientbrowser, "bot") !== false || strpos($current_page, "?secret=") !== false) {
		return;
	}

	$e_refid = $wpdb->escape($referrer_id);
	$e_date = $wpdb->escape($clientdate);
	$e_time = $wpdb->escape($clienttime);
	$e_browser = $wpdb->escape($clientbrowser);
	$e_ip = $wpdb->escape($clientip);
	$e_url = $wpdb->escape($clienturl);

	$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
	if ($e_url != $current_page) {
		if (empty($e_url)) {
			$e_url = $current_page; //"Data unavailable - May be the URL was entered directly in the browser";
		}
		if (wp_aff_true_click()) {
			$updatedb = "INSERT INTO $affiliates_clickthroughs_table_name (refid,date,time,browser,ipaddress,referralurl,buy,campaign_id) VALUES ('$e_refid', '$e_date', '$e_time', '$e_browser', '$e_ip', '$e_url', '','$campaign_id')";
			$results = $wpdb->query($updatedb);
		}
	}
}

function wp_aff_true_click($clientip = '') {
	global $wpdb;
	$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;

	$cooldown_time_in_unix = mktime(date("H"), date("i"), date("s") - 5);
	$cooldown_time = date("H:i:s", $cooldown_time_in_unix);
	$cur_time = date("H:i:s");
	if (empty($clientip)) {
		$clientip = $_SERVER['REMOTE_ADDR'];
	}
	$find = $wpdb->get_results("SELECT * FROM $affiliates_clickthroughs_table_name WHERE time between '$cooldown_time' and '$cur_time' and ipaddress='$clientip'", OBJECT);
	if ($find) {
		return false;
	}
	//Set the following value to true if you want to track one click per IP address
	$track_unique_clicks = false;
	if ($track_unique_clicks) {
		$find = $wpdb->get_results("SELECT * FROM $affiliates_clickthroughs_table_name WHERE ipaddress = '$clientip'", OBJECT);
		if ($find) {
			return false;
		}
	}
	return true;
}

function wp_aff_true_sale($clientip = '') {
	global $wpdb;
	$affiliates_sales_table_name = WP_AFF_SALES_TBL_NAME;

	$cooldown_time_in_unix = mktime(date("H"), date("i"), date("s") - 10);
	$cooldown_time = date("H:i:s", $cooldown_time_in_unix);
	$cur_time = date("H:i:s");
	if (empty($clientip)) {
		$clientip = $_SERVER['REMOTE_ADDR'];
	}
	$find = $wpdb->get_results("SELECT * FROM $affiliates_sales_table_name WHERE time between '$cooldown_time' and '$cur_time' and ipaddress='$clientip'", OBJECT);
	if ($find) {
		return false;
	}
	return true;
}

function wp_aff_record_remote_click($referrer_id, $clientbrowser, $clientip, $clienturl, $campaign_id = '') {
	global $wpdb;
	$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;

	$clientdate = (date("Y-m-d"));
	$clienttime = (date("H:i:s"));

	// Ignore bots and wordpress trackbacks
	if (strpos($clientbrowser, "WordPress") !== false || strpos($clientbrowser, "bot") !== false) {
		return;
	}

	$e_refid = $wpdb->escape($referrer_id);
	$e_date = $wpdb->escape($clientdate);
	$e_time = $wpdb->escape($clienttime);
	$e_browser = $wpdb->escape($clientbrowser);
	$e_ip = $wpdb->escape($clientip);
	$e_url = $wpdb->escape($clienturl);

	if (wp_aff_true_click()) {
		$updatedb = "INSERT INTO $affiliates_clickthroughs_table_name (refid,date,time,browser,ipaddress,referralurl,buy,campaign_id) VALUES ('$e_refid', '$e_date', '$e_time', '$e_browser', '$e_ip', '$e_url', '','$campaign_id')";
		$results = $wpdb->query($updatedb);
	}
}

function record_click_for_eStore_cart($referrer_id) {
	global $wpdb;
	$cookie_life_time = aff_get_cookie_life_time();

	$domain_url = $_SERVER['SERVER_NAME'];
	$cookie_domain = str_replace("www", "", $domain_url);
	setcookie('ap_id', $referrer_id, $cookie_life_time, "/", $cookie_domain);

	$_SESSION['ap_id'] = $referrer_id;

	$campaign_id = '';
	$clientdate = (date("Y-m-d"));
	$clienttime = (date("H:i:s"));
	$clientbrowser = $_SERVER['HTTP_USER_AGENT'];
	$clientip = $_SERVER['REMOTE_ADDR'];
	$clienturl = $_SERVER['HTTP_REFERER'];

	$e_refid = $wpdb->escape($referrer_id);
	$e_date = $wpdb->escape($clientdate);
	$e_time = $wpdb->escape($clienttime);
	$e_browser = $wpdb->escape($clientbrowser);
	$e_ip = $wpdb->escape($clientip);
	$e_url = $wpdb->escape($clienturl);
	$current_page = wp_aff_current_page_url();

	$affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
	$updatedb = "INSERT INTO $affiliates_clickthroughs_table_name (refid,date,time,browser,ipaddress,referralurl,buy,campaign_id) VALUES ('$e_refid', '$e_date', '$e_time', '$e_browser', '$e_ip', '$e_url', '','$campaign_id')";
	$results = $wpdb->query($updatedb);
}

function wp_aff_record_remote_lead($referrer_id, $buyer_email, $reference, $clientip, $clientbrowser = '', $buyer_name = '') {
	global $wpdb;
	$affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
	$clientdate = (date("Y-m-d"));
	$clienttime = (date("H:i:s"));

	// Ignore bots and wordpress trackbacks
	if (strpos($clientbrowser, "WordPress") !== false || strpos($clientbrowser, "bot") !== false) {
		return;
	}
	$referrer = $wpdb->escape($referrer_id);
	$buyer_email = $wpdb->escape($buyer_email);
	$reference = $wpdb->escape($reference);
	$clientdate = $wpdb->escape($clientdate);
	$clienttime = $wpdb->escape($clienttime);
	$ipaddress = $wpdb->escape($clientip);

	$updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress) VALUES ('$buyer_email','$referrer','$reference','$clientdate','$clienttime','$ipaddress')";
	$results = $wpdb->query($updatedb);
}

if (isset($_POST['wp_aff_affiliate_id_submit'])) {
	$referrer_id = strip_tags($_POST['wp_aff_affiliate_id']);
	record_click($referrer_id);
}

function wp_aff_set_affiliate_id_form() {
	$output = "";
	if (!isset($_POST['wp_aff_affiliate_id'])) {
		$_POST['wp_aff_affiliate_id'] = "";
	}
	$output .= '<a name="aff_id_entry_anchor"></a>';
	$output .= '<form method="post" action="#aff_id_entry_anchor">';
	$output .= AFF_USERNAME . ': ';
	$output .= '<input name="wp_aff_affiliate_id" type="text" size="20" value="' . $_POST['wp_aff_affiliate_id'] . '"/>';
	$output .= '<div class="submit">';
	$output .= '<input type="submit" name="wp_aff_affiliate_id_submit" value="Submit" />';
	$output .= '</div>';
	$output .= '</form>';
	return $output;
}

function wp_aff_check_if_account_exists($email) {
	global $wpdb;
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	$resultset = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE email = '$email'", OBJECT);
	if ($resultset) {
		return true;
	} else {
		return false;
	}
}

function wp_aff_check_if_account_exists_by_affiliate_id($affiliate_id) {
	global $wpdb;
	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	$resultset = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$affiliate_id'", OBJECT);
	if ($resultset) {
		return true;
	} else {
		return false;
	}
}

function wp_aff_create_affilate($user_name, $pwd, $acompany, $atitle, $afirstname, $alastname, $awebsite, $aemail, $apayable, $astreet, $atown, $astate, $apostcode, $acountry, $aphone, $afax, $date, $paypal_email, $commission_level, $referrer) {
	global $wpdb;
	include_once(ABSPATH . WPINC . '/class-phpass.php');
	$wp_hasher = new PasswordHash(8, TRUE);
	$pwd = $wp_hasher->HashPassword($pwd);

	$affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
	$updatedb = "INSERT INTO $affiliates_table_name (refid,pass,company,title,firstname,lastname,website,email,payableto,street,town,state,postcode,country,phone,fax,date,paypalemail,commissionlevel,referrer) VALUES ('" . $user_name . "', '" . $pwd . "', '" . $acompany . "', '" . $atitle . "', '" . $afirstname . "', '" . $alastname . "', '" . $awebsite . "', '" . $aemail . "', '" . $apayable . "', '" . $astreet . "', '" . $atown . "', '" . $astate . "', '" . $apostcode . "', '" . $acountry . "', '" . $aphone . "', '" . $afax . "', '$date','" . $paypal_email . "','" . $commission_level . "','" . $referrer . "')";
	$results = $wpdb->query($updatedb);
}

function wp_aff_create_affilate_using_array_data($fields) {
	global $wpdb;
	$inTable = WP_AFF_AFFILIATES_TBL_NAME;
	$fieldss = '';
	$valuess = '';
	$first = true;
	foreach ($fields as $field => $value) {
		if ($first)
			$first = false;
		else {
			$fieldss .= ' , ';
			$valuess .= ' , ';
		}
		$fieldss .= " $field ";
		$valuess .= " '" . $wpdb->escape($value) . "' ";
	}

	$query .= " INSERT INTO $inTable ($fieldss) VALUES ($valuess)";
	$results = $wpdb->query($query);
	return $results;
}

function wp_aff_send_sign_up_email($user_name, $pwd, $affiliate_email) {
	$affiliate_login_url = get_option('wp_aff_login_url');

	$email_subj = get_option('wp_aff_signup_email_subject');
	$body_sign_up = get_option('wp_aff_signup_email_body');
	$from_email_address = get_option('wp_aff_senders_email_address');
	$headers = 'From: ' . $from_email_address . "\r\n";

	$tags1 = array("{user_name}", "{email}", "{password}", "{login_url}");
	$vals1 = array($user_name, $affiliate_email, $pwd, $affiliate_login_url);
	$aemailbody = str_replace($tags1, $vals1, $body_sign_up);

	if (get_option('wp_aff_admin_notification')) {
		$admin_email_subj = "New affiliate sign up notification";
		wp_mail($from_email_address, $admin_email_subj, $aemailbody);
	}
	wp_mail($affiliate_email, $email_subj, $aemailbody, $headers);
}

function wp_aff_send_commission_notification($affiliate_email, $txn_id = '') {
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	if (get_option('wp_aff_notify_affiliate_for_commission')) {
		$from_email_address = $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address');
		$headers = 'From: ' . $from_email_address . "\r\n";
		$notify_subj = $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_subject'); //AFF_COMMISSION_RECEIVED_NOTIFICATION_SUBJECT
		$notify_body = $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_body'); //AFF_COMMISSION_RECEIVED_NOTIFICATION_BODY
		wp_aff_platform_send_email($affiliate_email, $notify_subj, $notify_body, $headers);
	}
	if ($wp_aff_platform_config->getValue('wp_aff_notify_admin_for_commission') == '1') {
		$admin_email = get_option('wp_aff_contact_email');
		$subj = "Affiliate commission notification";
		$body = "This is an auto-generated email letting you know that one of your affiliates has earned a commission. You can log into your WordPress admin dashboard and get more details about this transaction.";
		$from_email_address = $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address');
		$headers = 'From: ' . $from_email_address . "\r\n";
		wp_aff_platform_send_email($admin_email, $subj, $body, $headers);
	}
}

function wp_aff_platform_send_email($email, $subject, $body, $headers) {
	if (function_exists('wp_mail')) {
		wp_mail($email, $subject, $body, $headers);
	} else {
		include_once('lib/email.php');
		wp_affiliate_send_mail($email, $body, $subject, $headers);
	}
}

function wp_aff_redirect_to_non_affiliate_url() {
	$curr_page = wp_aff_current_page_url();
	$ap_id_pos = strpos($curr_page, "?ap_id");
	if (empty($ap_id_pos)) {
		$ap_id_pos = strpos($curr_page, "&ap_id");
	}
	$target_url = substr($curr_page, 0, $ap_id_pos);
	header('Location: ' . $target_url);
	exit;
}

function wp_affiliate_referrer_handler($atts) {
	$referrer = wp_affiliate_get_referrer();
	if (empty($referrer)) {
		$referrer = "None";
	}
	return $referrer;
}

function wp_affiliate_get_referrer() {
	if (!empty($_SESSION['ap_id'])) {
		$referrer = $_SESSION['ap_id'];
	} else if (isset($_COOKIE['ap_id'])) {
		$referrer = $_COOKIE['ap_id'];
	}
	return $referrer;
}

function wp_aff_current_page_url() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function wp_aff_login_widget_init() {
	$widget_options = array('classname' => 'wp_affiliate_widget', 'description' => __("Display WP Affiliate Login Widget"));
	wp_register_sidebar_widget('wp_affiliate_widget', __('WP Affiliate Login'), 'show_wp_aff_login_widget', $widget_options);
}

function show_wp_aff_login_widget($args) {
	extract($args);
	//$widget_title = get_option('wp_aff_login_widget_title');
	$widget_title = AFF_WIDGET_TITLE;
	if (empty($widget_title))
		$widget_title = "Affiliate Login";
	echo $before_widget;
	echo $before_title . $widget_title . $after_title;
	echo aff_login_widget();
	echo $after_widget;
}

function wp_aff_front_head_content() {
	echo '<link type="text/css" rel="stylesheet" href="' . WP_AFF_PLATFORM_URL . '/affiliate_platform_style.css" />' . "\n";
}

function wp_aff_plugin_conflict_check() {
	$msg = "";

	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	// WP Super cache check
	if (function_exists('wp_cache_serve_cache_file') && $wp_aff_platform_config->getValue('wp_aff_do_not_show_sc_warning') != '1') {
		$sc_integration_incomplete = false;
		global $wp_super_cache_late_init;
		if (false == isset($wp_super_cache_late_init) || ( isset($wp_super_cache_late_init) && $wp_super_cache_late_init == 0 )) {
			$sc_integration_incomplete = true;
		}
		if (defined('TIPS_AND_TRICKS_SUPER_CACHE_OVERRIDE')) {
			$sc_integration_incomplete = false;
		}
		if ($sc_integration_incomplete) {
			$msg .= '<p>You have the WP Super Cache plugin active. Please make sure to follow <a href="http://www.tipsandtricks-hq.com/forum/topic/using-the-plugins-together-with-wp-super-cache-plugin" target="_blank">this instruction</a> to make it work with the WP Affiliate Platform plugin. You can ignore this message if you have already applied the recommended changes.';
			$msg .= '<input class="button " type="button" onclick="document.location.href=\'admin.php?page=wp_aff_platform_settings&wpap_hide_sc_msg=1\';" value="Hide this Message">';
			$msg .= '</p>';
		}
	}
	if (function_exists('w3tc_pgcache_flush') && class_exists('W3_PgCache')) {
		// W3 Total Cache is active
		$integration_in_place = false;
		$w3_pgcache = & W3_PgCache::instance();
		foreach ($w3_pgcache->_config->get_array('pgcache.reject.cookie') as $reject_cookie) {
			if (strstr($reject_cookie, "cart_in_use") !== false) {
				$integration_in_place = true;
			}
		}
		if (!$integration_in_place) {
			$msg .= '<p>You have the W3 Total Cache plugin active. Please make sure to follow <a href="http://www.tipsandtricks-hq.com/forum/topic/using-the-plugins-with-w3-total-cache-plugin" target="_blank">these instructions</a> to make it work with the WP Affiliate plugin.</p>';
		}
	}
	//Check schema version
	$installed_schema_version = get_option("wp_affiliates_version");
	if ($installed_schema_version != WP_AFFILIATE_PLATFORM_DB_VERSION) {
		$msg .= '<p>It looks like you did not follow the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/wordpress-affiliate-platform-installation-guide-6" target="_blank">WP Affiliate upgrade instruction</a> to update the plugin. The database schema is out of sync and need to be updated. Please deactivate the plugin and follow the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/wordpress-affiliate-platform-installation-guide-6" target="_blank">upgrade instruction from here</a> to upgrade the plugin and correct this.</p>';
	}

	if (!empty($msg)) {
		echo '<div class="updated fade">' . $msg . '</div>';
	}
}

//Add the Admin Menus
if (is_admin()) {
	if (get_bloginfo('version') >= 3.0) {
		define("AFFILIATE_MANAGEMENT_PERMISSION", "add_users");
	} else {
		define("AFFILIATE_MANAGEMENT_PERMISSION", "edit_themes");
	}

	function wp_aff_platform_add_admin_menu() {
		add_menu_page(__("Affiliate Platform", 'wp_affiliate'), __("WP Affiliate", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, __FILE__, "wp_aff_show_stats");
		add_submenu_page(__FILE__, __("WP Affiliate Settings", 'wp_affiliate'), __("Settings", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'wp_aff_platform_settings', "show_aff_platform_settings_page");
		add_submenu_page(__FILE__, __("WP Affiliates", 'wp_affiliate'), __("Manage Affiliates", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'affiliates', "aff_top_affiliates_menu");
		add_submenu_page(__FILE__, __("WP Affiliates Edit", 'wp_affiliate'), __("Add/Edit Affiliates", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'affiliates_addedit', "edit_affiliates_menu");
		add_submenu_page(__FILE__, __("WP Affiliate Banners", 'wp_affiliate'), __("Manage Ads", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'manage_banners', "manage_banners_menu");
		add_submenu_page(__FILE__, __("WP Aff Banner Edit", 'wp_affiliate'), __("Add/Edit Ads", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'edit_banners', "wp_aff_edit_ads_menu");
		add_submenu_page(__FILE__, __("WP Affiliate Leads", 'wp_affiliate'), __("Manage Leads", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'manage_leads', "aff_top_leads_menu");
		add_submenu_page(__FILE__, __("WP Affiliate Clicks", 'wp_affiliate'), __("Click Throughs", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'clickthroughs', "clickthroughs_menu");
		add_submenu_page(__FILE__, __("WP Affiliate Sales", 'wp_affiliate'), __("Sales/Comm Data", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'aff_sales', "aff_top_sales_menu");
		add_submenu_page(__FILE__, __("WP Payouts", 'wp_affiliate'), __("Manage Payouts", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'manage_payouts', "manage_payouts_menu");
		add_submenu_page(__FILE__, __("WP Payouts History", 'wp_affiliate'), __("Payouts History", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'payouts_history', "payouts_history_menu");
	}

	//Include menus
	require_once(dirname(__FILE__) . '/aff_stats_menu.php');
	require_once(dirname(__FILE__) . '/wp_affiliate_platform_menu.php');
	require_once(dirname(__FILE__) . '/affiliates_menu.php');
	require_once(dirname(__FILE__) . '/leads_menu.php');
	require_once(dirname(__FILE__) . '/clickthroughs_menu.php');
	require_once(dirname(__FILE__) . '/banners_menu.php');
	require_once(dirname(__FILE__) . '/payouts_menu.php');
	require_once(dirname(__FILE__) . '/payouts_history_menu.php');
	require_once(dirname(__FILE__) . '/sales_menu.php');
}

// Insert the options page to the admin menu
if (is_admin()) {
	add_action('admin_menu', 'wp_aff_platform_add_admin_menu');
}

function wp_aff_load_libraries() {
	wp_enqueue_script('jquery');
}

function wp_aff_init_action_handler() {
	wp_aff_load_libraries();
	wp_aff_login_widget_init();
	wp_aff_affiliate_view2_login_handler();
	wp_aff_affiliate_view2_logout_handler();
	wp_aff_wp_user_integraton_hooks_handler();
}

function wp_aff_handle_plugins_loaded_hook() {
	if (is_admin()) {//Check if DB needs to be updated
		if (get_option('wp_affiliates_version') != WP_AFFILIATE_PLATFORM_DB_VERSION) {
			wp_affiliate_platform_run_activation();
		}
	}
}

add_action('init', 'wp_aff_init_action_handler');
add_action('plugins_loaded', 'wp_aff_handle_plugins_loaded_hook');
add_action('wp_head', 'wp_aff_front_head_content');
add_action('wp_affiliate_process_cart_commission', 'wp_affiliate_process_cart_commission_handler');
add_action('wp_affiliate_shopperpress_track_commission', 'wp_affiliate_shopperpress_track_commission_handler');

add_shortcode('wp_aff_award_custom_commission', 'wp_aff_award_custom_commission_handler');
add_shortcode('wp_aff_login', 'wp_aff_login_handler');
add_shortcode('wp_aff_login_onpage_version', 'wp_aff_login_onpage_version_handler');
add_shortcode('wp_affiliate_view', 'wp_affiliate_view_handler');
add_shortcode('wp_affiliate_referrer', 'wp_affiliate_referrer_handler');
add_shortcode('wp_aff_set_affiliate_id', 'wp_aff_set_affiliate_id_form');

if (!is_admin()) {
	add_filter('widget_text', 'do_shortcode');
}

if (is_admin()) {
	add_action('admin_notices', 'wp_aff_plugin_conflict_check');
}
?>
