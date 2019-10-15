<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Bargain as BargainModel;
use think\Controller;
use think\Request;

class Bargain extends Base
{
    // 去砍价
    public function bargain()
    {
        // 获取参数
        $param = $this->takePostParam();

        // 检验参数
        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['mobile', ['require','number'],''],
            ['address', ['require'],''],
            ['total_amount', ['require','number'],''],
            ['gid', ['require'],''],
            ['type', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 生成唯一订单号
        $order_num = $this->order_num();

        // 用户id
        $user_id = $param['user_id'];

        // 生成待支付订单
        $res = $this->makeOrder($order_num, $param);

        if (!$res) {
            return $this->errorReturn('1002','生成待支付订单失败',$res);
        }

        $BargainModel = new BargainModel();
        // 生成待支付订单成功后返回砍价页面所需信息
        $row = $BargainModel->getBargainInfo($user_id, $res['bargain_id']);

        if (!$row) {
            $this->errorReturn('1002','没有找到数据',$row);
        }

        $data['data'] = $row;
        $data['order_num'] = $res['order_num'];

        return $this->successReturn('200',$data);
    }

    // 校验用户是否二次参与砍价
    public function checkBargain()
    {
        // 获取参数
        $param = $this->takePostParam();

        // 检验参数
        $validate = new \think\Validate([
            ['user_id', ['require'],'number'],
            ['order_num', ['require','number'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $order_num = $param['order_num'];

        $BargainModel = new BargainModel();
        $res = $BargainModel->checkBar($user_id, $order_num);
        if ($res) {
            return $this->errorReturn('1002','您已经砍过了',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 生成待支付订单
    public function makeOrder($order_num, $param)
    {
        // 类型：购物车、商品列表还是商品详情
        $type = $param['type'];

        $BargainModel = new BargainModel();

        // 判断是哪种类型,商品列表、购物车还是商品详情
        switch ($type) {
            // 商品列表、购物车
            case '1':

                return $BargainModel->goodsCart($order_num, $param);
                break;
            // 商品详情
            case '2':

                return $BargainModel->goodsDetails($order_num, $param);
                break;
            default:
                return $this->errorReturn('1001','请求参数类型正确',$type);
                break;
        }
    }

    // 让朋友来砍一刀
    public function makeAmount()
    {
        // 获取参数
        $param = $this->takePostParam();

        // 检验参数
        $validate = new \think\Validate([
            ['openid', ['require'],''],
            ['order_num', ['require','number'],''],
            ['id', ['require','number'],''],
            ['highest_price', ['require','number'],''],
            ['before_price', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $openid = $param['openid'];
        $order_num = $param['order_num'];
        $user_bargain_id = $param['id'];
        $before_price = $param['before_price'];
        $highest_price = $param['highest_price'];
        $BargainModel = new BargainModel();
        $res = $BargainModel->makeBar($openid, $order_num, $user_bargain_id, $before_price, $highest_price);

        if ($res == 'fail') {
            return $this->errorReturn('1002','您已经砍过了',$res);
        }

        return $this->successReturn('200',$res);
    }
}
