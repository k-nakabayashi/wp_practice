<?php

namespace MyApp\Domain\Model;

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
