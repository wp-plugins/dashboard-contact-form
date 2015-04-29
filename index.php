<?php 
/**
 * Plugin Name: Dashboard Contact Form
 * Plugin URI: http://www.papik-wordpress.eu/plugins/dashboard-contact-form/
 * Description: Custom dashboard widget with optional contact form
 * Version: 1.0.5
 * Author: Pavel Riha
 * Author URI: http://www.larxdigital.com
 * Network: true
 * Text domain: cdw_widget 
 * License:  GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
if(!defined('ABSPATH')) die('Direct access not allowed!');
if (is_admin()) include plugin_dir_path( __FILE__ )."settings.php";

add_action('admin_init','cdw_init');
function cdw_init() { 
	if (!session_id())
    session_start();
	load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	include plugin_dir_path( __FILE__ )."filter.php";
	include plugin_dir_path( __FILE__ )."widget.php";
	
}
/*
** Instalation actions
*/

register_activation_hook( __FILE__, 'cdw_install' );
register_uninstall_hook( __FILE__, 'cdw_uninstall' );
/*
** Activation of plugin
*/
function cdw_install(){
	add_option('cdw_dashboard_text',cdw_default_data());
	add_option('cdw_password',md5(''));
}
/*
** Good manner to delete data after uninstall
*/
function cdw_uninstall(){
	delete_option('cdw_dashboard_text');
	delete_option('cdw_password');
}
/*
** Data loader
*/
function cdw_data_load(){
 $cdw_data = unserialize(get_option('cdw_dashboard_text',cdw_default_data()));
 $cdw_default = cdw_default_data();
 // fix for wrongly saved data in previous version as not intended
 if(!isset($cdw_data['placeholder_message'])) 
 {	
	$cdw_data['placeholder_message'] = $cdw_default['placeholder_message'];
	$cdw_data['form_message'] = $cdw_default['form_message'];
}
 if(!isset($cdw_data['roles_cf'])) $cdw_data['roles_cf'] = $cdw_default['roles_cf'];
 unset($cdw_default);
 return $cdw_data;
}
/*
** Default data and settings to dashboard widget
*/
function cdw_default_data() {
load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 return serialize(array( 
 'title' 				=> __('Dashboard Contact Form','cdw_widget'), 
 'message' 				=> sprintf(__('This is example text. You can change it <a href="%s">here</a>.','cdw_widget'),admin_url("index.php?page=cdw_widget_options")),
 'target_mail' 			=> get_option('admin_email') ,
 'form_title'			=>	__('Contact us:','cdw_widget'),
 'form_message'			=>	__("<strong>Sender:</strong> %SENDER_NAME%&lt;%SENDER_EMAIL%&gt;<br />\n<strong>Receiver:</strong> %TARGET_EMAIL%",'cdw_widget'),
 'placeholder_title'	=>	__('Message Title','cdw_widget'),
 'placeholder_message' 	=>  __('Type here your message.','cdw_widget'),
 'roles'				=>  cdw_all_roles(),
 'roles_cf'				=>  cdw_all_roles()
 ));
}
/*
** List of all roles
*/
function cdw_all_roles(){
 $roles = get_editable_roles();
 $rolelist =array();
 foreach($roles as $role)
 {
  $rolelist[] = $role['name'];
 }
 return $rolelist;
}