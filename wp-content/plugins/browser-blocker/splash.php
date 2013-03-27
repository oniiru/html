<?php
$siteurl = get_option('siteurl');
$plugin_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__));
$img_url = $plugin_url . '/images/';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<meta http-equiv="Content-Style-Type" content="text/css" />
<style type="text/css" media="screen">
	/*------------------------------------------------*/
	/*-----------------[RESET]------------------------*/
	/*------------------------------------------------*/

	/* http://meyerweb.com/eric/tools/css/reset/ */
	/* v1.0 | 20080212 */

	html, body, div, span, applet, object, iframe,
	h1, h2, h3, h4, h5, h6, p, blockquote, pre,
	a, abbr, acronym, address, big, cite, code,
	del, dfn, em, font, img, ins, kbd, q, s, samp,
	small, strike, strong, sub, sup, tt, var,
	b, u, i, center,
	dl, dt, dd, ol, ul, li,
	fieldset, form, label, legend { margin: 0; padding: 0; border: 0; outline: 0; font-size: 100%; vertical-align: baseline; background: transparent; }
	body { line-height: 1; }
	ol, ul { list-style: none; }
	blockquote, q {	quotes: none; }
	blockquote:before, blockquote:after,q:before, q:after { content: ''; content: none; }
	:focus { outline: 0; }
	ins { text-decoration: none; }
	del { text-decoration: line-through; }
	table { border-collapse: collapse; border-spacing: 0; }
	
	/*------------------------------------------------*/
	/*-----------------[DESIGN]-----------------------*/
	/*------------------------------------------------*/
	
	body{}
	#container { width: 950px; margin: 80px auto; }
	#warning { width: 800px; margin: 10px auto; padding: 10px 10px 23px; background: #FFFFB5; border: 1px solid #FFD34F; text-align: center; font-size: 20px; font-weight: bold; vertical-align: middle; clear: both; }
	.left { float: left; margin-left: 10px;	}
	.right { float: right; margin-right: 10px; 	}
	h1 { font-size: 36px; font-family: 'Trebuchet MS', Helvetica, sans-serif; color: #024A80;}
	#battle { float: left; width: 300px; margin: 70px 20px 0 0;}
	p { padding: 20px; color: #666; font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; }
	#browsers { margin: 10px 0; float: left; }
	#browsers a { color: none; text-decoration: none;}
	.browser { float: left; padding: 0 10px 10px; width: 128px; }
	.browser img { border: none; }
	.browser h2 { font-size: 16px; margin: 0 auto; color: #de9604; margin: 5px 0; }
	.browser p { padding: 0; margin-bottom: 10px; font-size: 12px; line-height: 14px;}
	#credit { margin-top: 20px; color: #666; font-size: 10px; text-align: center; }
	#credit a { color: #666; text-decoration: underline; }
	#credit a:hover { text-decoration: none; }
</style>

</head>
<body>
	<div id="container">
		<?php
		$image = stripslashes(get_option("Browser_Blocker_Splash_Img"));
		if( $image == "" ){
			$image = $img_url."browser-wars.jpg";
		}
		
		$title = stripslashes(get_option("Browser_Blocker_title"));
		if( $title == "" ){
			$title = "Sorry! Your browser doesn't have what it takes to view this site!";
		}
		
		$link_text = stripslashes(get_option("Browser_Blocker_BPtext"));
		if( $link_text == "" ){
			$link_text = "Click here to proceed to the website";
		}
		
		$msg = stripslashes(get_option("Browser_Blocker_Msg"));
		if( $msg == "" ){
			$msg = "With all the changes that happen around the web, it is best to keep your browser up to date. Please choose a browser from those listed below and Upgrade your Web Experience.";
		}
		$browsers = explode('~', get_option('Browser_Blocker_Display_Browsers'));
		?>
		
	<img id="battle" src="<? echo $image ?>" />
	<h1><?php echo $title ?></h1>
	<p id="description"><?php echo $msg ?></p>
	<div id="browsers">
		<?php
		if(in_array('1',$browsers)){
		?>
		<div id="chrome" class="browser">
			<a href="http://www.google.com/chrome/" title="Download Google Chrome">
				<img src="<?=$img_url ?>chrome.jpg" />
				<center><h2>Google Chrome</h2>
				<?php if(get_option("Browser_Blocker_DwnldDesc")){ ?><p>runs web pages with lightning speed.</p><?php } ?></center>
				<img src="<?=$img_url ?>download.jpg">
			</a>
		</div>
		
		<?php
		}
		?>
		<?php
		if(in_array('2',$browsers)){
		?>
		
		<div id="firefox" class="browser">
			<a href="http://www.firefox.com" title="Download Mozilla Firefox">
				<img src="<?=$img_url ?>firefox.jpg" />
				<center><h2>Mozilla Firefox</h2>
				<?php if(get_option("Browser_Blocker_DwnldDesc")){ ?><p>made to make the web a better place.</p><?php } ?></center>
				<img src="<?=$img_url ?>download.jpg">
			</a>
		</div>
		
		<?php
		}
		?>
		<?php
		if(in_array('3',$browsers)){
		?>
		
		<div id="safari" class="browser">
			<a href="http://www.apple.com/safari/" title="Download Apple Safari">
				<img src="<?=$img_url ?>safari.jpg" />
				<center><h2>Apple Safari</h2>
				<?php if(get_option("Browser_Blocker_DwnldDesc")){ ?><p>improves the way you view the web.</p><?php } ?></center>
				<img src="<?=$img_url ?>download.jpg">
			</a>
		</div>
		
		<?php
		}
		?>
		<?php
		if(in_array('4',$browsers)){
		?>
		
		<div id="ie" class="browser">
			<a href="http://www.microsoft.com/ie9" title="Download Internet Explorer">
				<img src="<?=$img_url ?>ie.jpg" />
				<center><h2>Internet Explorer</h2>
				<?php if(get_option("Browser_Blocker_DwnldDesc")){ ?><p>experience a more beautiful web.</p><?php } ?></center>
				<img src="<?=$img_url ?>download.jpg">
			</a>
		</div>
		
		<?php
		}
		?>
		<?php
		if(in_array('5',$browsers)){
		?>
		
		<div id="opera" class="browser">
			<a href="http://www.opera.com" title="Download Opera">
				<img src="<?=$img_url ?>opera.jpg" />
				<center><h2>Opera</h2>
				<?php if(get_option("Browser_Blocker_DwnldDesc")){ ?><p>faster, smoother, and easier to use.</p><?php } ?></center>
				<img src="<?=$img_url ?>download.jpg">
			</a>
		</div>
		
		<?php
		}
		?>
	</div>
	<div style="clear:both"></div>
	<?php
	if(get_option('Browser_Blocker_Bypass') == 1){
		
		if (strpos($_SERVER['REQUEST_URI'], '?') !== false){
		  $bb_newUrl = $_SERVER['REQUEST_URI'] . '&sid='.$_SESSION["BB_SESSION_ID"];
		}else{
		  $bb_newUrl = $_SERVER['REQUEST_URI'] . '?sid='.$_SESSION["BB_SESSION_ID"];
		}
	?>
	<div id="warning">
	
	<img src="<?=$img_url ?>exclamation.png" class="left" />
	<img src="<?=$img_url ?>exclamation.png" class="right" />	
		<a href="<?php echo $bb_newUrl ?>"><?php echo $link_text; ?></a>
	
	</div>
	<?
	}
	?>
	<?php
	if(get_option('Browser_Blocker_Credit') == 1){
	?>
	<div id="credit">
		Browser Blocker <a href="http://www.wordpress.org">Wordpress</a> Plugin created by <a href="http://www.macnative.com">Macnative.com</a>
	</div>
	<?
	}
	?>
	</div>
	<?php
	if(get_option('Browser_Blocker_Code') != ""){
	
	echo get_option("Browser_Blocker_Code");
	
	}
	?>
</body>
</html>