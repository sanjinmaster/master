<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Cart as CartModel;
use think\Controller;
use think\Request;

class Cart extends Base
{
    // 加入购物车
    public function addShopCar()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['cid', ['require','number'],''],
            ['gid', ['require','number'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $cid = $param['cid'];
        $gid = $param['gid'];

        $CartModel = new CartModel();
        $res = $CartModel->addCart($user_id, $cid, $gid);

        return $this->successReturn('200',$res);
    }

    // 减少购物车数量
    public function reduceShopCar()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['cid', ['require','number'],''],
            ['gid', ['require','number'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $cid = $param['cid'];
        $gid = $param['gid'];

        $CartModel = new CartModel();
        $res = $CartModel->reduceCart($user_id, $cid, $gid);

        return $this->successReturn('200',$res);
    }

    // 购物车列表
    public function shopCarList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $where = ['user_id' => $user_id];

        $CartModel = new CartModel();
        $res = $CartModel->cartList($where);
        if ($res == null) {
            return $this->errorReturn('1002','您还没有添加商品到购物车...',$param);
        }

        return $this->successReturn('200',$res);
    }

    // 删除商品
    public function delGoods()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['cid', ['require'],''],
            ['gid', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $cid = trim($param['cid']);
        $gid = trim($param['gid']);
        $where = ['user_id' => $user_id];

        $CartModel = new CartModel();
        $res = $CartModel->delCartGoods($where, $cid, $gid);
        if ($res == null) {
            return $this->errorReturn('1002','您还没有添加商品到购物车...',$param);
        }

        return $this->successReturn('200',$res);
    }
}
