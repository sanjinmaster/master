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

    // 提现奖励金到支付宝
    public function rewardOut()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['take_out_amount', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $take_out_amount = $param['take_out_amount'];

        // 校验提现金额是否小于1元
        if ($take_out_amount < 1) {
            return $this->errorReturn('1001','提现金额不能少于人民币一元,提现失败',$param);
        }

        $MyModel = new MyModel();

        // 校验提现金额大于1元情况下是否超多账户最多金额
        $total_reward = $MyModel->getReward($user_id);

        if ($take_out_amount > $total_reward) {
            return $this->errorReturn('1001','账户余额不足,提现失败',$param);
        }

        $res = $MyModel->addReward($user_id, $take_out_amount);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
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
        $param = $this->takePutParam();

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
