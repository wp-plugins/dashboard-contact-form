<?php 
if(!defined('ABSPATH')) die('Direct access not allowed!');
add_filter('form_content', 'cdw_form_placeholders');
function cdw_form_placeholders($message , $cdw_data = null)
{
	$user = wp_get_current_user();
	if($cdw_data == null) $cdw_data  = cdw_data_load();
	$message = str_replace('%SENDER_NAME%', $user->display_name,$message);
	$message = str_replace('%SENDER_EMAIL%', $user->user_email,$message);
	$message = str_replace('%TARGET_EMAIL%', $cdw_data['target_mail'],$message);
	$message = str_replace('%COPY_EMAILS%', $cdw_data['target_mail'],$message);
	return $message;
}
