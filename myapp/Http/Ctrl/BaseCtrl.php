<?php

namespace Myapp\Http\Ctrl;
use Myapp\Domain\MyResponse;

abstract class BaseCtrl {
    public static function getResponse($_result, $_message, $_data = []) {
        return MyResponse::getResponse(
            $_result,
            $_message,
            $_data
        );
    }
}