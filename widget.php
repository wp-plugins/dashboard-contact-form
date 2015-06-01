<?php if(!defined('ABSPATH')) die('Direct access not allowed!');
add_action( 'wp_dashboard_setup', 'cdw_custom_dashboard_widgets');
add_action( 'wp_ajax_cdw_send_message', 'cdw_send_message' );

/*
** function loading dashboard widget
*/
if (!function_exists('cdw_custom_dashboard_widgets')) {
	function cdw_custom_dashboard_widgets() {
	global $wp_meta_boxes;
	// lets load dashboard with predefined title
	global $cdw_data;
	 $cdw_data = cdw_data_load();
	 global $current_user;
	 $user_role = $current_user->roles[0];
	 //echo "<pre>user_rule:\n".print_r($user_role,true)."</pre>\n";
	 //echo "<pre>user_roles:\n".print_r($cdw_data['roles'],true)."</pre>";
	 //echo "<pre>get_editable_roles():\n".print_r(get_editable_roles(),true)."</pre>";
	 //display only if allowed for the role of current user
		if (!empty($cdw_data['roles']) && in_array($user_role,$cdw_data['roles']) || current_user_can('manage_options')) {
			add_meta_box('cdw_widget', $cdw_data['title'], 'cdw_dashboard_widget', 'dashboard', 'side', 'high');
			add_action( 'admin_enqueue_scripts', 'cdw_fix_styles' );
			// if user of its role has widget enabled
			if (in_array($user_role,$cdw_data['roles_cf'])) add_action( 'admin_enqueue_scripts', 'cdw_localize_form' );
		}
	}
}
/*
** Load CSS fixes
*/
if (!function_exists('cdw_fix_styles')) {
	 function cdw_fix_styles(){
		wp_enqueue_style('cdw_fix', plugins_url('css/cdw_fix.css', __FILE__));
	 }
}
/*
** script localization and form control
*/
if (!function_exists('cdw_localize_form')) {
	function cdw_localize_form() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('scfjs', plugins_url( 'js/scf.js' , __FILE__ ), array('jquery'));
		global $cdw_data;
		$localize = array(
		  'ajaxurl' => admin_url( 'admin-ajax.php' ),
		  'sending'=> __('Submitting message...','cdw_widget'),
		  'invalid' => __('Fill in all fields, some fields are empty','cdw_widget')
					);
				wp_localize_script('scfjs', 'SCF', $localize);
				
	 }
 }/*
** function displaying widget itself
*/
if (!function_exists('cdw_dashboard_widget')) {
	function cdw_dashboard_widget() {
		$cdw_data = cdw_data_load();
		$cdw_data['message']= apply_filters('the_content',$cdw_data['message']); 
		$cdw_data['message'] = apply_filters('form_content',$cdw_data['message'],$cdw_data);
		echo $cdw_data['message'];
		 global $current_user;
		 $user_role = $current_user->roles[0];
		if (in_array($user_role,$cdw_data['roles_cf'])) {
			$user = wp_get_current_user();
			$encoding = get_option('blog_charset');
			?><div style="clear: both"></div>
			<form name="post" action="<?php echo admin_url(); ?>" method="post" id="cdw-support" class="initial-form hide-if-no-js">

					
					<div class="input-text-wrap">
						<hr />
						<?php if(isset($cdw_data['form_title'])) : ?> <h4><strong><?php echo $cdw_data['form_title']; ?></strong></h4><?php endif; ?>
							<?php $cdw_data['form_message'] = apply_filters('form_content',$cdw_data['form_message'],$cdw_data); 
							echo stripslashes($cdw_data['form_message']); ?></br>
						<input name="author_name" id="cdw_author" autocomplete="off" type="hidden" value ="<?php echo esc_attr($user->display_name); ?>">
					</div>

					<div class="input-text-wrap">
						<label for="mail_title">
							<?php _e('Title:','cdw_widget'); ?></label>
						<input name="mail_title" id="cdw_title" autocomplete="off" type="text" placeholder="<?php echo isset($cdw_data['placeholder_title']) ? stripslashes(htmlspecialchars($cdw_data['placeholder_title'],ENT_QUOTES,$encoding)) : ''; ?>">
					</div>

					<div class="textarea-wrap">
						<label for="mail_content"><?php _e('Your message:','cdw_widget'); ?></label>
						<textarea name="mail_content" id="cdw_content" class="mceEditor" rows="3" cols="15" autocomplete="off" value="" placeholder = "<?php echo isset($cdw_data['placeholder_message']) ? stripslashes(htmlspecialchars($cdw_data['placeholder_message'],ENT_QUOTES,$encoding)) : ''; ?>" ></textarea>
					</div>

					<div class="submit" style="margin-top:12px">
						<input name="author_email" id="cdw_email" class="author" value="<?php echo $user->user_email; ?>" type="hidden">	
						<input name="save" id="send-message" class="button button-primary" value="<?php _e('Send message','cdw_widget') ?>" type="submit">
						<img class="scf-ajax" src="<?php echo plugins_url( 'img/sending.gif', __FILE__ ); ?>" alt="<?php _e('Sending Message','cdw_widget');?>" style="display: none; margin: 10px 20px 0 0">			<br class="clear">
						
					</div>
				<div class="formmessage"><p></p></div>
				</form><?php 
			}
	}
}
/*
** ajax callback to send message
*/
if (!function_exists('cdw_send_message')) {
	function cdw_send_message(){
		
		$cdw_data = cdw_data_load();
		$name = sanitize_text_field($_POST['author_name']);
		$email = sanitize_email($_POST['author_email']);
		$subject = sanitize_text_field($_POST['mail_title']);
		$message = apply_filters('the_content',wp_kses_data($_POST['mail_content']));
		$user = wp_get_current_user();
		$username = $user->user_login;
		$message .= "<br />\n-------------------------------------\n<br />".sprintf(__('This message has been sent from <strong>%1$s</strong> at <strong>%2$s</strong> by user <strong>%3$s</strong>.','cdw_widget'),str_replace('http://','',get_option('siteurl')), date(get_option('date_format').', '.get_option('time_format'),time()),$username);
		$headers = "From: \"".$name."\" <". $email . ">\r\n";
		$headers .= "X-Sender: ".$email."\r\n";
		$headers .= "Reply-To: " . $email ."\r\n";
		if(!empty($cdw_data['cc_mail'])) $headers .= "Cc: ".$cdw_data['cc_mail']."\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n"; // Mime typ
		$headers .= "MIME-Version: 1.0 \r\n";
		$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";

		/* Recipients */
		$to = $cdw_data['target_mail'];
		/* Sending e-mail */
		if (@wp_mail( $to, $subject, $message, $headers ) === true)
			printf(__('Message succesfully sent to <strong>%s</strong>','cdw_widget'), $to);
		else _e('Couldn\'t send message.','cdw_widget');
		die(); // Important

	}
}