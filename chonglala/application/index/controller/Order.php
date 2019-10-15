<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Order as OrderModel;
use app\index\model\Bargain as BargainModel;
use think\Controller;
use think\Request;

class Order extends Base
{
    // 订单状态详情
    public function orderStatus()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['type', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $type = $param['type'];

        // type  1 待付款  2 待接单  3 待服务  4 待确认  5 已完成
        $OrderModel = new OrderModel();
        switch ($type) {
            case '1':
                $res = $OrderModel->orderDataDfk('1', $user_id);
                break;
            case '2':
                $res = $OrderModel->orderDataDjd('2', $user_id);
                break;
            case '3':
                $res = $OrderModel->orderDataDfw('3', $user_id);
                break;
            case '4':
                $res = $OrderModel->orderDataDqr('4', $user_id);
                break;
            case '5':
                $res = $OrderModel->orderDataYwc('5', $user_id);
                break;
            default:
                return $this->errorReturn('1002','type错误',$type);
                break;
        }

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 待付款-查看砍价
    public function lookDfk()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['bargain_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $bargain_id = $param['bargain_id'];

        $BargainModel = new BargainModel();
        $res = $BargainModel->getBargainInfo($user_id, $bargain_id);

        return $this->successReturn('200',$res);
    }

    // 订单详情
    public function orderDetails()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['type', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $type = $param['type'];
        $order_num = $param['order_num'];

        // type  1 待付款  2 待接单  3 待服务  4 待确认  5 已完成
        $OrderModel = new OrderModel();
        switch ($type) {
            case '1':
                // 支付状态,待支付
                $status = 1;
                $pay_status = 2;
                $res = $OrderModel->orderDataDetails($status, $user_id, $pay_status, $order_num);
                break;
            case '2':
                // 支付状态,待接单
                $status = 2;
                $pay_status = 1;
                $res = $OrderModel->orderDataDetails($status, $user_id, $pay_status, $order_num);
                break;
            case '3':
                // 支付状态,待服务
                $status = 3;
                $pay_status = 1;
                $res = $OrderModel->orderDataDetailsDoctor($status, $user_id, $pay_status, $order_num);
                break;
            case '4':
                // 支付状态,待确认
                $status = 4;
                $pay_status = 1;
                $res = $OrderModel->orderDataDetailsDoctor($status, $user_id, $pay_status, $order_num);
                break;
            case '5':
                // 支付状态,已完成
                $status = 5;
                $pay_status = 1;
                $res = $OrderModel->orderDataDetailsDoctor($status, $user_id, $pay_status, $order_num);
                break;
            default:
                return $this->errorReturn('1002','type错误',$type);
                break;
        }

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 待付款取消订单(此时为删除订单)
    public function cancelOrderDfk()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $order_num = $param['order_num'];

        $OrderModel = new OrderModel();
        $res = $OrderModel->delOrderDfk($user_id, $order_num);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 待服务、确认订单
    public function confirmOrder()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $order_num = $param['order_num'];

        $OrderModel = new OrderModel();
        $res = $OrderModel->confirmDqr($user_id, $order_num);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 已完成、评价
    public function evaluateNote()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['doctor_id', ['require','number'],''],
            ['order_num', ['require','number'],''],
            ['evaluate_note', ['require'],''],
            ['grade', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $order_num = $param['order_num'];
        $evaluate_note = $param['evaluate_note'];
        $grade = $param['grade'];
        $images = $param['images'];
        $doctor_id = $param['doctor_id'];

        $OrderModel = new OrderModel();
        $res = $OrderModel->evalNote($user_id, $order_num, $evaluate_note, $grade, $images, $doctor_id);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 查看医生信息、包含对医生的评价等信息
    public function doctorPjInfo()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['doctor_id', ['require','number'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $doctor_id = $param['doctor_id'];

        $OrderModel = new OrderModel();
        $res = $OrderModel->getDoctorPj($doctor_id);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }
}
