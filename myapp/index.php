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