<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Settlement as SettlementModel;
use think\Controller;
use think\Request;

class Settlement extends Base
{
    // 去结算(发生在商品列表、购物车)
    public function setAmountCart()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['user_id', ['require'],''],
            ['gid', ['require'],''],
            ['cid', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $cid_str = trim($param['cid']);
        $cid = explode(',',trim($param['cid']));
        $gid = trim($param['gid']);
        $user_id = $param['user_id'];

        $count = count($cid);
        if ($count >= 2) {
            // 校验结算商品是否是同一分类
            $check = $this->checkMakePublic($cid);
            if ($check == false) {
                return $this->errorReturn('1001','选择的结算商品不能包含不同分类',$gid);
            }
        }

        $SettlementModel = new SettlementModel();
        $res = $SettlementModel->cartAmount($user_id, $gid, $cid_str);

        // 检验是否有添加商品到购物车
        if ($res == null) {
            return $this->errorReturn('1002','您还没有添加商品到购物车...',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 去结算(发生在商品详情)
    public function goodsDetailsAmount()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['gid', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $gid = $param['gid'];
        $user_id = $param['user_id'];

        // 校验商品是否属于不同分类
        $SettlementModel = new SettlementModel();
        $res = $SettlementModel->detailsSet($gid, $user_id);

        return $this->successReturn('200',$res);
    }

    // 结算、获取默认地址
    public function getDefaultAddress()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        // 校验商品是否属于不同分类
        $SettlementModel = new SettlementModel();
        $res = $SettlementModel->getAddress($user_id);

        if ($res == false) {
            return $this->errorReturn('1001','您没选择默认地址',$param);
        }

        return $this->successReturn('200',$res);
    }

    // 获取优惠券信息
    public function couponInfo()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        // 校验商品是否属于不同分类
        $SettlementModel = new SettlementModel();
        $res = $SettlementModel->getCoupon($user_id);

        if (!$res) {
            return $this->errorReturn('1002','您没有优惠券或者已使用完了',$param);
        }

        return $this->successReturn('200',$res);
    }

}
