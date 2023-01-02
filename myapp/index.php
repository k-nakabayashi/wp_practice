<?php

// // NOTE: 同時リクエストによる登録に耐えられないのでは？
// $latest_member_id = null;
// $spMember = new Myapp\Domain\Model\SpwmMember(SwpmAuth::get_instance());

// // require_once "utils.php";
require_once "hooks.php";

//画面系
// require_once "shortcodes.php"; // ssr
// require_once "api.php"; // json
 

function debug_log($message, $display_timestamp=true) {

    // WP_CONTENT_DIR = /var/www/html/wp-content
    $log_message = "";

    if ($display_timestamp == true) {
        $log_message = sprintf("%s:%s\n", date_i18n('Y-m-d H:i:s'), $message);
    } else {
        $log_message = $message;
    }

    error_log($log_message, 3, WP_CONTENT_DIR . WP_DEBUG_LOG_PATH);
}

function debug_log2($dict_args, $display_timestamp=true) {

    $log_message = "\n";

    if (is_string($dict_args)) {

        $log_message = $dict_args;
    
    } else {
    
        foreach ($dict_args as $key => $data) {
            if (is_array($data)) {
                $log_message .= $key . "\n";
                foreach ($data as $key2 => $data2) {
                    $log_message .= "** {$key2} : {$data2}";
                    $log_message .= "\n";
                }
            } else {
                $log_message .= "{$key} : {$data}";
                $log_message .= "\n";
            }
        }

    }
    debug_log($log_message, $display_timestamp);
}

function debug_log3($dict_args, $display_timestamp=true) {
    $dict_args = json_decode(json_encode($dict_args), true);
    debug_log2($dict_args, $display_timestamp);
}


function set_post_data($formdata) {

    $hash_tags_from_req = $formdata["hash_tags"];
    $post_content = "";
    if ( !empty( $formdata['post_content'] ) ) {
        $post_content = $formdata['post_content'];
    } 

    # ハッシュタグを抽出する。
    $hash_tags_from_req = preg_replace("/( |　)/", ",", $hash_tags_from_req);
    $hash_tags_from_req = preg_replace("/(、)/", ",", $hash_tags_from_req);
    $tmp_hash_tags = explode(",", $hash_tags_from_req);

    // preg_match_all('/#[^\s　]+/', $hash_tags_from_req, $tmp);
    $hash_tags_str = "";
    $hash_tags = array();

    # ハッシュタグをチェックする。
    if (count($tmp_hash_tags) != 0) {

        # 詰め替える
        foreach($tmp_hash_tags as $hash_tag) {

            if ($hash_tag == "") {
                continue;
            }

            // テキストの無害化
            $hash_tag = tcd_membership_sanitize_content($hash_tag);

            if (tcd_membership_check_forbidden_words($hash_tag)) {
                // throw new Exception('不正な文字が使用されいます。');
                return false;
            }
            
            array_push($hash_tags, $hash_tag);
        }
        

        $hash_tags = array_unique($hash_tags);
        $hash_tags_str = convert_has_tags_to_str($hash_tags);

        $search_text = $formdata['post_title'] . $post_content . $hash_tags_str;

        # 各自のデータをセットする。
        $formdata['search_text'] = $search_text;
        $formdata['hash_tags'] = $hash_tags;

    } else {

        # 最低限の各自のデータをセットする。
        $formdata['search_text'] = $formdata['post_title'] . $post_content;
        $formdata['hash_tags'] = array();
    }

    // debug_log("set_post_data : hash_tags ==");
    // debug_log2($formdata['hash_tags']);

    return $formdata;
}

function convert_has_tags_to_str($hash_tags, $preg_replace=true) {

    if (is_string($hash_tags)) {
        return $hash_tags;
    }
    if (is_array($hash_tags)) {
        if (count($hash_tags) == 0) {
            return "";
        }
        return "\n".implode(" ", $hash_tags);
    }

    return false;
}

function withdraw_search_text_and_hash_tags($postarr) {
    // wp_insert_postで使用する
    $search_text = $postarr["search_text"];
    $hash_tags = convert_has_tags_to_str($postarr["hash_tags"]);

    return [ $search_text, $hash_tags ];
}
/**
 * WP_Postオブジェクトにフォーム配列を代入して返す
 */
function tcd_membership_set_post_data_from2_array( $tcd_membership_post, $formdata ) {

	foreach ( get_tcd_membership_form2_input_keys( $tcd_membership_post->post_type ) as $key ) {
		if ( isset( $formdata[$key] ) ) {
			$tcd_membership_post->$key = $formdata[$key];
		} else {
            //　未設定の場合
            if ($key == 'hash_tags') {
                $tcd_membership_post->$key = array();
            } else {
                $tcd_membership_post->$key = '';
            }
		}
	}

	return $tcd_membership_post;
}

/**
 * フォーム維持するinputキー配列を返す
 */
function get_tcd_membership_form2_input_keys( $post_type = 'post' ) {
	global $dp_options;

	$input_keys = array();

	# HACK : モンキー：　search_textとhash_tagsを追加した。
	if ( 'post' === $post_type ) {
		$input_keys = array(
			'post_title',
			'post_status',
			'category',
			'main_image',
			'search_text',
			'hash_tags'
		);

		for ( $i = 0; $i < 10; $i++ ) {
			$si = 0 < $i ? $i : '';
			$input_keys[] = 'headline' . $si;
			$input_keys[] = 'description' . $si;
			$input_keys[] = 'image' . $si;
		}

	} elseif ( $post_type === $dp_options['photo_slug'] ) {
		$input_keys = array(
			'post_title',
			'post_content',
			'post_status',
			'category',
			'main_image',
			'textalign',
			'search_text',
			'hash_tags'
		);
	}

	return $input_keys;
}


/**
 * プレビューフォーム出力
 */
function the_tcd_membership_preview_form2() {
	global $tcd_membership_vars, $tcd_membership_post;
?>
			<form class="p-membership-form js-membership-form--normal c-Form-Confirm" action="" method="post">
				<div class="p-membership-form__button">
					<button class="p-button p-rounded-button p-submit-button a-Btn" name="to_complete" type="submit" value="1"><?php _e( 'Save', 'tcd-w' ); ?></button>
					<button class="p-membership-form__back-button a-Btn" name="to_input" type="submit" value="1"><?php _e( 'Back', 'tcd-w' ); ?></button>
					<input type="hidden" name="post_id" value="<?php echo esc_attr( $tcd_membership_post->ID ); ?>">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'tcd-membership-' . $tcd_membership_vars['memberpage_type'] . '-' . $tcd_membership_post->ID ) ); ?>">
					
<?php
	// post値維持用のinput[type="hidden"]出力
	echo "\t\t\t\t\t";
	foreach ( get_tcd_membership_form2_input_keys( $tcd_membership_post->post_type ) as $key ) :
        if ($key == "hash_tags") {
            $value = esc_attr(convert_has_tags_to_str($tcd_membership_post->$key));
        } else {
           $value = esc_attr( $tcd_membership_post->$key );
        }
		echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . $value . '">';
	endforeach;
	echo "\n";
?>
				</div>
			</form>
<?php
}
