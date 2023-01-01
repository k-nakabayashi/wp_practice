<?php
namespace Myapp\Views\Patient\Detail;

use Myapp\Domain\Model\UserDetail;
use Myapp\Domain\Model\SpwmMember;
use Myapp\Domain\Model\DiseasesModel;
use Myapp\Domain\Model\BirthSituation;

class PatientDetail {
    private $twig;
    private $me;
    private $user_detail_model;
    private $user_detail;
    private $ajaxUrl;
    public $spM;

    private $patient_id;

    public function __construct($_twig, $spMember)
    {   
        $this->twig = $_twig;
        $this->me = wp_get_current_user();
        $this->user_detail_model = new UserDetail();
        $this->user_detail = $this->user_detail_model->readByUserId($this->me->ID);
        $this->ajaxUrl = admin_url( 'admin-ajax.php');
        $this->spM = $spMember;
    }

    public function __invoke()
{

        return $this->patient_detail();
    }

    public function setPatientId($id)
{
        $this->patient_id = $id;

        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////     以下、UIパーツ
    ////////////////////////////////////////////////////////////////////////////////
    private function patient_detail()
{
        $performer_id = $this->user_detail['id'];

        // 患者情報
        $patient_detail = $this->user_detail_model->read2ByUserId($performer_id, $this->patient_id);

        if(count($patient_detail) < 1)
{
            return "<p>不正なアクセスです。</p>";
        }

        $patient_detail = setBoxList($patient_detail, 'birth');
        $patient_detail = setBoxList($patient_detail, 'applicable_disease');

        // 疾患マスタの一覧
        $diseases_list = DiseasesModel::$initial_data;

        return $this->twig->render('Patient_Detail.html', [
            
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "",
            // HACK: 認証で使うかも
            // 'user_detail' => $this->user_detail,
            'patient_detail' => $this->user_detail_model->convertColumn($patient_detail),
            'column_list' => $this->user_detail_model->getColumnNameListForDisplay(),

            //出産状況の該当項目
            'birth_situation_list' => BirthSituation::$columns,
            'birth_situation_count' => count(BirthSituation::$columns),

            //出産状況の該当項目
            'diseases_name_index' => DiseasesModel::$NAME_INDEX,
            'diseases_list' => $diseases_list,
            'diseases_count' => count($diseases_list),

            'action' => 'api_test'
        ] );
    }

}