<?php
global $dp_options, $fullname_types, $dp_default_options;
?>
<div class="theme_option_message" style="margin-top: 0;">
	<p><?php _e( 'Front-end User Registration', 'tcd-w' ); ?></p>
	<p><?php _e( '1.Click “Join”, then enter their email address and password. 2.Click “Register”. So your website will then ask them confirm their email. 3.Check their email for the confirmation, and click on the link in the email from your website. 4.Complete the form by entering the username, Area, and others. 5.New account is made!', 'tcd-w' ); ?></p>
	<p><?php _e( 'The Username will use as the display name, nickname, and the URL of each of author page. When you log into the WordPress, please use the mail address as username of WordPress.', 'tcd-w' ); ?></p>
	<p><?php _e( 'If you try to select a username and see that it has already been claimed, you will need to select a different one.', 'tcd-w' ); ?></p>
	<p><?php _e( 'Frontend registered member is set the "Contributor" User Role. Ignore the setting of "New User Default Role" setting in <a href="options-general.php" target="_blank">WordPress General Settings</a>.', 'tcd-w' ); ?></p>
	<p><?php _e( '"Contributor" and "Subscriber" User Role can not access the <a href="index.php" target="_blank">WordPress backend</a>.', 'tcd-w' ); ?></p>
</div>
<?php // ログインフォーム設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Login form settings', 'tcd-w' ); ?></h3>
	<h4 class="theme_option_headline2"><?php _e( 'Description below login form', 'tcd-w' ); ?></h4>
	<p><?php _e( 'Set the description displayed under the login button.', 'tcd-w' ); ?></p>
	<textarea class="large-text" cols="50" rows="4" name="dp_options[membership][login_form_desc]"><?php echo esc_textarea( $dp_options['membership']['login_form_desc'] ); ?></textarea>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
<?php // 仮登録設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Registration settings', 'tcd-w' ); ?></h3>
	<h4 class="theme_option_headline2"><?php _e( 'Registration title', 'tcd-w' ); ?></h4>
	<p><?php _e( 'Set the contents to display the temporary member registration form.', 'tcd-w' ); ?></p>
	<input class="regular-text" type="text" name="dp_options[membership][registration_headline]" value="<?php echo esc_attr( $dp_options['membership']['registration_headline'] ); ?>">
	<h4 class="theme_option_headline2"><?php _e( 'Registration description', 'tcd-w' ); ?></h4>
	<textarea class="large-text" cols="50" rows="4" name="dp_options[membership][registration_desc]"><?php echo esc_textarea( $dp_options['membership']['registration_desc'] ); ?></textarea>
	<h4 class="theme_option_headline2"><?php _e( 'Registration Complete title', 'tcd-w' ); ?></h4>
	<input class="regular-text" type="text" name="dp_options[membership][registration_complete_headline]" value="<?php echo esc_attr( $dp_options['membership']['registration_complete_headline'] ); ?>">
	<h4 class="theme_option_headline2"><?php _e( 'Registration Complete description', 'tcd-w' ); ?></h4>
	<textarea class="large-text" cols="50" rows="4" name="dp_options[membership][registration_complete_desc]"><?php echo esc_textarea( $dp_options['membership']['registration_complete_desc'] ); ?></textarea>
	<p class="description"><?php _e( 'Available Variables', 'tcd-w' ); ?>: [user_email]</p>
	<h4 class="theme_option_headline2"><?php _e( 'Registration description below login form', 'tcd-w' ); ?></h4>
	<textarea class="large-text" cols="50" rows="4" name="dp_options[membership][login_registration_desc]"><?php echo esc_textarea( $dp_options['membership']['login_registration_desc'] ); ?></textarea>
	<h4 class="theme_option_headline2"><?php _e( 'Registration button label below login form', 'tcd-w' ); ?></h4>
	<input class="regular-text" type="text" name="dp_options[membership][login_registration_button_label]" value="<?php echo esc_attr( $dp_options['membership']['login_registration_button_label'] ); ?>">
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
<?php // 本会員登録設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Registration Account settings', 'tcd-w' ); ?></h3>
	<p><?php _e( 'Set the contents to display the member registration form and form item to be used.', 'tcd-w' ); ?></p>
	<h4 class="theme_option_headline2"><?php _e( 'Registration Account title', 'tcd-w' ); ?></h4>
	<input class="regular-text" type="text" name="dp_options[membership][registration_account_headline]" value="<?php echo esc_attr( $dp_options['membership']['registration_account_headline'] ); ?>">
	<h4 class="theme_option_headline2"><?php _e( 'Registration Account description', 'tcd-w' ); ?></h4>
	<textarea class="large-text" cols="50" rows="4" name="dp_options[membership][registration_account_desc]"><?php echo esc_textarea( $dp_options['membership']['registration_account_desc'] ); ?></textarea>
	<h4 class="theme_option_headline2"><?php _e( 'Registration Account Complete title', 'tcd-w' ); ?></h4>
	<input class="regular-text" type="text" name="dp_options[membership][registration_account_complete_headline]" value="<?php echo esc_attr( $dp_options['membership']['registration_account_complete_headline'] ); ?>">
	<h4 class="theme_option_headline2"><?php _e( 'Registration Account Complete description', 'tcd-w' ); ?></h4>
	<textarea class="large-text" cols="50" rows="4" name="dp_options[membership][registration_account_complete_desc]"><?php echo esc_textarea( $dp_options['membership']['registration_account_complete_desc'] ); ?></textarea>
	<p class="description"><?php _e( 'Available Variables', 'tcd-w' ); ?>: [user_email], [user_display_name], [login_url], [login_button]</p>
	<p><?php _e( 'If both "Registration Account Complete title" and "Registration Account Complete description" are empty, displayed the login form.', 'tcd-w' ); ?></p>
	<h4 class="theme_option_headline2"><?php _e( 'Display fields settings', 'tcd-w' ); ?></h4>
	<ul>
		<li><label><input name="dp_options[membership][show_registration_fullname]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_fullname'] ); ?>><?php _e( 'Show fullname field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_gender]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_gender'] ); ?>><?php _e( 'Show gender field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_area]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_area'] ); ?>><?php _e( 'Show area field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_birthday]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_birthday'] ); ?>><?php _e( 'Show birthday field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_company]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_company'] ); ?>><?php _e( 'Show company field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_job]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_job'] ); ?>><?php _e( 'Show job field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_desc]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_desc'] ); ?>><?php _e( 'Show description field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_website]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_website'] ); ?>><?php _e( 'Show website field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_facebook]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_facebook'] ); ?>><?php _e( 'Show facebook field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_twitter]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_twitter'] ); ?>><?php _e( 'Show twitter field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_instagram]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_instagram'] ); ?>><?php _e( 'Show instagram field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_youtube]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_youtube'] ); ?>><?php _e( 'Show youtube field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_registration_tiktok]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_registration_tiktok'] ); ?>><?php _e( 'Show tiktok field', 'tcd-w' ); ?></label></li>
	</ul>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
<?php // アカウント編集設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Account settings', 'tcd-w' ); ?></h3>
	<h4 class="theme_option_headline2"><?php _e( 'Display fields settings', 'tcd-w' ); ?></h4>
	<p><?php _e( 'Set form items to be used in the edit account.', 'tcd-w' ); ?></p>
	<ul>
		<li><label><input name="dp_options[membership][show_account_gender]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_account_gender'] ); ?>><?php _e( 'Show gender field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_account_area]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_account_area'] ); ?>><?php _e( 'Show area field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_account_birthday]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_account_birthday'] ); ?>><?php _e( 'Show birthday field', 'tcd-w' ); ?></label></li>
	</ul>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
<?php // プロフィールフォーム設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Profile settings', 'tcd-w' ); ?></h3>
	<h4 class="theme_option_headline2"><?php _e( 'Display fields settings', 'tcd-w' ); ?></h4>
	<p><?php _e( 'Set form items to be used in the member profile.', 'tcd-w' ); ?></p>
	<ul>
		<li><label><input name="dp_options[membership][show_profile_fullname]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_fullname'] ); ?>><?php _e( 'Show fullname field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_area]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_area'] ); ?>><?php _e( 'Show area field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_birthday]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_birthday'] ); ?>><?php _e( 'Show birthday field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_company]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_company'] ); ?>><?php _e( 'Show company field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_job]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_job'] ); ?>><?php _e( 'Show job field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_desc]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_desc'] ); ?>><?php _e( 'Show description field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_website]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_website'] ); ?>><?php _e( 'Show website field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_facebook]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_facebook'] ); ?>><?php _e( 'Show facebook field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_twitter]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_twitter'] ); ?>><?php _e( 'Show twitter field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_instagram]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_instagram'] ); ?>><?php _e( 'Show instagram field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_youtube]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_youtube'] ); ?>><?php _e( 'Show youtube field', 'tcd-w' ); ?></label></li>
		<li><label><input name="dp_options[membership][show_profile_tiktok]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['show_profile_tiktok'] ); ?>><?php _e( 'Show tiktok field', 'tcd-w' ); ?></label></li>
	</ul>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
<?php // フォームフィールド設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Fields settings', 'tcd-w' ); ?></h3>
	<p><?php _e( 'Set form fields settings.', 'tcd-w' ); ?></p>
	<table class="theme_option_table">
		<tr>
			<td><?php _ex( 'Username', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_display_name]" value="<?php echo esc_attr( $dp_options['membership']['field_label_display_name'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'Email', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_email]" value="<?php echo esc_attr( $dp_options['membership']['field_label_email'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'Password', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_password]" value="<?php echo esc_attr( $dp_options['membership']['field_label_password'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'Password (confirm)', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_password_confirm]" value="<?php echo esc_attr( $dp_options['membership']['field_label_password_confirm'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'Current Password', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_current_password]" value="<?php echo esc_attr( $dp_options['membership']['field_label_current_password'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'New Password', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_new_password]" value="<?php echo esc_attr( $dp_options['membership']['field_label_new_password'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'New Password (confirm)', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_new_password_confirm]" value="<?php echo esc_attr( $dp_options['membership']['field_label_new_password_confirm'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'Remember Me', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_login_remember]" value="<?php echo esc_attr( $dp_options['membership']['field_label_login_remember'] ); ?>"></td>
			<td><input type="checkbox" value="1" checked="checked" disabled="disabled"><?php _e( 'Required field', 'tcd-w' ); ?></td>
		</tr>
		<tr>
			<td><?php _ex( 'Fullname', 'User form field label', 'tcd-w' ); ?></td>
			<td>
				<?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_fullname]" value="<?php echo esc_attr( $dp_options['membership']['field_label_fullname'] ); ?>">
			<td><label><input name="dp_options[membership][field_required_fullname]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_fullname'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
<?php
if ( 'type1' === $dp_options['membership']['fullname_type'] ) :
?>
				<?php _e( 'Placeholder of last name', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_last_name]" value="<?php echo esc_attr( $dp_options['membership']['field_label_last_name'] ); ?>">
				<br>
				<?php _e( 'Placeholder of first name', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_first_name]" value="<?php echo esc_attr( $dp_options['membership']['field_label_first_name'] ); ?>">
<?php
else :
?>
				<?php _e( 'Placeholder of first name', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_first_name]" value="<?php echo esc_attr( $dp_options['membership']['field_label_first_name'] ); ?>">
				<br>
				<?php _e( 'Placeholder of last name', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_last_name]" value="<?php echo esc_attr( $dp_options['membership']['field_label_last_name'] ); ?>">
<?php
endif;
?>
				<fieldset>
					<?php foreach ( $fullname_types as $option ) : ?>
					<p style="margin: 5px 0;"><label><input type="radio" name="dp_options[membership][fullname_type]" value="<?php echo esc_attr( $option['value'] ); ?>" <?php checked( $option['value'], $dp_options['membership']['fullname_type'] ); ?>><?php echo $option['label']; ?></label></p>
					<?php endforeach; ?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php _ex( 'Gender', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_gender]" value="<?php echo esc_attr( $dp_options['membership']['field_label_gender'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_gender]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_gender'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Residence area', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_area]" value="<?php echo esc_attr( $dp_options['membership']['field_label_area'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_area]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_area'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Birthday', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_birthday]" value="<?php echo esc_attr( $dp_options['membership']['field_label_birthday'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_birthday]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_birthday'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Company name', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_company]" value="<?php echo esc_attr( $dp_options['membership']['field_label_company'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_company]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_company'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Job', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_job]" value="<?php echo esc_attr( $dp_options['membership']['field_label_job'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_job]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_job'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Biography', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_desc]" value="<?php echo esc_attr( $dp_options['membership']['field_label_desc'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_desc]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_desc'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Website', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_website]" value="<?php echo esc_attr( $dp_options['membership']['field_label_website'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_website]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_website'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Facebook', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_facebook]" value="<?php echo esc_attr( $dp_options['membership']['field_label_facebook'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_facebook]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_facebook'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Twitter', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_twitter]" value="<?php echo esc_attr( $dp_options['membership']['field_label_twitter'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_twitter]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_twitter'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Instagram', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_instagram]" value="<?php echo esc_attr( $dp_options['membership']['field_label_instagram'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_instagram]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_instagram'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Youtube', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_youtube]" value="<?php echo esc_attr( $dp_options['membership']['field_label_youtube'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_youtube]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_youtube'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Tiktok', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_tiktok]" value="<?php echo esc_attr( $dp_options['membership']['field_label_tiktok'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_tiktok]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_tiktok'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Mail magazine', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_mail_magazine]" value="<?php echo esc_attr( $dp_options['membership']['field_label_mail_magazine'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_mail_magazine]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_mail_magazine'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Member news notify', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_member_news_notify]" value="<?php echo esc_attr( $dp_options['membership']['field_label_member_news_notify'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_member_news_notify]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_member_news_notify'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Follow/Like/Comment notify', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_social_notify]" value="<?php echo esc_attr( $dp_options['membership']['field_label_social_notify'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_social_notify]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_social_notify'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td><?php _ex( 'Messages notify', 'User form field label', 'tcd-w' ); ?></td>
			<td><?php _e( 'Label', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_label_messages_notify]" value="<?php echo esc_attr( $dp_options['membership']['field_label_messages_notify'] ); ?>"></td>
			<td><label><input name="dp_options[membership][field_required_messages_notify]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['field_required_messages_notify'] ); ?>><?php _e( 'Required field', 'tcd-w' ); ?></label></td>
		</tr>
		<tr>
			<td colspan="3"><?php _e( 'Required field html', 'tcd-w' ); ?> : <input class="regular-text" type="text" name="dp_options[membership][field_required_html]" value="<?php echo esc_attr( $dp_options['membership']['field_required_html'] ); ?>"></td>
		</tr>
	</table>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
<?php // エリア設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Residence area settings', 'tcd-w' ); ?></h3>
	<h4 class="theme_option_headline2"><?php _e( 'Residence area select options', 'tcd-w' ); ?></h4>
	<p><?php _e( 'Set the items of the area field.', 'tcd-w' ); ?></p>
	<textarea class="large-text" cols="50" rows="10" name="dp_options[membership][area]"><?php echo esc_textarea( $dp_options['membership']['area'] ); ?></textarea>
	<p class="description"><?php _e( 'Please enter One option per line.', 'tcd-w' ); ?></p>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
