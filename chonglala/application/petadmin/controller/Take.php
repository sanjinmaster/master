<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
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
        if ($status != null) { $where_status = [ 'status' => ['=',"$status"] ]; }
        if ($ali_pay != null) { $where_ali_pay = [ 'alipay' => ['=',"$ali_pay"] ]; }

        $TakeModel = new TakeModel();
        $res = $TakeModel->taList($order, $page, $pageSize, $where_status, $where_ali_pay);

        return $this->successReturn('200',$res);
    }

    // 同意、拒绝
    public function agreeNo()
    {
        $param = $this->takePutparam();

        $validate = new \think\Validate([
            ['take_out_id', ['require','number'],''],
            ['is_agree_no', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 提现表id
        $take_out_id = $param['take_out_id'];
        // 1 同意  2 拒绝
        $is_agree_no = $param['is_agree_no'];

        $TakeModel = new TakeModel();
        $res = $TakeModel->agrNo($take_out_id, $is_agree_no);

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
