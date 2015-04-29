<?php 
if(!defined('ABSPATH')) die('Direct access not allowed!');
add_filter('form_content', 'cdw_form_placeholders');
function cdw_form_placeholders($cdw_data)
{
$user = wp_get_current_user();

 $cdw_data['form_message'] = str_replace('%SENDER_NAME%', $user->display_name,$cdw_data['form_message']);
 $cdw_data['form_message'] = str_replace('%SENDER_EMAIL%', $user->user_email,$cdw_data['form_message']);
 $cdw_data['form_message'] = str_replace('%TARGET_EMAIL%', $cdw_data['target_mail'],$cdw_data['form_message']);
 $cdw_data['form_message'] = str_replace('%COPY_EMAILS%', $cdw_data['target_mail'],$cdw_data['form_message']);
 return $cdw_data;
}
