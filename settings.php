<?php 
/**
* Admin page
*/ 
if(!defined('ABSPATH')) die('Direct access not allowed!');
add_action('admin_menu', 'cdw_plugin_menu');

// default options page - add
if(!function_exists('cdw_plugin_menu')) {
	function cdw_plugin_menu() {
		load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
		add_options_page(__('Dashboard Contact Form options','cdw_widget'),__('Dashboard Contact Form options','cdw_widget'),'manage_options','cdw_widget_options','cdw_widget_options');
	}
}
if (!function_exists('cdw_widget_options')) {
	function cdw_widget_options(){
		?><div class="wrap">
		
		<?php
		$empty_pass = md5('');
		$cdw_pass = get_option('cdw_password', $empty_pass );
		
		 if (isset($_POST['cdw_new_pass1']) ){
				 if($cdw_pass == md5(@$_POST['cdw_old_pass']) || $cdw_pass == $empty_pass || wp_verify_nonce( @$_POST['cdw_new_pass'], 'cdw_reset_password' ))  
				 {
					 if($_POST['cdw_new_pass1'] == $_POST['cdw_new_pass2']){
						 update_option('cdw_password',md5($_POST['cdw_new_pass1']));
						 $cdw_pass = get_option('cdw_password');
						 $_SESSION['cdw_verified'] = md5($_POST['cdw_new_pass1']);
						 echo '<div id="message" class="updated"><p><strong>'.__('Password succesfully changed.','cdw_widget').'</strong></p></div>';
					}
					 else {
						echo '<div id="message" class="error"><p><strong>'.__('New passwords mismatch, password NOT updated!','cdw_widget').'</strong></p></div>';
					 }
				 }
				 else 
				 {
					echo '<div id="message" class="error"><p><strong>'.__('Current password invalid, password NOT updated!','cdw_widget').'</strong></p></div>';
				 }
		}
		if(@$_GET['action'] == 'forgotten_password' )
		{
			if (cdw_reset_mail())
			{
				echo '<div id="message" class="updated"><p>'.sprintf(__('E-mail with link to reset this plugin password has been mailed to site admin on <strong>%s</strong>.','cdw_widget'),get_option('admin_email')).'</p></div>';
			}
			else 
			{
				echo '<div id="message" class="error"><p>'.sprintf(__('Failed to send message to site admin on <strong>%s</strong>.','cdw_widget'),get_option('admin_email')).'</p></div>';
			}
		}
		if(@$_GET['action'] == 'reset_password')
		{
			if(cdw_verify_nonce(@$_GET['cdw_key'],"reset_password"))
			{
				?>
				<h3><?php _e('Please enter your new password:','cdw_widget'); ?></h3>
				<form method="post" action="<?php echo admin_url('options-general.php?page=cdw_widget_options'); ?>">
				<table><tbody>
				<tr><td><label for="cdw_new_pass1"><?php _e('Enter new password:','cdw_widget'); ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_new_pass1" value="" /></td></tr>
				<tr><td><label for="cdw_new_pass2"><?php _e('Confirm new password:','cdw_widget'); ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_new_pass2" value="" /><?php wp_nonce_field( 'cdw_reset_password', 'cdw_new_pass' ); ?>
				</td></tr>
				<tr><td colspan="2"><input class="button-primary" type="submit" value="<?php _e('Set new password','cdw_widget'); ?>" /></td></tr>
				</tbody></table>
			</form>
			<?php 
			
			}
			else
			{
			 echo '<div id="message" class="error"><p><strong>'.__('Error: Authentization key is outdated, password NOT deleted!','cdw_widget').'</strong></p></div>';
			}
		}
		if ($cdw_pass ==  md5(@$_POST['cdw_pass']) || $cdw_pass == $empty_pass || @$_SESSION['cdw_verified'] == $cdw_pass  )
		{
			if (!isset($_SESSION['cdw_verified']))
			{	
				isset($_POST['cdw_pass']) ? $_SESSION['cdw_verified'] = md5($_POST['cdw_pass']) : $_SESSION['cdw_verified'] = $empty_pass;
			}
			if(!isset($_GET['action'])) {
				cdw_widget_options_form();
				return true;
			}
		}
		if (isset($_POST['cdw_pass']) && md5(@$_POST['cdw_pass']) != @$_SESSION['cdw_verified'])
		{
			echo '<div id="message" class="error"><p><strong>'.__('Error: invalid password','cdw_widget').'</strong></p></div>';
		}
		if (@$_GET['action'] == 'cdw_logout' && isset($_SESSION['cdw_verified'])) 
		{ 
			unset($_SESSION['cdw_verified']); 
		}
		if(@$_GET['action'] !== 'reset_password')
			{	?>
			<h2><?php _e('Dashboard Contact Form','cdw_widget'); ?></h2>
			<p><?php _e('Type your secret password to edit Dashboard Contact Form:','cdw_widget'); ?></p> 
			<form method="post" action="<?php echo admin_url('options-general.php?page=cdw_widget_options'); ?>">
			<label for="cdw_pass"><?php _e('Type password:','cdw_widget'); ?></label>&nbsp;<input type="password" autocomplete="off" class="style-input" name="cdw_pass" value="" /><br /><br/>
			<input class="button-primary" type="submit" value="<?php _e('Enter','cdw_widget'); ?>" />&nbsp;&nbsp;<a class="button button-primary button-large" href="<?php echo admin_url('options-general.php?page=cdw_widget_options')."&amp;action=forgotten_password"; ?>" onclick="return confirm('<?php printf(__("Do you really want to reset this password? Mail with reset url will be sent to %s (site admin)","cdw_widget"),get_option('admin_email'));?>');"><?php _e('Reset password (site admin only)','cdw_widget'); ?></a>
			</form>
		
		<?php 
	}?>
	</div><?php 

	}
}
/*
** default options page - itself
*/
if (!function_exists('cdw_widget_options_form')) {
	function cdw_widget_options_form() {

		if(isset($_POST['action'])) {
		 if ($_POST['action']=="update" && wp_verify_nonce( $_POST['cdw_update_nonce'], 'cdw_update_html' )) {
		 $cdw_old = cdw_data_load();
		 
		 $cdw_data = array( 
							'title'					=>	$_POST['title'],
							'message'				=>	wp_kses_post($_POST['message']) ,
							'target_mail' 			=>  is_email($_POST['target_mail']) ? sanitize_email($_POST['target_mail']) : $cdw_old['target_mail'],
							'cc_mail' 				=>  cdw_are_emails($_POST['cc_mail']) ? $_POST['cc_mail'] : $cdw_old['cc_mail'],
							'form_title' 			=>  wp_kses_post($_POST['form_title']) ,
							'form_message' 			=>  wp_kses_post($_POST['form_message']) ,
							'placeholder_title' 	=>  wp_kses_post($_POST['placeholder_title']) ,
							'placeholder_message' 	=>  wp_kses_post( $_POST['placeholder_message'] ),
							'roles'					=>  isset($_POST['cdw_role'])? array_values($_POST['cdw_role']) : array(),
							'roles_cf'				=>  isset($_POST['cdw_role_cf'])? array_values($_POST['cdw_role_cf']) : array()
			);
			
			if (!is_email($_POST['target_mail'])) { echo '<div id="message" class="error"><p><strong>'; 
			_e('Email has invalid format, not updated. Other settings saved. (You may have only one e-mail in this field).','cdw_widget');}
				else { echo '<div id="message" class="updated"><p><strong>'.__('All settings successfully saved.','cdw_widget');}
			echo '</strong></p></div>';
			update_option('cdw_dashboard_text',serialize($cdw_data));
			unset($cdw_data);
		 }
		 else echo '<div id="message" class="Error"><p><strong>'.__('ERROR: No settings saved, try again.','cdw_widget').'</strong></p></div>';
		 }
		 
		$cdw_data = cdw_data_load();
		$cdw_pass = get_option('cdw_password',md5(''));
		$has_empty = $cdw_pass == md5('') ? true : false; 
		
		$encoding = get_option('blog_charset');
	//if( WP_DEBUG && WP_DEBUG_DISPLAY) echo "<pre>".htmlspecialchars(print_r($cdw_data,true),ENT_COMPAT,"UTF-8")."</pre>";
	//if(isset($_POST) && WP_DEBUG && WP_DEBUG_DISPLAY) echo "POST: <pre>".print_r($_POST,true)."</pre>";
		?>
		<style type="text/css">
			.style-input { width: 250px; }
			.alignright {float: right; }
		</style>
		<h2><?php _e('Dashboard Contact Form','cdw_widget'); ?></h2>
		<form method="post" action="<?php echo admin_url('options-general.php?page=cdw_widget_options'); ?>">
		<table><tbody>
		<tr><td><label for="title"><?php _e('Widget title:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="title" value="<?php echo htmlspecialchars($cdw_data['title'],ENT_QUOTES,$encoding); ?>" />
		
		<?php if(!$has_empty) : ?><a href="<?php echo admin_url('options-general.php?page=cdw_widget_options&amp;action=cdw_logout'); ?>" class="button-primary alignright" onclick="return confirm('<?php _e('Do you really want to leave the edit mode? (Remeber to save your work here first!)','cdw_widget');?>');"><?php _e('Logout from dashboard edit','cdw_widget'); ?></a><?php endif; ?></td></tr>
		<tr><td><label for="message"><?php _e('Widget text:','cdw_widget'); ?></label></td><td><?php  wp_editor( $cdw_data['message'], 'message_editor', array('textarea_name' => 'message', 'textarea_rows' => '20','tinymce' => true , 'drag_drop_upload' => true) ); ?> </td></tr>
		<tr><td colspan="2"><hr /></td></tr>
		<tr><td colspan="2"><h3><?php _e('Contact form settings:','cdw_widget'); ?></h3></td></tr>
		<tr><td><label for="email"><?php _e('E-mail*:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="target_mail" value="<?php echo $cdw_data['target_mail']; ?>" /></td></tr>
		<tr><td><label for="cc_email"><?php _e('Copy (optional):','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="cc_mail" value="<?php if(isset($cdw_data['cc_mail'])) echo htmlspecialchars($cdw_data['cc_mail'],ENT_QUOTES,$encoding); ?>" /></td></tr>
		<tr><td></td><td><em><?php _e('(You can input multiple e-mails separated by a coma -  \',\')','cdw_widget'); ?></em></td></tr>
		<tr><td><label for="form_title"><?php _e('Form title:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="form_title" value="<?php echo(isset($cdw_data['form_title']) ? stripslashes(htmlspecialchars($cdw_data['form_title'],ENT_QUOTES,$encoding)) : ''); ?>" /></td></tr>
		<tr><td><label for="form_message"><?php _e('Form message**:','cdw_widget'); ?></label></td><td>
		
		<textarea cols="80" rows="6" name="form_message"><?php echo(isset($cdw_data['form_message'])) ? stripslashes(esc_textarea($cdw_data['form_message'])) : ''; ?></textarea></td></tr>
		<tr><td colspan="2">
		<h5><?php _e('Placeholders:','cdw_widget'); ?></h5>
		<em><?php _e('<strong>%SENDER_NAME%</strong> - display name of current logged - in user (you).','cdw_widget'); ?></em><br />
		<em><?php _e('<strong>%SENDER_EMAIL%</strong> - e-mail of currently logged-in user (you).','cdw_widget'); ?></em><br />
		<em><?php _e('<strong>%TARGET_EMAIL%</strong> - e-mail where this message will be mailed to, usually site admin.','cdw_widget'); ?></em><br />
		<em><?php _e('<strong>%COPY_EMAILS%</strong> - list of e-mails of additional receivers of this message.','cdw_widget'); ?></em><br /><br />
		<em><?php _e('** This section is NOT automatically formatted and requires HTML elements!','cdw_widget'); ?></em><br /><br />
		
		</td></tr>
		<tr><td><label for="placeholder_title"><?php _e('Title placeholder:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="placeholder_title" value="<?php echo(isset($cdw_data['placeholder_title']) ? stripslashes(htmlspecialchars($cdw_data['placeholder_title'],ENT_QUOTES,$encoding)) : __('Message Title','cdw_widget')); ?>" /></td></tr>
		
		<tr><td><label for="placeholder_message"><?php _e('Message placeholder:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="placeholder_message" value="<?php echo(isset($cdw_data['placeholder_message']) ? stripslashes(htmlspecialchars($cdw_data['placeholder_message'],ENT_QUOTES,$encoding)) : '' ); ?>" /></td></tr>
		</tbody></table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="cdw_pass" value="<?php echo $_POST['cdw_pass']; ?>" />
		<?php wp_nonce_field( 'cdw_update_html', 'cdw_update_nonce' );
		$rolles = cdw_all_roles(); ?>
		<table><tbody>
		<tr><th><?php _e('Display for roles:','cdw_widget'); ?>&nbsp;&nbsp;&nbsp;</th></tr> <?php  echo "\n";
		foreach($rolles as $rolename)
		{
		// enable for roles
		$readonly = $rolename == "Administrator"  ? "readonly=\"readonly\" onclick=\"return false\" " :  "";
		$checked = $rolename == "Administrator" ? "checked=\"checked\"" : cdw_checked($rolename,$cdw_data['roles']);
		echo "<tr><td><input type=\"checkbox\" name=\"cdw_role[]\" class=\"cdw_role\" ".$readonly."id=\"role_".$rolename."\" value=\"".$rolename."\" ".$checked."><label class=\"cdw_role_label\" for=\"role_".$rolename."\">".translate_user_role($rolename)."</label></td></tr>\n";
			
		}
		?></tbody></table>
		<br />
		
		<?php // enable contact form for roles: ?>
		<table><tbody>
		<tr><th><?php _e('Enable contact form for roles:','cdw_widget'); ?></th></tr> <?php  echo "\n";
		foreach($rolles as $rolename)
		{
			$readonly_cf = !in_array($rolename, $cdw_data['roles']) && $rolename != "Administrator"  ?  "readonly=\"readonly\" onclick=\"return false\" disabled=\"disabled\" " :  "";
			$checked_cf = cdw_checked($rolename,$cdw_data['roles_cf']);
			echo "<tr><td><input type=\"checkbox\" name=\"cdw_role_cf[]\" class=\"cdw_role\" ".$readonly_cf."id=\"role_".$rolename."_cf\" value=\"".$rolename."\" ".$checked_cf."><label class=\"cdw_role_label\" for=\"role_".$rolename."_cf\">".translate_user_role($rolename)."</label></td></tr>\n";
		}
		?></tbody></table>
		<br />
		<input class="button-primary" type="submit" value="<?php _e('Save','cdw_widget'); ?>" />
		</form>
		<p><em><sup>*</sup><?php _e('E-mail address where will be delivered all messages from the contact form','cdw_widget'); ?></em></p>
		<br /><hr />
		<?php 
		
		/**
		* Set or change passwords:
		*/?>
		<h3><?php _e('Set or change password','cdw_widget'); ?></h3>
		<form method="post" action="<?php echo admin_url('options-general.php?page=cdw_widget_options'); ?>">
		<table><tbody>
		<?php if(!$has_empty) : ?><tr><td><label for="cdw_old_pass"><?php _e('Enter current password:','cdw_widget'); ?><?php if($has_empty) : ?><sup>*</sup><?php endif; ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_old_pass" value="" /></td></tr><?php else: 
		?><em><?php _e('(Current password is blank.)','cdw_widget'); ?></em>
		<?php endif; ?>
		<tr><td><label for="cdw_new_pass1"><?php _e('Enter new password:','cdw_widget'); ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_new_pass1" value="" /></td></tr>
		<tr><td><label for="cdw_new_pass2"><?php _e('Confirm new password:','cdw_widget'); ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_new_pass2" value="" /></td></tr>
		<tr><td colspan="2">
		<input class="button-primary" type="submit" value="<?php _e('Set new password','cdw_widget'); ?>" />
		<input type="hidden" name="cdw_pass" value="<?php echo $_POST['cdw_pass']; ?>" /></td></tr>
		</tbody></table>
	</form>
	<hr />
	<p><?php _e('If you like this plugin, you can click on the \'Donate\' button here (opens a new window) and send me 5$. Thank you!','cdw_widget'); ?></p><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=ripasu%40volny%2ecz&amp;item_name=Dashbopard%20Contact%20Form%20Donation&amp;item_number=Support%20Open%20Source&amp;no_shipping=0&amp;no_note=1&amp;tax=0&amp;currency_code=USD&amp;amount=5&amp;lc=US&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8">
	<img type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
	</a>

	<br />
		

		<?php
	}
}
/*
** is this role checked? 
*/
if (!function_exists('cdw_checked')) {
	function cdw_checked($role,$rolelist){
		if($rolelist != null && in_array($role,$rolelist)) return "checked=\"checked\""; else return "";
	}
}
/*
** Checks the e-mails separated with a semicolon
*/
if (!function_exists('cdw_are_emails')) {	
	function cdw_are_emails($string){
		if (empty($string)) return true;
		if (is_email($string)) return true;
		$emails = str_replace(";",",",$string);
		$emails = explode(",",$string);
		// if string ends wih a semicolon skip the last item of $emails array because it is empty, or it must be 
		if (strpos(",",$string) == strlen($string) - 1) $emc = count($emails) - 1; else $emc = count($emails);
		if (is_array($emails) && count($emails) > 0)
		{
		 for($i = 0; $i++; $i < $emc)
		 {
			if(!is_email(trim($emails[$i]))) return false;
		 }
		 // if all are successfully checked, array of emails is ok
		 return true;
		}
		// no other input of e-mails is accepted
		return false;
	}
}
/*
** verification functions and reset mail functions
*/
if (!function_exists('cdw_create_nonce')) {
	function cdw_create_nonce($action = -1) {
		$admin_email = get_option('admin_email');
		$token = wp_get_session_token();
		$i = wp_nonce_tick();

		return substr( wp_hash( $i . '|' . $action . '|' . $admin_email . '|' . $token, 'nonce' ), -12, 10 );
	}
}
if (!function_exists('cdw_verify_nonce')) {
	function cdw_verify_nonce( $nonce, $action = -1 ) {
		$nonce = (string) $nonce;
		$admin_email = get_option('admin_email');

		if ( empty( $nonce ) ) {
			return false;
		}

		$token = wp_get_session_token();
		$i = wp_nonce_tick();

		// Nonce generated 0-12 hours ago
		$expected = substr( wp_hash( $i . '|' . $action . '|' . $admin_email . '|' . $token, 'nonce'), -12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			return 1;
		}

		// Nonce generated 12-24 hours ago
		$expected = substr( wp_hash( ( $i - 1 ) . '|' . $action . '|' . $admin_email . '|' . $token, 'nonce' ), -12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			return 2;
		}

		// Invalid nonce
		return false;
	}
}
if (!function_exists('cdw_reset_mail')) {
	function cdw_reset_mail(){
		$cdw_data = cdw_data_load();
		$user = wp_get_current_user();
		$name = $user->display_name;
		$email = $user->user_email;
		$username = $user->user_login;
		$home_url = str_replace('http://','',get_bloginfo('url'));
		$reset_link = admin_url('options-general.php?page=cdw_widget_options').'&amp;action=reset_password&amp;cdw_key=' . cdw_create_nonce("reset_password");
		$subject = sprintf(__('Resetting Dashboard Contact Form password for %s','cdw_widget'),$home_url);
		$message = sprintf(__('User <strong>%s</strong> asked you to reset Dashboard Contact Form password for <strong>%s</strong>.<br /> To reset password, click on the following url:<a target="_blank" href="%s">%s</a>. If you don\'t want reset this password, simply ignore this message.','cdw_widget'),$username, $home_url ,$reset_link, $reset_link );
		$message .= "<br />\n-------------------------------------\n<br />".sprintf(__('This message has been sent from <strong>%1$s</strong> at <strong>%2$s</strong> by user <strong>%3$s</strong>.','cdw_widget'),$home_url, date(get_option('date_format').', '.get_option('time_format'),time()),$username);
		$headers = "From: \"".$name."\" <". $email . ">\r\n";
		$headers .= "X-Sender: ".$email."\r\n";
		$headers .= "Reply-To: " . $email ."\r\n";
		if(!empty($cdw_data['cc_mail'])) $headers .= "Cc: ".$cdw_data['cc_mail']."\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n"; // Mime typ
		$headers .= "MIME-Version: 1.0 \r\n";
		$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";

		/* Recipients */
		$to = get_option('admin_email');
		/* Sending e-mail */
		return wp_mail( $to, $subject, $message, $headers );
	}
}
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'cdw_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'cdw_register_plugin_links', 10, 2 );

if ( ! function_exists( 'cdw_plugin_action_links' ) ) {
	function cdw_plugin_action_links( $links, $file ) {		
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

			if ( dirname($file) == dirname($this_plugin )) {
				$settings_link = '<a href="'.admin_url('options-general.php?page=cdw_widget_options').'">' . __( 'Settings', 'cdw_widget' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}
if ( ! function_exists( 'cdw_register_plugin_links' ) ) {
	function cdw_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( dirname($file) == dirname($base) ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="'.admin_url('options-general.php?page=cdw_widget_options').'">' . __( 'Settings', 'cdw_widget' ) . '</a>';;
			$links[]	=	'<a href="http://wordpress.org/plugins/dashboard-contact-form/faq/" target="_blank">' . __( 'FAQ', 'cdw_widget' ) . '</a>';
			$links[]	=	'<a href="https://wordpress.org/support/plugin/dashboard-contact-form" target="_blank">' . __( 'Support', 'cdw_widget' ) . '</a>';
		}
		return $links;
	}
}