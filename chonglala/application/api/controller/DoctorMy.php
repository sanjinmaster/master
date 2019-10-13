<?php

namespace app\api\controller;

use app\api\model\DoctorMy as DoctorMyModel;
use think\Cache;
use think\Controller;
use think\Request;

class DoctorMy extends Base
{
    // 医生我的(头部信息)
    public function myInfo()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 医生id
        $id = trim($param['id']);
        $RegisterModel = new DoctorMyModel();
        $res = $RegisterModel->getMyInfo($id);

        return $this->successReturn('200',$res);
    }

    // 更换头像
    public function switchHeadImg()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number'],
            ['head_images', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 医生id
        $id = trim($param['id']);
        // 头像
        $head_images = trim($param['head_images']);

        $DoctorModel = new DoctorMyModel();
        $res = $DoctorModel->updateHeadImg($id, $head_images);

        return $this->successReturn('200',$res);
    }

    // 更换昵称
    public function switchNickname()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number'],
            ['nickname', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 医生id
        $id = trim($param['id']);
        // 头像
        $nickname = trim($param['nickname']);

        $DoctorModel = new DoctorMyModel();
        $res = $DoctorModel->updateNickname($id, $nickname);

        return $this->successReturn('200',$res);
    }

    // 修改密码
    public function updatePwd()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['mobile', ['require','length' => 11,'number'],''],
            ['verify_code', ['require'],'number'],
            ['password', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $mobile = $param['mobile'];
        $password = $param['password'];
        $verify_code = $param['verify_code'];

        $DoctorMyModel = new DoctorMyModel();

        // 校验验证码是否正确
        $cache_code = Cache::store('redis')->get("$mobile");
        if ($verify_code != $cache_code) {
            return $this->errorReturn('1001','验证码有误',$verify_code);
        }

        // 校验密码是否在8-16区间,以及弱口令密码
        $checkPwd = DoctorMy::checkPwd($password);

        if (!$checkPwd) {
            return $this->errorReturn('1001','密码必须含有小写字母、大写字母、数字、特殊符号的两种及以上',$checkPwd);
        }

        $res = $DoctorMyModel->updatePass($mobile, $password);

        return $this->successReturn('200',$res);
    }

    // 更换手机号、下一步
    public function next()
    {
        $param = $this->takeGetParam();

        $mobile = $param['mobile'];
        $verify_code = $param['verify_code'];

        // 校验验证码是否正确
        $cache_code = Cache::store('redis')->get("$mobile");
        if ($verify_code != $cache_code) {
            return $this->errorReturn('1001','验证码有误',$verify_code);
        }

        $is_next = Cache::store('redis')->set("$verify_code","$verify_code");
        if (!$is_next) {
            return $this->errorReturn('1001','fail',$is_next);
        }

        return $this->successReturn('200','success');
    }

    // 更换手机号、提交
    public function submit()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['mobile', ['require','length' => 11,'number'],''],
            ['verify_code', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $mobile = $param['mobile'];
        $verify_code = $param['verify_code'];

        // 校验验证码是否正确
        $cache_code = Cache::store('redis')->get("$mobile");
        if ($verify_code != $cache_code) {
            return $this->errorReturn('1001','验证码有误',$verify_code);
        }

        $DoctorModel = new DoctorMyModel();
        $res = $DoctorModel->updateMobile($mobile);

        return $this->successReturn('200',$res);
    }

    // 医院地址、所属医院
    public function getHospitalInfo()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = trim($param['id']);

        $DoctorModel = new DoctorMyModel();
        $res = $DoctorModel->HospitalInfo($id);

        return $this->successReturn('200',$res);
    }
}
