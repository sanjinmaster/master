<?php

namespace app\api\controller;

use app\common\controller\Base as BaseController;
use think\Request;

class Base extends BaseController
{
    // 发送验证码
    public function sendCode()
    {
        return $this->successReturn('200','我是哈哈');
    }
}
