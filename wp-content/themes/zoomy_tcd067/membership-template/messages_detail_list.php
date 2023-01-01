<?php
global $user_id, $message_user_id, $messages;

if ( $messages ) :
	$current_ts = current_time( 'timestamp', false );
	$current_ts_gmt = current_time( 'timestamp', true );
	$current_ts_offset = $current_ts - $current_ts_gmt;

	foreach ( $messages as $message ) :
		if ( $message->sender_user_id == $user_id ) :
			$is_sender = true;
			$message_type = 'type1';
			$read_status = 'sender-';
		else :
			$is_sender = false;
			$message_type = 'type2';
			$read_status = 'recipient-';
		endif;

		if ( $message->recipient_read ) :
			$read_status .= 'already-read';
			$read_label = __( 'Already read', 'tcd-w' );
		else :
			$read_status .= 'unread';
			$read_label = __( 'Unread', 'tcd-w' );
		endif;

		$message_sent_gmt_ts = strtotime( $message->sent_gmt );
		$message_sent_ts = $message_sent_gmt_ts + $current_ts_offset;
		if ( date( 'Ymd', $message_sent_ts ) === date( 'Ymd', $current_ts ) ) :
			 $message_sent = human_time_diff( $message_sent_ts, $current_ts ) . __( ' ago', 'tcd-w' );
		else :
			 $message_sent = date( 'Y.m.d H:i', $message_sent_ts );
		endif;
?>
	<div class="p-messages-detail__message p-messages-detail__message--<?php echo esc_attr( $message_type . ' ' . $read_status ); ?>" data-message-id="<?php echo esc_attr( $message->id ); ?>" data-delete-nonce="<?php echo esc_attr( wp_create_nonce( 'tcd-messages-delete-' . $message->id ) ); ?>">
<?php
		if ( 'type2' === $message_type ) :
?>
		<div class="p-messages-detail__message-user-thumbnail p-messages-user-thumbnail"><?php echo get_avatar( $message_user_id, 96 ); ?></div>
<?php
		endif;
?>
		<div class="p-messages-detail__message-body p-body"><?php echo get_tcd_membership_messages_message( $message ); ?></div>
		<div class="p-messages-detail__message-meta">
			<div class="p-messages-detail__message-date"><?php echo esc_html( $message_sent ); ?></div>
<?php
		if ( $is_sender ) :
?>
			<div class="p-messages-detail__message-read-status"><?php echo esc_html( $read_label ); ?></div>
<?php
		endif;
?>
			<a class="p-messages-detail__message-delete" href="#">削除</a>
		</div>
	</div>
<?php
	endforeach;
endif;
