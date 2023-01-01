<?php
namespace Myapp\Views\Patient;

use Myapp\Domain\Model\UserDetail;
use Myapp\Domain\Model\SpwmMember;
use Myapp\Domain\Model\DiseasesModel;

class PatientList {
    private $twig;
    private $user;
    private $user_detail;
    private $ajaxUrl;
    public $spM;

    public function __construct($_twig, $spMember)
    {   
        $this->twig = $_twig;
        $this->user = wp_get_current_user();
        $this->user_detail = (new UserDetail())->readByUserId($this->user->ID);
        $this->ajaxUrl = admin_url( 'admin-ajax.php');
        $this->spM = $spMember;
    }

    public function __invoke()
{

        return 
            '<div>'
            .$this->setScript()

            .$this->patient_list()
            .'</div>';
    }

    private function setScript()
{
        return $this->twig->render('FormScript.html', [
            'ajaxUrl' => $this->ajaxUrl
        ] );
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////     以下、UIパーツ
    ////////////////////////////////////////////////////////////////////////////////
    private function patient_list()
{
        
        $user_list = $this->spM->getMyPatientList($this->user_detail['id']);

        return $this->twig->render('Patient_List.html', [
            'HOST' => "http://localhost/wp06/web/",
            'prefix_form_id' => __FUNCTION__,
            'sub_title' => "",
            'user_detail' => $this->user_detail,
            'user_list' => $user_list,
            'action' => 'api_test'
        ] );
    }

}