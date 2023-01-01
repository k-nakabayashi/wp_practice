<?php
namespace Myapp\Views\Profile;

use Myapp\Domain\Model\UserDetail;
use Myapp\Domain\Model\SpwmMember;
use Myapp\Domain\Model\DiseasesModel;
use Myapp\Domain\Model\BirthSituation;

class MyProfileForm {
    private $twig;
    private $user;
    private $user_detail_model;
    private $user_detail;
    private $ajaxUrl;
    public $spM;

    public function __construct($_twig, $spMember)
    {   
        $this->twig = $_twig;
        $this->user = wp_get_current_user();
        $this->user_detail_model = new UserDetail();
        $this->user_detail =  $this->user_detail_model->readByUserId($this->user->ID);
        $this->ajaxUrl = admin_url( 'admin-ajax.php');
        $this->spM = $spMember;
    }

    public function __invoke() {

        return 
            '<div class="m-MyProfile">'
            .$this->setScript()
            .$this->peformer_form()
            .$this->base_form()
            .$this->body_form()
            .$this->lifestyle_form()
            .$this->symptoms_form()
            .$this->birth_situation_form()
            .$this->medical_history_form()
            .$this->applicable_items_form()
            .'</div>';
    }

    private function setScript() {
        return $this->twig->render('FormScript.html', [
            'ajaxUrl' => $this->ajaxUrl
        ] );
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////     以下、UIパーツ
    ////////////////////////////////////////////////////////////////////////////////
    private function peformer_form() {
        
        // 施術者(membership_level == 3)の一覧を取得する
        $perfomer_list = $this->spM->readByMemberLevelWithoutMe(3, $this->user_detail['id']);

        return $this->twig->render('FormProfile__MyPerfomer.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "担当者情報",
            'user_detail' => $this->user_detail,
            'user_list' => json_encode($perfomer_list, true),
            'action' => 'api_updateMyPerfomerData'
        ] );
    }

    private function base_form() {
    
        $this->user_detail["birthday"] = $this->user_detail_model->convertDateTime($this->user_detail["birthday"]);

        return $this->twig->render('FormProfile__Base.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "基礎プロフィール",
            'user_detail' => $this->user_detail,
            'action' => 'api_updateBaseData'
        ] );
    }

    private function body_form() {

        return $this->twig->render('FormProfile__Body.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "身体プロフィール",
            'user_detail' => $this->user_detail,
            'action' => 'api_updateBodyData'
        ] );
    }

    private function lifestyle_form() {

        return $this->twig->render('FormProfile__Lifestyle.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "生活習慣",
            'user_detail' => $this->user_detail,
            'action' => 'api_updateLifestyleData'
        ] );

    }

    private function symptoms_form() {
    
        $this->user_detail["onset_time"] = $this->user_detail_model->convertDateTime($this->user_detail["onset_time"]);
        
        return $this->twig->render('FormProfile__Symptom.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "痛みや症状",
            'user_detail' => $this->user_detail,
            'action' => 'api_updateSymptomData'
        ] );
    }

    private function birth_situation_form() {
        
        $this->user_detail = setBoxList($this->user_detail, 'birth');

        return $this->twig->render('FormProfile__Birth.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "出生時の状況",
            'user_detail' => $this->user_detail,
            'birth_situation_list' => BirthSituation::$columns,
            'birth_situation_count' => count(BirthSituation::$columns),
            'action' => 'api_updateBirthData'
        ] );
    }

    private function medical_history_form() {

        return $this->twig->render('FormProfile__History.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "既往歴",
            'user_detail' => $this->user_detail,
            'action' => 'api_updateHistoryData'
        ] );
    }

    private function applicable_items_form() {
        
        $this->user_detail = setBoxList($this->user_detail, 'applicable_disease');

        $disease_list = (new DiseasesModel())->read();

        // var_dump(($this->user_detail['applicable_disease']));

        return $this->twig->render('FormProfile__ApplicableItems.html', [
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "該当項目",
            'user_detail' => $this->user_detail,
            'disease_list' => $disease_list,
            'disease_count' => count($disease_list) - 1,
            'action' => 'api_updateApplicableItemData'
        ] );
    }


}
