<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class Register extends Base
{
    public function send()
    {
        return $this->sendCode();
    }
}
