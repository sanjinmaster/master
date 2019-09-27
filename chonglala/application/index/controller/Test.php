<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/25
 * Time: 14:46
 */
namespace app\index\controller;

use app\common\controller\Base;

class Test extends Base
{
    public function test()
    {
        return $this->fetch('test');
    }
}