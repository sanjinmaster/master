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
            ['order_num', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $OrderModel = new OrderModel();
        $order_num = trim($param['order_num']);

        // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
        $res = $OrderModel->getOrderListDjd($order_num);

        return $this->successReturn('200',$res);
    }
}
