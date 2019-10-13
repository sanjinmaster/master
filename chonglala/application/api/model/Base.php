<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Base extends Model
{
    // 基类model

    // 更新token
    public static function updateToken($token, $time_out, $id, $type)
    {
        switch ($type) {
            case '1' :
                // 医生
                $res_find = Db::name('user_token')->field('token')->where(['app_did' => $id])->find();
                if ($res_find) {
                    // 更新token
                    $data = [
                        'token' => $token,
                        'time_out' => date("Y-m-d H:i:s",$time_out),
                        'create_time' => date("Y-m-d H:i:s",time()),
                    ];
                    $res = Db::name('user_token')->where(['app_did' => $id])->update($data);
                }else {
                    // 新增token
                    $data = [
                        'app_did' => $id,
                        'token' => $token,
                        'create_time' => date("Y-m-d H:i:s",time()),
                        'time_out' => date("Y-m-d H:i:s",$time_out),
                    ];
                    $res = Db::name('user_token')->insertGetId($data);
                }
                break;
            case '2' :
                // 医院
                $res_find = Db::name('user_token')->field('token')->where(['app_hid' => $id])->find();
                if ($res_find) {
                    // 更新token
                    $data = [
                        'token' => $token,
                        'time_out' => date("Y-m-d H:i:s",$time_out),
                        'create_time' => date("Y-m-d H:i:s",time()),
                    ];
                    $res = Db::name('user_token')->where(['app_hid' => $id])->update($data);
                }else {
                    // 新增token
                    $data = [
                        'app_hid' => $id,
                        'token' => $token,
                        'create_time' => date("Y-m-d H:i:s",time()),
                        'time_out' => date("Y-m-d H:i:s",$time_out),
                    ];
                    $res = Db::name('user_token')->insertGetId($data);
                }
                break;
            default :
                return false;
                break;
        }

        return $res;
    }

    // 获取token
    public static function getToken($token)
    {
        $res = Db::name('user_token')->field('time_out')->where(['token' => $token])->find();
        return $res;
    }
}
