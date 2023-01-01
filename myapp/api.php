<?php
use Myapp\Router\MyRouter;
use Myapp\Http\Ctrl;


///////////////////////////////////////////////////////////
////////////////        初期化        ///////////////////
/////////////////////////////////////////////////////////

//todo 初期設定で起動する
// (new Ctrl\ConstListCtrl())->insertIntialData();

///////////////////////////////////////////////////////////
///////////////    プロフィール関連       ///////////////////
/////////////////////////////////////////////////////////
function api_updateMyPerfomerData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateMyPerfomerData');
}
MyRouter::addApiWithLogin("api_updateMyPerfomerData");

function api_updateBaseData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateBaseData');
}
MyRouter::addApiWithLogin("api_updateBaseData");

function api_updateBodyData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateBodyData');
}
MyRouter::addApiWithLogin("api_updateBodyData");

function api_updateLifestyleData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateLifestyleData');
}
MyRouter::addApiWithLogin("api_updateLifestyleData");

function api_updateSymptomData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateSymptomData');
}
MyRouter::addApiWithLogin("api_updateSymptomData");

function api_updateBirthData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateBirthData');
}
MyRouter::addApiWithLogin("api_updateBirthData");

function api_updateHistoryData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateHistoryData');
}
MyRouter::addApiWithLogin("api_updateHistoryData");

function api_updateApplicableItemData()
{
    MyRouter::post("auth", new Ctrl\ProfileCtrl(), 'updateApplicableItemData');
}
MyRouter::addApiWithLogin("api_updateApplicableItemData");

///////////////////////////////////////////////////////////
///////////////    診察診断 関連       ///////////////////
/////////////////////////////////////////////////////////
function api_insertExamData()
{
    MyRouter::post("auth", new Ctrl\ExamCtrl(), 'insertExamData');
}
MyRouter::addApiWithLogin("api_insertExamData");