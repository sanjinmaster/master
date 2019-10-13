<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\Order as OrderModel;
use think\Controller;
use think\Request;

class Order extends Base
{
    // 订单列表
    public function orderList()
    {
        $param = $this->takeGetParam();
        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 必选参数
        $page = $param['page'];
        $pageSize = $param['pageSize'];

        // 筛选参数
        $order_status = $param['order_status'];
        $make_time = $param['make_time'];
        $before_time = $param['before_time'];
        $search_content = $param['search_content'];

        // 拼装筛选条件
        $where_status = null;
        $where_make = null;
        $where_before = null;
        $where_search = null;
        if ($order_status != null) { $where_status = [ 'status' => ['=',"$order_status"] ]; }
        if ($make_time != null) { $where_make = [ 'make_date' => ['=',"$make_time"] ]; }
        if ($before_time != null) { $where_before = [ 'before_time' => ['=',"$before_time"] ]; }
        if ($search_content != null) { $where_search = [ 'mobile' => ['=',"$search_content"] ]; }

        $order = "order_id desc";
        $OrderModel = new OrderModel();

        $res = $OrderModel->getOrderList($where_status,$where_make,$where_before,$where_search,$order,$pageSize,$page);

        return $this->successReturn('200',$res);
    }

    // 订单详情
    public function orderDetails()
    {
        $param = $this->takeGetParam();
        $validate = new \think\Validate([
            ['order_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order_id = $param['order_id'];
        $OrderModel = new OrderModel();
        $res = $OrderModel->listDetails($order_id);

        return $this->successReturn('200',$res);
    }

    // 删除
    public function delOrder()
    {
        $param = $this->takeDeleteParam();
        $validate = new \think\Validate([
            ['order_id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order_id = $param['order_id'];
        $OrderModel = new OrderModel();
        $res = $OrderModel->delOrder($order_id);
        return $this->successReturn('200',$res);
    }
}
