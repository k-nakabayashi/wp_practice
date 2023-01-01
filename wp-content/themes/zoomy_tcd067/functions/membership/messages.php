<?php

/**
tcd_membership_messagesテーブルの削除管理カラム「sender_deleted」「recipient_deleted」の仕様
0 : 未削除
1 : 単一削除済み
2 : 全削除済み
9 : システムブロックで送信時に削除済み
いずれの削除済み状態でも未削除に戻す方法は用意していません。
*/

/**
 * メッセージ機能が利用可能か
 */
function tcd_membership_messages_type() {
	global $dp_options;

	static $messages_type = null;

	if ( null !== $messages_type ) {
		return $messages_type;
	}

	$messages_type = false;

	// 要データーベースバージョン1.1
	if ( tcd_membership_database_version_compare( '1.1' ) ) {
		// 会員制システムオプション 全員利用可
		if ( 'type1' === $dp_options['membership']['use_messages_type'] ) {
			$messages_type = $dp_options['membership']['use_messages_type'];

		// 会員制システムオプション フォロワーのみ利用可+フォロー使用可
		} elseif ( 'type2' === $dp_options['membership']['use_messages_type'] && $dp_options['membership']['use_follow'] ){
			$messages_type = $dp_options['membership']['use_messages_type'];
		}
	}

	return $messages_type;
}

/**
 * メッセージ scripts
 */
function tcd_membership_messages_wp_enqueue_scripts() {
	global $dp_options, $tcd_membership_vars;

	$messages_enqueue = false;

	$messages_localize = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'ajax_error_message' => __( 'Error was occurred. Please retry again.', 'tcd-w' ),
		'create_message_headline' => $dp_options['membership']['messages_word_create_message'] . '<span class="p-icon-messages-paperplane"></span>',
		'send_button' => $dp_options['membership']['messages_word_send_message'],
		'confirm_button_ok' => __( 'OK', 'tcd-w' ),
		'confirm_button_cancel' => __( 'Cancel', 'tcd-w' ),
		'confirm_delete' => $dp_options['membership']['messages_word_delete_confirm'],
		'confirm_delete_all' => $dp_options['membership']['messages_word_delete_all_confirm'],
		'confirm_block' => $dp_options['membership']['messages_word_block_confirm'],
		'confirm_unblock' => $dp_options['membership']['messages_word_unblock_confirm']
	);

	// メッセージinbox・ブロック一覧を表示の場合
	if ( in_array( $tcd_membership_vars['memberpage_type'], array( 'messages', 'messages_blocked_members' ) ) ) {
		$messages_enqueue = true;

	// メッセージ作成・ブログ・写真シングルページ、投稿者ページの場合
	} elseif ( 'messages_create' === $tcd_membership_vars['memberpage_type'] || is_singular( array( 'post', $dp_options['photo_slug'] ) ) || is_author() ) {
		$messages_enqueue = true;
		$messages_localize['modal_send'] = true;
	}

	if ( $messages_enqueue ) {
		wp_enqueue_script( 'zoomy-messages', get_template_directory_uri() . '/js/messages.js', array(), version_num(), true );
		wp_localize_script( 'zoomy-messages', 'TCD_MEMBERSHIP_MESSAGES', $messages_localize );
	}
}

/**
 * body class
 */
function tcd_membership_messages_body_classes( $classes ) {
	global $tcd_membership_vars;

	// メッセージinboxの場合membership-messages-messages追加
	if ( 'messages' === $tcd_membership_vars['memberpage_type'] ) {
		$classes[] = 'membership-messages-messages';
	// メッセージ作成・ブロック一覧の場合membership-messages追加
	} elseif ( in_array( $tcd_membership_vars['memberpage_type'], array( 'messages_create', 'messages_blocked_members' ) ) ) {
		$classes[] = 'membership-messages';
	}

	return $classes;
}

/**
 * 未読メッセージ数取得
 */
function get_tcd_membership_messages_unread_number( $user_id = null, $sender_user_id = null, $sent_gmt_from = null ) {
	global $wpdb;

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	} elseif ( $user_id instanceof WP_User) {
		$user_id = $user_id->ID;
	} else {
		$user_id = intval( $user_id );
	}

	if ( 0 >= $user_id ) {
		return false;
	}

	if ( $sender_user_id ) {
		$sender_user_id = intval( $sender_user_id );
	}

	$sent_gmt_from_ts_min = mktime( 0, 0, 0, 1, 1, 2000 );
	if ( is_string( $sent_gmt_from ) && preg_match('#^\d{4}[-/]\d{1,2}[-/]\d{1,2}#', $sent_gmt_from ) ) {
		$sent_gmt_from = strtotime( $sent_gmt_from );
	}
	if ( $sent_gmt_from && is_numeric( $sent_gmt_from ) && $sent_gmt_from >= $sent_gmt_from_ts_min ) {
		$sent_gmt_from_ts = intval( $sent_gmt_from );
	} else {
		$sent_gmt_from_ts = null;
	}

	$tablename = get_tcd_membership_tablename( 'messages' );

	$where_recipient = "recipient_user_id = {$user_id} AND recipient_deleted = 0 AND recipient_read = 0";

	// 対象ユーザー指定の場合（ブロックしていても取得します）
	if ( $sender_user_id ) {
		$where_recipient .= " AND sender_user_id = {$sender_user_id}";
	} else {
		// ユーザーのブロックユーザーID配列
		$blocked_user_ids = get_tcd_membership_messages_block_user_ids( $user_id, false );

		if ( $blocked_user_ids ) {
			$blocked_user_ids = implode( ',', $blocked_user_ids );
			$where_recipient .= " AND sender_user_id NOT IN ({$blocked_user_ids})";
		}
	}

	$sql = "SELECT COUNT(id) FROM {$tablename} "
		 . "WHERE ( {$where_recipient} )";

	if ( $sent_gmt_from_ts ) {
		$sql .= " AND sent_gmt >= '" . date( 'Y-m-d H:i:s', $sent_gmt_from_ts ) . "'";
	}

	return intval( $wpdb->get_var( $sql ) );
}

/**
 * 該当ユーザーが送信者・受信者の対象ユーザーごとの最新メッセージ一覧取得
 */
function get_tcd_membership_messages_latest_list( $args ) {
	global $wpdb;

	$default_args = array(
		'user_id' => null,
		'less_message_id' => 0,
		'limit' => apply_filters( 'get_tcd_membership_messages_latest_list_limit', 50 ),
		'search' => null
	);
	$default_args = apply_filters( 'get_tcd_membership_messages_latest_list_default_args', $default_args );
	$args = wp_parse_args( $args, $default_args );
	$args = apply_filters( 'get_tcd_membership_messages_latest_args', $args );

	extract( $args );

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	} else {
		$user_id = intval( $user_id );
	}

	if ( 0 >= $user_id ) {
		return false;
	}

	if ( $less_message_id ) {
		$less_message_id = intval( $less_message_id );
	}

	$tablename = get_tcd_membership_tablename( 'messages' );

	$query_where_add = null;
	$query_limit = null;

	$where_sender = "sender_user_id = {$user_id} AND sender_deleted = 0";
	$where_recipient = "recipient_user_id = {$user_id} AND recipient_deleted = 0";

	// ユーザーのブロックユーザーID配列
	$arr_blocked_user_ids = get_tcd_membership_messages_block_user_ids( $user_id, false );
	if ( $arr_blocked_user_ids ) {
		$str_blocked_user_ids = implode( ',', $arr_blocked_user_ids );
		$where_sender .= " AND recipient_user_id NOT IN ({$str_blocked_user_ids})";
		$where_recipient .= " AND sender_user_id NOT IN ({$str_blocked_user_ids})";
	}

	if ( $search ) {
		$search = trim( $search, "* \n\r\t\v\0" );
		if ( ! $search ) {
			return false;
		}

		$arr_search_user_ids = get_users( array(
			'count_total' => false,
			'exclude' => $arr_blocked_user_ids,
			'fields' => 'ID',
			'orderby' => 'ID',
			'order' => 'ASC',
			'search' => '*' . $search . '*',
			'search_columns' => array( 'display_name' )
		) );

		if ( $arr_search_user_ids ) {
			$str_search_user_ids = implode( ',', $arr_search_user_ids );
			$where_sender .= " AND recipient_user_id IN ({$str_search_user_ids})";
			$where_recipient .= " AND sender_user_id IN ({$str_search_user_ids})";
		} else {
			return false;
		}
	}

	// id未満指定があれば（メッセージinboxのユーザーリストの次ページ用）
	if ( 0 < $less_message_id ) {
		$query_where_add .= " AND id < {$less_message_id}";
	}

	// 表示件数
	if ( 0 < $limit ) {
		$query_limit = "LIMIT " . intval( $limit );
	}

	// 対象ユーザーごとの最新メッセージIDを取得するサブクエリー
	$subquery = "SELECT MAX(id) FROM {$tablename} "
		 . "WHERE ( ( {$where_sender} ) OR ( {$where_recipient} ) ) "
		// このGROUP BYはA→B、B→Aを同じものとしてグループ化する
		 . "GROUP BY IF ( sender_user_id < recipient_user_id, CONCAT(sender_user_id, ',', recipient_user_id), CONCAT(recipient_user_id, ',', sender_user_id) )";

	$sql = "SELECT * FROM {$tablename} WHERE id IN ( {$subquery} ) {$query_where_add} ORDER BY id DESC {$query_limit}";

	return $wpdb->get_results( $sql );
}

/**
 * 特定ユーザーとのメッセージ一覧取得
 */
function get_tcd_membership_messages_user_messages( $args ) {
	global $wpdb;

	$default_args = array(
		'user_id' => null,
		'target_user_id' => null,
		'get_all_unread' => false,
		'greater_message_id' => 0,
		'return_order_asc' => true,
		'less_message_id' => 0,
		'limit' => apply_filters( 'get_tcd_membership_messages_user_messages_limit', 50 )
	);
	$default_args = apply_filters( 'get_tcd_membership_messages_user_messages_default_args', $default_args );
	$args = wp_parse_args( $args, $default_args );
	$args = apply_filters( 'get_tcd_membership_messages_user_messages_args', $args );

	extract( $args );

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	} else {
		$user_id = intval( $user_id );
	}

	if ( 0 >= $user_id || 0 >= $target_user_id ) {
		return false;
	}

	if ( $greater_message_id ) {
		$greater_message_id = intval( $greater_message_id );
	}

	if ( $less_message_id ) {
		$less_message_id = intval( $less_message_id );
	}

	$tablename = get_tcd_membership_tablename( 'messages' );

	$where_sender = "sender_user_id = {$user_id} AND sender_deleted = 0 AND recipient_user_id = {$target_user_id} ";
	$where_recipient = "recipient_user_id = {$user_id} AND recipient_deleted = 0 AND sender_user_id = {$target_user_id}";
	$where_add = null;
	$query_limit = null;

	// 未読全取得フラグがあれば、limit調整
	if ( $get_all_unread ) {
		$limit_all_unread = null;

		// 最古の未読id取得
		$sql = "SELECT id FROM {$tablename} "
			 . "WHERE recipient_user_id = {$user_id} AND recipient_deleted = 0 AND recipient_read = 0 AND sender_user_id = {$target_user_id} "
			 . "ORDER BY id ASC LIMIT 1";
		$unread_message_id = $wpdb->get_var( $sql );

		if ( $unread_message_id ) {
			// 最古の未読id以降のメッセージ数取得
			$sql = "SELECT COUNT(id) FROM {$tablename} "
				 . "WHERE ( ( {$where_sender} ) OR ( {$where_recipient} ) ) AND id >={$unread_message_id}";
			$limit_all_unread = $wpdb->get_var( $sql );
		}

		if ( $limit_all_unread ) {
			// + 2 は即時のajaxロードを防ぐため
			$limit = max( $limit, $limit_all_unread + 2 );
		}
	} else {
		// id超過指定がある場合 ※未読全取得フラグありなら無視されます
		if ( $greater_message_id ) {
			$where_add .= " AND id > {$greater_message_id}";
		}

		// id未満指定がある場合 ※未読全取得フラグありなら無視されます
		if ( $less_message_id ) {
			$where_add .= " AND id < {$less_message_id}";
		}
	}

	if ( 0 < $limit ) {
		$query_limit = "LIMIT {$limit}";
	}

	// メッセージ取得 ※この時点ではid降順
	$sql = "SELECT * FROM {$tablename} "
		 . "WHERE ( ( {$where_sender} ) OR ( {$where_recipient} ) ) {$where_add} "
		 . "ORDER BY id DESC {$query_limit}";
	$messages = $wpdb->get_results( $sql );

	// 昇順フラグがあれば逆順に並び替え
	if ( $messages && $return_order_asc ) {
		$messages = array_reverse( $messages );
	}

	return $messages;
}

/**
 * 該当ユーザーが送信者・受信者の対象ユーザー数取得
 */
function get_tcd_membership_messages_users_count( $user_id = null, $search = null ) {
	global $wpdb;

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	} else {
		$user_id = intval( $user_id );
	}

	if ( 0 >= $user_id ) {
		return false;
	}

	$tablename = get_tcd_membership_tablename( 'messages' );

	$where_sender = "sender_user_id = {$user_id} AND sender_deleted = 0";
	$where_recipient = "recipient_user_id = {$user_id} AND recipient_deleted = 0";

	// ユーザーのブロックユーザーID配列
	$arr_blocked_user_ids = get_tcd_membership_messages_block_user_ids( $user_id, false );
	if ( $arr_blocked_user_ids ) {
		$str_blocked_user_ids = implode(',', $arr_blocked_user_ids );
		$where_sender .= " AND recipient_user_id NOT IN ({$str_blocked_user_ids})";
		$where_recipient .= " AND sender_user_id NOT IN ({$str_blocked_user_ids})";
	}

	if ( $search ) {
		$search = trim( $search, "* \n\r\t\v\0" );
		if ( ! $search ) {
			return false;
		}

		$arr_search_user_ids = get_users( array(
			'count_total' => false,
			'exclude' => $arr_blocked_user_ids,
			'fields' => 'ID',
			'orderby' => 'ID',
			'order' => 'ASC',
			'search' => '*' . $search . '*',
			'search_columns' => array( 'display_name' )
		) );

		if ( $arr_search_user_ids ) {
			$str_search_user_ids = implode( ',', $arr_search_user_ids );
			$where_sender .= " AND recipient_user_id IN ({$str_search_user_ids})";
			$where_recipient .= " AND sender_user_id IN ({$str_search_user_ids})";
		} else {
			return false;
		}
	}

	// 対象ユーザーごとの最新メッセージIDを取得するサブクエリー
	$subquery = "SELECT MAX(id) FROM {$tablename} "
		 . "WHERE ( ( {$where_sender} ) OR ( {$where_recipient} ) ) "
		// このGROUP BYはA→B、B→Aを同じものとしてグループ化する
		 . "GROUP BY IF ( sender_user_id < recipient_user_id, CONCAT( sender_user_id, ',', recipient_user_id ), CONCAT( recipient_user_id, ',', sender_user_id ) )";

	$sql = "SELECT COUNT(id) FROM {$tablename} WHERE id IN ( {$subquery} )";

	return intval( $wpdb->get_var( $sql ) );
}

/**
 * 暗号化対応のメッセージ本文を返す
 */
function get_tcd_membership_messages_message( $messages_db_row, $excerpt = false ) {
	if ( ! isset( $messages_db_row->message, $messages_db_row->message_encrypted ) ) {
		return null;
	}

	$message = $messages_db_row->message;

	// 暗号化フラグがあれば復号化
	if ( $messages_db_row->message_encrypted ) {
		// openssl_encrypt(), openssl_decrypt()が存在すれば暗号化
		if ( function_exists( 'openssl_decrypt' ) ) {
			$timestamp = strtotime( $messages_db_row->sent_gmt );
			$message = openssl_decrypt( $message, 'AES-128-ECB', sprintf( 'tcd-send-message--%d-%d--%d', $messages_db_row->sender_user_id, $messages_db_row->recipient_user_id, $timestamp ) );
		}
	}

	if ( $excerpt ) {
		// タグ削除
		$message = strip_tags( $message );

		// 全角半角スペース・改行等を半角スペースに変換
		$message = preg_replace( '/[\x00\s]+/u', ' ', $message );

		$message_length = strlen( $message );

		if ( function_exists( 'mb_strimwidth' ) ) {
			$message = mb_strimwidth( $message, 0, 80, '…' );
		} elseif ( function_exists( 'wp_trim_words' ) ) {
			$message = wp_trim_words( $message, 80, '' );
		} else {
			$message = substr( $message, 0, 110 );
		}

		if ( $message && strlen( $message ) < $message_length ) {
			$message .= '…';
		}
	} else {
		// aタグが存在すればtarget="_blank"追加
		if ( preg_match_all( '/<a\s[^>]+?>/si', $message, $matches ) ) {
			foreach ( $matches[0] as $m ) {
				if ( preg_match( '/\s*target=[\'\"]?[^\'\"]*[\'\"]?/i', $m, $matches2 ) ) {
					$m_replace = str_replace( $matches2[0], ' target="_blank"', $m );
				} else {
					$m_replace = str_replace( '>', ' target="_blank">', $m );
				}
				$message = str_replace( $m, $m_replace, $message );
			}
		}

		// URL自動リンク
		$pattern = '/(= ?[\'\"]|<a.*?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/';
		$message = preg_replace_callback( $pattern, function( $matches ) {
			// 既にリンクの場合等はそのまま
			if ( isset( $matches[1] ) ) return $matches[0];
			return "<a href=\"{$matches[0]}\" target=\"_blank\">{$matches[0]}</a>";
		}, $message );

		// imgタグが存在すればloading="lazy"追加
		if ( preg_match_all( '/<img\s[^>]+?>/si', $message, $matches ) ) {
			foreach ( $matches[0] as $m ) {
				// 閉じタグ削除
				$m_replace = str_replace( array( ' />', '/>' ), '>', $m );

				if ( preg_match( '/\s*loading=[\'\"]?[^\'\"]*[\'\"]?/i', $m, $matches2 ) ) {
					$m_replace = str_replace( $matches2[0], ' loading="lazy"', $m_replace );
				} else {
					$m_replace = str_replace( '>', ' loading="lazy">', $m_replace );
				}
				$message = str_replace( $m, $m_replace, $message );
			}
		}

		// wpautop
		$message = wpautop( $message );
	}

	return $message;
}

/**
 * 該当ユーザーのブロックユーザーID配列取得
 */
function get_tcd_membership_messages_block_user_ids( $user_id = null, $merge_system_blocked = false ) {
	global $dp_options;

	$blocked_user_ids = array();

	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	} else {
		$user_id = intval( $user_id );
	}

	// ユーザーメタからユーザーのブロックユーザーID配列取得
	if ( $user_id && 0 < $user_id ) {
		$_blocked_user_ids = get_user_meta( $user_id, 'tcd_messages_block_user_ids', true );
		if ( $_blocked_user_ids ) {
			$_blocked_user_ids = explode( ',', $_blocked_user_ids );
		}
		if ( $_blocked_user_ids ) {
			$blocked_user_ids = $_blocked_user_ids;
		}
	}

	// システムのブロックユーザーIDを含む
	if ( $merge_system_blocked && $dp_options['membership']['messages_block_users'] ) {
		$_blocked_user_ids = explode( ',', str_replace( array( "\r\n", "\r", "\n" ), ',', $dp_options['membership']['messages_block_users'] ) );
		if ( $_blocked_user_ids ) {
			$blocked_user_ids = array_merge( $blocked_user_ids, $_blocked_user_ids );
		}
	}

	if ( $blocked_user_ids ) {
		$_blocked_user_ids = $blocked_user_ids;
		$blocked_user_ids = array();
		foreach( $_blocked_user_ids as $_blocked_user_id ) {
			$_blocked_user_id = intval( $_blocked_user_id );
			if ( 0 < $_blocked_user_id ) {
				$blocked_user_ids[] = $_blocked_user_id;
			}
		}

		if ( $blocked_user_ids ) {
			$blocked_user_ids = array_unique( $blocked_user_ids );
			sort( $blocked_user_ids );
		}
	}

	return $blocked_user_ids;
}

/**
 * 引数ユーザーID $target_user_id をブロックしているか
 */
function tcd_membership_messages_is_blocked( $target_user_id, $user_id = null, $system_blocked = false ) {
	$target_user_id = intval( $target_user_id );

	$blocked_user_ids = get_tcd_membership_messages_block_user_ids( $user_id, $system_blocked );
	if ( $blocked_user_ids && in_array( $target_user_id, $blocked_user_ids, true ) ) {
		return true;
	}

	return false;
}

/**
 * 引数ユーザーID $target_user_id にメッセージ送信可能か
 */
function tcd_membership_messages_can_send_message( $target_user_id, $user_id = null, $checkRecievedMessage = false ) {
	global $tcd_membership_vars, $wpdb;

	if ( ! $tcd_membership_vars['messages_type'] ) {
		return false;
	}

	$target_user_id = intval( $target_user_id );

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	} else {
		$user_id = intval( $user_id );
	}

	$ret = false;

	if ( ! current_user_can( 'read' ) || 0 >= $user_id || 0 >= $target_user_id || $user_id == $target_user_id ) {
		return false;
	} elseif ( 'type1' === $tcd_membership_vars['messages_type'] ) {
		$ret = true;
	} elseif ( 'type2' === $tcd_membership_vars['messages_type'] ) {
		// フォロワー（相手が自分をフォローしている）ならtrue
		if ( is_following( $user_id, $target_user_id ) ) {
			$ret = true;

		// $checkRecievedMessageフラグありなら相手からメッセージ受信したことがあればtrue
		// 自分が相手をフォローしていて相手は自分をフォローしいない状態で、相手から送られたメッセージへの返信等
		} elseif ( $checkRecievedMessage ) {
			$tablename = get_tcd_membership_tablename( 'messages' );
			$sql = "SELECT id FROM {$tablename} "
				 . "WHERE sender_user_id = {$target_user_id} AND recipient_user_id = {$user_id} AND recipient_deleted = 0 "
				 . "ORDER BY id DESC LIMIT 1";
			if ( $wpdb->get_var( $sql ) ) {
				$ret = true;
			}
		}
	}

	if ( $ret ) {
		// 自分が相手をブロックしていればfalse
		if ( tcd_membership_messages_is_blocked( $target_user_id, $user_id, false ) ) {
			$ret = false;
		}
	}

	return $ret;
}

/**
 * メッセージ作成 宛先タイプ配列取得
 */
function get_tcd_membership_messages_recipients_types() {
	global $tcd_membership_vars;

	if ( 'type1' === $tcd_membership_vars['messages_type'] ) {
		return array( 'all', 'follower', 'following' );
	} elseif ( 'type2' === $tcd_membership_vars['messages_type'] ) {
		return array( 'follower' );
	} else {
		return array();
	}
}

/**
 * メッセージ作成 宛先ユーザーID配列取得
 */
function get_tcd_membership_messages_recipients_user_ids( $args ) {
	global $dp_options, $tcd_membership_vars, $wpdb;

	$default_args = array(
		'type' => null,
		'paged' => 1,
		'user_id' => null,
		'users_per_page' => -1,
		'search' => null
	);
	$default_args = apply_filters( 'get_tcd_membership_messages_recipients_default_args', $default_args );
	$args = wp_parse_args( $args, $default_args );
	$args = apply_filters( 'tcd_membership_login_form_args', $args );

	if ( ! in_array( $args['type'], get_tcd_membership_messages_recipients_types(), true ) ) {
		return false;
	}

	if ( $args['user_id'] ) {
		$user_id = intval( $args['user_id'] );
	} else {
		$user_id = get_current_user_id();
	}

	// get_usersのincludeとexclude用変数
	$include = array();
	$exclude = array( $user_id );

	// ユーザーブロック+システムブロックのユーザーを除外
	$blocked_user_ids = get_tcd_membership_messages_block_user_ids( $user_id, false );
	if ( $blocked_user_ids ) {
		$exclude = array_merge( $exclude, $blocked_user_ids );
	}

	if ( 'follower' === $args['type'] ) {
		$tablename = get_tcd_membership_tablename( 'actions' );
		$sql = "SELECT user_id FROM {$tablename} WHERE type = 'follow' AND target_user_id = %d";
		$include = $wpdb->get_col( $wpdb->prepare( $sql, $user_id ) );

		if ( ! $include ) {
			return array();
		}

	} elseif ( 'following' === $args['type'] ) {
		$tablename = get_tcd_membership_tablename( 'actions' );
		$sql = "SELECT target_user_id FROM {$tablename} WHERE type = 'follow' AND user_id = %d";
		$include = $wpdb->get_col( $wpdb->prepare( $sql, $user_id ) );

		if ( ! $include ) {
			return array();
		}
	}

	// get_usersでincludeとexcludeが指定されている場合excludeが無視される対策（WordPress 5.8時点）
	if ( $include && $exclude ) {
		$include = array_diff( $include, $exclude );

		// $includeが空なら終了
		if ( ! $include ) {
			return array();
		}
	}

	$get_users_args = array(
		'count_total' => false,
		'exclude' => $exclude,
		'fields' => 'ID',
		'include' => $include,
		'orderby' => 'display_name',
		'order' => 'ASC'
	);

	if ( 0 < $args['users_per_page'] ) {
		$get_users_args['number'] = $args['users_per_page'];
		$get_users_args['paged'] = $args['paged'];
	}

	if ( $args['search'] ) {
		$get_users_args['search'] = '*' . trim( wp_unslash( $args['search'] ), ' *' ) . '*';
		$get_users_args['search_columns'] = array( 'display_name' );
	}

	$get_users_args = apply_filters( 'get_tcd_membership_messages_recipients_get_users_args', $get_users_args, $args );

	return get_users( $get_users_args );
}

/**
 * メッセージ作成 全宛先の宛先数・最大ページ数を取得
 */
function get_tcd_membership_messages_recipients_list_totals( $user_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$users_per_page = is_mobile() ? 4 : 8;
	$ret = array();

	foreach( get_tcd_membership_messages_recipients_types() as $list_type ) {
		$total = count( get_tcd_membership_messages_recipients_user_ids( array(
			'type' => $list_type,
			'users_per_page' => -1,
			'user_id' => $user_id
		) ) );
		$ret[$list_type]['total'] = $total;
		$ret[$list_type]['max_num_pages'] = ceil( $total / $users_per_page );
	}

	return $ret;
}

/**
 * メッセージ追加保存
 */
function tcd_membership_messages_add_message( $sender_user_id, $recipient_user_id, $message ) {
	global $wpdb;

	$sender_user_id = intval( $sender_user_id );
	$recipient_user_id = intval( $recipient_user_id );

	if ( 0 >= $sender_user_id || 0 >= $recipient_user_id || ! $message ) {
		return false;
	}

	$sender = get_user_by( 'id', $sender_user_id );
	$recipient = get_user_by( 'id', $recipient_user_id );
	$sender_deleted = 0;
	$recipient_deleted = 0;
	$timestamp = current_time( 'timestamp', true );

	// システムブロックのユーザーID配列取得
	$system_blocked_user_ids = get_tcd_membership_messages_block_user_ids( false, true );

	// 送信者がシステムブロックされていれば初期状態で受信者削除済みにする
	if ( in_array( $sender_user_id, $system_blocked_user_ids, true ) ) {
		$recipient_deleted = 9;
	}

	// 受信者がシステムブロックされていれば初期状態で受信者削除済みにする
	if ( in_array( $recipient_user_id, $system_blocked_user_ids, true ) ) {
		$recipient_deleted = 9;
	}

	// タグ削除
	$allowed_tags = apply_filters( 'tcd_membership_messages_add_message_allowed_tags', '<a><img>' );
	$message = strip_tags( $message, $allowed_tags );

	// タグ修正
	$message = force_balance_tags( $message );

	// メッセージ暗号化フラグ
	$is_message_encript = apply_filters( 'tcd_membership_messages_add_message_is_message_encript', true );
	if ( $is_message_encript ) {
		// openssl_encrypt(), openssl_decrypt()が存在すれば暗号化
		if ( function_exists( 'openssl_encrypt' ) && function_exists( 'openssl_decrypt' ) ) {
			$message = openssl_encrypt( $message, 'AES-128-ECB', sprintf( 'tcd-send-message--%d-%d--%d', $sender_user_id, $recipient_user_id, $timestamp ) );
		} else {
			$is_message_encript = false;
		}
	}

	return $wpdb->insert(
		get_tcd_membership_tablename( 'messages' ),
		array(
			'sender_user_id' => $sender_user_id,
			'sender_display_name' => $sender ? $sender->display_name : '',
			'recipient_user_id' => $recipient_user_id,
			'recipient_display_name' => $recipient ? $recipient->display_name : '',
			'message' => $message,
			'message_encrypted' => $is_message_encript ? 1 : 0,
			'sender_deleted' => $sender_deleted,
			'recipient_deleted' => $recipient_deleted,
			'sent_gmt' => date( 'Y-m-d H:i:s',$timestamp )
		),
		array(
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
		)
	);
}

/**
 * メッセージ 受信者既読にする ※未使用
 */
function tcd_membership_messages_recipient_read( $messages_db_row, $user_id = null ) {
	global $wpdb;

	$tablename = get_tcd_membership_tablename( 'messages' );

	if ( is_int( $messages_db_row ) ) {
		$sql = "SELECT * FROM {$tablename} WHERE id = %d";
		$messages_db_row = $wpdb->get_row( $wpdb->prepare( $sql, $messages_db_row ) );
	}

	if ( ! $messages_db_row || empty( $messages_db_row->id ) || empty( $messages_db_row->recipient_user_id ) || ! empty( $messages_db_row->recipient_read ) ) {
		return false;
	}

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id || 0 >= $user_id || $messages_db_row->recipient_user_id != $user_id ) {
		return false;
	}

	return $wpdb->update(
		$tablename,
		array(
			'recipient_read' => 1
		),
		array(
			'id' => $messages_db_row->id
		),
		array(
			'%d',
		),
		array(
			'%d'
		)
	);
}

/**
 * ajax ユーザーごとの最新メッセージ一覧取得
 */
function ajax_tcd_membership_messages_get_list() {
	global $dp_options, $user_id, $messages;

	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$user_id = get_current_user_id();
		$less_message_id = 0;
		$search = null;

		// 次ページ読み込み用にid未満指定がある場合
		if ( ! empty( $_REQUEST['less_message_id'] ) ) {
			$less_message_id = intval( $_REQUEST['less_message_id'] );
			if ( 0 >= $less_message_id ) {
				$json['error'] = __( 'Invalid request.', 'tcd-w' );
			}
		}

		if ( ! empty( $_REQUEST['search'] ) ) {
			$search = wp_unslash( $_REQUEST['search'] );
		}

		if ( empty( $json['error'] ) ) {
			// リスト取得
			$messages = get_tcd_membership_messages_latest_list( array(
				'user_id' => $user_id,
				'less_message_id' => $less_message_id,
				'search' => $search
			) );

			// 1ページ目の場合
			if ( ! $less_message_id ) {
				// ユーザー数・全体未読数
				$json['users_count'] = get_tcd_membership_messages_users_count( $user_id, $search );
				$json['total_unread'] = get_tcd_membership_messages_unread_number( $user_id );

				// メッセージ無し
				if ( ! $messages && $dp_options['membership']['messages_word_no_recipients'] ) {
					$json['html'] = '<div class="p-messages-users__item no_users">' . esc_html( $dp_options['membership']['messages_word_no_recipients'] ) . '</div>';
				}
			}

			if ( $messages ) {
				// 表示するmesssage.idの最小値
				$min_message_id = 0;
				foreach ( $messages as $message ) {
					if ( $min_message_id ) {
						$min_message_id = min( $min_message_id, intval( $message->id ) );
					} else {
						$min_message_id = intval( $message->id );
					}
				}
				$json['min_message_id'] = $min_message_id;

				// 次ページがあるか
				$has_next_page = get_tcd_membership_messages_latest_list( array(
					'user_id' => $user_id,
					'less_message_id' => $min_message_id,
					'search' => $search,
					'limit' => 1
				) );

				if ( $has_next_page ) {
					$json['has_next_page'] = true;
				} else {
					$json['has_next_page'] = false;
				}

				// render
				ob_start();
				get_template_part( 'membership-template/messages_list' );
				$json['html'] = ob_get_contents();
				ob_end_clean();
			} else {
				$json['has_next_page'] = false;
			}
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}

/**
 * ajax 特定ユーザーのメッセージ一覧取得
 */
function ajax_tcd_membership_messages_get_user_messages() {
	global $dp_options, $user_id, $message_user, $message_user_id, $message_user_display_name, $messages, $wpdb;

	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_REQUEST['user_id'] ) ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$user_id = get_current_user_id();
		$message_user_id = intval( $_REQUEST['user_id'] );
		$message_user = get_user_by( 'id', $message_user_id );
		$less_message_id = 0;

		// 退会等でユーザー取得できなかった場合は最終メッセージから$message_user_display_nameを取得
		if ( ! $message_user ) {
			$tablename = get_tcd_membership_tablename( 'messages' );
			$sql = "SELECT * FROM {$tablename} "
				 . "WHERE sender_user_id = {$message_user_id} OR recipient_user_id = {$message_user_id} "
				 . "ORDER BY id DESC LIMIT 1";
			$message_row = $wpdb->get_row( $sql );
			if ( $message_row ) {
				if ( $message_row->sender_user_id == $message_user_id ) {
					$message_user_display_name = $message_row->sender_display_name;
				} elseif ( $message_row->recipient_user_id == $message_user_id ) {
					$message_user_display_name = $message_row->recipient_display_name;
				}
			}
		}

		// 前ページ用にid未満指定がある場合
		if ( ! empty( $_REQUEST['less_message_id'] ) ) {
			$less_message_id = intval( $_REQUEST['less_message_id'] );
			if ( 0 >= $less_message_id ) {
				$json['error'] = __( 'Invalid request.', 'tcd-w' );
			}
		}

		if ( empty( $json['error'] ) ) {
			// 全体未読数
			$json['total_unread'] = get_tcd_membership_messages_unread_number( $user_id );

			// 該当ユーザーとの未読数
			$json['user_unread'] = get_tcd_membership_messages_unread_number( $user_id, $message_user_id );

			// メッセージ取得
			$messages = get_tcd_membership_messages_user_messages( array(
				'user_id' => $user_id,
				'target_user_id' => $message_user_id,
				'less_message_id' => $less_message_id,
				'get_all_unread' => $less_message_id === 0
			) );

			// メッセージあり
			if ( $messages ) {
				$max_message_id = 0;
				$min_message_id = 0;
				$unread_message_ids = array();

				foreach ( $messages as $message ) {
					if ( $max_message_id ) {
						$max_message_id = max( $max_message_id, intval( $message->id ) );
						$min_message_id = min( $min_message_id, intval( $message->id ) );
					} else {
						$max_message_id = intval( $message->id );
						$min_message_id = intval( $message->id );
					}

					// 受信者で未読の場合
					if ( $message->recipient_user_id == $user_id && ! $message->recipient_read) {
						$unread_message_ids[] = intval( $message->id );
					}
				}

				// 表示するmesssage.idの最大値・最小値
				$json['max_message_id'] = $max_message_id;
				$json['min_message_id'] = $min_message_id;

				// 前ページがあるか
				$has_prev_page = get_tcd_membership_messages_user_messages( array(
					'user_id' => $user_id,
					'target_user_id' => $message_user_id,
					'less_message_id' => $min_message_id,
					'limit' => 1
				) );

				if ( $has_prev_page ) {
					$json['has_prev_page'] = true;
				} else {
					$json['has_prev_page'] = false;
				}

				// render
				ob_start();

				if ( $less_message_id ) {
					get_template_part( 'membership-template/messages_detail_list' );
				} else {
					get_template_part( 'membership-template/messages_detail' );
				}

				$json['html'] = ob_get_contents();
				ob_end_clean();

				// まとめて受信者既読にする
				if ( $unread_message_ids ) {
					$tablename = get_tcd_membership_tablename( 'messages' );
					$sql = "UPDATE {$tablename} SET recipient_read = 1 "
						 . "WHERE recipient_user_id = {$user_id} AND recipient_read = 0 AND id IN(" . implode( ',', $unread_message_ids ) . ")";
					$wpdb->query( $sql );
				}
			} else {
				$json['has_prev_page'] = false;
			}
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}

/**
 * ajax 特定ユーザーの新着メッセージ一覧取得
 */
function ajax_tcd_membership_messages_get_user_messages_latest() {
	global $dp_options, $user_id, $message_user_id, $messages, $wpdb;

	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_REQUEST['user_id'], $_REQUEST['greater_message_id'] ) ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$user_id = get_current_user_id();
		$message_user_id = intval( $_REQUEST['user_id'] );
		$greater_message_id = intval( $_REQUEST['greater_message_id'] );

		if ( 0 >= $greater_message_id ) {
			$json['error'] = __( 'Invalid request.', 'tcd-w' );
		} else {
			// 全体未読数
			$json['total_unread'] = get_tcd_membership_messages_unread_number( $user_id );

			// 該当ユーザーとの未読数
			$json['user_unread'] = get_tcd_membership_messages_unread_number( $user_id, $message_user_id );

			// メッセージ取得
			$messages = get_tcd_membership_messages_user_messages( array(
				'user_id' => $user_id,
				'target_user_id' => $message_user_id,
				'greater_message_id' => $greater_message_id,
				'limit' => -1
			) );

			// メッセージあり
			if ( $messages ) {
				$max_message_id = 0;
				$unread_message_ids = array();

				foreach ( $messages as $message ) {
					$max_message_id = max( $max_message_id, intval( $message->id ) );

					// 受信者で未読の場合
					if ( $message->recipient_user_id == $user_id && ! $message->recipient_read) {
						$unread_message_ids[] = intval( $message->id );
					}
				}

				// 表示するmesssage.idの最大値
				$json['max_message_id'] = $max_message_id;

				// render
				ob_start();
				get_template_part( 'membership-template/messages_detail_list' );
				$json['html'] = ob_get_contents();
				ob_end_clean();

				// まとめて受信者既読にする
				if ( $unread_message_ids ) {
					$tablename = get_tcd_membership_tablename( 'messages' );
					$sql = "UPDATE {$tablename} SET recipient_read = 1 "
						 . "WHERE recipient_user_id = {$user_id} AND recipient_read = 0 AND id IN(" . implode( ',', $unread_message_ids ) . ")";
					$wpdb->query( $sql );
				}
			}
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}

/**
 * ajax 特定ユーザーとのメッセージ全削除
 */
function ajax_tcd_membership_messages_delete_all() {
	global $wpdb;

	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_POST['user_id'], $_POST['nonce'] ) ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$target_user_id = intval( $_POST['user_id'] );
		$user_id = get_current_user_id();

		if ( 0 >= $target_user_id ) {
			$json['error'] = __( 'Invalid request.', 'tcd-w' );
		} elseif ( ! wp_verify_nonce( $_POST['nonce'], 'tcd-messages-delete-all-' . $target_user_id ) ) {
			$json['error'] = __( 'Invalid nonce.', 'tcd-w' );
		} else {
			$tablename = get_tcd_membership_tablename( 'messages' );

			$result = $wpdb->update(
				$tablename,
				array(
					'sender_deleted' => 2,
				),
				array(
					'sender_user_id' => $user_id,
					'sender_deleted' => 0,
					'recipient_user_id' => $target_user_id
				),
				array(
					'%d',
				),
				array(
					'%d',
					'%d',
					'%d'
				)
			);

			$result2 = $wpdb->update(
				$tablename,
				array(
					'recipient_deleted' => 2,
				),
				array(
					'sender_user_id' => $target_user_id,
					'recipient_user_id' => $user_id,
					'recipient_deleted' => 0
				),
				array(
					'%d',
				),
				array(
					'%d',
					'%d',
					'%d'
				)
			);

			if ( $result || $result2 ) {
				$json['success'] = true;
			} else {
				$json['error'] = __( 'Failed to delete messages.', 'tcd-w' );
			}
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}

/**
 * ajax 単一メッセージ削除
 */
function ajax_tcd_membership_messages_delete() {
	global $wpdb;

	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_POST['message_id'], $_POST['nonce'] ) ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$message_id = intval( $_POST['message_id'] );
		$user_id = get_current_user_id();

		if ( 0 >= $message_id ) {
			$json['error'] = __( 'Invalid request.', 'tcd-w' );
		} elseif ( ! wp_verify_nonce( $_POST['nonce'], 'tcd-messages-delete-' . $message_id ) ) {
			$json['error'] = __( 'Invalid nonce.', 'tcd-w' );
		} else {
			$tablename = get_tcd_membership_tablename( 'messages' );

			$sql = "SELECT * FROM {$tablename} WHERE id = %d";
			$message_row = $wpdb->get_row( $wpdb->prepare( $sql, $message_id ) );

			if ( ! $message_row ) {
				$json['error'] = __( 'Invalid request.', 'tcd-w' );
			} else {
				$updateData = array();

				if ( $message_row->sender_user_id == $user_id ) {
					$updateData['sender_deleted'] = 1;
				} elseif ( $message_row->recipient_user_id == $user_id ) {
					$updateData['recipient_deleted'] = 1;
				}

				if ( ! $updateData ) {
					$json['error'] = __( 'Invalid request.', 'tcd-w' );
				} else {
					$result = $wpdb->update(
						$tablename,
						$updateData,
						array(
							'id' => $message_row->id
						),
						array(
							'%d',
						),
						array(
							'%d'
						)
					);

					if ( $result ) {
						$json['success'] = true;
					} else {
						$json['error'] = __( 'Failed to delete message.', 'tcd-w' );
					}
				}
			}
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}

/**
 * ajax メッセージ送信
 */
function ajax_tcd_membership_messages_send_message() {
	global $dp_options, $user_id, $message_user_id, $messages, $wpdb;

	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_POST['recipient_user_id'], $_POST['nonce'], $_POST['message'] ) ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$recipient_user_id = intval( $_POST['recipient_user_id'] );
		$recipient_user = get_user_by( 'id', $recipient_user_id );
		$user_id = get_current_user_id();
		$message = trim( wp_unslash( $_POST['message'] ) );

		// 全角スペースありのtrim
		$trim_message = preg_replace( '/\A[\x00\s]++|[\x00\s]++\z/u', '', $message );

		if ( ! $recipient_user ) {
			$json['error'] = __( 'Invalid request.', 'tcd-w' );
		} elseif ( ! wp_verify_nonce( $_POST['nonce'], 'tcd-create-message-' . $recipient_user_id ) ) {
			$json['error'] = __( 'Invalid nonce.', 'tcd-w' );
		} elseif ( ! $trim_message ) {
			$json['error'] = __( 'Message is empty.', 'tcd-w' );

		// 禁止用語チェック
		} elseif( $dp_options['membership']['use_messages_forbidden_words'] && tcd_membership_check_forbidden_words( $message ) ) {
			$json['error'] = $dp_options['membership']['messages_word_has_forbidden_words'] ? $dp_options['membership']['messages_word_has_forbidden_words'] : __( 'Message has forbidden words.', 'tcd-w' );

		// 該当ユーザーにメッセージ送信不可か（フォロワーのみ利用可能での受信メッセージチェックあり）
		} elseif ( ! tcd_membership_messages_can_send_message( $recipient_user_id, $user_id, true ) ) {
			$json['error'] = $dp_options['membership']['messages_word_cannot_send'] ? $dp_options['membership']['messages_word_cannot_send'] : __( 'You cannot send a message to this member.', 'tcd-w' );

		// メッセージ送信成功
		} elseif ( tcd_membership_messages_add_message( $user_id, $recipient_user_id, $message ) ) {
			$json['success'] = true;
			$json['message'] = $dp_options['membership']['messages_word_send_message_success'];

		// メッセージ送信失敗
		} else {
			$json['error'] = __( 'Failed to send message.', 'tcd-w' );
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}

/**
 * ajax メッセージ作成での各種宛先リスト取得
 */
function ajax_tcd_membership_messages_get_recipients() {
	global $user_ids, $paged;

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_GET['list_type'] ) || ! in_array( $_GET['list_type'], array( 'all', 'follower', 'following' ), true ) ) {
		echo '<p class="p-author__list-error">' . __( 'Invalid request.', 'tcd-w' ) . '</p>';
	} elseif ( ! current_user_can( 'read' ) ) {
		echo '<p class="p-author__list-error">' . __( 'Require login.', 'tcd-w' ) . '</p>';
	} else {
		$user_id = get_current_user_id();

		if ( isset( $_GET['paged'] ) ) {
			$paged = (int) $_GET['paged'];
		} else {
			$paged = 1;
		}
		if ( 0 >= $paged ) {
			$paged = 1;
		}

		$user_ids = get_tcd_membership_messages_recipients_user_ids( array(
			'type' => $_GET['list_type'],
			'paged' => $paged,
			'user_id' => $user_id,
			'users_per_page' => is_mobile() ? 4 : 8,
			'search' => ! empty( $_GET['search'] ) ? wp_unslash( $_GET['search'] ) : null
		) );

		get_template_part( 'membership-template/messages_create_list' );
	}

	exit;
}

/**
 * ajax ブロック一覧取得
 */
function ajax_tcd_membership_messages_get_blocked_members() {
	global $blocked_users, $paged;

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		echo '<p class="p-author__list-error">' . __( 'Invalid request.', 'tcd-w' ) . '</p>';
	} elseif ( ! current_user_can( 'read' ) ) {
		echo '<p class="p-author__list-error">' . __( 'Require login.', 'tcd-w' ) . '</p>';
	} else {
		$user_id = get_current_user_id();
		$blocked_user_ids = get_tcd_membership_messages_block_user_ids( $user_id, false );

		if ( isset( $_GET['paged'] ) ) {
			$paged = (int) $_GET['paged'];
		} else {
			$paged = 1;
		}
		if ( 0 >= $paged ) {
			$paged = 1;
		}

		if ( $blocked_user_ids ) {
			$get_users_args = array(
				'count_total' => false,
				'include' => $blocked_user_ids,
				'number' => is_mobile() ? 4 : 8,
				'orderby' => 'display_name',
				'order' => 'ASC',
				'paged' => $paged,
				'search' => ! empty( $_GET['search'] ) ? '*' . trim( wp_unslash( $_GET['search'] ), ' *' ) . '*' : null,
				'search_columns' => array( 'display_name' )
			);

			$get_users_args = apply_filters( 'ajax_tcd_membership_messages_get_blocked_members_get_users_args', $get_users_args );
			$blocked_users = get_users( $get_users_args );
		}

		get_template_part( 'membership-template/messages_blocked_members_list' );
	}

	exit;
}

/**
 * ajax ブロック追加
 */
function ajax_tcd_membership_messages_add_block() {
	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_POST['user_id'], $_POST['nonce'] ) ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$target_user_id = intval( $_POST['user_id'] );
		$target_user = get_user_by( 'id', $target_user_id );
		$user_id = get_current_user_id();

		if ( ! $target_user ) {
			$json['error'] = __( 'Invalid request.', 'tcd-w' );
		} elseif ( ! wp_verify_nonce( $_POST['nonce'], 'tcd-messages-block-' . $target_user_id ) ) {
			$json['error'] = __( 'Invalid nonce.', 'tcd-w' );

		// 既にブロック中の場合は何もしない
		} elseif ( ! tcd_membership_messages_is_blocked( $target_user_id, $user_id ) ) {
			// ユーザーメタ取得
			$blocked_user_ids = get_user_meta( $user_id, 'tcd_messages_block_user_ids', true );

			// カンマ区切りで追加
			if ( $blocked_user_ids ) {
				$blocked_user_ids .= ',' . $target_user_id;
			} else {
				$blocked_user_ids = $target_user_id;
			}

			// ユーザーメタ保存
			update_user_meta( $user_id, 'tcd_messages_block_user_ids', $blocked_user_ids );
		}

		$json['success'] = true;
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}

/**
 * ajax ブロック解除
 */
function ajax_tcd_membership_messages_remove_block() {
	$json = array();

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! isset( $_POST['user_id'], $_POST['nonce'] ) ) {
		$json['error'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error'] = __( 'Require login.', 'tcd-w' );
	} else {
		$target_user_id = intval( $_POST['user_id'] );
		$user_id = get_current_user_id();

		if ( ! wp_verify_nonce( $_POST['nonce'], 'tcd-messages-unblock-' . $target_user_id ) ) {
			$json['error'] = __( 'Invalid nonce.', 'tcd-w' );
		} else {
			// ユーザーメタ取得
			$blocked_user_ids = get_user_meta( $user_id, 'tcd_messages_block_user_ids', true );

			if ( $blocked_user_ids ) {
				// 複数削除の高速化で文字列処理
				$blocked_user_ids = ',' . $blocked_user_ids . ',';
				$blocked_user_ids = str_replace( ',' . $target_user_id . ',', ',', $blocked_user_ids );
				$blocked_user_ids = trim( $blocked_user_ids, ", \n\r\t\v\0" );

				// ユーザーメタ保存
				update_user_meta( $user_id, 'tcd_messages_block_user_ids', $blocked_user_ids );
			}

			$json['success'] = true;
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}
