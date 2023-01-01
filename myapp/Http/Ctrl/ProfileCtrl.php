<?php

namespace Myapp\Http\Ctrl;
use Myapp\Domain\Model\UserDetail;

class ProfileCtrl extends BaseCtrl{

    private $userDetail;
    
    public function __construct()
    {
        $this->userDetail = new UserDetail();
    }

    function create ($reqData) {

        $user_id = $reqData['user_id'];
        $member_id = $reqData['member_id'];
        
        $result = $this->userDetail->create($user_id, $member_id);

        return parent::getResponse(
            true,
            'プロフィールを追加しました。',
            $result
        );
    }

    function readByUseryId ($reqData) {
        $user_id = $reqData['user_id'];
    }



    public function updateMyPerfomerData ($reqData) {
        $user_id = $reqData['user_id'];

        $this->userDetail->update(
            ['perfomer_id' => $reqData['perfomer_id']],
            ['user_id' => $user_id],
            ['%d']
        );

        // //更新した身体プロフィールを返す?
        return parent::getResponse(
            true,
            '更新しました。'
        );
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////     共通処理
    ////////////////////////////////////////////////////////////////////////////////
    private function update ($target, $reqData) {
        
        $key_list = array_keys($reqData);

        foreach ($key_list AS $key) {
            if ($reqData[$key] == "") {
                $reqData[$key] = null;
            }
        }

        $user_id = $reqData['user_id'];

        $target_columns = UserDetail::$columns[$target];

        $sql_data = UserDetail::getSqlData(
            $target_columns, 
            $reqData
        );

        $this->userDetail->update(
            $sql_data[0],
            ['user_id' => $user_id],
            $sql_data[1]
        );
    }


    ////////////////////////////////////////////////////////////////////////////////
    ////////////////     ユーザー詳細の更新
    ////////////////////////////////////////////////////////////////////////////////

    public function updateBaseData ($reqData) {

        if ($reqData['birthday'] != "") {
            //日付を変換する
            $reqData['birthday'] = date("Y-m-d H:i:s", strtotime($reqData['birthday']));
        }

        $this->update(1, $reqData);

        return parent::getResponse(
            true,
            '更新しました。',
        );
    }

    public function updateBodyData ($reqData) {
       
        $this->update(2, $reqData);

        return parent::getResponse(
            true,
            '更新しました。',
        );
    }

    public function updateLifestyleData ($reqData) {

        $this->update(3, $reqData);

        return parent::getResponse(
            true,
            '更新しました。',
        );
    }

    public function updateSymptomData ($reqData) {

        if ($reqData['onset_time'] != "") {
            //日付を変換する
            $reqData['onset_time'] = date("Y-m-d H:i:s", strtotime($reqData['onset_time']));
        }

        $this->update(4, $reqData);

        return parent::getResponse(
            true,
            '更新しました。',
        );
    }
    
    public function updateBirthData ($reqData) {

        $this->update(5, $reqData);

        return parent::getResponse(
            true,
            '更新しました。',
        );
    }

    public function updateHistoryData ($reqData) {

        $this->update(6, $reqData);

        return parent::getResponse(
            true,
            '更新しました。',
        );
    }

    public function updateApplicableItemData ($reqData) {

        $this->update(7, $reqData);

        return parent::getResponse(
            true,
            '更新しました。',
        );
    }

}