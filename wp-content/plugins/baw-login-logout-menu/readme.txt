=== BAW Login Logout Menu ===
Contributors: juliobox, GregLone
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJGT942XKWJ6W
Tags: login, log in, logout, menu, nonce
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: trunk

Add real �Log in� and �Logout�, �Register� links into your WordPress menus!

== Description ==

With this plugin you can now add a real log in/logout, register items menu with autoswitch when user is logged in or not.
Nonce token is present on logout item.
2 titles, one for 'log in' and one for 'logout' can be set up.
Also, you can set the redirection page you want, just awesome.

== Installation ==

1. Upload the *"baw-login-logout-menu"* folder into the *"/wp-content/plugins/"* directory
1. Activate the plugin through the *"Plugins"* menu in WordPress
1. You can now add real log in and logout links in your Navigation Menus
1. See FAQ for usage

== Frequently Asked Questions ==

= How does this works? =

Visit your navigation admin menu page, you got a new box including 4 links, 'log in', 'logout', 'log in/logout', 'register'.

Add the link you want, for example "Log in|Logout":

1. You can change the 2 titles links, just separate them with a | (pipe)
1. You can add a page for redirection, example #bawloginout#index.php This will redirect users on site index.
1. You can add 2 pages for redirection, example #bawloginout#login.php|logout.php This will redirect users too.
1. For this redirection you can use the special value %actualpage%, this will redirect the user on the actual page.

You can also add 4 shortcodes in your theme template or in your pages/posts. just do this:
For theme : `<?php echo do_shortcode( '[loginout]' ); ?>`
In you posts/pages : `[loginout]`

The 4 shortcodes are "[login]", "[logout]", "[loginout]" and "[register]".
You can set 2 parameters, named "redirect" and "edit_tag" (but register).
Redirect: used to redirect the user after the action (log in or out) ; example : "/welcome/" or "index.php"
Edit_tag: used to modify the <a> tag, ; example " class='myclass'" or " id='myid' class='myclass' rel='friend'" etc

You can also modify the title link with [login]Clic here to connect[/login] for example

== Screenshots ==

1. The meta box in nav menu admin page

== Changelog ==

= 1.3 =
* 17 jul 2012
* Add a "Register" menu and shortcode. If you are logged, nothing is displayed.

= 1.2 =
* 29 jun 2012
* You can now add 2 pages for the #bawloginout# choice, check the FAQ

= 1.1 =
* 13 mar 2012
* 3 shortcodes added, see FAQ

= 1.0 =
* 08 mar 2012
* First release


== Upgrade Notice ==

None