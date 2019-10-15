<?php

namespace app\api\controller;

use app\api\model\Login as LoginModel;
use think\Controller;
use app\common\controller\Base as BaseController;
use think\Db;
use think\Request;

class Login extends BaseController
{
    // 医生、医院用户登录
    public function login()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['mobile', ['require','length' => 11,'number'],''],
            ['password', ['require'],''],
            ['type', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $mobile = trim($param['mobile']);
        $password = trim($param['password']);
        // 1 医生  2 医院
        $type = $param['type'];

        $RegisterModel = new LoginModel();

        switch ($type) {
            case '1' :
                // 医生登录
                $data = $RegisterModel->checkDoctorLogin($mobile, $password);

                if (!$data['id']) {
                    return $this->errorReturn('1001','账号或密码错误',$data);
                }

                // 为医生用户生成别名,用于派单
                $doctor_alias = $this->createAlias();
                $alias = $RegisterModel->updateAlias(1, $data, $doctor_alias);
                break;
            case '2' :
                // 医院登录
                $data = $RegisterModel->checkHospitalLogin($mobile, $password);

                if (!$data['id']) {
                    return $this->errorReturn('1001','账号或密码错误',$data);
                }

                // 为医院用户生成别名生成别名,用于派单
                $hospital_alias = $this->createAlias();
                $alias = $RegisterModel->updateAlias(2, $data, $hospital_alias);
                break;
            default :
                return $this->errorReturn('1001','type错误',$type);
                break;
        }

        $time = strtotime('now');

        // 获取token
        $token = Login::getToken($mobile, $password, $time);

        $time_out = strtotime("+7 days");

        $id = $data['id'];
        // 更新用户token
        $res_token = $RegisterModel::updateToken($token, $time_out, $id, $type);
        if (!$res_token) {
            return $this->errorReturn('1001','token生成失败',$res_token);
        }

        $data['token'] = $token;
        $data['alias'] = $alias;

        return $this->successReturn('200',$data);
    }

    // 注销登录
    public function logout()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['token', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $token = trim($param['token']);

        $LoginModel = new LoginModel();

        $res = $LoginModel->delToken($token);

        return $this->successReturn('200',$res);
    }
}
