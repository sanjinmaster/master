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
        return Db::name('app_user_doctor')->field('id')->where(['mobile' => $mobile,'password' => md5($password)])->find();
    }

    // 医院用户登录校验
    public function checkHospitalLogin($mobile, $password)
    {
        return Db::name('app_user_hospital')->field('id')->where(['mobile' => $mobile,'password' => md5($password)])->find();
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
