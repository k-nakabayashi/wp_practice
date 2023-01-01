<?php
namespace Myapp\Domain;

class MyResponse {

    public static function getResponse($_result, $_message, $_data = []) {
        return [
            'result' => $_result,
            'message' => $_message,
            'data' => $_data
        ];
    }
}

// vendor/bin/phpcs myapp/domain/model