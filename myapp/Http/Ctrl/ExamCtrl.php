<?php

namespace Myapp\Http\Ctrl;
use Myapp\Domain\Model\ExamDetail;

class ExamCtrl extends BaseCtrl{

    private $examDetail;
    
    public function __construct()
    {
        $this->examDetail = new ExamDetail();
    }

    function insertExamData($reqData) {

        // $perfomer_id = $reqData['perfomer_id'];
        $result = $this->examDetail->insert($reqData);
        
        return parent::getResponse(
            true,
            'OK',
            $result
        );
    }

 


}