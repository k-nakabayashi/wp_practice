<?php

namespace Myapp\Domain\Model;

# TODO: 使用するDBをlaravel_dbに変えたい

class UserDetail extends BaseModel
{
    
    public static $columns = [

        // TODO: Min Maxを設定する

        // 0 master
        [
            // このテーブルのID
            'id' => '%d', //wp_user_details' id

            // 別テーブルのID
            'member_id' => '%d', //wp_swpm_members_tbl's member_id
            'user_id' => '%d', //wp_users's id

            //担当者のID
            'perfomer_id' => '%d', //wp_user_details' id

            'createdAt' => '%s',
            'updatedAt' => '%s',
            'deleted' => '%d',
        ],
        // 1 基礎プロフィール
        [
            'birthday' => '%s',
            'gender' => '%d',
            'occupation' => '%s',
            'partner' => '%d',
            'sports_history' => '%s',
        ],

        // 2 身体プロフィール
        [
            'body_height' => '%f',
            'body_weight' => '%f',
            'dominant_hand' => '%d',
            'max_blood_preasure' => '%f',
            'min_blood_preasure' => '%f',
            'blood_type' => '%s',
            'right_sight' => '%f',
            'left_sight' => '%f',
            'teeth_bite' => '%d',
        ],

        // 3 生活習慣
        [
            'drinking' => '%d',
            'smoking' => '%d',
            'sleeping_time' => '%d',
        ],

        // 4 痛みや症状
        [
            'symptom_site' => '%s',
            'killing_movement' => '%s',
            'onset_time' => '%s',
            'casuse' => '%s',
            'allergy' => '%s',
        ],

        // 5 出生時の状況
        [

            'birthing_time' => '%d',
            'birth_weight' => '%f',
            'birth_icu_day' => '%d',

            'birth' => '%s',
            'birth_memo' => '%s',
        ],

        // 6 既往歴
        [
            'medical_history' => '%s',
        ],
        // 7 該当項目
        [
            // 'applicable_other' => '%s',
            'applicable_disease' => '%s',
        ]
    ];

    public function getColumnNameListForDisplay()
    {
        return [

            // 1 基礎プロフィール
            'birthday',
            'gender',
            'occupation',
            'partner',
            'sports_history',

            // 2 身体プロフィール
            'body_height',
            'body_weight',
            'dominant_hand',
            'max_blood_preasure',
            'min_blood_preasure',
            'blood_type',
            'right_sight',
            'left_sight',
            'teeth_bite',

            // 3 生活習慣
            'drinking',
            'smoking',
            'sleeping_time',

            // 4 痛みや症状
            'symptom_site',
            'killing_movement',
            'onset_time',
            'casuse',
            'allergy',

            // 5 出生時の状況
            'birthing_time',
            'birth_weight',
            'birth_icu_day',

            'birth',
            'birth_memo',
            // 6 既往歴
            'medical_history',
            // 7 該当項目
            'applicable_disease',
        ];
    }

    //////////////////////////////////////////////////////////
    ////////////////        DB処理        ////////////////////
    /////////////////////////////////////////////////////////
    public function create($user_id, $member_id)
    {
        global $wpdb;
        $wpdb->insert(
            $wpdb->user_details,
            ['user_id' => $user_id, 'member_id' => $member_id],
            ['%d', '%d']
        );

        return $wpdb->insert_id;
    }

    public function readByUserId($user_id, $str_taget_columns = "*")
    {
        
        global $wpdb;
        $sql = $wpdb->prepare(
            "
                SELECT 
                    $str_taget_columns
                FROM 
                    $wpdb->user_details 
                WHERE 
                    user_id = %d
            ",
            $user_id,
        );

        //todo　wp自体の初期化時点で、adminがuser_detailで作成されるようにする。多分、無理。membeshipはプラグインだから
        $records = $wpdb->get_results($sql, ARRAY_A);
        if (count($records) < 1) {
            return [];
        }

        return $records[0];
    }


    public function read2ByUserId($performer_id, $patient_id)
    {
        global $wpdb;
        $tabel_name = $wpdb->prefix . "swpm_members_tbl";

        $sql = $wpdb->prepare(
            "
                SELECT 
                    CONCAT(spm.first_name, ' ' , spm.last_name) as name,
                    spm.email,
                    user_detail.*
                FROM
                    $tabel_name as spm
                INNER JOIN
                    $wpdb->user_details as user_detail
                    ON 
                        spm.member_id = user_detail.member_id
                WHERE 
                    perfomer_id = %d AND
                    user_detail.id = %d
            ",
            $performer_id,
            $patient_id
        );

        //todo　wp自体の初期化時点で、adminがuser_detailで作成されるようにする。多分、無理。membeshipはプラグインだから
        $records = $wpdb->get_results($sql, ARRAY_A);
        if (count($records) < 1) {
            return [];
        }

        return $records[0];
    }

    public function update($value_pare, $where_list, $type_list)
    {
        
        global $wpdb;
        //todo updatedAt is now time
        $value_pare['updatedAt'] = current_time('mysql');

        return $wpdb->update(
            $wpdb->user_details,
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

    public function convertColumn($user_detail)
    {

        // 基礎プロフィール
        $user_detail["birthday"] = $this->convertDateTime($user_detail["birthday"]);
        $user_detail = $this->setGender($user_detail);
        $user_detail = $this->setPartner($user_detail);
        $user_detail = $this->setAge($user_detail);


        // 身体プロフィール
        $user_detail = $this->setDominantHand($user_detail);
        $user_detail = $this->setTeethbite($user_detail);

        // 生活習慣
        $user_detail = $this->setDrinking($user_detail);
        $user_detail = $this->setSmoking($user_detail);

        // 痛みや症状
        $user_detail["onset_time"] = $this->convertDateTime($user_detail["onset_time"]);

        return $user_detail;
    }

    private function setAge($user_detail)
    {

        $birthday = $user_detail["birthday"];

        $now = date("Y-m-d");
        $c = (int)date('Ymd', strtotime($now));
        $b = (int)date('Ymd', strtotime($birthday));
        $age = (int)(($c - $b) / 10000) . "歳";

        $user_detail["age"] = $age;

        return $user_detail;
    }
    private function setGender($user_detail)
    {

        $target = "";

        switch ($user_detail["gender"]) {
            case 0:
                $target = "男";
                break;
            case 1:
                $target = "女";
                break;
        }

        $user_detail["gender"] = $target;

        return $user_detail;
    }

    private function setPartner($user_detail)
    {

        $target = "";
        switch ($user_detail["partner"]) {
            case 0:
                $target = "独身";
                break;
            case 1:
                $target = "結婚";
                break;
            case 1:
                $target = "その他";
                break;
            default:
                break;
        }

        $user_detail["partner"] = $target;

        return $user_detail;
    }


    private function setDominantHand($user_detail)
    {

        $target = "";

        switch ($user_detail["dominant_hand"]) {
            case 0:
                $target = "右";
                break;
            case 1:
                $target = "左";
                break;
        }

        $user_detail["dominant_hand"] = $target;

        return $user_detail;
    }

    private function setTeethbite($user_detail)
    {

        $target = "";

        switch ($user_detail["teeth_bite"]) {
            case 0:
                $target = "揃っている";
                break;
            case 1:
                $target = "揃っていない";
                break;
            case 2:
                $target = "わからない";
                break;
        }

        $user_detail["teeth_bite"] = $target;

        return $user_detail;
    }

    private function setDrinking($user_detail)
    {

        $target = "";

        switch ($user_detail["drinking"]) {
            case 0:
                $target = "飲まない";
                break;
            case 1:
                $target = "時々";
                break;
            case 2:
                $target = "毎日";
                break;
        }

        $user_detail["drinking"] = $target;

        return $user_detail;
    }

    private function setSmoking($user_detail)
    {

        $target = "";

        switch ($user_detail["smoking"]) {
            case 0:
                $target = "吸わない";
                break;
            case 1:
                $target = "時々";
                break;
            case 2:
                $target = "毎日";
                break;
        }

        $user_detail["smoking"] = $target;

        return $user_detail;
    }
}


class BaseModel
{
    
    public static function getSqlData($target_columns, $data)
    {

        $value_list = [];
        foreach ($target_columns as $key => $value) {
            $value_list[$key] =
                $data[$key];
        }
        return [
            $value_list,
            array_values($target_columns),
        ];
    }

    public static function getTargetData($data, $target_column_number)
    {
        $target_data = [];

        $target_columns = array_keys(self::$columns[$target_column_number]);
        foreach ($target_columns as $column_name) {
            $target_data[$column_name] = $data[$column_name];
        }

        return $target_data;
    }
}
