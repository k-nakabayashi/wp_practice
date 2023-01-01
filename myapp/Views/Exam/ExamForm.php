<?php
namespace Myapp\Views\Exam;
use Myapp\Domain\Model\UserDetail;
use Myapp\Domain\Model\SpwmMember;
use Myapp\Domain\Model\DiseasesModel;
use Myapp\Domain\Model\ShugiSkill;


class ExamForm {
    private $twig;
    private $user;
    private $user_detail;
    private $ajaxUrl;
    private $spM;

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

        // 施術者の情報　※不要？
        // 被術者の情報
        // 診断
        // 施術の内容

        return 
            '<div class="m-Exam">'
            .$this->setScript()
            .$this->patient_form()
            .$this->setExamlScript()
            .'</div>';
    }

    private function setScript()
{
        return $this->twig->render('FormScript.html', [
            'ajaxUrl' => $this->ajaxUrl
        ] );
    }

    private function setExamlScript()
{
        return $this->twig->render('FormExam_ExamScript.html');
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////     以下、UIパーツ
    ////////////////////////////////////////////////////////////////////////////////
    private function patient_form()
{

        $patients_list = $this->spM->readByMemberLevelWithoutMe(2, $this->user_detail['id']);
        $skill_name_list = ShugiSkill::$skill_name_List;

        return $this->twig->render('FormExam__Questions.html', [
            'prefix_form_id' => __FUNCTION__,
            'user_detail' => $this->user_detail,
            'user_list' => json_encode($patients_list, true),
            'skill_name_list' => $skill_name_list,
            'action' => 'api_insertExamData'
        ] );
    }


    ////////////////////////////////////////////////////////////////////////////////
    ////////////////     詳細処理
    ////////////////////////////////////////////////////////////////////////////////

    private function setBoxList($user_detail, $target_name)
{
                
        if ($user_detail[$target_name] != null)
{
            
            $temp_birth_list = explode(",",  $user_detail[$target_name]);

            $user_detail[$target_name] = [];
            
            for ($i = 0; $i < count($temp_birth_list); ++ $i)
{
                if ($temp_birth_list[$i] != "")
{
                    $user_detail[$target_name][$i] = true;
                }
            }
        }
        return $user_detail;
    }

}