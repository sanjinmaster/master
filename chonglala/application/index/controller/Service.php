<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Service as ServiceModel;
use think\Controller;
use think\Request;

class Service extends Base
{
    // 首页商品列表(包含上门疫苗、体检、美容、火化)
    public function goodsList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['first_class', ['require'],''],
            ['second_class', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 顶级分类： 1 全部  2 喵咪  3 狗狗
        $first_class = $param['first_class'];
        // 二级分类： 1 疫苗  2 体检  3 美容  4 火化
        $second_class = $param['second_class'];
        $user_id = $param['user_id'];
        switch ($first_class) {
            // 此时一级分类为全部
            case '1':

                switch ($second_class) {
                    case '1':
                        // 当二级分类为上门疫苗时
                        $cid_str = '3,7';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '2':
                        // 当二级分类为上门体检时
                        $cid_str = '4,8';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '3':
                        // 当二级分类为上门美容时
                        $cid_str = '5,9';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '4':
                        // 当二级分类为上门火化时
                        $cid_str = '6,10';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    default:
                        return $this->errorReturn('1001','请求二级分类参数不符合要求',$second_class);
                        break;
                }
                break;
            // 此时一级分类为喵咪
            case '2':

                switch ($second_class) {
                    case '1':
                        // 当二级分类为上门疫苗时
                        $cid_str = '3';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '2':
                        // 当二级分类为上门体检时
                        $cid_str = '4';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '3':
                        // 当二级分类为上门美容时
                        $cid_str = '5';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '4':
                        // 当二级分类为上门火化时
                        $cid_str = '6';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    default:
                        return $this->errorReturn('1001','请求二级分类参数不符合要求',$second_class);
                        break;
                }
                break;
            // 此时一级分类为狗狗
            case '3':

                switch ($second_class) {
                    case '1':
                        // 当二级分类为上门疫苗时
                        $cid_str = '7';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '2':
                        // 当二级分类为上门体检时
                        $cid_str = '8';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '3':
                        // 当二级分类为上门美容时
                        $cid_str = '9';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    case '4':
                        // 当二级分类为上门火化时
                        $cid_str = '10';
                        $res = $this->getGoodsListPublic($cid_str, $user_id);
                        return $this->successReturn('200',$res);
                        break;
                    default:
                        return $this->errorReturn('1001','请求二级分类参数不符合要求',$second_class);
                        break;
                }
                break;
            default:
                return $this->errorReturn('1001','请求一级分类参数不符合要求',$first_class);
                break;
        }
    }

    // 获取商品列表
    public function getGoodsListPublic($cid_str, $user_id)
    {
        $ServiceModel = new ServiceModel();
        $res = $ServiceModel->getGoods($cid_str, $user_id);
        return $res;
    }

    // 商品详情
    public function goodsDetails()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['gid', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $gid = $param['gid'];
        $ServiceModel = new ServiceModel();
        $res = $ServiceModel->getGoodsDetails($gid);

        return $this->successReturn('200',$res);
    }

    // 商品列表搜索
    public function search()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['key_word', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $key_word = $param['key_word'];
        $ServiceModel = new ServiceModel();
        $res = $ServiceModel->searchGoods($key_word);
        if ($res == null) {
            return $this->errorReturn('1002','没有找到您想要的内容',$param);
        }

        return $this->successReturn('200',$res);
    }

}
