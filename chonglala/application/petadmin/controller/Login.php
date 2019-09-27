<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use think\Controller;
use think\Request;
use app\petadmin\model\Login as LoginModel;

class Login extends Base
{
    // 后台管理系统登录
    public function login()
    {
        $param = $this->takeGetParam();
        $username = $param['username'];
        $password = $param['password'];

        $validate = new \think\Validate([
            ['username', ['require'],''],
            ['password', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $loginMedel = new LoginModel();
        $hasUser = $loginMedel->findUserByName($username);

        if(empty($hasUser)) {
            return $this->errorReturn('1002','用户名不存在',$param);
        }

        if(md5($password) != $hasUser['password']) {
            return $this->errorReturn('1002','密码错误',$param);
        }

        if(1 != $hasUser['status']) {
            return $this->errorReturn('1002','您已被禁用,请联系管理员',$param);
        }

        // 登录信息存session
        session('login_info',$hasUser);

        // 更新管理员状态
        $par = [
          'login_time' => date('Y-m-d H:i:s'),
            'login_ip' => $this->request->ip(),
            'login_count' => $hasUser['login_count'] + 1,
        ];
        $res = $loginMedel->updateStatus($hasUser['id'], $par);

        if ($res == 1) {
            return $this->successReturn('200',$hasUser);
        }
    }

    // 登录退出
    public function loginOut()
    {
        // 清空登录信息
        session('login_info',null);
        $data['msg'] = '成功退出';
        return $this->successReturn('200',$data);
    }
}
