<?php
/*
Plugin Name: Twitter Timeline
Plugin URI: 
Description: Twitter Timeline Shortcode
Author: Denzel Chia
Version: 1.1
Author URI:
*/

/*
* function to get user timeline, does not require oAuth.
* @param string $user for username
* @param string $include_retweet, whether to include retweet or not.
* @param int $count, number of tweets to return.
*/
function truethemes_get_twitter_timeline($user,$include_retweet='true',$count){
$request_url = "https://api.twitter.com/1/statuses/user_timeline.xml?include_entities=true&include_rts={$include_retweet}&screen_name={$user}&count={$count}";
//use cURL for request, works without fopen
$rs_ch = @curl_init($request_url); //set curl handle
curl_setopt($rs_ch, CURLOPT_FOLLOWLOCATION ,0); // Do not follow location.
curl_setopt($rs_ch, CURLOPT_SSL_VERIFYPEER, 0); // Do not verify SSL
curl_setopt($rs_ch, CURLOPT_HEADER ,0);  // Do not return http header
curl_setopt($rs_ch, CURLOPT_RETURNTRANSFER ,1); // return the contents
curl_setopt($rs_ch, CURLOPT_TIMEOUT, 20);//set time out 
$response = curl_exec($rs_ch); //execute response
$xml = new SimpleXMLElement($response); // convert response to xml;
curl_close($rs_ch); //close handle
return $xml;//return xml
}

/*
* function to make twitter mention, link, hashtags, clickable.
* original script from http://www.snipe.net/2009/09/php-twitter-clickable-links/
*/
function truethemes_twitterify($ret) {
  $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
  $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
  $ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
  $ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
return $ret;
}

function truethemes_print_twitter_timeline($atts){
		extract(shortcode_atts(array(
  		'retweets' => 'true',
  		'num' => '3',
  		'user' => '', 
  		), $atts));
  		
if($retweets == 'false'){
$retweets = 0;
}

$twitter_status = truethemes_get_twitter_timeline($user,$retweets,$num);
$html = '<ul class="twitterList">';
foreach($twitter_status->status as $status){
$html .= "<li><span>".truethemes_twitterify($status->text)."</span><br/>";
$html .= '<span class="tweet_days">['.human_time_diff(strtotime($status->created_at)).' ago]</span></li>';
}
$html.="</ul>";
return $html;
}
add_shortcode('latest_tweets','truethemes_print_twitter_timeline');
?>