<?php

namespace app\api\controller;

use app\common\controller\Base as BaseController;
use app\api\model\Base as BaseModel;
use think\Request;

class Base extends BaseController
{
    // 初始化方法校验token合法性
    public function _initialize()
    {
        // 获取header头部token
        $token = $this->takeHeaderToken();

        if (!$token) {
            echo $this->errorReturn('1001','请求参数缺少token',$token);
            exit();
        }

        $BaseModel = new BaseModel();
        $res = $BaseModel::getToken($token);

        if (!empty($res)) {
            if (strtotime('now') - strtotime($res['time_out']) > 0) {
                echo $this->errorReturn('9003','token已过期',$res);
                exit();
            }
        }
    }
}
