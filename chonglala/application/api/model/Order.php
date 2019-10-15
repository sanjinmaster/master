<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Order extends Base
{
    // 医生、医院订单表
    protected $table = 'app_doctor_order';

    // 待接单
    public function getOrderListDjd($order_num)
    {
        $row = null;
        $rows = null;
        // 小程序用户下单信息
        $order_master = Db::name('order_master')
            ->field('user_id,address,order_num,before_time')
            ->where(['order_num' => $order_num])
            ->find();

        if ($order_master == null) {
            return null;
        }

        $row['address'] = $order_master['address'];
        $row['order_num'] = $order_master['order_num'];
        $row['before_time'] = $order_master['before_time'];

        // 小程序用户id
        $user_id = $order_master['user_id'];
        $username = Db::name('address')
            ->field('name')
            ->where(['user_id' => $user_id])
            ->find();
        if ($username == null) {
            return null;
        }

        $row['username'] = $username['name'];

        // 订单详情
        $order_details = Db::name('order_details')
            ->alias('a')
            ->join('goods_info b','a.gid = b.id')
            ->field('b.goods_name,a.num,b.price,b.images_url')
            ->where(['order_num' => $order_num])
            ->select();

        $row['order_details'] = $order_details;

        return $row;
    }
}
