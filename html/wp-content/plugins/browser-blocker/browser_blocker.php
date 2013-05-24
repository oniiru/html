<?php
/**
 * @package Browser Blocker
 * @author Randall Hinton
 * @version 0.5.6
 */
/*
Plugin Name: Browser Blocker
Plugin URI: http://www.macnative.com/development/browser-blocker
Description: Are you tired of always having to modify perfectly good working code to allow users on inferior browser to view your website. Well Waste Time No More!!! Browser Blocker allows you to control which browser get to visit your website, and which ones get to go sit in time out.
Author: Randall Hinton
Version: 0.5.6
Author URI: http://www.macnative.com/
*/

register_activation_hook( __FILE__, 'BrowserBlocker_Activate' );

/**
 * Activate Plugin and set default settings
 *
 * @since 0.1
 * @author randall@macnative.com
 */
function BrowserBlocker_Activate() {
		BrowserBlocker_DefaultSettings();
		BrowserBlocker_Add_Option_Menu();
}

if ( is_admin() ) {
	add_action('admin_menu', 'BrowserBlocker_Add_Option_Menu');
	add_action('admin_menu', 'BrowserBlocker_DefaultSettings');
}

/**
 * Adds settings link on the plugin administration page
 * 
 * @since 0.4
 * @author randall@macnative.com
 */
function BrowserBlocker_Add_Settings_Link($links) {
		$settings_link = '<a href="options-general.php?page=Browser_Blocker_Admin">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links;
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'BrowserBlocker_Add_Settings_Link' );


/**
 * Adds the plugin's default settings
 *
 * @since 0.1
 * @author randall@macnative.com
 */
function BrowserBlocker_DefaultSettings() {
	if( !get_option('Browser_Blocker_Enabled') ) {
		add_option('Browser_Blocker_Enabled', '0');
	}
	if( !get_option('Browser_Blocker_Credit') ) {
		add_option('Browser_Blocker_Credit', '1');
	}
	if( !get_option('Browser_Blocker_Title') ) {
		add_option('Browser_Blocker_Title', '');
	}
	if( !get_option('Browser_Blocker_Msg') ) {
		add_option('Browser_Blocker_Msg', '');
	}
	if( !get_option('Browser_Blocker_Splash_Img') ) {
		add_option('Browser_Blocker_Splash_Img', '');
	}
	if( !get_option('Browser_Blocker_Blocked') ) {
		add_option('Browser_Blocker_Blocked', '');
	}
	if( !get_option('Browser_Blocker_Pages') ) {
		add_option('Browser_Blocker_Pages', array('all'));
	}
	if( !get_option('Browser_Blocker_Display_Browsers') ) {
		add_option('Browser_Blocker_Display_Browsers', '1~2~3~4~');
	}
	if( !get_option('Browser_Blocker_DwnldDesc') ) {
		add_option('Browser_Blocker_DwnldDesc', '1');
	}
	if( !get_option('Browser_Blocker_Bypass') ) {
		add_option('Browser_Blocker_Bypass', '0');
	}
	if( !get_option('Browser_Blocker_BPtext') ) {
		add_option('Browser_Blocker_BPtext', '');
	}
	if( !get_option('Browser_Blocker_Code') ) {
		add_option('Browser_Blocker_Code', '');
	}
}

/**
 * Gets options string from the DB and converts it into an array
 *
 * @since 0.1
 * @author randall@macnative.com
 */
function BrowserBlocker_GetBlocked()
{
	$blocked = array();
	$blocked["browser"] = array();
	$blocked["direction"] = array();
	$blocked["version"] = array();
	$suboptions = explode("~",get_option('Browser_Blocker_Blocked'));
	for($x=0; $x < count($suboptions); $x++){
		$temp = explode(":",$suboptions[$x]);
		$parts = explode("_",$temp[0]);
		switch ($parts[0]){
		case "browser":
			$blocked["browser"][$parts[1]] = $temp[1];
			break;
		case "direction":
			$blocked["direction"][$parts[1]] = $temp[1];
			break;
		case "version":
			$blocked["version"][$parts[1]] = $temp[1];
			break;	
		}
	}
	return $blocked;
}



/**
 * Return Select Form Element
 *
 * @since 0.1
 * @author randall@macnative.com
 */
function BrowserBlocker_Make_Select($x = "", $fields, $class="", $id="select", $name="select") {
	echo '<select name="'.$name.'" id="'.$id.'" class="'.$class.'">';
		foreach ($fields as $shown => $value) {
			if($x == $value){
				echo '<option value="'.$value.'" selected />'.$shown.'</option>';
			}else{
				echo '<option value="'.$value.'" />'.$shown.'</option>';
			}
		}
	echo '</select>';
}

/**
 * Adds the plugin's options page
 * 
 * @since 0.1
 * @author randall@macnative.com
 */
function BrowserBlocker_Add_Option_Menu() {
		add_submenu_page('options-general.php', 'Browser Blocker', 'Browser Blocker', 'install_plugins', 'Browser_Blocker_Admin', 'BrowserBlocker_Options_Page');
}

/**
 * Return Admin Page CSS
 *
 * @since 0.1
 * @author randy@kgaps.com
 */
function BB_STYLE() {
	$siteurl = get_bloginfo('wpurl');
	$img_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images/';
?>
<style type="text/css">
#icon-bb {
	background: transparent url('<? echo get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images/' ?>bb-32.png') no-repeat 2px 2px;
	float: left;
	height: 34px;
	margin: 7px 8px 0 0;
	width: 36px;
}

.hidden {
	display: none;
}

.visible {
	display: block;
}

.bb_disabled{
	background: #EBEBEB;
}

#bb_ids_box {
	background: #F5F5F5;
	width: 759px;
}

#bb_ids_box ul{
	margin-top: 15px;
	width: 750px;
}

#bb_ids_box li{
	padding: 5px 0px 5px 25px;
	margin-bottom: 0px;
	display:block;
}

#bb_ids_box li.plain {
	padding: 5px 0px 5px 25px;
	margin-bottom: 0px;
}

#bb_ids_box li.disabled{
	background: #D5D5D5;
}

#bb_ids_box dl {
	padding-bottom: 5px;
	clear: both;
}

#bb_ids_box dt {
	float: left;
	width: 155px;
	padding: 10px 0px 0px 0px;
}

#bb_ids_box dd {
	float: left;
	width: 525px;
	padding: 5px 0px;
}

#bb_ids_box .labels {
	font-size: 12px;
	font-weight: bold;
	width: 225px;
	text-align: right;
	margin: 0px 15px 5px 0px;
}

#bb_ids_box label img {
	margin: 0px 5px -7px 0px;
}

#bb_ids_box .bb_nocheckbox {
	margin: 0px 5px -7px 27px;
}

#bb_ids_box .checkboxr{
	margin: 0px 15px 0px 0px;
}

.bb_example {
	font-size: 10px;
	font-style: italic;
	color: #999;
}

.bb_example2 {
	font-weight: bold;
	color: #888;
}

.bb_floater {
	float: left;
	margin-right: 5px;
}

.first {
	-moz-border-radius-bottomleft: 6px;
	-moz-border-radius-topleft: 6px;
	-webkit-border-bottom-left-radius: 6px;
	-webkit-border-top-left-radius: 6px;
	border-top-left-radius: 6px;
	border-bottom-left-radius: 6px;
}

.last {
	-moz-border-radius-bottomright: 6px;
	-moz-border-radius-topright: 6px;
	-webkit-border-bottom-right-radius: 6px;
	-webkit-border-top-right-radius: 6px;
	border-top-right-radius: 6px;
	border-bottom-right-radius: 6px;
}

.odd {
	background: #DDD url("<? echo $siteurl; ?>/wp-admin/images/gray-grad.png") repeat-x left top;
}

th {
	border-spacing: 0px;
	padding: 5px 10px;
}

td {
	text-align: center;
	border-spacing: 0px;
	padding: 3px 5px;
}

#bb_tabbed {
	background: #ECECEC;
	margin: 0 10px 10px 10px;
	border: 1px solid #CCC;
}

.tab {
	float: left;
	margin-left: 60px;
	padding: 5px 20px;
	font-size: 14px;
	margin-top: 10px;
}

.open {
	background: #ECECEC;
	border-top: 1px solid #CCC;
	border-right: 1px solid #CCC;
	border-left: 1px solid #CCC;
}

.closed {
	background: #DDD;
	border-top: 1px solid #AAA;
	border-right: 1px solid #AAA;
	border-left: 1px solid #AAA;
}

div.inside {
	height: 25px;
	clear: both;
}

#bb_blocked_ver {
	width: 40px;
	color: #666;
}

img.icon {
	width: 16px;
	height: 16px;
}

</style>
<?
}

/**
 * Adds content to the plugin's options page
 *
 * @since 0.1
 * @author randall@macnative.com
 */
function BrowserBlocker_Options_Page() {
	global $wpdb; 
	$wpdb->show_errors();
	$siteurl = get_option('siteurl');
	$plugin_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__));
	$img_url = $plugin_url . '/images/';
	
	BB_STYLE();
	
	if (isset($_POST['bb_update']) === true) {
		
		if(isset($_POST["clear"])){
			update_option('Browser_Blocker_Enabled', '0');
			update_option('Browser_Blocker_Credit', '1');
			update_option('Browser_Blocker_Title', '');
			update_option('Browser_Blocker_Msg', '');
			update_option('Browser_Blocker_Splash_Img', '');
			update_option('Browser_Blocker_Blocked', '');
			update_option('Browser_Blocker_Display_Browsers', '1~2~3~5~');
			update_option('Browser_Blocker_Bypass', '0');
			update_option('Browser_Blocker_BPtext', '');
			update_option('Browser_Blocker_Code', '');
			
			$successmessage = "Browser Blocker Options Cleared Out Successfully";
		}else{
		$errors=0;
		
		if($_POST['bb_update'] == "advanced"){
			
			if(get_option('Browser_Blocker_Splash_Img') != $_POST['bb_splash_img']){
				if($_POST['bb_splash_img'] != ""){
					if(!update_option('Browser_Blocker_Splash_Img', $_POST['bb_splash_img'])){
						$errors++;
					}
				}else{
					update_option('Browser_Blocker_Splash_Img', $_POST['bb_splash_img']);
				}
			}
			
			if(get_option('Browser_Blocker_Title') != $_POST['bb_text']){
				if($_POST['bb_text'] != ""){
				 	if(!update_option('Browser_Blocker_Title', $_POST['bb_text'])){
						$errors++;
					}
				}else{
					update_option('Browser_Blocker_Title', $_POST['bb_text']);
				}
			}
			
			if(get_option('Browser_Blocker_Msg') != $_POST['bb_msg']){
				if($_POST['bb_msg'] != ""){
					if(!update_option('Browser_Blocker_Msg', $_POST['bb_msg'])){
						$errors++;
					}
				}else{
					update_option('Browser_Blocker_Msg', $_POST['bb_msg']);
				}
			}
			
			if(get_option('Browser_Blocker_Code') != $_POST['bb_code']){
				if($_POST['bb_code'] != ""){
					if(!update_option('Browser_Blocker_Code', $_POST['bb_code'])){
						$errors++;
					}
				}else{
					update_option('Browser_Blocker_Code', $_POST['bb_code']);
				}
			}
			
			$downloads = get_option('Browser_Blocker_Display_Browsers');
			$browsers = "";
			for($x=1; $x <= 5; $x++){
				$browserd = "browser_".$x;
				if(isset($_POST[$browserd])){
					$browsers .= $x."~";
				}
			}
			update_option('Browser_Blocker_Display_Browsers', $browsers);
			
			if(isset($_POST['bb_desctext'])){
				$desctext = 1;
			}else{
				$desctext = 0;
			}
			
			if(get_option('Browser_Blocker_DwnldDesc') != $desctext){
				if(!update_option('Browser_Blocker_DwnldDesc', $desctext)){
					$errors++;
				}
			}
			
			if(isset($_POST['bb_bypass'])){
				$bypass = 1;
			}else{
				$bypass = 0;
			}
			
			if(get_option('Browser_Blocker_Bypass') != $bypass){
				if(!update_option('Browser_Blocker_Bypass', $bypass)){
					$errors++;
				}
			}
			
			if(get_option('Browser_Blocker_BPtext') != $_POST['bb_bypass_text']){
				if($_POST['bb_bypass_text'] != ""){
				 	if(!update_option('Browser_Blocker_BPtext', $_POST['bb_bypass_text'])){
						$errors++;
					}
				}else{
					update_option('Browser_Blocker_BPtext', $_POST['bb_bypass_text']);
				}
			}
			
			if(isset($_POST['bb_pages'])){
				if($_POST['bb_pages'] != get_option('Browser_Blocker_Pages')){
					if(!update_option('Browser_Blocker_Pages', $_POST['bb_pages'])){
						$errors++;
					}
				}
			}
		}
		
		if(get_option('Browser_Blocker_Enabled') != $_POST['bb_enable']){
			if(!update_option('Browser_Blocker_Enabled', $_POST['bb_enable'])){
				$errors++;
			}
		}
		
		if(isset($_POST['bb_attribute'])){
			$attribute = 1;
		}else{
			$attribute = 0;
		}
		
		if(get_option('Browser_Blocker_Credit') != $attribute){
			if(!update_option('Browser_Blocker_Credit', $attribute)){
				$errors++;
			}
		}
		
		$blocked = "";
		for($x=0; $x < $_POST["bb_versions_detail"]; $x++){
			if($x > 0){ $blocked .= "~"; }
			$browser = "bb_browser_".$x;
			$direction = "bb_direction_".$x;
			$version = "bb_version_".$x;
			if(isset($_POST[$browser])){
				$blocked .= "browser_".$x.":".$_POST[$browser]."~direction_".$x.":".$_POST[$direction]."~version_".$x.":".$_POST[$version];
			}
			
		}
		//echo $blocked;
		
		if(get_option('Browser_Blocker_Blocked') != $blocked){
			if(!update_option('Browser_Blocker_Blocked', $blocked)){
				$errors++;
			}
		}
		
		if($errors == 0){
			$successmessage = "Browser Blocker Options Updated Successfully";
		}else{
			$errormessage = "An Error Occurred While Updating Browser Blocker Options";
		}
	}
	
	}
		
	//$bb_options = BrowserBlocker_GetOptions();	
?>

<script type="text/javascript">

jQuery(document).ready(function($) {
	$(".fade").delay(4000).slideUp(1000);
	
	//add rows for new detail options
	$("#bb_add_browser").click(function(){
		
		$("#bb_versions").removeClass("hidden").addClass("visible");
			var rows = $("#bb_versions_detail").val();
			var browser = $("#bb_blocked").val();
			var direction = $("#bb_direction").val();
			var version = $("#bb_blocked_ver").val();
			var rowid = "#row" + rows;
			var new_row = "<tr id=\'row" + rows + "\'><td class='first'><input type='hidden' name='bb_browser_" + rows + "' id='bb_browser_" + rows + "' value='" + browser + "'>" + browser + "</td><td><input type='hidden' name='bb_direction_" + rows + "' id='bb_direction_" + rows + "' value='" + direction + "'>" + direction + "</td><td><input type='hidden' name='bb_version_" + rows + "' id='bb_version_" + rows + "' value='" + version + "'>" + version + "</td><td class='last removeRow'><img src='<?php echo $img_url."cross-circle.png" ?>' title='Remove Browser Version' /></td></tr>";
			$("#bb_versions").append(new_row);
			if(rows%2 != 0){
				$(rowid).addClass("odd");
				
			}
			$("#bb_versions_detail").val(++rows);
	
	});
	
	//remove rows
	$(".removeRow").live('click', function(){
		var rowid = $(this).closest("tr").attr("id");
		//alert(rowid);
		var agree = confirm('Are you sure you want to remove this detail row?\nThis action cannot be undone!');
		if(agree){
			$("#" + rowid).remove();
			$("#bb_versions_detail").val($("#bb_versions_detail").val()-1);
		}else{
			return false;
		}
	});
	
});
</script>

	<div class="wrap">
		<div id="icon-bb"></div><h2>Browser Blocker Admin Options</h2>
		<?php
		if(isset($successmessage)){	
			echo '<div id="message" class="updated fade">
				<p>
					<strong>
						' . $successmessage . '
					</strong>
				</p>
			</div>';
		}
		
		if(isset($errormessage)){	
			echo '<div id="message" class="error">
				<p>
					<strong>
						' . $errormessage . '
					</strong>
				</p>
			</div>';
		}
		?>
		
		<input type="hidden" name="action" value="edit" />
			<div id="poststuff" class="ui-sortable">
			<div id="bb_ids_box" class="postbox if-js-open">
			<h3>Browser Blocker Admin Options</h3>
			
			<a href="?page=<?php echo $_GET["page"] ?>&whichP=simple"><div id="bb_simple" class="tab <?php if(!isset($_GET["whichP"]) || $_GET["whichP"] == "simple"){ echo "open"; }else{ echo "closed"; } ?>" >Simple Options</div></a><a href="?page=<?php echo $_GET["page"] ?>&whichP=advanced"><div id="bb_advanced" class="tab <?php if(isset($_GET["whichP"]) && $_GET["whichP"] == "advanced"){ echo "open"; }else{ echo "closed";  } ?>" >Advanced Options</div></a><div style="clear:both"></div>
			<div id="bb_tabbed">
			
			<?
			if(isset($_GET["whichP"]) && $_GET["whichP"] == "advanced"){
				require_once('advanced.php');
			}else{
				require_once('simple.php');	
			}
			?>
			
			</div>
			
			</div>
			</div>
			
		</div>
 	</div>
<?php
}

/**
 * Display Browser Splash Page
 *
 * @since 0.1
 * @author randall@macnative.com
 */

$bb_blocked_pages = get_option('Browser_Blocker_Pages');
if(in_array('all',$bb_blocked_pages)){
	add_action('init', 'BrowserBlocker_Splash');
}else{
	add_action('wp', 'BrowserBlocker_Splash');
}

function BrowserBlocker_Splash() {
	global $wp_query;
	$siteurl = get_option('siteurl');
	$plugin_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__));
	$img_url = $plugin_url . '/images/';
	if(get_option('Browser_Blocker_Enabled') == 1){
		if(!is_admin()){
			if(isset($_GET["sid"]) && $_GET["sid"] == $_SESSION['BB_SESSION_ID']){
				$_SESSION["BB_BYPASS"] = true;
			}
			if($_SESSION["BB_BYPASS"]){
				
			}else{
				$bb_pages = get_option('Browser_Blocker_Pages');
				//print_r($bb_pages);
				//echo "<br />".$wp_query->post->ID;
				if($bb_pages && in_array($wp_query->post->ID,$bb_pages)){
					require_once('browser.php');
					$browser = new Browser();
					$blocked = BrowserBlocker_GetBlocked();
					if( in_array($browser->getBrowser(),$blocked["browser"])){
						for($x=0; $x < count($blocked["browser"]); $x++){
							if($browser->getBrowser() == $blocked["browser"][$x]){
								switch($blocked["direction"][$x]){
									case "Equals":
										if($browser->getVersion() == $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Less Than":
										if($browser->getVersion() < $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Greater Than":
										if($browser->getVersion() > $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Equal Or Greater":
										if($browser->getVersion() >= $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Equal Or Less":
										if($browser->getVersion() <= $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
								}
							}
						}
					}
				}else if(in_array('all',$bb_pages)){
					require_once('browser.php');
					$browser = new Browser();
					$blocked = BrowserBlocker_GetBlocked();
					if( in_array($browser->getBrowser(),$blocked["browser"])){
						for($x=0; $x < count($blocked["browser"]); $x++){
							if($browser->getBrowser() == $blocked["browser"][$x]){
								switch($blocked["direction"][$x]){
									case "Equals":
										if($browser->getVersion() == $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Less Than":
										if($browser->getVersion() < $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Greater Than":
										if($browser->getVersion() > $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Equal Or Greater":
										if($browser->getVersion() >= $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
									case "Equal Or Less":
										if($browser->getVersion() <= $blocked["version"][$x]){
											BrowserBlocker_Display($browser);
										}
										break;
								}
							}
						}
					}
				}else{
				
				}
			}
		}
	}
}

function BrowserBlocker_Display($browser) {

	require_once('splash.php');
	exit();
}

/**
 * Initialize PHP Sessions for splash page bypass
 *
 * @since 0.4.4
 * @author randall@macnative.com
 */

function init_sessions() {
    if (!session_id()) {
        session_start();
		if(!isset($_SESSION['BB_SESSION_ID'])){
			$_SESSION['BB_SESSION_ID'] = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].mktime());
		}
    }
}

add_action('init', 'init_sessions', 5);

?>