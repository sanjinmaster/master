<?php

namespace app\api\model;

use think\Db;
use think\Log;
use think\Model;

class Register extends Base
{
    // 医生表
    protected $table = 'app_user_doctor';

    // 验证码存储数据库
    public function saveVerifyCode($PhoneNumbers, $send_type, $verify_code)
    {
        try{
            $data = [
                'mobile' => $PhoneNumbers,
                'verify_code' => $verify_code,
                'type' => $send_type,
                'create_time' => date("Y-m-d H:i:s",time()),
            ];
            $res = Db::name('app_user_code')->insertGetId($data);
            if (!$res) {
                // 如果存储失败,写入日志
                Log::write($res);
            }
            return $res;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 校验医生用户是否已经注册过
    public function findDoctor($mobile)
    {
        $res = $this->field('id,mobile,password,qualifications_book,id_card,username,address')->where(['mobile' => $mobile])->find();
        return $res;
    }

    // 校验医院用户是否已经注册过
    public function findHospital($mobile)
    {
        $res = Db::name('app_user_hospital')->field('id,mobile,password,business_license,store_images,hospital_name,address,telephone')->where(['mobile' => $mobile])->find();
        return $res;
    }

    // 创建医生
    public function createDoctor($mobile, $verify_code, $password)
    {
        try{
            $data = [
                'mobile' => $mobile,
                'password' => md5($password),
                'create_time' => date("Y-m-d H:i:s",time()),
            ];
            $res = $this->insertGetId($data);
            if ($res) {
                $send_type = 1;
                $this->saveVerifyCode($mobile, $send_type, $verify_code);
            }

            return $res;
        }catch (\Exception $e) {
            Log::write($e->getMessage());
            return $e->getMessage();
        }
    }

    // 创建医院
    public function createHospital($mobile, $verify_code, $password)
    {
        try{
            $data = [
                'mobile' => $mobile,
                'password' => md5($password),
                'create_time' => date("Y-m-d H:i:s",time()),
            ];
            $res = Db::name('app_user_hospital')->insertGetId($data);
            if ($res) {
                $send_type = 1;
                $this->saveVerifyCode($mobile, $send_type,$verify_code);
            }

            return $res;
        }catch (\Exception $e) {
            Log::write($e->getMessage());
            return $e->getMessage();
        }
    }

    // 更新医生资料
    public function doctorUpdateInfo($id, $mobile, $qualifications_book, $id_card, $username, $address)
    {
        $data = [
            'qualifications_book' => $qualifications_book,
            'id_card' => $id_card,
            'username' => $username,
            'address' => $address,
            'update_time' => date("Y-m-d H:i:s",time())
        ];
        $res = $this->where(['id' => $id,'mobile' => $mobile])->update($data);
        return $res;
    }

    // 更新医院资料
    public function hospitalUpdateInfo($id, $mobile, $business_license, $store_images, $hospital_name, $address, $telephone)
    {
        $data = [
            'business_license' => $business_license,
            'store_images' => $store_images,
            'hospital_name' => $hospital_name,
            'address' => $address,
            'telephone' => $telephone,
            'update_time' => date("Y-m-d H:i:s",time())
        ];
        $res = Db::name('app_user_hospital')->where(['id' => $id,'mobile' => $mobile])->update($data);
        return $res;
    }

    // 忘记密码
    public function forgetPass($mobile, $password, $verify_code, $type)
    {
        $res = null;
        $data = [
            'password' => md5($password),
            'update_time' => date("Y-m-d H:i:s",time())
        ];

        $data_code = [
            'verify_code' => $verify_code,
            'create_time' => date("Y-m-d H:i:s",time()),
            'type' => 2,
        ];

        switch ($type) {
            case '1' :
                // 医生
                $res = $this->where(['mobile' => $mobile])->update($data);
                break;
            case '2' :
                // 医院
                $res = Db::name('app_user_hospital')->where(['mobile' => $mobile])->update($data);
                break;
            default:
                return false;
                break;
        }

        if ($res) {
            // 更新验证码
            Db::name('app_user_code')->where(['mobile' => $mobile])->update($data_code);
        }

        return $res;
    }
}
