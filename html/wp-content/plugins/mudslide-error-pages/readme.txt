=== Mudslide Custom Errors ===
Tags: template, error pages
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.0.1
Donate Link:N/A
Contributors: mudslidedesign

Allows the uses of a template page 'error.php' instead of the default wordpress rounded rectangle with the message in.

== Description ==

Allows the uses of a template page 'error.php' instead of the default wordpress rounded rectangle with the message in.
Variables $title and $message will be poulated for use in the template

== Installation ==

1. Upload `mudslide-custom-errors` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create an error.php in your templates, $title and $message variables will be populated

== Frequently Asked Questions ==

= What if I don't have an error.php tempalte? =

This will not cause any problems, if the file is not found it will revert to using the default die handler (_default_wp_die_handler), athough 
to get some use out of this plugin you should have the error.php file in your template.

== Screenshots ==

1. This is the standard wordpress error screen
2. This is using a template (error.php) file to style the error page

== Changelog ==

= 1.0 =
* Initial Release

== Upgrade Notice ==
= 1.0 =
Initial Release
