<?php
namespace Myapp\Http\Ctrl;
use Myapp\Domain\Model\DiseasesModel;

class ConstListCtrl extends BaseCtrl {

    public function insertIntialData() {
        (new DiseasesModel())->insertIntialData();
    }
}