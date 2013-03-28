<?php
/*
Plugin Name: Latest twitter sidebar widget
Plugin URI: http://www.tacticaltechnique.com/wordpress/latest-twitter-sidebar-widget/
Description: Creates a sidebar widget that displays the latest twitter updates for any user with public tweets.
Author: Corey Salzano
Email: coreysalzano@gmail.com
Version: 0.110801
Author URI: http://www.tacticaltechnique.com/
*/
$verion = "0.110801";

// no edits required, but if you do, share your mods with me!

// this plugin requires that PHP curl and PHP json be installed on your server

//load the saved or default options
if( !function_exists("get_admin_options_ltw")){
	function get_admin_options_ltw() {
		$optionName = "widget_latest_twitter";
		$default_options = array(	'user' => 'salzano',
									'count' => '3',
									'prefix' => '',
									'suffix' => '',
									'beforeUpdate' => '',
									'afterUpdate' => '',
									'showProfilePicTF'=> false,
									'showTwitterIconTF' => true,
									'showTweetTimeTF' => false,
									'widgetTitle' => '',
									'includeRepliesTF' => true
									);
		$savedOptions = get_option($optionName);
		if(!empty($savedOptions)){
			foreach ($savedOptions as $key => $option) $default_options[$key] = $option;
		}
		update_option($optionName, $default_options);
		return $default_options;
	}
}

function latest_twitter_sidebar_widget() {
	$saved_options = get_admin_options_ltw( );
	$username = $saved_options['user'];
	$updateCount = $saved_options['count'];
	$prefix = stripslashes($saved_options['prefix']);
	$suffix = stripslashes($saved_options['suffix']);
	$beforeUpdate = stripslashes($saved_options['beforeUpdate']);
	$afterUpdate = stripslashes($saved_options['afterUpdate']);
	$showProfilePicTF = $saved_options['showProfilePicTF'];
	$showTwitterIconTF = $saved_options['showTwitterIconTF'];
	$showTweetTimeTF = $saved_options['showTweetTimeTF'];
	$widgetTitle = $saved_options['widgetTitle'];
	$includeRepliesTF = $saved_options['includeRepliesTF'];

	if ( !function_exists('fix_twitter_update') ){
		function fix_twitter_update($origTweet,$entities) {
			if( $entities == null ){ return $origTweet; }
			foreach( $entities->urls as $url ){
				$index[$url->indices[0]] = "<a href=\"".$url->url."\">".$url->url."</a>";
				$endEntity[(int)$url->indices[0]] = (int)$url->indices[1];
			}
			foreach( $entities->hashtags as $hashtag ){
				$index[$hashtag->indices[0]] = "<a href=\"http://twitter.com/#!/search?q=%23".$hashtag->text."\">#".$hashtag->text."</a>";
				$endEntity[$hashtag->indices[0]] = $hashtag->indices[1];
			}
			foreach( $entities->user_mentions as $user_mention ){
				$index[$user_mention->indices[0]] = "<a href=\"http://twitter.com/".$user_mention->screen_name."\">@".$user_mention->screen_name."</a>";
				$endEntity[$user_mention->indices[0]] = $user_mention->indices[1];
			}
			$fixedTweet="";
			for($i=0;$i<iconv_strlen($origTweet, "UTF-8" );$i++){
				if(iconv_strlen($index[(int)$i], "UTF-8" )>0){
					$fixedTweet .= $index[(int)$i];
					$i = $endEntity[(int)$i]-1;
				} else{
					$fixedTweet .= iconv_substr( $origTweet,$i,1, "UTF-8" );
				}
			}
			return $fixedTweet;
		}
	}

	if( !function_exists('twitter_time_ltw')){
		function twitter_time_ltw($a) {
			//get current timestamp
			$b = strtotime("now");
			//get timestamp when tweet created
			$c = strtotime($a);
			//get difference
			$d = $b - $c;
			//calculate different time values
			$minute = 60;
			$hour = $minute * 60;
			$day = $hour * 24;
			$week = $day * 7;

			if(is_numeric($d) && $d > 0) {
				//if less then 3 seconds
				if($d < 3) return "right now";
				//if less then minute
				if($d < $minute) return floor($d) . " seconds ago";
				//if less then 2 minutes
				if($d < $minute * 2) return "about 1 minute ago";
				//if less then hour
				if($d < $hour) return floor($d / $minute) . " minutes ago";
				//if less then 2 hours
				if($d < $hour * 2) return "about 1 hour ago";
				//if less then day
				if($d < $day) return floor($d / $hour) . " hours ago";
				//if more then day, but less then 2 days
				if($d > $day && $d < $day * 2) return "yesterday";
				//if less then year
				if($d < $day * 365) return floor($d / $day) . " days ago";
				//else return more than a year
				return "over a year ago";
			}
		}
	}

	if ( !function_exists('curl_to_file') ){
		function curl_to_file( $url, $fileName ){
			if ( function_exists('curl_init')) {
				$userAgent = "Latest twitter widget WP plugin " . $version;
				$curl = curl_init( $url );
				$filePath = dirname(__FILE__) ."/". $fileName;
				$fp = fopen( $filePath, "w");
				curl_setopt ($curl, CURLOPT_URL, $url );
				curl_setopt($curl, CURLOPT_FILE, $fp);
				curl_setopt($curl, CURLOPT_REFERER, get_bloginfo('home'));
				curl_setopt($curl, CURLOPT_USERAGENT, $userAgent );
				if (!$result = curl_exec($curl)) {
					curl_close ($curl);
					return false;
				} else{
					curl_close ($curl);
					return true;
				}
			} else{
				return false;
			}
		}
	}

	if( !function_exists("file_missing_or_old")){
		function file_missing_or_old( $fileName, $ageInHours ){
			$fileName = dirname(__FILE__) ."/". $fileName;
			if( !file_exists( $fileName )){
				return true;
			} else{
				$fileModified = filemtime( $fileName );
				$today = time( );
				$hoursSince = round(($today - $fileModified)/3600, 3);
				if( $hoursSince > $ageInHours ){
					return true;
				} else{
					return false;
				}
			}
		}
	}

	if( !function_exists("get_json_data_from_file")){
		function get_json_data_from_file( $jsonFileName ){
			$fileName = dirname(__FILE__) ."/". $jsonFileName;
			$jsonData = "";
			if( file_exists( $fileName )){
				$theFile = fopen( $fileName, "r" );
				$jsonData = fread( $theFile, filesize( $fileName ));
				fclose( $theFile );
			}
			return $jsonData;
		}
	}

	$jsonFileName = $username . ".json";
	$jsonTempFileName = $username . ".json.tmp";

	if( file_missing_or_old( $jsonFileName, .5 )){
		//back up the old data
		if( file_exists( dirname(__FILE__) ."/". $jsonFileName )){
			copy( dirname(__FILE__) ."/". $jsonFileName, $jsonTempFileName );
		}
		//get new data from twitter
		$jsonURL = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=" . $username . "&include_entities=true";
		curl_to_file( $jsonURL, $jsonFileName );
	}

	$jsonData = get_json_data_from_file( $jsonFileName );
	$haveTwitterData = true;
	// $jsonData now has the feed content

	if( strlen( $jsonData )){
		$tweets = json_decode( $jsonData );
	} else{
		// no tweets
		$haveTwitterData = false;
	}

	// check for errors--rate limit or curl not installed
	// data returned will be: {"error":"Rate limit exceeded. Clients may not make more than 150 requests per hour.","request":"\/1\/statuses\/user_timeline.json?screen_name=salzano&include_entities=true"}

	if( iconv_strlen( $tweets->error, "UTF-8" )){
		//don't have tweets because of an error
		$haveTwitterData = false;
		//delete the json file so it will surely be downloaded on next page view
		unlink( dirname(__FILE__) ."/". $jsonFileName );
		//make the backup file the new primary file
		if( file_exists( dirname(__FILE__) . "/" . $jsonTempFileName )){
			rename( dirname(__FILE__) . "/" . $jsonTempFileName, dirname(__FILE__) . "/" . $jsonFileName );
		}
		//get that data
		$jsonData = get_json_data_from_file( $jsonFileName );
		if( strlen( $jsonData )){
			$haveTwitterData = true;
			$tweets = json_decode( $jsonData );
		}
	} else{
		//good file, create a backup
		if( file_exists( dirname(__FILE__) . "/" . $jsonFileName )){
			copy( dirname(__FILE__) . "/" . $jsonFileName, dirname(__FILE__) . "/" . $jsonTempFileName );
		}
	}

	// $jsonData now has the feed content, $tweets has been json_decoded

	if( $haveTwitterData && $showProfilePicTF ){
		//make sure we have the profile picture saved locally
		$twitterUserData = $tweets[0]->user;
		$profilePicURL = $twitterUserData->profile_image_url;
		$profilePicPieces = explode( ".", $profilePicURL );
		$profilePicExt = end( $profilePicPieces );
		$profilePicFileName = $username . "." . $profilePicExt;
		if( file_missing_or_old( $profilePicFileName, .5 )){
			curl_to_file( $profilePicURL, $profilePicFileName );
		}
	}

	// output the widget
	echo "<li id=\"latest-twitter-widget\">";
	if( !$haveTwitterData ){
		echo "No data available on twitter.com for user " . $username;
	} else{
		if( strlen( $widgetTitle ) > 0 ){
			echo "<h3 id=\"latest-twitter-widget-title\">" . stripslashes( $widgetTitle ) . "</h2>";
		}
		$linkHTML = "<a href=\"http://twitter.com/".$username."\">";
		$pluginURL = get_bloginfo('home')."/wp-content/plugins/latest-twitter-sidebar-widget/";
		$icon = $pluginURL . "twitter.png";
		$pic = $pluginURL . $profilePicFileName;
		if( $showTwitterIconTF ){
			echo $linkHTML . "<img id=\"latest-twitter-widget-icon\" src=\"".$icon."\" alt=\"t\"></a>";
		} else{
			if( $showProfilePicTF ){
				echo $linkHTML . "<img id=\"latest-twitter-widget-pic\" src=\"".$pic."\" alt=\"\"></a>";
			}
		}

		//echo stripslashes( $prefix );
		if( $haveTwitterData ){
			$i=1;
			foreach( $tweets as $tweet ){
				if( $i > $updateCount ){ break; }
				if( !$includeRepliesTF && strlen( $tweet->in_reply_to_screen_name )){ 		continue;	}
				//echo $beforeUpdate;
				echo "<div class=\"latest-twitter-tweet\">&quot;" . fix_twitter_update( $tweet->text, $tweet->entities ) . "&quot;</div>";
				//echo $afterUpdate;
				if( $showTweetTimeTF ){
					echo "<div class=\"latest-twitter-tweet-time\" id=\"latest-twitter-tweet-time-" . $i . "\">" . twitter_time_ltw( $tweet->created_at ) . "</div>";
				}
				$i++;
			}
		}


	}
	//echo stripslashes( $suffix ) . "</li>";
	echo "</li>";
}

function init_latest_twitter(){
	register_sidebar_widget("Latest twitter", "latest_twitter_sidebar_widget");
	register_widget_control("Latest twitter", "latest_twitter_control");
}

function latest_twitter_control() {

	if ( !function_exists('quot') ){
		function quot($txt){
			return str_replace( "\"", "&quot;", $txt );
		}
	}

	$options = get_admin_options_ltw( );

	if ( $_POST['latest-twitter-submit'] ) {
		// get posted values from form submission
		$new_options['user'] = esc_html($_POST['latest-twitter-user']);
		$new_options['count'] = esc_html($_POST['latest-twitter-count']);
		$new_options['prefix'] = "";
		$new_options['suffix'] = "";
		$new_options['beforeUpdate'] = "";
		$new_options['afterUpdate'] = "";
		$new_options['widgetTitle'] = esc_html( $_POST['latest-twitter-widgetTitle']);
		$new_options['showTwitterIconTF'] = false;
		$new_options['showProfilePicTF'] = false;
		switch( $_POST['showIconOrPic'] ){
			case "icon":
				$new_options['showTwitterIconTF'] = true;
				break;
			case "pic":
				$new_options['showProfilePicTF'] = true;
				break;
			case "none":
				break;
		}
		if( $_POST['showTweetTimeTF']=="1"){
			$new_options['showTweetTimeTF'] = true;
		} else{
			$new_options['showTweetTimeTF'] = false;
		}
		if( $_POST['includeRepliesTF']=="1"){
			$new_options['includeRepliesTF'] = true;
		} else{
			$new_options['includeRepliesTF'] = false;
		}
		// if the posted options are different, save them
		if ( $options != $new_options ) {
			$options = $new_options;
			update_option('widget_latest_twitter', $options);
		}
	}

	// format some of the options as valid html
	$username = htmlspecialchars($options['user'], ENT_QUOTES);
	$updateCount = htmlspecialchars($options['count'], ENT_QUOTES);
	$prefix = stripslashes(quot($options['prefix']));
	$suffix = stripslashes(quot($options['suffix']));
	$beforeUpdate = stripslashes(quot($options['beforeUpdate']));
	$afterUpdate = stripslashes(quot($options['afterUpdate']));
	$showTwitterIconTF = $options['showTwitterIconTF'];
	$showProfilePicTF = $options['showProfilePicTF'];
	$showTweetTimeTF = $options['showTweetTimeTF'];
	$widgetTitle = stripslashes(quot($options['widgetTitle']));
	$includeRepliesTF = $options['includeRepliesTF'];
?>
	<div>
	<label for="latest-twitter-user" style="line-height:35px;display:block;">Twitter user: @<input type="text" size="12" id="latest-twitter-user" name="latest-twitter-user" value="<?php echo $username; ?>" /></label>
	<label for="latest-twitter-count" style="line-height:35px;display:block;">Show <input type="text" id="latest-twitter-count" size="2" name="latest-twitter-count" value="<?php echo $updateCount; ?>" /> twitter updates</label>
	<label for="latest-twitter-widgetTitle" style="line-height:35px;display:block;">Widget title: <input type="text" id="latest-twitter-widgetTitle" size="16" name="latest-twitter-widgetTitle" value="<?php echo $widgetTitle; ?>" /></label>
	<p>&nbsp;</p>
	<p><input type="radio" id="latest-twitter-showTwitterIconTF" value="icon" name="showIconOrPic"<?php if($showTwitterIconTF){ ?> checked="checked"<?php } ?>><label for="latest-twitter-showTwitterIconTF"> Show twitter icon</label></p>
	<p><input type="radio" id="latest-twitter-showProfilePicTF" value="pic" name="showIconOrPic"<?php if($showProfilePicTF){ ?> checked="checked"<?php } ?>><label for="latest-twitter-showProfilePicTF"> Show profile picture</label></p>
	<p><input type="radio" id="latest-twitter-showNeitherImageTF" value="none" name="showIconOrPic"<?php if((!$showProfilePicTF) && (!$showTwitterIconTF)){ ?> checked="checked"<?php } ?>><label for="latest-twitter-showNeitherImageTF"> Show no image</label></p>
	<p>&nbsp;</p>
	<p><input type="checkbox" id="showTweetTimeTF" value="1" name="showTweetTimeTF"<?php if($showTweetTimeTF){ ?> checked="checked"<?php } ?>> <label for="showTweetTimeTF">Show tweeted "time ago"</label></p>
	<p><input type="checkbox" id="includeRepliesTF" value="1" name="includeRepliesTF"<?php if($includeRepliesTF){ ?> checked="checked"<?php } ?>> <label for="includeRepliesTF">Include replies</label></p>
	<p>&nbsp;</p>
	<p>To style the output of the widget, modify <a href="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/latest-twitter-sidebar-widget/latest_twitter_widget.css">this CSS stylesheet</a>. You should also back this file up before updating the plugin.</p>
	<input type="hidden" name="latest-twitter-submit" id="latest-twitter-submit" value="1" />
	</div>
<?php

}

function latest_twitter_widget_css( ){
	echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . get_bloginfo('wpurl') ."/wp-content/plugins/latest-twitter-sidebar-widget/latest_twitter_widget.css\" />" . "\n";
}

add_action("plugins_loaded", "init_latest_twitter");
add_action('wp_head', 'latest_twitter_widget_css');

?>