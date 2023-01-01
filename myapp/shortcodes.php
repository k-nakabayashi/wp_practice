<?php

use Myapp\Views\Profile\MyProfileForm;
use Myapp\Views\Exam\ExamForm;
use Myapp\Views\Patient\PatientList;
use Myapp\Views\Patient\Detail\PatientDetail;

use Myapp\Domain\Model;


function my_profile_form()
{
    if (is_user_logged_in()) {

        $loader = new Twig_Loader_Filesystem(__DIR__."/views/profile");
        $twig = new Twig_Environment($loader);

        global $spMember;
            
        return (new MyProfileForm($twig, $spMember))();

    } else {
        return "<p>不正なアクセスです。</p>";
    }
}

add_shortcode('my_profile_form', "my_profile_form");


function my_exam_form() {

    if (is_user_logged_in()) {

        global $spMember;

        if (current_user_can( 'manage_options' ) ) {
            return "<p>管理者</p>";
        }
        
        if (!current_user_can( 'manage_options' ) ) {
            //管理者以外の場合の処理
            if ($spMember->getMyLevel() <= 2) {
                return "<p>不正なアクセスです。</p>";
            }
        }
 

        $loader = new Twig_Loader_Filesystem(__DIR__."/views/exam");
        $twig = new Twig_Environment($loader);


        return (new ExamForm($twig, $spMember))();

    } else {
        return "<p>不正なアクセスです。</p>";
    }
}

add_shortcode('my_exam_form', "my_exam_form");

function my_patient_list() {

    if (is_user_logged_in()) {

        global $spMember;

        if (current_user_can( 'manage_options' ) ) {
            return "<p>管理者</p>";
        }
        
        if (!current_user_can( 'manage_options' ) ) {
            //管理者以外の場合の処理
            if ($spMember->getMyLevel() <= 2) {
                return "<p>不正なアクセスです。</p>";
            }
        }

        $loader = new Twig_Loader_Filesystem(__DIR__."/views/patient");
        $twig = new Twig_Environment($loader);

        return (new PatientList($twig, $spMember))();

    } else {
        return "<p>不正なアクセスです。</p>";
    }
}

add_shortcode('my_patient_list', "my_patient_list");


function my_patient_detail() {

    if (is_user_logged_in()) {

        global $spMember;

        if (current_user_can( 'manage_options' ) ) {
            return "<p>管理者</p>";
        }
        
        if (!current_user_can( 'manage_options' ) ) {
            //管理者以外の場合の処理
            if ($spMember->getMyLevel() <= 2) {
                return "<p>不正なアクセスです。</p>";
            }
        }

        if(!isset($_GET['id'])) {
            return "<p>不正なアクセスです。</p>";
        }

        //todo 患者の担当者がログイン中のユーザーと同一人物かを判定する。

        $loader = new Twig_Loader_Filesystem(__DIR__."/views/patient/detail");
        $twig = new Twig_Environment($loader);

        $patientDetail = new PatientDetail($twig, $spMember);
        $patientDetail->setPatientId($_GET['id']);
        return $patientDetail();

    } else {
        return "<p>不正なアクセスです。</p>";
    }
}

add_shortcode('my_patient_detail', "my_patient_detail");


