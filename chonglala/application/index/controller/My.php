<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\My as MyModel;
use think\Controller;
use think\Request;

class My extends Base
{
    // 我的、头部信息
    public function personalInfo()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];

        $MyModel = new MyModel();
        $res = $MyModel->getHeadInfo($user_id);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 下级详情
    public function nextDetails()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];


        $MyModel = new MyModel();
        $res = $MyModel->getNextInfo($user_id);

        return $this->successReturn('200',$res);
    }

    // 分享
    public function sharePet()
    {

    }

    // 优惠券列表
    public function getCoupon()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['status', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $status = $param['status'];

        $MyModel = new MyModel();
        $res = $MyModel->getCouponInfo($user_id, $status);

        return $this->successReturn('200',$res);
    }

    // 奖励金详情
    public function rewardDetails()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];

        $MyModel = new MyModel();
        $res = $MyModel->getRewardInfo($user_id);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 提现奖励金
    public function rewardOut()
    {

    }

    // 奖励金明细
    public function moneyDetails()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];

        $MyModel = new MyModel();
        $res = $MyModel->getMoneyInfo($user_id);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 绑定支付宝账号
    public function bindAliPay()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['alipay', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $alipay = $param['alipay'];

        $MyModel = new MyModel();
        $res = $MyModel->addAliPay($user_id, $alipay);

        if (!$res){
            return $this->successReturn('6001','你已经绑定过支付宝账号了');
        }

        return $this->successReturn('200',$res);
    }

    // 修改支付宝账号
    public function updateAliPay()
    {
        $param = $this->takePatchParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['alipay', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $alipay = $param['alipay'];

        $MyModel = new MyModel();
        $res = $MyModel->editAliPay($user_id, $alipay);

        return $this->successReturn('200',$res);
    }

    // 意见反馈
    public function ideaBack()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['feedback_note', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $feedback_note = $param['feedback_note'];

        $MyModel = new MyModel();
        $res = $MyModel->feedback($user_id, $feedback_note);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }
}
