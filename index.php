<?php 
/**
 * Plugin Name: Dashboard Contact Form
 * Plugin URI: http://www.papik-wordpress.eu/plugins/dashboard-contact-form/
 * Description: Custom dashboard widget with optional contact form
 * Version: 1.0.1
 * Author: Pavel Riha
 * Author URI: http://www.papik-wordpress.eu
 * Network: true
 * Text domain: cdw_widget 
 * License:  GPL2
 */
if(!defined('ABSPATH')) die('Direct access not allowed!');
if (is_admin()) include plugin_dir_path( __FILE__ )."settings.php";

add_action('admin_init','cdw_init');
function cdw_init() { 
	if (!session_id())
    session_start();
	include plugin_dir_path( __FILE__ )."widget.php";
	
	load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
/*
** load admin settings
*/

register_activation_hook( __FILE__, 'cdw_install' );
register_uninstall_hook( __FILE__, 'cdw_uninstall' );
/*
** activation of plugin
*/
function cdw_install(){
	add_option('cdw_dashboard_text',cdw_default_data());
	add_option('cdw_password',md5(''));
}
/*
** good manner to delete data after uninstall
*/
function cdw_uninstall(){
	delete_option('cdw_dashboard_text');
	delete_option('cdw_password');
}
/*
** data loader
*/
function cdw_data_load(){
 return unserialize(get_option('cdw_dashboard_text',cdw_default_data()));
}
/*
** Default data and settings to dashboard widget
*/
function cdw_default_data() {
load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 return serialize(array( 
 'title' => __('Dashboard Contact Form','cdw_widget'), 
 'message' => sprintf(__('This is example text. You can change it <a href="%s">here</a>.','cdw_widget'),admin_url("index.php?page=cdw_widget_options")),
 'use_form' => 'on',
 'target_mail' => 'mail@example.com' ,
 'roles'=> get_editable_roles(),
 'placeholder_title'	=>	__('Message Title','cdw_widget'),
 'form_message' 	=>  __('Type here your message.','cdw_widget')));
}