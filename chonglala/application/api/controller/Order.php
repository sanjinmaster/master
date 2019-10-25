<?php

namespace app\api\controller;

use app\api\model\Order as OrderModel;
use think\Controller;
use think\Request;

class Order extends Base
{
    // 待接单订单列表
    public function orderListDjd()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['order_num', ['require'],'number'],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $OrderModel = new OrderModel();
        $order_num = trim($param['order_num']);

        $where = [
            'order_num' => $order_num,
            'status' => 2,
            'pay_status' => 1,
            'deleted' => 0,
        ];
        // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
        $res = $OrderModel->getOrderListDjd($order_num, $where);

        return $this->successReturn('200',$res);
    }

    // 医生待服务、待确认、已完成订单列表
    public function orderListDfw()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number'],
            ['type', ['require'],'number'],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $OrderModel = new OrderModel();
        $doctor_id = trim($param['id']);
        $type = trim($param['type']);

        // type  1 待服务  2 待确认  3 已完成
        switch ($type) {
            case '1' :
                $where = [
                    'doctor_id' => $doctor_id,
                    'status' => 2,
                    'is_accept' => 1,
                    'deleted' => 0,
                ];
                // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
                $res = $OrderModel->getOrderListDct($where);
                break;
            case '2' :
                $where = [
                    'doctor_id' => $doctor_id,
                    'status' => 3,
                    'is_accept' => 1,
                    'deleted' => 0,
                ];
                // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
                $res = $OrderModel->getOrderListDct($where);
                break;
            case '3' :
                $where = [
                    'doctor_id' => $doctor_id,
                    'status' => 4,
                    'is_accept' => 1,
                    'deleted' => 0,
                ];
                // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
                $res = $OrderModel->getOrderListDct($where);
                break;
            default :
                return $this->errorReturn('1001','type不符合要求',$type);
                break;
        }

        return $this->successReturn('200',$res);
    }

    // 医院待服务、待确认、已完成订单列表
    public function orderListDhs()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number'],
            ['type', ['require'],'number'],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $OrderModel = new OrderModel();
        $hospital_id = trim($param['id']);
        $type = trim($param['type']);

        // type  1 待服务  2 待确认  3 已完成
        switch ($type) {
            case '1' :
                $where = [
                    'hospital_id' => $hospital_id,
                    'status' => 2,
                    'is_accept' => 1,
                    'deleted' => 0,
                ];
                // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
                $res = $OrderModel->getOrderListDhs($where);
                break;
            case '2' :
                $where = [
                    'hospital_id' => $hospital_id,
                    'status' => 3,
                    'is_accept' => 1,
                    'deleted' => 0,
                ];
                // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
                $res = $OrderModel->getOrderListDhs($where);
                break;
            case '3' :
                $where = [
                    'hospital_id' => $hospital_id,
                    'status' => 4,
                    'is_accept' => 1,
                    'deleted' => 0,
                ];
                // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
                $res = $OrderModel->getOrderListDhs($where);
                break;
            default :
                return $this->errorReturn('1001','type不符合要求',$type);
                break;
        }

        return $this->successReturn('200',$res);
    }

    // 待接单订单列表详情
    public function orderDetails()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['type', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $type = $param['type'];
        $order_num = $param['order_num'];

        // type  1 待接单  2 待服务  3 待确认  4 已完成
        $OrderModel = new OrderModel();
        switch ($type) {
            case '1':
                // 待接单
                $status = 2;
                $pay_status = 1;
                $res = $OrderModel->getOrderDetailsDjd($status, $pay_status, $order_num);
                break;
            case '2':
                // 待服务
                $status = 3;
                $pay_status = 1;
                $res = $OrderModel->getOrderDetailsDfRw($status, $pay_status, $order_num);
                break;
            case '3':
                // 待确认
                $status = 4;
                $pay_status = 1;
                $res = $OrderModel->getOrderDetailsDfRw($status, $pay_status, $order_num);
                break;
            case '4':
                // 已完成
                $status = 5;
                $pay_status = 1;
                $res = $OrderModel->getOrderDetailsDfRw($status, $pay_status, $order_num);
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
}
