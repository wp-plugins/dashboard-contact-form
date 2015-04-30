=== Custom Dashboard Widget & Dashboard Contact Form ===
Contributors: Papik81
Tags: dashboard, message form, contact form, dashboard widget, user info
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ER96JFW7V7UJG&lc=CZ&amount=5&cy_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Requires at least: 3.5
Tested up to: 4.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows to create a simple dashboard widget for users to inform them and alows them send messages to a predefined e-mail address.

== Description ==
If you want a custom dashboard widget promoting your company, this simple plugin allows you to create it using built-in editor, which supposrts all the Wordpress formating. There is no problem put any external links  to your site and even upload custom logo or any picture! If you're admin, this is the best way to inform all editors and also allows contact you via predefeined email. 


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

= Version 1.0.2 =
* Admins may now choose for which roles is contact form enabled
* Default target email is admin email
* Messages are sent now in HTML format
* Bugfix: title after sending form is now the same as set in settings
* Bugfix: email copy now can be filled blank
* Bugfix: added styles to cdw_fix.css

= Version 1.0.3 =
* Improved formatting on contact form
* Bugfix: default settings, on install is widget and contact form enabled for ALL roles

= Version 1.0.3.1 =
* Bugfix: fixed formatting in settings :)

= Version 1.0.4 =
* Remade settings page to be more intuitive
* Some translation files updates

= Version 1.0.5 =
* Added feature to change text after contact form title
* Bugfix: message placeholder now can be edited
* Bugfix: problems with quotes

= Version 1.0.6 =
* Bugfix: problems with quotes in form message
