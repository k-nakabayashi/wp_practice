<?php
global $dp_options, $blocked_users, $paged;
if ( ! $dp_options ) $dp_options = get_design_plus_option();

if ( $blocked_users ) :
	foreach ( $blocked_users as $user ) :
?>
<div class="p-user-list__item">
	<div class="p-user-list__item__inner">
		<a class="p-hover-effect--<?php echo esc_attr( $dp_options['hover_type'] ); ?>" href="<?php echo esc_attr( get_author_posts_url( $user->ID ) ); ?>" target="_blank">
			<div class="p-author__thumbnail js-object-fit-cover">
				<div class="p-author__thumbnail__inner p-hover-effect__image"><?php echo get_avatar( $user->ID, 300 ); ?></div>
			</div>
			<div class="p-author__name"><?php echo esc_html( $user->display_name ); ?></div>
		</a>
		<div class="p-author__unblock">
			<a class="p-button-unblock" href="#" data-user-id="<?php echo esc_attr( $user->ID ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'tcd-messages-unblock-' . $user->ID ) ); ?>"><?php echo esc_html( $dp_options['membership']['messages_word_unblock'] ); ?></a>
		</div>
	</div>
</div>
<?php
	endforeach;
elseif ( 1 === $paged ) :
?>
<p class="no_users"><?php echo esc_html( $dp_options['membership']['messages_word_no_blocked_members'] ); ?></p>
<?php
endif;
