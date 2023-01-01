<?php

namespace Myapp\Router;
use Exception;

///////////////////////////////////////////////////////////
////////////////      Routerの設定      ///////////////////
/////////////////////////////////////////////////////////
class MyRouter {

    ///////////////////////////////////////////////////////////
    ////////////////      httpの処理　      ///////////////////
    /////////////////////////////////////////////////////////
    static function isPOST()
{
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }
    static function isGET()
{
        return $_SERVER["REQUEST_METHOD"] == "GET";
    }


    static  function response($data)
{
        echo json_encode(["result" => true, "data" => $data, 'message' => $data['message']]);
    }

    static function formatRequestData()
{
        $data = null;

        if (self::isPOST())
{
            $data = $_POST;
        } else if (self::isGET())
{
            $data = $_GET;
        } else {
            return null;
        }
        
        $formatTargetKey = [
            "user_id",
            // "member_id",
        ];

        // foreach ($data as $key => $value)
{
        //     if (array_key_exists($key, $formatTargetKey))
{
        //         $data[$key] = (int) $value;
        //     }
        // }

        return $data;
    }


    ///////////////////////////////////////////////////////////
    ////////////////      Hook追加　　      ///////////////////
    /////////////////////////////////////////////////////////

    static function addApiWithLogin($action_name)
{
        add_action( 'wp_ajax_'.$action_name, $action_name);
    }
    
    static function addApi($action_name)
{
        add_action('wp_ajax_'.$action_name, $action_name);
        add_action('wp_ajax_nopriv_'.$action_name, $action_name);
    }

    /////////////////////////////////////////////////////////
    ////////////////      メイン処理　　      /////////////////
    /////////////////////////////////////////////////////////

    static function exec($middle_method, $ctrl, $action)
{
        $message = null;
        $response = null;

        try {
            $reqData = self::formatRequestData();

            $message = forward_static_call_array([__CLASS__, $middle_method], [$reqData]);
            if ( $message != null)
{
                throw new Exception();
            }

            $response = call_user_func([$ctrl, $action], $reqData);

        } catch (Exception $e)
{
            if ( $message === null)
{
                $response = ["result" => false, "message" => "不明なエラー発生。"];
            } else {
                $response = ["result" => false, "message" => $message];
            }
           
        } 

        // return $response = ["result" => false, "message" => $reqData];
        return $response;
    }

    /////////////////////////////////////////////////////////
    ////////////////      詳細処理　　      ///////////////////
    /////////////////////////////////////////////////////////

    static function post($middle_method, $ctrl, $action)
{
        if(!self::isPOST())
{
            echo json_encode(["result" => false, "message" => "1 : 不正なアクセスです。" ]);
        } else {
            echo json_encode(self::exec($middle_method, $ctrl, $action));
        }
        wp_die();
    }

    static function get()
{
        if(!self::isGET())
{
            echo json_encode(["result" => false, "message" => "1 : 不正なアクセスです。" ]);
        } else {
            echo json_encode(self::exec($middle_method, $ctrl, $action));
        }
        wp_die();
    }

    static function auth($reqData)
{

        $user = wp_get_current_user();
        
        if (!array_key_exists('user_id', $reqData))
{
            return "2: 不正なアクセスです。";
        }
        
        if ( (int )$reqData['user_id'] !== $user->ID )
{
            //ログインユーザーが違うと不正とする。
            return "3 : 不正なアクセスです。";

        } else {
            return null;
        }
    }

    static function noAuth($reqData)
{
        return "OK";
    }
    
    static function none($reqData)
{
        return "OK";
    }
}