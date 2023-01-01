<?php

/**
 * 管理画面 投稿一覧・写真一覧 報告するカラムinit
 */
function tcd_membership_report_admin_init() {
	global $dp_options, $pagenow, $typenow;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	// クイック編集対策
	$post_type = $typenow;
	$is_inline_edit = false;
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
		$is_inline_edit = true;
		if ( ! empty( $_POST['post_type'] ) ) {
			$post_type = $_POST['post_type'];
		}
	}

	if ( 'edit.php' === $pagenow || $is_inline_edit ) {
		if ( in_array( $post_type, array( 'post', '' ), true ) && $dp_options['show_report'] ) {
			add_filter( 'manage_post_posts_columns', 'tcd_membership_report_manage_columns', 11 );
			add_filter( 'manage_posts_custom_column', 'tcd_membership_report_custom_column', 10, 2 );
			add_filter( 'manage_edit-post_sortable_columns', 'tcd_membership_report_sortable_columns', 10 );
			add_filter( 'posts_clauses', 'tcd_membership_report_posts_clauses', 10, 2 );

		} elseif ( $dp_options['photo_slug'] === $post_type && $dp_options['show_report_photo'] ) {
			add_filter( 'manage_' . $dp_options['photo_slug'] . '_posts_columns', 'tcd_membership_report_manage_columns', 11 );
			add_filter( 'manage_' . $dp_options['photo_slug'] . '_posts_custom_column', 'tcd_membership_report_custom_column', 10, 2 );
			add_filter( 'manage_edit-' . $dp_options['photo_slug'] . '_sortable_columns', 'tcd_membership_report_sortable_columns', 10 );
			add_filter( 'posts_clauses', 'tcd_membership_report_posts_clauses', 10, 2 );
		}
	}
}
add_action( 'admin_init', 'tcd_membership_report_admin_init' );

/*
 * 管理画面 投稿一覧・写真一覧 報告するカラム追加
 */
function tcd_membership_report_manage_columns( $posts_columns ) {
	global $dp_options;

	if ( ! isset( $posts_columns['report_count'] ) ) {
		$posts_columns['report_count'] = sprintf( __( 'Count of "%s"', 'tcd-w' ), $dp_options['membership']['report_label'] );
		if ( mb_strpos( $posts_columns['report_count'], 'する' ) ) {
			$posts_columns['report_count'] = str_replace( 'する', '', $posts_columns['report_count'] );
		}
	}

	return $posts_columns;
}

/*
 * 管理画面 投稿一覧・写真一覧 報告するカラム表示
 */
function tcd_membership_report_custom_column( $column_name, $post_id ) {
	global $dp_options, $wpdb;

	if ( 'report_count' === $column_name ) {
		$tablename_actions = get_tcd_membership_tablename( 'actions' );

		$sql = "SELECT COUNT(act.id) FROM {$tablename_actions} AS act "
			 . "WHERE act.type = 'report' AND act.post_id = %d";

		$result = $wpdb->get_var( $wpdb->prepare( $sql, $post_id ) );

		if ( 0 < $result ) {
			echo $result;
		}
	}
}

/**
 * 管理画面 投稿一覧・写真一覧 報告するカラムソート
 */
function tcd_membership_report_sortable_columns( $sortable_columns ) {
	$sortable_columns['report_count'] = 'report_count';
	return $sortable_columns;
}

/**
 * 管理画面 投稿一覧・写真一覧 報告するカラムソートクエリー
 */
function tcd_membership_report_posts_clauses( $clauses, $wp_query ) {
	global $dp_options, $pagenow, $typenow, $wpdb;

	// 管理画面のメインクエリー以外は終了
	if ( ! is_admin() || ! $wp_query->is_main_query() ) return $clauses;

	if ( 'edit.php' === $pagenow && 'report_count' === $wp_query->get( 'orderby' ) ) {
		if (
			( in_array( $typenow, array( 'post', '' ), true ) && $dp_options['show_report'] ) ||
			( $dp_options['photo_slug'] === $typenow && $dp_options['show_report_photo'] )
		) {
			// join
			$tablename_actions = get_tcd_membership_tablename( 'actions' );
			$clauses['join'] .= " LEFT JOIN {$tablename_actions} AS tcdma_report ON (tcdma_report.post_id = {$wpdb->posts}.ID AND tcdma_report.type = 'report') ";

			// groupby
			if ( $clauses['groupby'] ) {
				if (false === strpos($clauses['groupby'], "{$wpdb->posts}.ID" ) ) {
					$clauses['groupby'] .= ", {$wpdb->posts}.ID";
				}
			} else {
				$clauses['groupby'] = "{$wpdb->posts}.ID";
			}

			// orderby
			if ( 'desc' === strtolower( $wp_query->get( 'order' ) ) ) {
				$order = 'DESC';
			} else {
				$order = 'ASC';
			}
			if ( ! $clauses['orderby'] ) {
				$clauses['orderby'] = "{$wpdb->posts}.ID $order";
			}
			$clauses['orderby'] = "COUNT(tcdma_report.id) $order, " . $clauses['orderby'];
		}
	}

	return $clauses;
}
