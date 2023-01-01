<?php
global $user_id, $messages;

if ( $user_id && $messages ) :
	$current_ts = current_time( 'timestamp', false );
	$current_ts_gmt = current_time( 'timestamp', true );
	$current_ts_offset = $current_ts - $current_ts_gmt;

	foreach ( $messages as $message ) :
		if ( $message->sender_user_id == $user_id ) :
			$message_user_id = $message->recipient_user_id;
			$message_user_display_name = $message->recipient_display_name;
		elseif ( $message->recipient_user_id == $user_id ) :
			$message_user_id = $message->sender_user_id;
			$message_user_display_name = $message->sender_display_name;
		else :
			continue;
		endif;

		$message_user = get_user_by( 'id', $message_user_id );
		$unread = get_tcd_membership_messages_unread_number( $user_id, $message_user_id );

		$message_sent_gmt_ts = strtotime( $message->sent_gmt );
		$message_sent_ts = $message_sent_gmt_ts + $current_ts_offset;
		if ( date( 'Ymd', $message_sent_ts ) === date( 'Ymd', $current_ts ) ) :
			 $message_sent = human_time_diff( $message_sent_ts, $current_ts ) . __( ' ago', 'tcd-w' );
		else :
			 $message_sent = date( 'Y.m.d H:i', $message_sent_ts );
		endif;
?>
<div class="p-messages-users__item" data-user-id="<?php echo esc_attr( $message_user_id ); ?>">
	<div class="p-messages-user">
		<div class="p-messages-user__thumbnail p-messages-user-thumbnail"><?php echo get_avatar( $message_user_id, 96 ); ?></div>
		<div class="p-messages-user__name"><?php echo esc_html( $message_user ? $message_user->display_name : $message_user_display_name ); ?></div>
		<div class="p-messages-user__badge p-messages-user__unread"><?php echo $unread ? $unread : ''; ?></div>
	</div>
	<div class="p-messages-users__item-message"><?php echo get_tcd_membership_messages_message( $message, true ); ?></div>
	<div class="p-messages-users__item-meta p-messages-users__item-date"><?php echo esc_html( $message_sent ); ?></div>
</div>
<?php
	endforeach;
endif;
