<?php

namespace app\api\controller;

use app\common\controller\SendSms;
use think\Cache;
use think\cache\driver\Redis;
use think\Controller;
use app\common\controller\Base as BaseController;
use app\api\model\Register as RegisterModel;
use think\Db;
use think\Request;

class Register extends BaseController
{
    // 返回给app端公钥
    public function publicRsaKey()
    {
        $data['PUBLIC_APP_KEY'] = PUBLIC_APP_KEY;
        return $data;
    }

    // 发送短信验证码
    public function sendSmsPublic()
    {
        // 验证码随机数
        $templateParam['rand'] = rand('100000','999999');

        // 用私钥解密
        //$param = Register::rsa_decode($this->takePostParam());
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['mobile', ['require','length' => 11,'number'],''],
            ['type', ['require','number'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $PhoneNumbers = $param['mobile'];
        $Sms_type = $param['type'];

        $sendSms = new SendSms();
        $res_send = $sendSms->sendVerifyCode($PhoneNumbers, $Sms_type, $templateParam);

        // 发送失败
        if ($res_send['Code'] != 'OK') {
            return $this->errorReturn('1002','发送失败',$res_send);
        }

        $code = $templateParam['rand'];
        // 存redis
        Cache::store('redis')->set("$PhoneNumbers","$code",60);
        //$res_send['verify_code'] = $code;

        return $this->successReturn('200',$res_send);
    }

    // 医生、医院用户注册
    public function doctorRegister()
    {
        // 用私钥解密
        //$param = $this::rsa_decode($this->takePostParam());
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['mobile', ['require','length' => 11,'number'],''],
            ['verify_code', ['require'],'number'],
            ['password', ['require'],''],
            ['type', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $mobile = $param['mobile'];
        $verify_code = $param['verify_code'];
        $password = $param['password'];

        // 1 医生  2 医院
        $type = $param['type'];

        $RegisterModel = new RegisterModel();

        // 校验验证码是否正确
        $cache_code = Cache::store('redis')->get("$mobile");

        if ($verify_code != $cache_code) {
            return $this->errorReturn('1001','验证码有误',$verify_code);
        }

        // 校验密码是否在8-16区间,以及弱口令密码
        $checkPwd = Register::checkPwd($password);

        if (!$checkPwd) {
            return $this->errorReturn('1001','密码必须含有小写字母、大写字母、数字、特殊符号的两种及以上',$checkPwd);
        }

        switch ($type) {
            case '1' :
                // 校验医生用户是否已经注册
                $res_user = $RegisterModel->findDoctor($mobile);
                if ($res_user) {
                    return $this->errorReturn('1001','您已经注册过了',$res_user);
                }

                $res = $RegisterModel->createDoctor($mobile, $verify_code, $password);
                break;
            case '2' :
                // 校验医院用户是否已经注册
                $res_user = $RegisterModel->findHospital($mobile);
                if ($res_user) {
                    return $this->errorReturn('1001','您已经注册过了',$res_user);
                }

                $res = $RegisterModel->createHospital($mobile, $verify_code, $password);

                break;
            default:
                return $this->errorReturn('1001','type错误',$type);
                break;
        }

        $data['id'] = $res;

        return $this->successReturn('200',$data);
    }

    // 根据用户id更新医生证书
    public function updateDoctor()
    {
        $param = $this->takePatchParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['mobile', ['require','length' => 11,'number'],''],
            ['qualifications_book', ['require']],
            ['id_card', ['require'],'number'],
            ['username', ['require']],
            ['address', ['require']]
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $mobile = $param['mobile'];
        $qualifications_book = $param['qualifications_book'];
        $id_card = $param['id_card'];
        $username = $param['username'];
        $address = $param['address'];

        $RegisterModel = new RegisterModel();

        $res = $RegisterModel->doctorUpdateInfo($id, $mobile, $qualifications_book, $id_card, $username, $address);

        return $this->successReturn('200',$res);
    }

    // 根据用户id更新医院证书
    public function updateHospital()
    {
        $param = $this->takePatchParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['mobile', ['require','length' => 11,'number'],''],
            ['business_license', ['require']],
            ['store_images', ['require'],'number'],
            ['hospital_name', ['require']],
            ['address', ['require']],
            ['telephone', ['require']]
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $mobile = $param['mobile'];
        $business_license = $param['business_license'];
        $store_images = $param['store_images'];
        $hospital_name = $param['hospital_name'];
        $address = $param['address'];
        $telephone = $param['telephone'];

        $RegisterModel = new RegisterModel();

        $res = $RegisterModel->hospitalUpdateInfo($id, $mobile, $business_license, $store_images, $hospital_name, $address, $telephone);

        return $this->successReturn('200',$res);
    }

    // 忘记密码
    public function forgetPwd()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['mobile', ['require','length' => 11,'number'],''],
            ['verify_code', ['require'],'number'],
            ['password', ['require'],''],
            ['type', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $mobile = $param['mobile'];
        $verify_code = $param['verify_code'];
        $password = $param['password'];
        // 1 医生  2 医院
        $type = $param['type'];

        $RegisterModel = new RegisterModel();

        // 校验验证码是否正确
        $cache_code = Cache::store('redis')->get("$mobile");

        if ($verify_code != $cache_code) {
            return $this->errorReturn('1001','验证码有误',$verify_code);
        }

        // 校验密码是否在8-16区间,以及弱口令密码
        $checkPwd = Register::checkPwd($password);

        if (!$checkPwd) {
            return $this->errorReturn('1001','密码必须含有小写字母、大写字母、数字、特殊符号的两种及以上',$checkPwd);
        }

        // 更新密码
        $res = $RegisterModel->forgetPass($mobile, $password, $verify_code, $type);
        if (!$res) {
            return $this->errorReturn('1001','您还没有注册,密码更新失败',$res);
        }

        return $this->successReturn('200',$res);
    }
}
