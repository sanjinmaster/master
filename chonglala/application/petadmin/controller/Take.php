<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\common\controller\TakeAliPay;
use app\petadmin\model\Take as TakeModel;
use think\Controller;
use think\Request;

class Take extends Base
{
    // 提现管理列表
    public function takeList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order = 'id desc';
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $status = $param['status'];
        $ali_pay = $param['search_content'];

        $where_status = null;
        $where_ali_pay = null;
        if ($status != null) { $where_status = [ 'a.status' => $status]; }
        if ($ali_pay != null) { $where_ali_pay = [ 'b.alipay' => $ali_pay ]; }

        $TakeModel = new TakeModel();
        $res = $TakeModel->taList($order, $page, $pageSize, $where_status, $where_ali_pay);

        return $this->successReturn('200',$res);
    }

    // 同意提现
    public function agreeAliPayTake()
    {
        $param = $this->takePutparam();

        $validate = new \think\Validate([
            ['take_out_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        /*$zfb_account = $_POST['zfb_account'];
        $money = $_POST['money'];

        $AliPay = new TakeAliPay();
        $res = $AliPay->fundTransToaccountTransfer($zfb_account,$money,'有车壹选用户提现','邀请伙伴加入有车壹选吧');

        $ver = strpos ($res ,'Success');
        if ($ver===false) {
            return $this->errorReturn('1001','提现失败，请联系客服',$res);
        }*/

        // 提现表id
        $take_out_id = $param['take_out_id'];

        $TakeModel = new TakeModel();
        $res = $TakeModel->agr($take_out_id);

        return $this->successReturn('200',$res);
    }

    // 拒绝提现
    public function noAliPayTake()
    {
        $param = $this->takePutparam();

        $validate = new \think\Validate([
            ['take_out_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 提现表id
        $take_out_id = $param['take_out_id'];

        $TakeModel = new TakeModel();
        $res = $TakeModel->agrNo($take_out_id);

        return $this->successReturn('200',$res);
    }

    // 删除
    public function delTakeTal()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['take_put_id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $take_put_id = $param['take_put_id'];
        $TakeModel = new TakeModel();
        $res = $TakeModel->deleteTake($take_put_id);

        return $this->successReturn('200',$res);
    }
}
