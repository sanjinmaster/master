<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/10
 * Time: 11:18
 */
namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Banner as BannerModel;
use think\Request;

class Banner extends Base
{
    // 小程序轮播图(用于首页)
    public function banner()
    {
        $BannerModel = new BannerModel();
        $res = $BannerModel->getBanner();
        if ($res == null) {
            return $this->errorReturn('1002','无满足条件的返回','');
        }
        return $this->successReturn('200',$res);
    }
}