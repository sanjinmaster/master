<?php

namespace app\api\controller;

use think\Controller;
use app\api\model\MeetOrder as MeetOrderModel;
use think\Request;

class MeetOrder extends Base
{
    // 待接单状态下认证过的医生
    public function receiptOrder()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 认证过的医生
        $rz_doctor_id = $param['id'];
        $order_num = $param['order_num'];

        $MeetOrderModel = new MeetOrderModel();
        $res = $MeetOrderModel->receiptRzDoctor($rz_doctor_id, $order_num);

        if (!$res){
            return $this->successReturn('6001','您来晚一步,订单已被其他人抢到');
        }

        return $this->successReturn('200',$res);
    }
}
