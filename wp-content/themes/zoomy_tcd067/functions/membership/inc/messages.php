<?php
global $dp_options, $use_messages_type_options;
?>
<?php // メッセージ設定 ?>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Messages settings', 'tcd-w' ); ?></h3>
	<p><?php _e( 'Set a private message settings that members can send a message to a another members.', 'tcd-w' ); ?></p>
	<h4 class="theme_option_headline2"><?php _e( 'Message usage settings', 'tcd-w' ); ?></h4>
	<fieldset class="radios">
		<?php foreach ( $use_messages_type_options as $option ) : ?>
		<label><input type="radio" name="dp_options[membership][use_messages_type]" value="<?php echo esc_attr( $option['value'] ); ?>" <?php checked( $option['value'], $dp_options['membership']['use_messages_type'] ); ?>><?php printf( $option['label'], __( 'Information', 'tcd-w' ) ); ?></label>
		<?php endforeach; ?>
	</fieldset>
	<h4 class="theme_option_headline2"><?php _e( 'Forbidden words settings', 'tcd-w' ); ?></h4>
	<label><input name="dp_options[membership][use_messages_forbidden_words]" type="checkbox" value="1" <?php checked( 1, $dp_options['membership']['use_messages_forbidden_words'] ); ?>><?php _e( 'If message contains forbidden words, reject to send message.', 'tcd-w' ); ?></label>
	<h4 class="theme_option_headline2"><?php _e( 'Block members settings', 'tcd-w' ); ?></h4>
	<p><?php _e( 'Please set the members to be blocked by user ID.<br>Blocked members can send messages, but not be delivered to recipients and never recieve any messages.<br>Messages sent or received while blocked will not be displayed even if unblock them.', 'tcd-w' ); ?></p>
	<textarea class="large-text" cols="50" rows="10" name="dp_options[membership][messages_block_users]"><?php echo esc_textarea( $dp_options['membership']['messages_block_users'] ); ?></textarea>
	<p class="description"><?php _e( 'Please enter One option per line or comma-seperated list.', 'tcd-w' ); ?></p>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
<div class="theme_option_field cf">
	<h3 class="theme_option_headline"><?php _e( 'Messages wording settings', 'tcd-w' ); ?></h3>
	<p><?php _e( 'You can change the some wording of the messages.', 'tcd-w' ); ?></p>
	<table class="theme_option_table">
		<tr>
			<td><?php _e( 'Create new message', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_create_new_message]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_create_new_message'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Create a message', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_create_message]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_create_message'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Send message', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_send_message]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_send_message'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Message has been sent.', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_send_message_success]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_send_message_success'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'You cannot send a message to this member.', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_cannot_send]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_cannot_send'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Message has forbidden words.', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_has_forbidden_words]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_has_forbidden_words'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'All members', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_all_members]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_all_members'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Member was not found.', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_no_recipients]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_no_recipients'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Search members', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_search_members]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_search_members'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Blocked members', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_blocked_members]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_blocked_members'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Blocked member was not found.', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_no_blocked_members]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_no_blocked_members'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Block', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_block]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_block'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Do you want to block this member?', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_block_confirm]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_block_confirm'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Unblock', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_unblock]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_unblock'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Do you want to unblock this member?', 'tcd-w' ); ?></td>
			<td><input class="regular-text" type="text" name="dp_options[membership][messages_word_unblock_confirm]" value="<?php echo esc_attr( $dp_options['membership']['messages_word_unblock_confirm'] ); ?>"></td>
		</tr>
		<tr>
			<td><?php _e( 'Do you want to delete all messages from this member?', 'tcd-w' ); ?></td>
			<td><textarea class="regular-text" name="dp_options[membership][messages_word_delete_all_confirm]"><?php echo esc_textarea( $dp_options['membership']['messages_word_delete_all_confirm'] ); ?></textarea></td>
		</tr>
		<tr>
			<td><?php _e( 'Do you want to delete this message?', 'tcd-w' ); ?></td>
			<td><textarea class="regular-text" name="dp_options[membership][messages_word_delete_confirm]"><?php echo esc_textarea( $dp_options['membership']['messages_word_delete_confirm'] ); ?></textarea></td>
		</tr>
	</table>
	<input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-w' ); ?>">
</div>
