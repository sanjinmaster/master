<?php

namespace app\api\model;

use think\Db;
use think\Log;
use think\Model;

class Login extends Base
{
    // 医生用户登录校验
    public function checkDoctorLogin($mobile, $password)
    {

        $res = Db::name('app_user_doctor')
            ->field('id,mobile,qualifications_book,id_card,username,address')
            ->where(['mobile' => $mobile,'password' => md5($password)])
            ->find();

        return $res;
    }

    // 医院用户登录校验
    public function checkHospitalLogin($mobile, $password)
    {
        $res = Db::name('app_user_hospital')
            ->field('id,address,mobile,telephone,business_license,hospital_name,store_images')
            ->where(['mobile' => $mobile,'password' => md5($password)])
            ->find();

        return $res;
    }

    // 更新用户别名
    public function updateAlias($type, $res, $alias)
    {
        switch ($type) {
            case '1' :
                $res = Db::name('app_user_doctor')->where(['id' => $res['id']])->update(['alias' => $alias]);
                break;
            case '2' :
                $res = Db::name('app_user_hospital')->where(['id' => $res['id']])->update(['alias' => $alias]);
                break;
            default :
                return false;
                break;
        }

        if (!$res) {
            return false;
        }

        return $alias;
    }

    // 注销登录
    public function delToken($token)
    {
        try{
            return Db::name('user_token')->where(['token' => $token])->delete();
        } catch (\Exception $e) {
            Log::write($e->getMessage());
            return $e->getMessage();
        }
    }
}
