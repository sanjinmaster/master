<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\common\controller\CreditScore;
use app\index\model\WxNotifyBack as WxNotifyBackModel;
use think\Controller;
use think\Request;

class WxNotifyBack extends Base
{
    // 生成待支付订单
    public function makeOrder($order_num, $param)
    {
        // 类型：购物车、商品列表还是商品详情
        $type = $param['type'];

        $WxNotifyBackModel = new WxNotifyBackModel();

        // 判断有没有支付过此订单
        $check_pay = $WxNotifyBackModel->checkPay($order_num);
        if ($check_pay == 1) {
            return $check_pay;
        }

        // 判断是哪种类型,商品列表、购物车还是商品详情
        switch ($type) {
            // 商品列表、购物车
            case '1':

                return $WxNotifyBackModel->goodsCart($order_num, $param);
                break;
            // 商品详情
            case '2':

                return $WxNotifyBackModel->goodsDetails($order_num, $param);
                break;
            default:
                return $this->errorReturn('1001','请求参数类型正确',$type);
                break;
        }
    }

    // 支付成功回调、修改订单状态
    public function notifyBack()
    {
        // 获取参数
        $xmlData = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if (!$result) {
            return $this->errorReturn('1001','请求参数不合法',$result);
        }

        // 根据订单编号修改状态
        $WxNotifyBackModel = new WxNotifyBackModel();
        $res = $WxNotifyBackModel->updateOrderStatus($result);

        if ($res) {
            $param = $this->takePutParam();
            $CreditScore = new CreditScore();
            $CreditScore->pushApp($param);
        }

        return $this->successReturn('200',$res);
    }

    // 订单退款回调
    public function cancelBack()
    {
        // 获取参数
        $xmlData = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        // 解密req_info
        $req_info = $result['req_info'];
        $mch_key = "938bfd36a2f5c90f66c59fe51c5e653d";

        // 解密后的req_info
        $decryptReqInfo = WxNotifyBack::decipheringReqInfo($mch_key,$req_info);

        $result['out_refund_no'] = $decryptReqInfo['out_refund_no'];
        $result['refund_fee'] = $decryptReqInfo['refund_fee'];

        // 根据订单编号修改状态
        $WxNotifyBackModel = new WxNotifyBackModel();
        $res = $WxNotifyBackModel->updateCancelOrder($result);

        return $this->successReturn('200',$res);
    }
}
