<?php
global $dp_options, $user_ids, $paged;
if ( ! $dp_options ) $dp_options = get_design_plus_option();

if ( $user_ids ) :
	if ( 1 === $paged ) :
?>
<div class="p-user-list p-author__list__inner">
<?php
	endif;

	foreach ( $user_ids as $user_id ) :
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) continue;
		if ( ! tcd_membership_messages_can_send_message( $user->ID ) ) continue;
?>
	<div class="p-user-list__item p-author__list-item">
		<div class="p-user-list__item__inner">
			<a class="p-hover-effect--<?php echo esc_attr( $dp_options['hover_type'] ); ?>" href="<?php echo esc_attr( get_author_posts_url( $user->ID ) ); ?>" target="_blank">
				<div class="p-author__thumbnail js-object-fit-cover">
					<div class="p-author__thumbnail__inner p-hover-effect__image"><?php echo get_avatar( $user->ID, 300 ); ?></div>
				</div>
				<div class="p-author__name"><?php echo esc_html( $user->display_name ); ?></div>
			</a>
			<div class="p-author__create-message">
				<a class="p-button-message p-has-icon p-icon-messages-envelope js-create-message" href="#" title="<?php echo esc_attr( $dp_options['membership']['messages_word_create_message'] ); ?>" data-user-id="<?php echo esc_attr( $user->ID ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'tcd-create-message-' . $user->ID ) ); ?>"></a>
			</div>
		</div>
	</div>
<?php
	endforeach;

	if ( 1 === $paged ) :
?>
</div>
<?php
	endif;
elseif ( 1 === $paged ) :
?>
<p class="no_users"><?php echo esc_html( $dp_options['membership']['messages_word_no_recipients'] ); ?></p>
<?php
endif;
