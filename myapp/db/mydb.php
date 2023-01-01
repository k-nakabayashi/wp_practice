<?php
require_once( ABSPATH . WPINC . '/wp-db.php' );
class my_wpdb extends wpdb {

    public function __construct($dbuser, $dbpassword, $dbname, $dbhost)
    {
       parent::__construct($dbuser, $dbpassword, $dbname, $dbhost);
    }
    // データベーステーブルの定義を変更(hogeを追加)
    public function addMyTables()
    {
        array_push($this->tables, "user_details");
        array_push($this->tables, "diseases");
        array_push($this->tables, "exam_list");
    }
   
}