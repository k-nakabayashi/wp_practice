<?php
global $dp_options, $tcd_membership_vars;

get_header();
?>
<main class="l-main has-bg--pc has-bg--mobile">
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
		<div id="js-messages-blocked-members" class="p-author__list p-user-list l-inner"></div>
	</div>
</main>
<?php
get_footer();
