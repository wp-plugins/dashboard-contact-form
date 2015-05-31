<?php 
/**
 * Plugin Name: Custom Dashboard Widget & Dashboard Contact Form
 * Plugin URI: http://www.papik-wordpress.eu/plugins/dashboard-contact-form/
 * Description: Custom dashboard widget with optional contact form
 * Version: 1.1.0
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
add_action('wp_logout','cdw_session_destroy');
/*
** Instalation actions
*/

register_activation_hook( __FILE__, 'cdw_full_install' );
register_uninstall_hook( __FILE__, 'cdw_full_uninstall' );

function cdw_network_propagate($pfunction, $networkwide) {
    global $wpdb;
 
    if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function 
        // for each blog id
        if ($networkwide) {
            $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                call_user_func($pfunction, $networkwide);
            }
            switch_to_blog($old_blog);
            return;
        }   
    } 
    call_user_func($pfunction, $networkwide);
}
/**
 * Multisite install function
 * @return void
*/
function cdw_full_install($networkwide) {
    cdw_network_propagate('cdw_install', $networkwide);    
}
/**
 * Multisite uninstall function
 * @return void
*/
function cdw_full_uninstall($networkwide) {
    cdw_network_propagate('cdw_uninstall', $networkwide);    
}
if (!function_exists('cdw_init')) {
	function cdw_init() { 
		// start unique session
		global $current_user;
		get_currentuserinfo();
		$session_id = 'CDW'.$current_user->user_login;
		if (!session_id($session_id)) {
			session_name($session_id);
			session_start();
		}
		load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
		include plugin_dir_path( __FILE__ )."filter.php";
		include plugin_dir_path( __FILE__ )."widget.php";
		
	}
}
/**
 * Function on adding site on multisite
 * @return void
 */
add_action( 'wpmu_new_blog', 'cdw_new_blog', 10, 6);        
 
function cdw_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;
	$old_blog = $wpdb->blogid;
    if (is_plugin_active_for_network('wp-category-meta.php')) {
        switch_to_blog($blog_id);
        cdw_install();
        switch_to_blog($old_blog);
    }
}
/*
** Activation of plugin
*/
if (!function_exists('cdw_install')) {
	function cdw_install(){
		add_option('cdw_dashboard_text',cdw_default_data());
		add_option('cdw_password',md5(''));
	}
}
/*
** Good manner to delete data after uninstall
*/
if (!function_exists('cdw_uninstall')) {
	function cdw_uninstall(){
		delete_option('cdw_dashboard_text');
		delete_option('cdw_password');
	}
}
/*
** Data loader
*/
if (!function_exists('cdw_data_load')) {
	function cdw_data_load(){
	 $cdw_data = unserialize(get_option('cdw_dashboard_text',cdw_default_data()));
	 $cdw_default = unserialize(cdw_default_data());
	 // adds optional form message
	 if(!isset($cdw_data['form_message'])) 
	 {	
	   $form_message = array('form_message' => $cdw_default['form_message']);
		$cdw_data = array_merge($cdw_data,$form_message);
	}
	 if(!isset($cdw_data['roles_cf'])) $cdw_data['roles_cf'] = $cdw_default['roles_cf'];
	 unset($cdw_default);
	 return $cdw_data;
	}
}
/*
** Default data and settings to dashboard widget
*/
if (!function_exists('function cdw_default_data')) {
	function cdw_default_data() {
	load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	 return serialize(array( 
	 'title' 				=> __('Dashboard Contact Form','cdw_widget'), 
	 'message' 				=> sprintf(__('<img class="aligncenter size-medium" src="%s" alt="larx-logo" width="300" height="96" /><p style="text-align: center;">This is example text. You can change it <a href="%s">here</a>.</p>','cdw_widget'),plugins_url( 'larx-logo.png' , __FILE__ ),admin_url('options-general.php?page=cdw_widget_options')),
	 'target_mail' 			=> get_option('admin_email') ,
	 'form_title'			=>	__('Contact us:','cdw_widget'),
	 'form_message'			=>	__("<strong>Sender:</strong> %SENDER_NAME%&lt;%SENDER_EMAIL%&gt;<br />\n<strong>Receiver:</strong> %TARGET_EMAIL%",'cdw_widget'),
	 'placeholder_title'	=>	__('Message Title','cdw_widget'),
	 'placeholder_message' 	=>  __('Type here your message.','cdw_widget'),
	 'roles'				=>  cdw_all_roles(),
	 'roles_cf'				=>  cdw_all_roles()
	 ));
	}
}
/*
** List of all roles
*/
if (!function_exists('cdw_all_roles')) {
	function cdw_all_roles(){
	 $roles = get_editable_roles();
	 $rolelist = array();
	 foreach($roles as $key=>$role)
	 {
	  $rolelist[$key]=$role['name'];
	 }
	 return $rolelist;
	}
}
/*
** Ensure destroying session
*/
if (!function_exists('cdw_session_destroy')) {
	function cdw_session_destroy(){
		global $current_user;
		get_currentuserinfo();
		$session_id = 'CDW'.$current_user->user_login;
		if (session_id($session_id)) session_destroy();
	}
}