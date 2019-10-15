<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Pay extends Model
{
    // 更新订单的预约时间
    public function updateOrder($order_num, $before_time)
    {
        $res = Db::name('order_master')->where(['order_num' => $order_num])->update(['before_time' => $before_time]);
        return $res;
    }
}
