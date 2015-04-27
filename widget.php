<?php if(!defined('ABSPATH')) die('Direct access not allowed!');
add_action( 'wp_dashboard_setup', 'cdw_custom_dashboard_widgets');
//add_action( 'admin_init','cdw_load_textdomain');
add_action( 'wp_ajax_cdw_send_message', 'cdw_send_message' );

function cdw_load_textdomain() {

  load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
  
}
/*
** function loading dashboard widget
*/
function cdw_custom_dashboard_widgets() {
global $wp_meta_boxes;
// lets load dashboard with predefined title
 $cdw_data = cdw_data_load();
 global $current_user;
 $user_role = $current_user->roles[0];
 //display only if allowed for the role of current user
	if (!empty($cdw_data['roles']) && in_array(ucfirst($user_role),$cdw_data['roles']) || current_user_can('manage_options')) {
		add_meta_box('cdw_widget', $cdw_data['title'], 'cdw_dashboard_widget', 'dashboard', 'side', 'high');
		add_action( 'admin_enqueue_scripts', 'cdw_fix_styles' );
		if ($cdw_data['use_form'] == "on") add_action( 'admin_enqueue_scripts', 'cdw_localize_form' );
	}
}
/*
** Load CSS fixes
*/
 function cdw_fix_styles(){
 wp_enqueue_style('cdw_fix', plugins_url('css/cdw_fix.css', __FILE__));
 }
/*
** script localization and form control
*/
 function cdw_localize_form() {
 wp_enqueue_script('jquery');
 wp_enqueue_script('scfjs', plugins_url( 'js/scf.js' , __FILE__ ), array('jquery'));
 $localize = array(
  'ajaxurl' => admin_url( 'admin-ajax.php' ),
  'sending'=> __('Submitting message','cdw_widget'),
  'invalid' => __('Fill in all fields, some fields are empty','cdw_widget'),
  'title' => __('I need help...','cdw_widget')
	        );
	    wp_localize_script('scfjs', 'SCF', $localize);
		
 }/*
** function displaying widget itself
*/
function cdw_dashboard_widget() {
 $cdw_data = cdw_data_load();
if(isset($cdw_data['message'])) echo apply_filters('the_content',$cdw_data['message']); 

if (isset($cdw_data['use_form']) && $cdw_data['use_form'] == "on") {
$user = wp_get_current_user();
?><div style="clear: both"></div>
<form name="post" action="<?php echo admin_url(); ?>" method="post" id="cdw-support" class="initial-form hide-if-no-js">

		
		<div class="input-text-wrap">
			

				<?php _e('Your e-mail (deliver to adress):','cdw_widget'); ?><br /> <strong><?php echo  $user->display_name.' &lt;'.$user->user_email.'&gt;'; ?></strong></br></br>
			<input name="author_name" id="cdw_author" autocomplete="off" type="hidden" value ="<?php echo $user->display_name; ?>">
		</div>

		<div class="input-text-wrap">
			<label for="mail_title">
				<?php _e('Title:','cdw_widget'); ?></label>
			<input name="mail_title" id="cdw_title" autocomplete="off" type="text" placeholder="<?php echo(isset($cdw_data['form_title']) ? $cdw_data['form_title'] : __('I need help...','cdw_widget')); ?>">
		</div>

		<div class="textarea-wrap">
			<label for="mail_content"><?php _e('Please describe your problem here:','cdw_widget'); ?></label>
			<textarea name="mail_content" id="cdw_content" class="mceEditor" rows="3" cols="15" autocomplete="off" value="" placeholder = "<?php echo(isset($cdw_data['form_message']) ? $cdw_data['form_message'] : sprintf(__('I have a problem on %s','cdw_widget'),get_bloginfo('name')) ); ?>" ></textarea>
		</div>

		<div class="submit" style="margin-top:12px">
			<input name="author_email" id="cdw_email" class="author" value="<?php echo $user->user_email; ?>" type="hidden">	
			<?php wp_nonce_field( 'scf_html', 'scf_nonce' ); ?>					
			<input name="save" id="send-message" class="button button-primary" value="<?php _e('Send message','cdw_widget') ?>" type="submit">
			<img class="scf-ajax" src="<?php echo plugins_url( 'img/sending.gif', __FILE__ ); ?>" alt="<?php _e('Sending Message','cdw_widget');?>" style="display: none; margin: 10px 20px 0 0">			<br class="clear">
			
		</div>
	<div class="formmessage"><p></p></div>
	</form><?php }
}
/*
** ajax callback to send message
*/
function cdw_send_message(){
	
	if ( wp_verify_nonce( $_POST['cdw_nonce'], 'scf_html' ) ) {
		$cdw_data = cdw_data_load();
        $name = sanitize_text_field($_POST['author_name']);
		$email = sanitize_email($_POST['author_email']);
		$subject = sanitize_text_field($_POST['mail_title']);
		$message = wp_kses_data($_POST['mail_content']);
		$headers = "From: \"".$name."\" <". $email . ">\r\n";
		$headers .= "X-Sender: ".$email."\r\n";
		$headers .= "Reply-To: " . $email ."\r\n";
		if(!empty($cdw_data['cc_mail'])) $headers .= "Cc: ".$cdw_data['cc_mail']."\r\n";
		$headers .= "Content-Type: text/plain; charset=utf-8\r\n"; // Mime typ
		$headers .= "MIME-Version: 1.0 \r\n";
		$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";

	 	/* Recipients */
		$to = $cdw_data['target_mail'];
		/* Sending e-mail */
		if (@wp_mail( $to, $subject, $message, $headers ) === true)
			printf(__('Message succesfully sent to <strong>%s</strong>','cdw_widget'), $to);
		else _e('Couldn\'t send message.','cdw_widget');
	}
	else _e('Sending message failed due to empty verification','cdw_widget');
	die(); // Important

}