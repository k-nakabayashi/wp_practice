<?php

/**
 * コメント保存時、tcd_membership_actionsテーブルにレコード追加
 */
function tcd_membership_insert_comment_action( $comment_id, $comment ) {
	global $dp_options;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	// 承認済み以外は終了
	if ( ! in_array( $comment->comment_approved, array( 1, '1', 'approve' ), true ) ) {
		return;
	}

	// 記事がない場合は終了
	$post = get_post( $comment->comment_post_ID );
	if ( ! $comment->comment_post_ID || ! $post ) {
		return;
	}

	// コメントIDがメタテーブルに保存済みの場合は終了
	if ( get_tcd_membership_meta_by_meta( 'comment_id', $comment_id ) ) {
		return;
	}

	// 記事投稿者とコメント者が異なる場合は記事投稿者に通知
	if ( $comment->user_id && $post->post_author != $comment->user_id ) {
		// アクション保存
		$action_id = insert_tcd_membership_action( 'comment', $comment->user_id, $post->post_author, $comment->comment_post_ID );
		if ( $action_id ) {
			// メタ保存
			update_tcd_membership_action_meta( $action_id, 'comment_id', $comment_id );
		}
	}

	// 要データーベースバージョン1.0.1
	if ( tcd_membership_database_version_compare( '1.0.1' ) ) {
		// 通知ユーザーID配列
		$target_user_ids = array();

		// スレッド表示の返信
		if ( $comment->comment_parent ) {
			$comment_parent = get_comment( $comment->comment_parent );
			if ( $comment_parent && $comment_parent->user_id ) {
				// 記事投稿者以外かつ自身のコメントへの返信以外なら通知
				if ( $post->post_author != $comment_parent->user_id && $comment->user_id != $comment_parent->user_id ) {
					$target_user_ids[] = (int) $comment_parent->user_id;
				}
			}
		}

		// テーマオプションでメンション有効
		if ( ( $post->post_type !== $dp_options['photo_slug'] && $dp_options['comment_mention_label'] ) ||
			( $post->post_type === $dp_options['photo_slug'] && $dp_options['photo_comment_mention_label'] ) ) {
			if ( false !== strpos( $comment->comment_content, '@' ) ) {
				// メンションからユーザー表示名抜き出し
				$mention_display_names = array();
				if ( preg_match_all( '/@(.+)$/m', $comment->comment_content, $matches ) ) {
					$mention_display_names = $matches[1];
				}
				if ( preg_match_all( '/@([^@\s]+)/', $comment->comment_content, $matches ) ) {
					$mention_display_names = array_merge( $mention_display_names, $matches[1]);
				}
				if ( $mention_display_names ) {
					global $wpdb;
					$sql = "SELECT * FROM $wpdb->users WHERE user_status = 0 AND display_name = %s";

					// 末尾に\rがつく場合があるので削除
					foreach( $mention_display_names as $key => $mention_display_name ) {
						$mention_display_names[$key] = rtrim( $mention_display_name, "\r" );
					}

					$mention_display_names = array_unique( $mention_display_names );

					foreach( $mention_display_names as $mention_display_name ) {
						// ユーザー表示名からユーザーID検索
						$mention_user_id = (int) $wpdb->get_var( $wpdb->prepare( $sql, $mention_display_name ) );

						// 記事投稿者は通知されているので除外、コメント投稿者自身のメンションも除外
						if ( $post->post_author == $mention_user_id || $comment->user_id == $mention_user_id ) {
							continue;
						}

						// 通知ユーザーID配列に追加
						if ( $mention_user_id && ! in_array( $mention_user_id, $target_user_ids, true ) ) {
							$target_user_ids[] = $mention_user_id;
						}
					}
				}
			}
		}

		if ( $target_user_ids ) {
			$target_user_ids = array_unique( $target_user_ids );
			sort( $target_user_ids );

			// アクション保存
			$action_id = insert_tcd_membership_action( 'comment_reply', $comment->user_id, $target_user_ids, $comment->comment_post_ID );
			if ( $action_id ) {
				// メタ保存
				update_tcd_membership_action_meta( $action_id, 'comment_id', $comment_id );
			}
		}
	}
}
add_action( 'wp_insert_comment', 'tcd_membership_insert_comment_action', 10, 2 );

/**
 * コメント承認時、tcd_membership_actionsテーブルにレコード追加
 */
function tcd_membership_wp_set_comment_status( $comment_id, $comment_status ) {
	// 承認済みの場合のみ
	if ( in_array( $comment_status, array( 1, '1', 'approve' ), true ) ) {
		tcd_membership_insert_comment_action( $comment_id, get_comment( $comment_id ) );
	}
}
add_action( 'wp_set_comment_status', 'tcd_membership_wp_set_comment_status', 10, 2 );
/**
 * コメント表示時、メンションを投稿者詳細ページにリンクする
 */
function tcd_membership_get_comment_text( $comment_content, $comment, $args ) {
	if ( ! is_admin() && false !== strpos( $comment_content, '@' ) ) {
		// メンションからユーザー表示名抜き出し
		$mention_display_names = array();
		if ( preg_match_all( '/@(.+)$/m', $comment_content, $matches ) ) {
			$mention_display_names = $matches[1];
		}
		if ( preg_match_all( '/@([^@\s]+)/', $comment_content, $matches ) ) {
			$mention_display_names = array_merge( $mention_display_names, $matches[1]);
		}
		if ( $mention_display_names ) {
			global $wpdb;
			$sql = "SELECT * FROM $wpdb->users WHERE user_status = 0 AND display_name = %s";

			// 末尾に\rがつく場合があるので削除
			foreach( $mention_display_names as $key => $mention_display_name ) {
				$mention_display_names[$key] = rtrim( $mention_display_name, "\r" );
			}

			$mention_display_names = array_unique( $mention_display_names );

			foreach( $mention_display_names as $mention_display_name ) {
				// ユーザー表示名からユーザーID検索
				$mention_user_id = (int) $wpdb->get_var( $wpdb->prepare( $sql, $mention_display_name ) );
				if ( ! $mention_user_id ) continue;

				$mention_user = get_user_by( 'id', $mention_user_id );
				if ( ! $mention_user ) continue;

				$mention_user_url = get_author_posts_url( $mention_user_id );
				if ( ! $mention_user_url ) continue;

				$comment_content = preg_replace(
					'/(@' . preg_quote( $mention_display_name ) . ')([@\s])/',
					'<a class="mention-link" href="' . esc_url( $mention_user_url ) . '">$1</a>$2',
					$comment_content
				);
			}
		}
	}

	return $comment_content;
}
add_filter( 'get_comment_text', 'tcd_membership_get_comment_text', 10, 3 );

