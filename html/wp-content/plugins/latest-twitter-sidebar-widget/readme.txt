=== Latest twitter sidebar widget ===
Contributors: salzano
Donate link: http://www.tacticaltechnique.com/donate/
Tags: twitter, twitter widget, twitter sidebar, twitter plugin
Requires at least: 3.0.0
Tested up to: 3.2.1
Stable tag: 0.110801

Display the latest updates for any twitter user in a widget. Automatically embeds links to @users, #hashtags and urls.

== Description ==

<p>This widget will display the most recent tweets for any twitter user
who has their tweets set as public. Show up to 20 tweets. Include a link to your twitter profile and a twitter icon or your profile picture.</p>
<p>Include "time ago" timestamps below each tweet just like twitter. Updates automatically and looks great in any sidebar or widget area.</p>
<p>CSS friendly! All HTML includes element id attributes, and a default stylesheet.css is included.</p>

== Installation ==

1. Download latest-twitter-sidebar-widget.zip
1. Decompress the file contents
1. Upload the latest-twitter-sidebar-widget folder to a Wordpress plugins directory (/wp-content/plugins)
1. Activate the plugin from the Administration Dashboard
1. Open the Widgets page under the Appearance section
1. Drag the Latest Twitter widget to the active sidebar
1. Configure the widget options to suit your needs and click Save

== Frequently Asked Questions ==

= Can you change how this plugin looks on your site? = 

Yes. The plugin uses a .css stylesheet file. [Here are a few examples](http://www.tacticaltechnique.com/wordpress/latest-twitter-sidebar-widget/#css_samples) you can copy and paste into that file.

= How often does this plugin get my latest tweets from twitter? =

At least once every 30 minutes.

= Need more help? =

[Visit this plugin's home page](http://www.tacticaltechnique.com/wordpress/latest-twitter-sidebar-widget/)

== Screenshots ==

1. Widget configuration menu
1. Sample output

== Change Log ==

= 0.110801 =
* Remove extra slashes in icon and profile pic URLs
* Added FAQ section to plugin readme.txt
* Added my email address to standard plugin information header

= 0.110711 = 
* Saves a backup file of the tweets in case twitter returns an API error
* Removes before and after variables and now relies on the stylesheet only

= 0.110509 =
* Now properly handles international characters in tweets via UTF-8
* Added option to not include @replies

= 0.110412 =
* Fixed error when show profile picture is enabled and no tweets found
* Added optional widget title
* Updated screen shot

= 0.110330 = 
* Fixed hashtag links to twitter.com
* Fixed profile link when showing no profile image or twitter icon

= 0.110323 =
* Include tweet times with new option

= 0.110318 =
* Display twitter profile picture or the twitter icon or neither
* Now includes image alt tags to better support HTML standard spec
* Bigger input fields on the widget settings menu

= 0.110224 =
* Faster refresh rate for better synchronization
* Removed link target on links to twitter profile

= 0.101205 =
* Better error handling for twitter API response

= 0.101126 =
* Handle twitter API entities object null state
* Fix off-by-one count during counting of twitter updates
* Added twitter tag to readme file

= 0.101120 = 
* Introduced external .css file for formatting
* Added element ID to HTML tags

= 0.101115 =
* Rewrite to work with twitter's 2010 API
* Uses PHP curl to pull data from Twitter
* Uses PHP json to manipulate JSON data

= 0.100210 =
* More features added to the widget options to increase customization
* Added a link below the twitter updates "follow @username on twitter" to make link more obvious
* This will be the last version that supports PHP4's DOMXML object available from the WP Plugin Directory

= 0.090401 =
* Options available to choose username and number of twitter updates to show

= 0.081204 =
* Displays more than one latest update

= 0.081125 = 
* First build

== Upgrade Notice ==

= 0.110801 = 
Adding a plugin FAQ section to spread the information I have written for users of this plugin.

= 0.110711 = 
I finally killed the before and after text boxes from the widget options. These were bad features and encouraged bad web development techniques. The output is now completely controlled by the stylesheet in the plugin's folder. Thanks to Iko for pointing out the mistake I made during API error handling.

= 0.110509 =
This plugin now handles international characters in the UTF-8 character set properly. I found out about this issue thanks to comments from kyong and MikeC. Another Mike asked if he could exclude replies, and that option is in this version, too.

= 0.110412 =
Another bug that was introduced with the profile picture option has been fixed. A title for the whole widget is now an option.

= 0.110330 = 
This update fixes two bugs. Hashtags and the first update were both improperly linked to the twitter website.

= 0.110323 =
Include timestamps like "6 minutes ago" below each tweet, just like twitter

= 0.110318 =
Include your twitter profile picture instead of the twitter icon

= 0.110224 =
Downloads data from twitter once the local copy is 30 minutes old instead of a few hours

= 0.101205 =
Handle errors and gracefully omit tweets when they are not available

= 0.101126 =
Bug fixes based on user feedback

= 0.101120 =
Code improvements to enable easier CSS styling of the widget output

= 0.101115 =
Twitter loves to change their API. Updated to work with the new JSON format.

= 0.100210 =
More customization available in widget options. Final PHP4 / DOMXML build before a rewrite for PHP5

== History ==
<p>Thanks to Iko @nekoikono for pointing out I was handling twitter API errors improperly. Iko also wrote caching code before I did and emailed it to me. This was a tremendous help in identifying the bug.</p>

<p>Thanks to @doublesixx on twitter for helping me track down a bug that was fixed in version 0.110412.</p>

<p>Thanks to whomever posted the twitter_time PHP function online anonymously to turn timestamps into pretty "time ago" messages</p>

<p>Thanks to Paul H and Ron B for providing me with feedback for the last 2010 November updates.</p>

<p>This plugin is a testament to twitter's treatment of third party developers pre-2010. A poor API 
was followed by a new API and the destruction of the old. Lots of code broke, including this plugin. 
I enjoyed the near-total rewrite, and I am happy to maintain this plugin should such drastic changes 
occur once again.</p>

<p>The first version of this plugin was based upon code 
written by Ryan Barr.
I abandoned Ryan's code at version 0.081204, but I still
credit his example for introducing me to the 2008 Twitter API.</p>