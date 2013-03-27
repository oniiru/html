=== Browser Blocker ===
Contributors: bdoga, tdawg2
Plugin URI: http://www.macnative.com/development/browser-blocker
Author URI: http://www.macnative.com
Donate link: http://www.macnative.com/development/donate 
Tags: browser, ie, internet explorer, chrome, firefox, opera, netscape, iphone, mobile, blackberry, android, safari, ipad, lynx, block, css, errors, display, splash, reject, recommend, download, awesome, pages, ultimate
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 0.5.6

Browser Blocker allows you to pick and choose which browsers(versions) can access your web page and which ones are given a blocked splash screen.

== Description ==

The Browser Blocker Plugin puts the power in your hands to control what browsers(versions) can access your website content and which ones cannot. For the browsers(versions) that you block, the user has control over the splash screen that their clients receive. There is a "simple" mode for those who just want to block some browsers with the default settings, and there is an "advanced" mode for those who want to have a little more control over what is displayed to their clients.

If you would like to see more of our plugins feel free to drop by the [Browser Blocker plugin page][1] or the [Macnative.com Homepage][2]. 

 [1]: http://www.macnative.com/development/browser-blocker/
 [2]: http://www.macnative.com/

This plugin utilizes the [Browser detection library] that was built by [Chris Schuld][] A big thanks to him for his major contribution.
[Browser detection library]: http://chrisschuld.com/projects/browser-php-detecting-a-users-browser-from-php/
[Chris Schuld]: http://chrisschuld.com/

A big thanks to Vistaicons.com for creating the Web Browsers Icon Set that is used with the plugin. [Follow the link to see more of their icons][3].

 [3]: http://www.vistaicons.com/

Another big thanks to Yusuke Kamiyamane for the use of many of his Fugue Icon set icons throughout the Plugins Administrator, [please visit their homepage][4].

 [4]: http://p.yusukekamiyamane.com/


== Installation ==

1. Unzip and upload the Browser Blocker plugin folder to wp-content/plugins/
1. Activate the plugin from your WordPress admin panel.
1. Installation finished.

== Frequently Asked Questions ==

= How do I set it up? =

The link to the plugin page will appear in the "Settings" admin menu. By default the "Simple" options page appears, go ahead and select "enable", then determine what versions of browsers that you would like to block from accessing your website. 

Once you have entered in a browser version click the green "+" icon to add it to the list. 

After you have added entries for all of the browser versions that you want to block, save the settings and your site will immediately start blocking those browsers.

= Help!, the plugin is now blocking browsers that I did not intend =

Browser blocker uses a php based browser detection library, and it does it's best to identify the browser and version that are visiting your site, but sometimes it can give false positives. 

If you are receiving this sort of an error, you have 2 courses of action, First you can try clearing your settings and set them afresh to see if that resolves your issue, or you can disable the plugin all together. 

If for some reason you have blocked yourself from your website, you can disable the plugin by changing the plugin's folder name in your wordpress plugins directory '/wp-content/plugins/browser-blocker' to be something else '/wp-content/plugins/browser-not-blocker' or something similar, and this will disable the active plugin.

= What if this plugin doesn't do something that I want it to? =

[Drop me a line][3] and I will work on getting it added.
 [3]: http://www.macnative.com/contact/

== Screenshots == 

1. Simple Admin Interface
2. Advanced Admin Interface
3. Default Splash Page

== Changelog ==

= 0.5.6 [2012-06-16] =
* Fixed Bug caused the bypass link to send users to the sites homepage rather than the requested page.
* Changed up some of the activation code hoping that it helps resolve an activation issue that some users are experiencing.
* Updated for Wordpress 3.4.2

= 0.5.5 [2012-06-16] =
* Fixed Bug that caused download links to not be accessible via IE6
* Updated for Wordpress 3.4.1

= 0.5.4 [2012-06-16] =
* Updated for Wordpress 3.4

= 0.5.3 [2012-04-28] =
Bugfix
* Addressed a minor issue that added backslashes to some characters in custom text from the Advanced Settings Page

= 0.5.2 [2012-04-25] =
* Updated for Wordpress 3.3.2

= 0.5.1 [2012-04-06] =
Bugfix
* Added some images that for some reason were not added by SVN for the 0.5 release

= 0.5 [2012-04-05] =
New Features
* Added option to allow blocked users to bypass the splash page and proceed to the site even though they have been blocked (in Advanced Settings).
* Added option to add custom code at the bottom of the splash page, ie: google analytics (in Advanced Settings).
* Added Opera as an upgrade/download option.

= 0.4.4 [2012-03-01] =
Bugfix
* Fixed an issue that limited the number of characters that could be entered in the Title and Image Field on the Advanced Options Page.

New Features
* Added Option to Display or Hide the Tagline Text between the Browser Icon and the Download Button

= 0.4.3 [2012-02-23] =
Bugfix
* Fixed an issue that made it difficult to remove browser versions after they have been added.
* Fixed an issue that continuously added slashes to special characters on each submit.

= 0.4.2 [2012-02-11] =
Bugfix
* The 4.0 release included the ability to block specific pages from being accessible, this seems to have allowed the blocked browsers to make it into certain analytics tracking utilities, this bugfix should address that behavior.

= 0.4.1 [2012-02-07] = 
Bugfix
* For some reason Wordpress.org plugin system did not update version number in it's system, so a new minor release to make sure users get the update.

= 0.4 [2012-02-07] =
New Features
* Added Settings link on the Plugin Administration Panel for easy access
* Added advanced option to select specific pages to block vs. blocking the whole site

= 0.3 [2012-01-17] = 
New Features
* Added a button in both the Simple and Advanced interfaces that would restore the default settings, clearing out any previous settings.

Other Changes
* Removed the Reset Defaults form button on the Advanced screen, as it was rendered unnecessary by the new button.
* Added additional information to the Readme File

= 0.2 [2011-09-24] = 
Bugfixes
* Fixed error that would display on Advanced Screen with each settings save action.

New Features
* Add Ability to Select the Browser Download Buttons that are displayed on the Splash Page.

= 0.1 [2011-09-24] = 
* Initial Release

== Upgrade Notice ==

= 0.5.3 = 
Fixed an issue that inserted backslashes in some of the custom text fields of the splash page


