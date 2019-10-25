<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use app\common\model\TakeAliPayCallBack as TakeAliPayCallBackModel;

class TakeAliPayCallBack extends Base
{
    // 提现回调
    public function AliPayNotify()
    {
        $request    = input('post.');
        $signType = $request['sign_type'];
        file_put_contents('./' . mb_convert_encoding('log-callback', "UTF-8", "UTF-8") . '.txt', '查询到的所有数据' . date('Y-m-d H:i:s') . ':' .'request:'. implode('', $request). PHP_EOL, FILE_APPEND);
        $alipay = new TakeAliPay();
        file_put_contents('./' . mb_convert_encoding('log-callback', "UTF-8", "UTF-8") . '.txt', '$signType'.$signType . PHP_EOL, FILE_APPEND);
        $flag = $alipay->rsaCheck($request, $signType);

        if ($flag) {
            file_put_contents('./' . mb_convert_encoding('log-callback', "UTF-8", "UTF-8") . '.txt', '支付成功了' . PHP_EOL, FILE_APPEND);
            //支付成功:TRADE_SUCCESS   交易完成：TRADE_FINISHED
            if ($request['trade_status'] == 'TRADE_SUCCESS' || $request['trade_status'] == 'TRADE_FINISHED') {

                //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
                $buyer_pay_amount = $request['buyer_pay_amount'];
                $out_trade_no   =   $request['out_trade_no'];
                $TakeAliPayCallBackModel = new TakeAliPayCallBackModel();
                $res = $TakeAliPayCallBackModel->successBack($out_trade_no,$buyer_pay_amount);

                if ($res) {
                    exit('success'); //成功处理后必须输出这个字符串给支付宝
                } else {
                    exit('fail');
                }
            } else {
                file_put_contents('./' . mb_convert_encoding('log-callback', "UTF-8", "UTF-8") . '.txt', '支付成失败fail1' . PHP_EOL, FILE_APPEND);
                exit('fail');
            }
        } else {
            file_put_contents('./' . mb_convert_encoding('log-callback', "UTF-8", "UTF-8") . '.txt', '支付成失败fail2' . PHP_EOL, FILE_APPEND);
            exit('fail');
        }
    }
}
