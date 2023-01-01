<?php

/**
 * TCD Membership scripts
 */
function tcd_membership_wp_enqueue_scripts() {
	global $tcd_membership_vars, $_wp_additional_image_sizes;

	wp_enqueue_script( 'tcd-membership', get_template_directory_uri() . '/js/membership.js', array( 'jquery', 'jquery-form' ), version_num(), true );

	$localize = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'ajax_error_message' => __( 'Error was occurred. Please retry again.', 'tcd-w' ),
		'login_url' => get_tcd_membership_memberpage_url( 'login' ),
		'registration_url' => get_tcd_membership_memberpage_url( 'registration' )
	);

	// アカウント削除メッセージ自動表示
	if ( isset( $_GET['account_deleted'] ) && ! current_user_can( 'read' ) ) {
		$localize['auto_modal_alert_message'] = __( 'Account deleted.', 'tcd-w' );
	}

	// ブラウザ離脱時のメッセージ
	if ( ! empty( $tcd_membership_vars['confirm_page_leave'] ) ) {
		$localize['confirm_page_leave'] = $tcd_membership_vars['confirm_page_leave'];
	}
	if ( ! empty( $tcd_membership_vars['browser_back_alert_messege'] ) ) {
		$localize['browser_back_alert_messege'] = $tcd_membership_vars['browser_back_alert_messege'];
	}

	wp_localize_script( 'tcd-membership', 'TCD_MEMBERSHIP', $localize );

	// 画像アップロード
	if ( is_tcd_membership_image_upload_type() ) {
		wp_enqueue_script( 'tcd-membership-upload', get_template_directory_uri() . '/js/membership-upload.js', array( 'jquery' ), version_num(), true );
		$localize_upload = array(
			'not_image_file' => __( 'Please select the image file.', 'tcd-w' ),
			'drop_one_file' => __( 'Please drop only one file.', 'tcd-w' ),
			'drop_not_image_file' => __( 'Please drop the image file.', 'tcd-w' )
		);

		if ( in_array( $tcd_membership_vars['memberpage_type'], array( 'add_blog', 'edit_blog' ) ) ) {
			$localize_upload['memberpage_type'] = $tcd_membership_vars['memberpage_type'];
			$localize_upload['image_size'] = $_wp_additional_image_sizes['size3'];
		} elseif ( in_array( $tcd_membership_vars['memberpage_type'], array( 'add_photo', 'edit_photo' ) ) ) {
			$localize_upload['memberpage_type'] = $tcd_membership_vars['memberpage_type'];
			$localize_upload['image_size_photo1'] = $_wp_additional_image_sizes['size-photo1'];
			$localize_upload['image_size_photo2'] = $_wp_additional_image_sizes['size-photo2'];
			$localize_upload['image_size_photo3'] = $_wp_additional_image_sizes['size-photo3'];
		}

		wp_localize_script( 'tcd-membership', 'TCD_MEMBERSHIP_UPLOAD', $localize_upload );
	}
}
add_action( 'wp_enqueue_scripts', 'tcd_membership_wp_enqueue_scripts' );

/**
 * ゲスト権限チェック メンバーページはtcd_membership_memberpage_wp()内でチェックされる
 */
function tcd_membership_wp_guest_permission() {
	global $dp_options;

	if ( is_admin() ) return;

	if ( ! current_user_can( 'read' ) && ! is_front_page() ) {
		$require_login = false;

		if ( is_page() ) {
			$_wp_page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			if ( 'page__authors.php' === $_wp_page_template && is_tcd_membership_guest_require_login( 'author', 'archive' ) ) {
				$require_login = true;
			}
		} elseif ( is_singular( 'post' ) ) {
			$require_login = is_tcd_membership_guest_require_login( 'blog', 'single' );
		} elseif ( is_singular( $dp_options['photo_slug'] ) ) {
			$require_login = is_tcd_membership_guest_require_login( 'photo', 'single' );
		} elseif ( is_post_type_archive( $dp_options['photo_slug'] ) || is_tax( $dp_options['photo_category_slug'] ) ) {
			$require_login = is_tcd_membership_guest_require_login( 'photo', 'archive' );
		} elseif ( is_post_type_archive( $dp_options['information_slug'] ) ) {
			$require_login = is_tcd_membership_guest_require_login( 'information', 'archive' );
		} elseif ( is_author() ) {
			$require_login = is_tcd_membership_guest_require_login( 'author', 'single' );
		} elseif ( ! is_search() ) {
			$require_login = is_tcd_membership_guest_require_login( 'blog', 'archive' );
		}

		if ( $require_login ) {
			$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$redirect = add_query_arg( 'redirect_to', rawurlencode( rawurldecode( $current_url ) ), get_tcd_membership_memberpage_url( 'login' ) );
			wp_safe_redirect( $redirect );
			exit;
		}
	}
}
add_action( 'wp', 'tcd_membership_wp_guest_permission', 1 );

/**
 * 検索時に一覧表示権限がある投稿タイプのみに限定する
 */
function tcd_membership_pre_get_posts_guest_permission( $wp_query ) {
	global $dp_options;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	// フロントエンドメインクエリーで検索時
	if ( ! is_admin() && $wp_query->is_main_query() && $wp_query->is_search() ) {
		// 検索対象投稿タイプ
		$search_target_post_types = $wp_query->get( 'post_type' );

		// 投稿タイプ未指定時はテーマオプション設定から
		if ( ! $search_target_post_types && $dp_options['search_target_post_types'] && is_array( $dp_options['search_target_post_types'] ) ) {
			$search_target_post_types = $dp_options['search_target_post_types'];
		}

		// ログインしていなければ各投稿タイプのゲスト権限に応じて検索対象投稿タイプから削除
		if ( ! current_user_can( 'read' ) && $search_target_post_types && is_array( $search_target_post_types ) ) {
			$key = array_search( 'post', $search_target_post_types );
			if ( false !== $key && 'type1' === $dp_options['membership']['guest_permission_blog'] ) {
				unset( $search_target_post_types[ $key ] );
			}

			$key = array_search( $dp_options['photo_slug'], $search_target_post_types );
			if ( false !== $key && 'type1' === $dp_options['membership']['guest_permission_photo'] ) {
				unset( $search_target_post_types[ $key ] );
			}

			$key = array_search( $dp_options['information_slug'], $search_target_post_types );
			if ( false !== $key && 'type1' === $dp_options['membership']['guest_permission_information'] ) {
				unset( $search_target_post_types[ $key ] );
			}
		}

		// 検索対象投稿タイプが空の場合はダミーの投稿タイプを指定
		if ( ! $search_target_post_types ) {
			$search_target_post_types = array( '_empty_search_target_post_types_' );
		}

		$wp_query->set( 'post_type', $search_target_post_types );
	}
}
add_action( 'pre_get_posts', 'tcd_membership_pre_get_posts_guest_permission', 11 );

/**
 * 引数に応じてゲスト権限しログインが必要ならjs-require-loinを出力
 */
function the_tcd_membership_guest_require_login_class( $type = 'blog', $archive = false, $before_space = '', $after_space = '' ) {
	echo get_tcd_membership_guest_require_login_class( $type, $archive, $before_space, $after_space );
}

/**
 * 引数に応じてゲスト権限しログインが必要ならjs-require-loinを返す
 */
function get_tcd_membership_guest_require_login_class( $type = 'blog', $archive = false, $before_space = '', $after_space = '' ) {
	if ( is_tcd_membership_guest_require_login( $type, $archive ) ) {
		return $before_space . 'js-require-login' . $after_space;
	}
}

/**
 * 引数に応じてゲスト権限しログインが必要かどうか
 */
function is_tcd_membership_guest_require_login( $type = 'blog', $archive = false ) {
	global $dp_options;

	if ( current_user_can( 'read' ) ) return false;

	if ( in_array( $type, array( 'information', $dp_options['information_slug'] ) ) ) {
		return ( 'type1' === $dp_options['membership']['guest_permission_information'] );
	}

	if ( is_string( $archive ) && 'archive' != $archive ) {
		$archive = false;
	}

	$guest_permission = null;

	if ( in_array( $type, array( 'blog', 'post' ) ) ) {
		$guest_permission = $dp_options['membership']['guest_permission_blog'];
	} elseif ( in_array( $type, array( 'photo', $dp_options['photo_slug'] ) ) ) {
		$guest_permission = $dp_options['membership']['guest_permission_photo'];
	} elseif ( in_array( $type, array( 'user', 'author', 'profile' ) ) ) {
		$guest_permission = $dp_options['membership']['guest_permission_profile'];
	}

	if ( $guest_permission ) {
		if ( ( ! $archive && 'type3' !== $guest_permission ) || ( $archive && 'type1' === $guest_permission ) ) {
			return true;
		}
	}

	return false;
}

/**
 * 投稿者記事数・フォロー数・最大ページ数を取得
 */
function get_author_list_totals( $target_user_id ) {
	$list_types = array( 'post', 'photo', 'follower', 'following' );
	$posts_per_page = is_mobile() ? 4 : 8;
	$ret = array();

	foreach( $list_types as $list_type ) {
		$total = get_author_list_total( $target_user_id, $list_type );
		$ret[$list_type]['total'] = $total;
		$ret[$list_type]['max_num_pages'] = ceil( $total / $posts_per_page );
	}

	return $ret;
}

/**
 * 投稿者記事数・フォロー数を取得
 */
function get_author_list_total( $target_user_id, $list_type ) {
	global $dp_options, $wpdb;

	if ( 'post' === $list_type ) {
		if (get_current_user_id() == $target_user_id ) {
			$where_post_status = "AND post_status IN ('publish', 'private', 'pending', 'draft')";
		} else {
			$where_post_status = "AND post_status = 'publish'";
		}
		$sql = "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d {$where_post_status}";
		return $wpdb->get_var( $wpdb->prepare( $sql, 'post', $target_user_id ) );
	} elseif ( 'photo' === $list_type ) {
		if (get_current_user_id() == $target_user_id ) {
			$where_post_status = "AND post_status IN ('publish', 'private', 'pending', 'draft')";
		} else {
			$where_post_status = "AND post_status = 'publish'";
		}
		$sql = "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d {$where_post_status}";
		return $wpdb->get_var( $wpdb->prepare( $sql, $dp_options['photo_slug'], $target_user_id ) );
	} elseif ( 'follower' === $list_type ) {
		$tablename = get_tcd_membership_tablename( 'actions' );
		$sql = "SELECT COUNT(id) FROM {$tablename} WHERE type = 'follow' AND target_user_id = %d";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $target_user_id ) );
		if ( $result ) {
			return (int) $result;
		}
	} elseif ( 'following' === $list_type ) {
		$tablename = get_tcd_membership_tablename( 'actions' );
		$sql = "SELECT COUNT(id) FROM {$tablename} WHERE type = 'follow' AND user_id = %d";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $target_user_id ) );
		if ( $result ) {
			return (int) $result;
		}
	}

	return 0;
}

/**
 * ajaxでの投稿者記事・フォローリスト取得
 */
function ajax_get_author_list() {
	global $dp_options, $wp_query, $paged, $wpdb, $user_ids;

	if ( ! isset( $_GET['list_type'], $_GET['user_id'] ) ) {
		echo '<p class="p-author__list-error">' . __( 'Invalid request.', 'tcd-w' ) . '</p>';
	} else {
		$target_user_id = (int) $_GET['user_id'];
		if ( 0 < $target_user_id ) {
			$target_user = get_user_by( 'id', $target_user_id );
		}
		if ( empty( $target_user ) ) {
			echo '<p class="p-author__list-error">' . __( 'Invalid request.', 'tcd-w' ) . '</p>';
		} else {
			if ( isset( $_GET['paged'] ) ) {
				$paged = (int) $_GET['paged'];
			} else {
				$paged = 1;
			}
			if ( 0 >= $paged ) {
				$paged = 1;
			}

			$posts_per_page = is_mobile() ? 4 : 8;
			$offset = ( $paged - 1 ) * $posts_per_page;

			if ( 'post' == $_GET['list_type'] ) {
				$wp_query->query( array(
					'author' => $target_user_id,
					'ignore_sticky_posts' => 1,
					'paged' => $paged,
					'post_type' => 'post',
					'post_status' => get_current_user_id() === $target_user_id ? array( 'publish', 'private', 'pending', 'draft' ) : 'publish',
					'posts_per_page' => $posts_per_page
				) );
				get_template_part( 'template-parts/author-list' );

			} elseif ( 'photo' == $_GET['list_type'] ) {
				$wp_query->query( array(
					'author' => $target_user_id,
					'ignore_sticky_posts' => 1,
					'paged' => $paged,
					'post_type' => $dp_options['photo_slug'],
					'post_status' => get_current_user_id() === $target_user_id ? array( 'publish', 'private', 'pending', 'draft' ) : 'publish',
					'posts_per_page' => $posts_per_page
				) );
				get_template_part( 'template-parts/author-list' );

			} elseif ( 'follower' == $_GET['list_type'] ) {
				$tablename = get_tcd_membership_tablename( 'actions' );
				$sql = "SELECT user_id FROM {$tablename} WHERE type = 'follow' AND target_user_id = %d ORDER BY id DESC LIMIT {$offset},{$posts_per_page}";
				$user_ids = $wpdb->get_col( $wpdb->prepare( $sql, $target_user_id ) );
				get_template_part( 'template-parts/author-list' );

			} elseif ( 'following' == $_GET['list_type'] ) {
				$tablename = get_tcd_membership_tablename( 'actions' );
				$sql = "SELECT target_user_id FROM {$tablename} WHERE type = 'follow' AND user_id = %d ORDER BY id DESC LIMIT {$offset},{$posts_per_page}";
				$user_ids = $wpdb->get_col( $wpdb->prepare( $sql, $target_user_id ) );
				get_template_part( 'template-parts/author-list' );

			} else {
				echo '<p class="p-author__list-error">' . __( 'Invalid request.', 'tcd-w' ) . '</p>';
			}
		}
	}

	exit;
}
add_action( 'wp_ajax_get_author_list', 'ajax_get_author_list' );
add_action( 'wp_ajax_nopriv_get_author_list', 'ajax_get_author_list' );

/**
 * ajaxでの報告
 */
function ajax_report_post() {
	global $dp_options;

	$json = array(
		'success' => false
	);

	if ( ! isset( $_POST['post_id'], $_POST['report_comment'] ) ) {
		$json['error_message'] = __( 'Invalid request.', 'tcd-w' );
	} elseif ( ! current_user_can( 'read' ) ) {
		$json['error_message'] = __( 'Require login.', 'tcd-w' );
	} else {
		$user_id = get_current_user_id();
		$post_id = (int) $_POST['post_id'];

		if ( 0 < $post_id ) {
			$target_post = get_post( $post_id );
			if ( empty( $target_post->post_status ) || 'publish' !== $target_post->post_status ) {
				$target_post = null;
			} elseif ( 'post' === $target_post->post_type ) {
				$post_type_label = $dp_options['blog_label'] ? $dp_options['blog_label'] : __( 'Blog', 'tcd-w' );
			} elseif ( $dp_options['photo_slug'] === $target_post->post_type ) {
				$post_type_label = $dp_options['photo_label'] ? $dp_options['photo_label'] : __( 'Photo', 'tcd-w' );
			}
		}

		if ( empty( $target_post ) ) {
			$json['error_message'] = __( 'Invalid request.', 'tcd-w' );
		} elseif ( ! in_array( $target_post->post_type, array( 'post', $dp_options['photo_slug'] ) ) ) {
			$json['error_message'] = sprintf( __( 'Disable report in %s.', 'tcd-w' ), $target_post->post_type );
		} elseif ( 'post' === $target_post->post_type && ! $dp_options['show_report'] ) {
			$json['error_message'] = sprintf( __( 'Disable report in %s.', 'tcd-w' ), $post_type_label );
		} elseif ( $dp_options['photo_slug'] === $target_post->post_type && ! $dp_options['show_report_photo'] ) {
			$json['error_message'] = sprintf( __( 'Disable report in %s.', 'tcd-w' ), $post_type_label );
		} else {
			// 報告済みの場合
			if ( get_tcd_membership_action( 'report', $user_id, 0, $post_id ) ) {
				$json['error_message'] = __( 'You already reported this article.', 'tcd-w' );
			} else {
				// 報告DB保存
				$action_id = insert_tcd_membership_action( 'report', $user_id, 0, $post_id );
				if ( $action_id ) {
					// メタ保存
					update_tcd_membership_action_meta( $action_id, 'report_comment', wp_unslash( $_POST['report_comment'] ) );

					// メール送信
					$user = wp_get_current_user();
					$mailto = $dp_options['membership']['mail_report_to'];
					if ( ! $mailto ) {
						$mailto = get_bloginfo( 'admin_email' );
					}
					$replaces = array(
						'[user_display_name]' => $user->display_name,
						'[user_email]' => $user->user_email,
						'[post_id]' => $post_id,
						'[post_url]' => get_permalink( $target_post ),
						'[post_type]' => $target_post->post_type,
						'[post_type_label]' => $post_type_label,
						'[report_comment]' => wp_unslash( $_POST['report_comment'] )
					);
					if ( tcd_membership_mail( 'report', $mailto, $replaces ) ) {
						$json['success'] = true;
					} else {
						$json['error_message'] = __( 'Failed to send mail.', 'tcd-w' );
						delete_tcd_membership_action_by_id( $action_id );
					}
				} else {
					$json['error_message'] = __( 'Failed to save the database.', 'tcd-w' );
				}
			}
		}
	}

	// JSON出力
	wp_send_json( $json );
	exit;
}
add_action( 'wp_ajax_report_post', 'ajax_report_post' );

/**
 * モーダル出力
 */
function render_tcd_membership_modal() {
	global $dp_options;

	if ( ! current_user_can( 'read' ) ):
		// ログインモーダル
		if ( 'login' !== get_tcd_membership_memberpage_type() ):

?>
<div id="js-modal-login" class="p-modal p-modal--login">
	<div class="p-modal__contents">
		<div class="p-modal__contents__inner">
<?php
		tcd_membership_login_form( array(
			'form_id' => 'js-modal-login-form',
			'modal' => true
		) );
?>
		</div>
		<button class="p-modal__close">&#xe91a;</button>
	</div>
</div>
<?php
		endif;

		// 会員登録モーダル
		if ( 'registration' !== get_tcd_membership_memberpage_type() ):
?>
<div id="js-modal-registration" class="p-modal p-modal--registration">
	<div class="p-modal__contents">
		<div class="p-modal__contents__inner">

<?php
		tcd_membership_registration_form( array(
			'form_id' => 'js-modal-registration-form',
			'modal' => true
		) );
?>
		</div>
		<button class="p-modal__close">&#xe91a;</button>
	</div>
</div>
<?php
		endif;
	else :
		// 削除確認モーダル
?>
<div id="js-modal-delete-confirm" class="p-modal p-modal--delete-confirm">
	<div class="p-modal__contents">
		<div class="p-modal__contents__inner">
			<form id="js-modal-delete-confirm-form" class="p-membership-form">
				<h2 class="p-member-page-headline"><?php _e( 'Really delete?', 'tcd-w' ); ?></h2>
				<div class="p-membership-form__button u-hidden-xs">
					<button class="p-button p-button--s p-button-gray p-rounded-button js-cancel-button"><?php _e( 'Cancel', 'tcd-w' ); ?></button>
					<button class="p-button p-button--s p-rounded-button js-submit-button"><?php _e( 'Yes, delete.', 'tcd-w' ); ?></button>
				</div>
				<div class="p-membership-form__button u-visible-xs">
					<button class="p-button p-button--s p-rounded-button js-submit-button"><?php _e( 'Yes, delete.', 'tcd-w' ); ?></button>
					<br>
					<button class="p-button p-button--s p-button-gray p-rounded-button js-cancel-button"><?php _e( 'Cancel', 'tcd-w' ); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
	endif;

	// 報告するモーダル
	if ( ( is_singular( 'post' ) && $dp_options['show_report'] ) || ( is_singular( $dp_options['photo_slug'] ) && $dp_options['show_report_photo'] ) ) :
?>
<div id="js-modal-report" class="p-modal p-modal--report">
	<div class="p-modal__contents">
		<div class="p-modal__contents__inner">
			<form id="js-modal-report-form" class="p-membership-form">
				<div class="p-membership-form__input">
					<h2 class="p-member-page-headline--color"><?php echo esc_html( $dp_options['membership']['report_label'] ? $dp_options['membership']['report_label'] : __( 'Report to administrator', 'tcd-w' ) ); ?></h2>
					<div class="p-modal__body p-body">
<?php
		if ( $dp_options['membership']['report_desc'] ) :
?>
						<div class="p-membership-form__desc"><?php echo wpautop( $dp_options['membership']['report_desc'] ); ?></div>
<?php
		endif;
?>
						<input type="hidden" name="post_id" value="<?php the_ID(); ?>">
						<input type="hidden" name="post_url" value="<?php the_permalink(); ?>" data-confirm-label="<?php esc_attr_e( 'URL', 'tcd-w' ); ?>">
						<textarea name="report_comment" cols="30" rows="6" placeholder="<?php esc_attr_e( 'Comment', 'tcd-w' ); ?>" data-confirm-label="<?php esc_attr_e( 'Comment', 'tcd-w' ); ?>"></textarea>
						<div class="p-membership-form__button">
							<button class="p-button p-rounded-button" type="submit"><?php _e( 'Next', 'tcd-w' ); ?></button>
						</div>
					</div>
				</div>
				<div class="p-membership-form__confirm">
					<h2 class="p-member-page-headline--color"><?php _e( 'Input contents confirmation', 'tcd-w' ); ?></h2>
					<div class="p-membership-form__body p-modal__body p-body"></div>
					<div class="p-membership-form__button">
						<button class="p-button p-rounded-button js-submit-button"><?php echo esc_html( $dp_options['membership']['report_button_label'] ? $dp_options['membership']['report_button_label'] : __( 'Report to administrator', 'tcd-w' ) ); ?></button>
						<button class="p-membership-form__back-button js-back-button"><?php _e( 'Back', 'tcd-w' ); ?></button>
					</div>
				</div>
				<div class="p-membership-form__complete">
					<h2 class="p-member-page-headline--color"><?php echo esc_html( $dp_options['membership']['report_complete_headline'] ? $dp_options['membership']['report_complete_headline'] : __( 'Report completed', 'tcd-w' ) ); ?></h2>

<?php
		if ( $dp_options['membership']['report_complete_desc'] ) :
?>
					<div class="p-membership-form__body p-modal__body p-bodyp-membership-form__desc"><?php echo wpautop( $dp_options['membership']['report_complete_desc'] ); ?></div>
<?php
		endif;
?>
				</div>
			</form>
		</div>
		<button class="p-modal__close">&#xe91a;</button>
	</div>
</div>
<?php
	endif;
}
add_action( 'wp_footer', 'render_tcd_membership_modal' );

/**
 * 未ログインの場合にREST APIで使える機能を制限
 * 参考 https://nendeb.com/541
 */
function tcd_membership_rest_pre_dispatch( $result, $wp_rest_server, $request ){
	$namespaces = $request->get_route();

	if ( current_user_can( 'read' ) ) {
		return $result;

	// /oembed/1.0
	} elseif ( strpos( $namespaces, 'oembed/' ) === 1 ){
		return $result;

	// /jetpack/v4
	} elseif( strpos( $namespaces, 'jetpack/' ) === 1 ){
		return $result;

	// contact form 7 (Ver4.7～)
	} elseif( strpos( $namespaces, 'contact-form-7/' ) === 1 ){
		return $result;
	}

	return new WP_Error( 'rest_disabled', __( 'The REST API on this site has been disabled.', 'tcd-w' ), array( 'status' => rest_authorization_required_code() ) );
}
add_filter( 'rest_pre_dispatch', 'tcd_membership_rest_pre_dispatch', 10, 3 );

/**
 * Embed/oEmbed制限 初期判別
 */
function tcd_membership_disable_autoembed_wp() {
	global $dp_options, $tcd_disable_autoembed_vars, $tcd_membership_post;

	// @TODO バックエンド記事編集でのGutenbergやTinyMCE時のEmbed/oEmbed展開も制限したい
	if (is_admin()) return;

	$tcd_disable_autoembed_vars = array(
		'disable_internal' => false,
		'disable_external' => false,
		'queried_object' => get_queried_object()
	);

	if ( is_singular( 'post' ) || is_tcd_membership_preview_blog() ) {
		if ( $dp_options['membership']['disable_oembed_internal_blog'] ) {
			$tcd_disable_autoembed_vars['disable_internal'] = true;
		}
		if ( $dp_options['membership']['disable_oembed_external_blog'] ) {
			$tcd_disable_autoembed_vars['disable_external'] = true;
		}

		if ( is_tcd_membership_preview_blog() && $tcd_membership_post ) {
			$tcd_disable_autoembed_vars['queried_object'] = $tcd_membership_post;
		}

	} elseif ( is_singular( $dp_options['photo_slug'] ) || is_tcd_membership_preview_photo() ) {
		if ( $dp_options['membership']['disable_oembed_internal_photo'] ) {
			$tcd_disable_autoembed_vars['disable_internal'] = true;
		}
		if ( $dp_options['membership']['disable_oembed_external_photo'] ) {
			$tcd_disable_autoembed_vars['disable_external'] = true;
		}

		if ( is_tcd_membership_preview_photo() && $tcd_membership_post ) {
			$tcd_disable_autoembed_vars['queried_object'] = $tcd_membership_post;
		}
	}

	if ( $tcd_disable_autoembed_vars['disable_internal'] || $tcd_disable_autoembed_vars['disable_external'] ) {
		add_action( 'the_post', 'tcd_membership_disable_autoembed_the_post' );
	}
}
add_action( 'wp', 'tcd_membership_disable_autoembed_wp', 11 );

/**
 * Embed/oEmbed制限 the_postアクション
 */
function tcd_membership_disable_autoembed_the_post() {
	global $wp_embed, $post, $tcd_disable_autoembed_vars;

	// メイン記事の場合
	if ( isset( $tcd_disable_autoembed_vars['queried_object']->ID ) && $post->ID === $tcd_disable_autoembed_vars['queried_object']->ID ) {
		// 内部サイト・外部サイト無効の場合、WP_Embed::autoembedフィルターがあれば一旦削除
		if ( $tcd_disable_autoembed_vars['disable_internal'] && $tcd_disable_autoembed_vars['disable_external'] ) {
			// WP_Embed::autoembedフィルターがあれば削除
			$priority = has_filter( 'the_content', array( $wp_embed, 'autoembed' ) );
			if ( false !== $priority ) {
				remove_filter( 'the_content', array( $wp_embed, 'autoembed' ), $priority );
				$tcd_disable_autoembed_vars['remove_filter_autoembed'] = $priority;
			}

		// 内部サイト無効の場合
		} elseif ( $tcd_disable_autoembed_vars['disable_internal'] ) {
			// wp_embed_register_handler登録
			$regex = '#^' . str_replace( array( 'http\://', 'https\://' ), 'https?\://', preg_quote( home_url(), '#' ) ) .'/?[^\s]*#i';
			wp_embed_register_handler(
				'tcd_internal',
				$regex,
				'tcd_membership_disable_autoembed_callback',
				8
			);
			$tcd_disable_autoembed_vars['embed_register_handler_internal'] = true;

		// 外部サイト無効の場合
		} elseif ( $tcd_disable_autoembed_vars['disable_external'] ) {
			// wp_embed_register_handler登録
			$regex = '#^(?!' . str_replace(array('http\://', 'https\://'), 'https?\://', preg_quote( home_url(), '#' ) ) . '/?)[^\s]*#i';
			wp_embed_register_handler(
				'tcd_external',
				$regex,
				'tcd_membership_disable_autoembed_callback',
				9
			);
			$tcd_disable_autoembed_vars['embed_register_handler_external'] = true;
		}

	// メイン記事以外の場合
	} else {
		// 削除されたWP_Embed::autoembedフィルターがあれば戻す
		if ( isset( $tcd_disable_autoembed_vars['remove_filter_autoembed'] ) ) {
			add_filter( 'the_content', array( $wp_embed, 'autoembed' ), $tcd_disable_autoembed_vars['remove_filter_autoembed'] );
			unset( $tcd_disable_autoembed_vars['remove_filter_autoembed'] );
		}

		// 内部サイト無効用wp_embed_register_handlerがあれば削除する
		if ( isset( $tcd_disable_autoembed_vars['embed_register_handler_internal'] ) ) {
			wp_embed_unregister_handler( 'tcd_internal', 8 );
			unset( $tcd_disable_autoembed_vars['embed_register_handler_internal'] );
		}

		// 外部サイト無効用wp_embed_register_handlerがあれば削除する
		if ( isset( $tcd_disable_autoembed_vars['embed_register_handler_external'] ) ) {
			wp_embed_unregister_handler( 'tcd_external', 9 );
			unset( $tcd_disable_autoembed_vars['embed_register_handler_external'] );
		}
	}
}

/**
 * Embed/oEmbed制限 wp_embed_register_handlerのコールバック関数
 */
function tcd_membership_disable_autoembed_callback( $matches, $attr, $url, $rawattr ) {
	return '<a href="' . esc_attr( $matches[0] ) . '" target="_blank">' . esc_html( $matches[0] ) . '</a>';
}
