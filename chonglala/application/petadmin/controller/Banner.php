<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\Banner as BannerModel;
use think\Controller;
use think\Request;

class Banner extends Base
{
    // 添加轮播图
    public function addBanner()
    {
        $param = $this->takePostParam();
        $validate = new \think\Validate([
            ['img_url', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $img_url = $param['img_url'];
        $BannerModel = new BannerModel();
        $res = $BannerModel->createBanner($img_url);

        return $this->successReturn('200',$res);
    }

    // 轮播图列表
    public function bannerList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order = 'id desc';
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $BannerModel = new BannerModel();
        $res = $BannerModel->banList($order, $page, $pageSize);

        return $this->successReturn('200',$res);
    }

    // 更换轮播图
    public function updateBanner()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['url', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $url = $param['url'];
        $BannerModel = new BannerModel();
        $res = $BannerModel->udBanner($id, $url);

        return $this->successReturn('200',$res);
    }

    // 删除轮播图
    public function delBanner()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $BannerModel = new BannerModel();
        $res = $BannerModel->deleteBanner($id);

        return $this->successReturn('200',$res);
    }
}
