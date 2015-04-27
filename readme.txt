=== Dashboard Contact Form ===
Contributors: Papik81
Tags: dashboard, message form, contact form, dashboard widget, user info
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ripasu%40volny%2ecz&item_name=Dashbopard%20Contact%20Form%20Donation&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&amount=5&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8
Requires at least: 3.5
Tested up to: 4.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows to create a simple dashboard widget for users to inform them and alows them send messages to a predefined e-mail address.

== Description ==
Allows to create a simple dashboard widget for users to inform them and aloww them send messages to a predefined e-mail address. Admins may choose a which all users groups can see this widget, also simply edit not only the message using built-in WP editor, but also placeholders and target e-mail (default is set to admin e-mail). Shortcodes and image upload is supported too. Czech translation available only.


== Installation ==
Best is to install directly from WordPress. If manual installation is required, please make sure that the plugin files are in a folder named "custom-dashboard-widget" (not two nested folders) in the WordPress plugins folder, usually "wp-content/plugins".

== Frequently Asked Questions ==
### Where can I edit the form? ###
Go to Settings and look for the "Custom Dashboard Widget".

###  When I set the password to lock editing the widget and forget it, is there a way to reset it? ###
The simplest way to reset the password is uninstaling the plugin and reinstalling it. Second, more complicated is for advanced users: go to your options table in your database (typically "wp_options"), find a value to name "cdw_password" and delete it, before saving it choose from functions "md5" (you may also type your own password rather than leaving it blank). The md5 value for a blank password should be "d41d8cd98f00b204e9800998ecf8427e".


== Screenshots ==
1. Editing screen
2. Optional password
3. Widget itself
4. You can use widget free of form just for information to other users.

== Changelog ==

= Version 1.0.0 =
* release version
 
 = Version 1.0.1 =
* Bugfix: fixes issues in setttings page for changing password