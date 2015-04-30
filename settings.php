<?php 
/**
* Admin page
*/ 
if(!defined('ABSPATH')) die('Direct access not allowed!');
add_action('admin_menu', 'cdw_plugin_menu');

// default options page - add
function cdw_plugin_menu() {
	load_plugin_textdomain( 'cdw_widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	add_options_page(__('Dashboard Contact Form options','cdw_widget'),__('Dashboard Contact Form options','cdw_widget'),'manage_options','cdw_widget_options','cdw_widget_options');
}
function cdw_widget_options(){

$cdw_pass = get_option('cdw_password',md5(''));
if (isset($_SESSION['cdw_verified']) && $_SESSION['cdw_verified'] == $cdw_pass || isset($_POST['cdw_pass']) && $cdw_pass ==  md5($_POST['cdw_pass']) || $cdw_pass == md5('') )
{
if (isset($_POST['cdw_pass']) && $cdw_pass == md5($_POST['cdw_pass'])) $_SESSION['cdw_verified'] = md5($_POST['cdw_pass']);
cdw_widget_options_form();
}
else
{

?><div class="wrap">
<h2><?php _e('Dashboard Contact Form','cdw_widget'); ?></h2>
<p><?php _e('Type your secret password to edit Dashboard Contact Form:','cdw_widget'); ?></p> 
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<label for="cdw_pass"><?php _e('Type password:','cdw_widget'); ?></label><input type="password" autocomplete="off" class="style-input" name="cdw_pass" value="" /><br />
<input class="button-primary" type="submit" value="<?php _e('Enter','cdw_widget'); ?>" />
</form></div>
<?php
}

}
/*
** default options page - itself
*/
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
			else { echo '<div id="message" class="updated"><p><strong>'; _e('All settings successfully saved.','cdw_widget');}
		echo '</strong></p></div>';
		update_option('cdw_dashboard_text',serialize($cdw_data));
		unset($cdw_data);
	 }}
	 
	 if (isset($_POST['cdw_old_pass'])){
		 if(md5($_POST['cdw_old_pass']) == get_option('cdw_password',md5(''))) 
		 {
			 if($_POST['cdw_new_pass1'] == $_POST['cdw_new_pass2']){
				 update_option('cdw_password',md5($_POST['cdw_new_pass1']));
				 echo '<div id="message" class="updated"><p><strong>'.__('Password succesfully changed.','cdw_widget').'</strong></p></div>';
				 $_SESSION['cdw_verified'] = md5($_POST['cdw_new_pass1']);
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
	 
	$cdw_data = cdw_data_load();
	$cdw_pass = get_option('cdw_password');
	$encoding = get_option('blog_charset');
//if( WP_DEBUG && WP_DEBUG_DISPLAY) echo "<pre>".htmlspecialchars(print_r($cdw_data,true),ENT_COMPAT,"UTF-8")."</pre>";
//if(isset($_POST) && WP_DEBUG && WP_DEBUG_DISPLAY) echo "POST: <pre>".print_r($_POST,true)."</pre>";
	?>
	<style type="text/css">
		.style-input { width: 250px; }
	</style>
	<div class="wrap"><h2><?php _e('Dashboard Contact Form','cdw_widget'); ?></h2>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<table><tbody>
	<tr><td><label for="title"><?php _e('Widget title:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="title" value="<?php echo htmlspecialchars($cdw_data['title'],ENT_QUOTES,$encoding); ?>" /></td></tr>
	<tr><td><label for="message"><?php _e('Widget text:','cdw_widget'); ?></label></td><td><?php  wp_editor( $cdw_data['message'], 'message_editor', array('textarea_name' => 'message', 'textarea_rows' => '20','tinymce' => true , 'drag_drop_upload' => true) ); ?> </td></tr>
	<tr><td colspan="2"><hr /></td></tr>
	<tr><td colspan="2"><h3><?php _e('Contact form settings:','cdw_widget'); ?></h3></td></tr>
	<tr><td><label for="email"><?php _e('E-mail*:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="target_mail" value="<?php echo $cdw_data['target_mail']; ?>" /></td></tr>
	<tr><td><label for="cc_email"><?php _e('Copy (optional):','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="cc_mail" value="<?php if(isset($cdw_data['cc_mail'])) echo htmlspecialchars($cdw_data['cc_mail'],ENT_QUOTES,$encoding); ?>" /></td></tr>
	<tr><td></td><td><em><?php _e('(You can input multiple e-mails separated by a coma -  \',\')','cdw_widget'); ?></em></td></tr>
	<tr><td><label for="form_title"><?php _e('Form title:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="form_title" value="<?php echo(isset($cdw_data['form_title']) ? stripslashes(htmlspecialchars($cdw_data['form_title'],ENT_QUOTES,$encoding)) : ''); ?>" /></td></tr>
	<tr><td><label for="form_message"><?php _e('Form message**:','cdw_widget'); ?></label></td><td>
	
	<textarea cols="70" rows="6" name="form_message"><?php echo(isset($cdw_data['form_message'])) ? stripslashes(esc_textarea($cdw_data['form_message'])) : ''; ?></textarea></td></tr>
	<tr><td colspan="2">
	<h5><?php _e('Placeholders:','cdw_widget'); ?></h5>
	<em><?php _e('<strong>%SENDER_NAME%</strong> - display name of current logged - in user (you).','cdw_widget'); ?></em><br />
	<em><?php _e('<strong>%SENDER_EMAIL%</strong> - email of currently loggen-in user (you).','cdw_widget'); ?></em><br />
	<em><?php _e('<strong>%TARGET_EMAIL%</strong> - email where this message will be mailed to, usually site admin.','cdw_widget'); ?></em><br />
	<em><?php _e('<strong>%COPY_EMAILS%</strong> - list of emails of additional receivers of this message.','cdw_widget'); ?></em><br /><br />
	<em><?php _e('** This section is NOT automatically formatted and requires HTML elements!','cdw_widget'); ?></em><br /><br />
	
	</td></tr>
	<tr><td><label for="placeholder_title"><?php _e('Title placeholder:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="placeholder_title" value="<?php echo(isset($cdw_data['placeholder_title']) ? stripslashes(htmlspecialchars($cdw_data['placeholder_title'],ENT_QUOTES,$encoding)) : __('Message Title','cdw_widget')); ?>" /></td></tr>
	
	<tr><td><label for="placeholder_message"><?php _e('Message placeholder:','cdw_widget'); ?></label></td><td><input type="text" class="style-input" name="placeholder_message" value="<?php echo(isset($cdw_data['placeholder_message']) ? stripslashes(htmlspecialchars($cdw_data['placeholder_message'],ENT_QUOTES,$encoding)) : '' ); ?>" /></td></tr>
	</tbody></table>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="cdw_pass" value="<?php if(isset($_POST['cdw_pass'])) echo $_POST['cdw_pass']; ?>" />
	<?php wp_nonce_field( 'cdw_update_html', 'cdw_update_nonce' );
	$rolles = cdw_all_roles(); ?>
	<table><tbody>
	<tr><th><?php _e('Display for roles:','cdw_widget'); ?></th></tr> <?php  echo "\n";
	foreach($rolles as $rolename)
	{
	// enable for roles
	$readonly = $rolename == "Administrator"  ? "readonly=\"readonly\" onclick=\"return false\" " :  "";
	$checked = $rolename == "Administrator" ? "checked=\"checked\"" : cdw_checked($rolename,$cdw_data['roles']);
	echo "<tr><td><input type=\"checkbox\" name=\"cdw_role[]\" class=\"cdw_role\" ".$readonly."id=\"role_".$rolename."\" value=\"".$rolename."\" ".$checked."><label class=\"cdw_role_label\" for=\"role_".$rolename."\">".translate_user_role($rolename)."</label></td></tr>\n";
	
	}
	?></tbody></table>
	<br />
	<table><tbody>
	<tr><th><?php _e('Enable contact form for roles:','cdw_widget'); ?></th></tr> <?php  echo "\n";
	foreach($rolles as $rolename)
	{
	
	// enable contact form for roles:
	$readonly_cf = !in_array($rolename, $cdw_data['roles']) && $rolename != "Administrator"  ?  "readonly=\"readonly\" onclick=\"return false\" disabled=\"disabled\" " :  "";
	$checked_cf = cdw_checked($rolename,$cdw_data['roles_cf']);
	echo "</tr><td><input type=\"checkbox\" name=\"cdw_role_cf[]\" class=\"cdw_role\" ".$readonly_cf."id=\"role_".$rolename."_cf\" value=\"".$rolename."\" ".$checked_cf."><label class=\"cdw_role_label\" for=\"role_".$rolename."_cf\">".translate_user_role($rolename)."</label></td></tr>\n";
	}
	?></tbody></table>
	<input class="button-primary" type="submit" value="<?php _e('Save','cdw_widget'); ?>" />
	</form>
	<p><em><sup>*</sup><?php _e('E-mail address where will be delivered all messages from the contact form','cdw_widget'); ?></em></p>
	<br /><hr />
	<?php /**
	* Set or change passwords:
	*/?>
	<h3><?php _e('Set or change password','cdw_widget'); ?></h3>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<?php $cdw_pass = get_option('cdw_password'); ?>
	<table><tbody>
	<tr><td><label for="cdw_old_pass"><?php _e('Enter current password:','cdw_widget'); ?><?php if($cdw_pass == md5('')) : ?><sup>*</sup><?php endif; ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_old_pass" value="" /></td></tr>
	<tr><td><label for="cdw_new_pass1"><?php _e('Enter new password:','cdw_widget'); ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_new_pass1" value="" /></td></tr>
	<tr><td><label for="cdw_new_pass2"><?php _e('Confirm new password:','cdw_widget'); ?></label></td><td><input type="password" autocomplete="off" class="style-input" name="cdw_new_pass2" value="" /></td></tr>
	<tr><td colspan="2"><input class="button-primary" type="submit" value="<?php _e('Set new password:','cdw_widget'); ?>" /></td></tr>
	</tbody></table>
</form>
<?php if($cdw_pass == md5('')) : ?><sup>*</sup><em><?php _e('(Current password is blank.)','cdw_widget'); ?></em><?php endif; ?>
<hr />
<p><?php _e('If you like this plugin, you can click on the \'Donate\' button here (opens a new window) and send me 5$. Thank you!','cdw_widget'); ?></p><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ripasu%40volny%2ecz&item_name=Dashbopard%20Contact%20Form%20Donation&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&amount=5&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8">
<img type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
</a>

<br />
	</div>

	<?php
}
/*
** is this role checked? 
*/
function cdw_checked($role,$rolelist){
	if($rolelist != null && in_array($role,$rolelist)) return "checked=\"checked\""; else return "";
}
/*
** Checks the e-mails separated with a semicolon
*/
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
?>