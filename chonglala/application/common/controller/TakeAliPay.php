<?php

namespace app\common\controller;

use think\Controller;
use think\Request;

class TakeAliPay extends Base
{
    var $aop;

    //支付宝
    //appid
    private $ALIPAY_APP_ID = '';
    //支付宝公钥
    private $ALIPAY_PUBLIC_KEY = '';
    //商户私钥
    private $ALIPAY_APP_PRIVATE_KEY = '';
    //回调地址
    private $ALIPAY_NOTIFY_URL = 'http://api.chongll.com/common/TakeAliPayCallBack/AliPayNotify';

    /**
     * 初始化
     */
    protected function _initialize()
    {
        parent::_initialize();
        //支付宝支付
        require_once(EXTEND_PATH.'alipay/aop/AopClient.php');
        $this->aop    =    new \AopClient();
        $this->aop->gatewayUrl             = "https://openapi.alipay.com/gateway.do";
        $this->aop->appId                 = $this->ALIPAY_APP_ID;
        $this->aop->rsaPrivateKey         = $this->ALIPAY_APP_PRIVATE_KEY;//私有密钥
        $this->aop->format                 = "JSON";
        $this->aop->charset                = "utf-8";
        $this->aop->signType            = "RSA2";
        $this->aop->alipayrsaPublicKey     = $this->ALIPAY_PUBLIC_KEY;//支付宝公钥
    }


    /**
     * 创建APP支付订单
     *
     * @param string $body 对一笔交易的具体描述信息。
     * @param string $subject 商品的标题/交易标题/订单标题/订单关键字等。
     * @param string $order_sn 商户网站唯一订单号
     * @return array 返回订单信息
     */
    public function tradeAppPay($body, $subject, $order_sn, $total_amount)
    {
        require_once(EXTEND_PATH.'alipay/aop/request/AlipayTradeAppPayRequest.php');
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent    =    [
            'body'                =>    $body,
            'subject'            =>    $subject,
            'out_trade_no'        =>    $order_sn,
            'timeout_express'    =>    '1d',//失效时间为 1天
            'total_amount'        =>    $total_amount,//价格
            'product_code'        =>    'QUICK_MSECURITY_PAY',
        ];
        //商户外网可以访问的异步地址 (异步回掉地址，根据自己需求写)
        $request->setNotifyUrl(AppConfig::$ALIPAY_NOTIFY_URL);
        $request->setBizContent(json_encode($bizcontent));
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $this->aop->sdkExecute($request);
        return $response;
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
    }

    /**
     * 单笔转账到支付宝账户
     * @param $account
     * @param $money
     * @param $subject
     * @param $desc
     */
    public function fundTransToaccountTransfer($account,$money,$subject,$desc)
    {
        require_once(EXTEND_PATH.'alipay/aop/request/AlipayFundTransToaccountTransferRequest.php');
        $request = new \AlipayFundTransToaccountTransferRequest();
        $out_biz_num = time().rand(1,1000);
        $bizcontent = [
            'out_biz_no' => $out_biz_num,
            'payee_type' => 'ALIPAY_LOGONID',
            'payee_account' => $account,
            'amount' => $money,
            'payer_show_name' => $subject,
            'remark' => $desc,
        ];
        $request->setBizContent(json_encode($bizcontent));
        $response = $this->aop->execute($request);
        return $response;
    }

    /**
     * 异步通知验签
     * @param string $params 参数
     * @param string $signType 签名类型：默认RSA
     * @return bool 是否通过
     */
    public function rsaCheck($params, $signType)
    {
        return 1;
        return $this->aop->rsaCheckV1($params, NULL, $signType);

    }
}
