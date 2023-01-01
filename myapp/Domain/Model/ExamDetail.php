<?php

namespace Myapp\Domain\Model;

class ExamDetail extends BaseModel
{

    public static $columns = [

        // 0 master
        [
            // このテーブルのID
            'id' => '%d',

            'createdAt' => '%s',
            'updatedAt' => '%s',
            'deleted' => '%d',
        ],
        [
            // 1 担当者プロフィール
            // [
            // 担当者のID
            'perfomer_id' => '%d',
            //wp_user_details' id
            // ],
            // 2 担当者プロフィール
            // [
            // 患者のID
            'patient_id' => '%d',
            //wp_user_details's id
            // ],

            // 3 生活習慣
            // [
            "symptom" => '%s',
            'symptom_change' => '%s',
            "today_meal" => '%s',
            "meal" => '%s',
            "alcohol" => '%d',
            "cigarettes" => '%d',
            'sleeping_time' => '%d',
            // ],

            // 4 診断
            // [
            "check_memo" => '%s',

            //　方向性
            "direction_1" => '%s',
            "direction_2" => '%s',
            "direction_3" => '%s',
            "direction_4" => '%s',
            "direction_5" => '%s',
            "direction_6" => '%s',
            "direction_7" => '%s',
            "direction_8" => '%s',

            // スコア
            "score_1" => '%d',
            "score_2" => '%d',
            "score_3" => '%d',
            "score_4" => '%d',
            "score_5" => '%d',
            "score_6" => '%d',
            "score_7" => '%d',
            "score_8" => '%d',
            // ],

            // 4 施術
            // [
            "perform_memo" => '%s',
            "perform_site" => '%s',
            "perform_effect" => '%s',
            // ],
        ]
    ];

    //////////////////////////////////////////////////////////
    ////////////////        DB処理        ////////////////////
    /////////////////////////////////////////////////////////
    public function insert($reqData)
    {
        global $wpdb;
        $target_data_list = ExamDetail::$columns[1];

        $sql_data = ExamDetail::getSqlData(
            ExamDetail::$columns[1],
            $reqData
        );

        
        $tabel_name = $wpdb->exam_list;

        $wpdb->insert(
            $tabel_name,
            $sql_data[0],
            $sql_data[1]
        );

        return $wpdb->insert_id;
    }

    public function update($value_pare, $where_list, $type_list)
    {
        global $wpdb;

        //todo updatedAt is now time
        $value_pare['updatedAt'] = current_time('mysql');

        return $wpdb->update(
            $wpdb->exam,
            $value_pare,
            $where_list,
            $type_list
        );
    }

    //////////////////////////////////////////////////////////
    ////////////////       コンバート        //////////////////
    /////////////////////////////////////////////////////////

    public function convertDateTime($input)
    {
        return date("Y-m-d", strtotime($input));
    }
}
