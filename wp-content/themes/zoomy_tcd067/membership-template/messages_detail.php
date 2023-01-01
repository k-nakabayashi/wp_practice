<?php
global $dp_options, $user_id, $message_user, $message_user_id, $message_user_display_name, $messages;

if ( $messages ) :
	$current_ts = current_time( 'timestamp', false );
	$current_ts_gmt = current_time( 'timestamp', true );
	$current_ts_offset = $current_ts - $current_ts_gmt;
?>
<div class="p-messages-detail__header p-messages-user">
<?php
	if ( $message_user ) :
?>
	<a class="p-messages-user__thumbnail p-messages-user-thumbnail" href="<?php echo esc_attr( get_author_posts_url( $message_user_id ) ); ?>" target="_blank"><?php echo get_avatar( $message_user_id, 96 ); ?></a>
	<a class="p-messages-user__name" href="<?php echo esc_attr( get_author_posts_url( $message_user_id ) ); ?>" target="_blank"><?php echo esc_html( $message_user->display_name ); ?></a>
<?php
	else :
?>
	<div class="p-messages-user__thumbnail p-messages-user-thumbnail"><?php echo get_avatar( null, 96 ); ?></div>
	<div class="p-messages-user__name"><?php echo esc_html( $message_user_display_name ? $message_user_display_name : 'Unknown user' ); ?></div>
<?php
	endif;
?>
	<ul class="p-messages-user__nav">
		<li class="p-messages-user__nav-block"><a class="p-icon-messages-block" href="#" title="<?php echo esc_attr( $dp_options['membership']['messages_word_block'] ); ?>" data-user-id="<?php echo esc_attr( $message_user_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'tcd-messages-block-' . $message_user_id ) ); ?>"></a></li>
		<li class="p-messages-user__nav-delete-all"><a class="p-icon-messages-delete" href="#" title="<?php esc_attr_e( 'Delete all messages', 'tcd-w' ); ?>" data-user-id="<?php echo esc_attr( $message_user_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'tcd-messages-delete-all-' . $message_user_id ) ); ?>"></a></li>
	</ul>
</div>
<div class="p-messages-detail__main p-messages-scrollbar">
<?php
get_template_part( 'membership-template/messages_detail_list' );
?>
</div>
<div class="p-messages-detail__footer">
	<a class="p-messages-detail__footer-create p-messages__create-message__headline" href="#"><?php echo esc_html( $dp_options['membership']['messages_word_create_message'] ); ?><span class="p-icon-messages-paperplane"></span></a>
	<form class="p-messages__create-message__form" data-user-id="<?php echo esc_attr( $message_user_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'tcd-create-message-' . $message_user_id ) ); ?>">
		<textarea class="p-messages__create-message__input"></textarea>
		<div class="p-messages__create-message__button">
			<button class="p-button p-rounded-button p-messages__create-message__submit" type="submit"><?php echo esc_html( $dp_options['membership']['messages_word_send_message'] ); ?></button>
		</div>
	</form>
</div>
<?php
endif;
