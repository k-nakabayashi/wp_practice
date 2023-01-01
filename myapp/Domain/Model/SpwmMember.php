<?php

namespace MyApp\Domain\Model;
// use MyApp\Domain\Model\BaseModel;

class SpwmMember extends BaseModel {
    
    public $swpmAuth;

    private $baseSQL;
    private $table_name;

    public function __construct($_swpmAuth)
    {   
        $this->swpmAuth = $_swpmAuth;
        $this->baseSQL = $this->getBaseSQL();
        global $wpdb;
        $this->tabel_name = $wpdb->prefix . "swpm_members_tbl";
    }

    public function readByMemberLevelWithoutMe ($level, $my_id) {
        
        //todo join user_details
        global $wpdb;
        $sql = $wpdb->prepare(
            "
                SELECT 
                    spm.member_id as member_id, 
                    user_detail.id as user_id,
                    CONCAT(spm.first_name, ' ' , spm.last_name) as name
                $this->baseSQL
                WHERE 
                    spm.membership_level = %d AND
                    user_detail.id != %d
            ", 
            $level,
            $my_id
        );
        
        return $wpdb->get_results($sql, ARRAY_A);
    }

    public function getMyPatientList($my_id) {
        
        //todo join user_details
        global $wpdb;
        $sql = $wpdb->prepare(
            "
                SELECT 
                    spm.member_id as member_id, 
                    user_detail.id as user_id,
                    CONCAT(spm.first_name, ' ' , spm.last_name) as name
                $this->baseSQL
                WHERE 
                    user_detail.perfomer_id = %d AND
                    user_detail.id != %d
            ", 
            $my_id,
            $my_id
        );
        
        return $wpdb->get_results($sql, ARRAY_A);
    }


    public function getMyLevel() {
        //2が一般
        //3以上が施術者
        return $this->swpmAuth->userData->membership_level;
    }

    //////////////////////////////////////////////////////////
    ////////////////        共通処理        ///////////////////
    /////////////////////////////////////////////////////////

    private function getBaseSQL() {
        
        global $wpdb;
        $tabel_name = $wpdb->prefix . "swpm_members_tbl";
        $sub_table_name = $wpdb->user_details;

        return "
                FROM 
                    $tabel_name as spm
                INNER JOIN
                    $sub_table_name as user_detail
                    ON 
                        spm.member_id = user_detail.member_id
            ";

    }

}

# HACK: importエラーが出る。ひとまずここにコピペ。後ほど消す。
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
