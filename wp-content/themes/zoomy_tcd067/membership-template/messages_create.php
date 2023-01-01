<?php
global $dp_options, $tcd_membership_vars;

get_header();
?>
<main class="l-main">
	<div class="p-member-page-header">
		<div class="l-inner">
			<h1 class="p-member-page-header__title"><?php echo esc_html( get_tcd_membership_memberpage_title( $tcd_membership_vars['memberpage_type'] ) ); ?></h1>
			<ul class="p-messages__page-header-nav">
				<li class="p-messages__page-header-nav__searchform">
					<form class="p-messages-search-members">
						<input class="p-messages-search-members__input" name="s" type="text" placeholder="<?php echo esc_attr( $dp_options['membership']['messages_word_search_members'] ); ?>">
						<button class="p-messages-search-members__submit" type="submit">&#xe947;</button>
					</form>
				</li>
				<li class="p-messages__page-header-nav__search"><a class="p-icon-messages-search js-messages-search" title="<?php echo esc_attr( $dp_options['membership']['messages_word_search_members'] ); ?>"></a></li>
				<li class="p-messages__page-header-nav__create"><a class="p-icon-messages-create" href="<?php echo esc_url( get_tcd_membership_memberpage_url( 'messages_create' ) ); ?>" title="<?php echo esc_attr( $dp_options['membership']['messages_word_create_new_message'] ); ?>"></a></li>
				<li class="p-messages__page-header-nav__block"><a class="p-icon-messages-block" href="<?php echo esc_url( get_tcd_membership_memberpage_url( 'messages_blocked_members' ) ); ?>" title="<?php echo esc_attr( $dp_options['membership']['messages_word_blocked_members'] ); ?>"></a></li>
			</ul>
		</div>
	</div>
	<div class="p-member-page p-messages p-messages--<?php echo esc_attr( $tcd_membership_vars['memberpage_type'] ); ?>">
		<div class="p-author__lists">
			<div id="js-author__list-tabs" class="p-author__list-tabs">
				<ul class="p-author__list-tabs__inner l-inner">
<?php
$list_totals = get_tcd_membership_messages_recipients_list_totals();

if ( 'type1' === $tcd_membership_vars['messages_type'] ) :
?>
					<li class="p-author__list-tab is-active" data-list-type="all" data-max-page="<?php echo absint( $list_totals['all']['max_num_pages'] ); ?>"><h2 class="p-author__list-tab_title"><?php echo esc_html( $dp_options['membership']['messages_word_all_members'] ); ?></h2><span class="p-author__list-tab_badge"><?php printf( __( '%d users', 'tcd-w' ), $list_totals['all']['total'] ); ?></span></li>
					<li class="p-author__list-tab" data-list-type="follower" data-max-page="<?php echo absint( $list_totals['follower']['max_num_pages'] ); ?>"><h2 class="p-author__list-tab_title"><?php _e( 'Follower', 'tcd-w' ) ?></h2><span class="p-author__list-tab_badge"><?php printf( __( '%d users', 'tcd-w' ), $list_totals['follower']['total'] ); ?></span></li>
					<li class="p-author__list-tab" data-list-type="following" data-max-page="<?php echo absint( $list_totals['following']['max_num_pages'] ); ?>"><h2 class="p-author__list-tab_title"><?php _e( 'Following', 'tcd-w' ) ?></h2><span class="p-author__list-tab_badge"><?php printf( __( '%d users', 'tcd-w' ), $list_totals['following']['total'] ); ?></span></li>
<?php
elseif ( 'type2' === $tcd_membership_vars['messages_type'] ) :
?>
					<li class="p-author__list-tab is-active" data-list-type="follower" data-max-page="<?php echo absint( $list_totals['follower']['max_num_pages'] ); ?>"><h2 class="p-author__list-tab_title"><?php _e( 'Follower', 'tcd-w' ) ?></h2><span class="p-author__list-tab_badge"><?php printf( __( '%d users', 'tcd-w' ), $list_totals['follower']['total'] ); ?></span></li>
<?php
endif;
?>
				</ul>
			</div>
		</div>
		<div class="p-author__lists__inner">
			<div id="js-messages-recipients" class="p-author__list l-inner"></div>
		</div>
	</div>
</main>
<?php
get_footer();
