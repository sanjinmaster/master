<?php

namespace app\index\controller;

use app\common\controller\Base;
use think\Controller;
use think\Exception;
use think\Loader;
use think\Request;

class Pay extends Base
{
    // 该方法引入微信支付sdk
    private function inc_WxApi()
    {
        require_once EXTEND_PATH.'/payment/include.php';
    }

    public function makePay()
    {
        // 引入微信支付封装包
        $this->inc_WxApi();
        // 获取参数
        $param = $this->takePostParam();

        // 检验参数
        $validate = new \think\Validate([
            ['openid', ['require'],''],
            ['user_id', ['require','number'],''],
            ['mobile', ['require','number'],''],
            ['address', ['require'],''],
            ['before_time', ['require'],''],
            ['order_note', ['require'],''],
            ['total_amount', ['require','number'],''],
            ['gid', ['require'],''],
            ['type', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $openid = $param['openid'];
        // 生成唯一订单号
        $order_num = $this->order_num();

        // 生成待支付订单
        $WxNotifyBack = new WxNotifyBack();
        $res = $WxNotifyBack->makeOrder($order_num, $param);
        if ($res == 1) {
            return $this->errorReturn('1002','您已付款,请勿重复付款',$res);
        }

        $config = [
            'token'          => 'd90dac55927f755bcd6cc81aaf96b970',
            // 小程序appid
            'appid'          => 'wx152de2fb5c169fd2',
            // 小程序secret
            'appsecret'      => 'c33b0422e8ec5b322d9a032588a99961',

            'encodingaeskey' => 'BJIUzE0gqlWy0GxfPp4J1oPTBmOrNDIGPNav1YFH5Z5',
            // 配置商户支付参数（可选，在使用支付功能时需要）
            // 微信商户id
            'mch_id'         => "1537578331",
            // 商户api秘钥
            'mch_key'        => '938bfd36a2f5c90f66c59fe51c5e653d',
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            'ssl_key'        => EXTEND_PATH.'/payment/cert/apiclient_key.pem',
            'ssl_cer'        => EXTEND_PATH.'/payment/cert/apiclient_cert.pem',
            // 缓存目录配置（可选，需拥有读写权限）
            'cache_path'     => '',
        ];

        $wechat = new \WeChat\Pay($config);

        // 组装参数
        $options = [
            // 商品描述
            'body'             => '杭州宠啦啦科技有限公司',
            // 商户订单号
            'out_trade_no'     => $order_num,
            // 订单总金额，单位为分
            'total_fee'        => '1',
            // 用户标识
            'openid'           => $openid,
            // 交易类型
            'trade_type'       => 'JSAPI',
            // 通知回调地址
            'notify_url'       => 'http://api.chongll.com/pet/home/notifyBack',
            // 终端IP
            'spbill_create_ip' => '127.0.0.1',
        ];

        try{

            // 生成预支付码
            $result = $wechat->createOrder($options);

            // 创建JSAPI参数签名
            $options = $wechat->createParamsForJsApi($result['prepay_id']);
            $row['options'] = $options;
            $row['order_num'] = $order_num;

            return $this->successReturn('200',$row);
        }catch (\Exception $e) {
            return $this->errorReturn('401',$e->getMessage().PHP_EOL,'出错了');
        }
    }

    // 待接单、待服务取消订单(此时为退款并删除订单)
    public function cancelOrderDjdDfw()
    {
        // 引入微信支付封装包
        $this->inc_WxApi();
        // 获取参数
        $xmlData = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if (!$result) {
            return $this->errorReturn('1001','请求参数不合法',$result);
        }

        $config = [
            'token'          => 'd90dac55927f755bcd6cc81aaf96b970',
            // 小程序appid
            'appid'          => 'wx152de2fb5c169fd2',
            // 小程序secret
            'appsecret'      => 'c33b0422e8ec5b322d9a032588a99961',

            'encodingaeskey' => 'BJIUzE0gqlWy0GxfPp4J1oPTBmOrNDIGPNav1YFH5Z5',
            // 配置商户支付参数（可选，在使用支付功能时需要）
            // 微信商户id
            'mch_id'         => "1537578331",
            // 商户api秘钥
            'mch_key'        => '938bfd36a2f5c90f66c59fe51c5e653d',
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            'ssl_key'        => EXTEND_PATH.'/payment/cert/apiclient_key.pem',
            'ssl_cer'        => EXTEND_PATH.'/payment/cert/apiclient_cert.pem',
            // 缓存目录配置（可选，需拥有读写权限）
            'cache_path'     => '',
        ];

        $wechat = new \WeChat\Pay($config);

        // 组装参数
        $options = [
            // 商户订单号
            'out_trade_no'     => $result['out_trade_no'],
            'out_refund_no'     => $result['out_refund_no'],
            // 订单总金额，单位为分
            'total_fee'        => $result['total_fee'],
            'refund_fee'        => $result['refund_fee'],
            // 通知回调地址
            'notify_url'       => 'http://api.chongll.com/pet/my/cancelBack',
            // 终端IP
            'spbill_create_ip' => '127.0.0.1',
        ];

        try{

            // 生成预支付码
            $result = $wechat->createRefund($options);

            return $this->successReturn('200',$result);
        }catch (\Exception $e) {
            return $this->errorReturn('401',$e->getMessage().PHP_EOL,'出错了');
        }
    }


}
