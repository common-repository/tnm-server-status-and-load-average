=== Plugin Name ===
Contributors: Christopher Erk
Tags: Server, Administrator, Optimization, Load Average
Requires at least: 3.0.0
Tested up to: 4.3.1
Stable tag: 1.1

The WordPress Server Load Plugin adds a Server Load Averages and Server Uptime widget into your Admin Dashboard and a load average widget to the admin bar that automatically refreshes.

== Description ==

This Plugin adds a Server Load Averages and Server Uptime widget into your 
Admin Dashboard and admin bar that automatically refreshes.

This Plugin adds an options page in your WordPress Admin Dashboard. After activating 
the Plugin, go to your Admin Dashboard Home and see the new widget and the load average in the admin bar.
You can configure the admin bar load average options from the settings page under:
Settings > [TNM] Server Status

Plugin developed by <a href="http://www.chriserk.com/">Christopher Erk</a>.

== Installation ==

1. Upload 'tnm-server-status' directory to the '/wp-content/plugins/ directory
1. Activate the plugin '[TNM] Server Status' through the 'Plugins' menu in WordPress
1. Go to the Admin Dashboard Homepage and find the new widget.

== Frequently Asked Questions ==

= Will it Increase My Server Load? =

Yes. But probably not enough for you to notice the difference. The AJAX will create a new request every few seconds which queries your database and your server.
Change this option in the settings to a higher number to reduce the number of requests made to the server.

== Screenshots ==

1. Wordpress Server Load Admin Widget with load average in admin bar

== Changelog ==

= 1.0 =
* Initial Release

= 1.1 =
* Bug Fixes