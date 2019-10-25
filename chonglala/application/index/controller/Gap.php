<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Gap as GapModel;
use think\Controller;
use think\Request;

class Gap extends Base
{
    // 查看地图(待服务)
    public function lookGap()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['order_num', ['require','number'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order_num = $param['order_num'];

        $GapModel = new GapModel();
        $res = $GapModel->getGapInfo($order_num);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }
}
