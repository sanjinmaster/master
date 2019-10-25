<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Order extends Base
{
    // 医生、医院订单表
    protected $table = 'app_doctor_order';

    // 待接单列表
    public function getOrderListDjd($order_num, $where)
    {
        $row = null;
        $rows = null;

        // 小程序用户下单信息
        $order_master = Db::name('order_master')
            ->field('user_id,address,order_num,before_time')
            ->where($where)
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
            ->where(['user_id' => $user_id,'default' => 1,'deleted' => 0])
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

    // 医生待服务、待确认、已完成订单列表
    public function getOrderListDct($where)
    {
        $row = null;
        $info = null;
        $user_id_arr = null;
        $order_num = null;

        // app订单表
        $doctor_order = Db::name('app_doctor_order')
            ->field('doctor_id,order_num')
            ->where($where)
            ->select();

        if ($doctor_order == null) { return null; }

        foreach ($doctor_order as $value) {
            $order_num[] = $value['order_num'];

            $row[$value['order_num']]['order_num'] = $value['order_num'];
        }
        $order_str_num = join(',',$order_num);

        // 用户订单主表和地址表
        $order_master = Db::name('order_master')
            ->alias('a')
            ->field('a.address,a.order_num,a.before_time,b.name')
            ->join('address b','a.user_id = b.user_id')
            ->where('a.order_num','in',$order_str_num)
            ->where('a.deleted',0)
            ->where(['b.deleted' => 0,'b.default' => 1])
            ->select();

        if ($order_master == null) { return null; }

        foreach ($order_master as $item) {

            $row[$item['order_num']]['before_time'] = $item['before_time'];
            $row[$item['order_num']]['address'] = $item['address'];
            $row[$item['order_num']]['username'] = $item['name'];
        }

        // 用户订单详情表和商品表
        $order_details = Db::name('order_details')
            ->alias('a')
            ->field('a.goods_name,a.order_num,a.price,a.num,b.images_url')
            ->join('goods_info b','a.gid = b.id')
            ->where('a.order_num','in',$order_str_num)
            ->where('a.deleted',0)
            ->where('b.deleted',0)
            ->select();

        foreach ($order_details as $detail) {
            $info['goods_name'] = $detail['goods_name'];
            $info['price'] = $detail['price'];
            $info['num'] = $detail['num'];
            $info['images_url'] = $detail['images_url'];
            $row[$detail['order_num']]['details'][] = $info;
        }

        return array_values($row);
    }

    // 医院待服务、待确认、已完成订单列表
    public function getOrderListDhs($where)
    {
        $row = null;
        $info = null;
        $user_id_arr = null;
        $order_num = null;

        // app订单表
        $doctor_order = Db::name('app_doctor_order')
            ->field('hospital_id,order_num')
            ->where($where)
            ->select();

        if ($doctor_order == null) { return null; }

        foreach ($doctor_order as $value) {
            $order_num[] = $value['order_num'];

            $row[$value['order_num']]['order_num'] = $value['order_num'];
        }
        $order_str_num = join(',',$order_num);

        // 用户订单主表和地址表
        $order_master = Db::name('order_master')
            ->alias('a')
            ->field('a.address,a.order_num,a.before_time,b.name')
            ->join('address b','a.user_id = b.user_id')
            ->where('a.order_num','in',$order_str_num)
            ->where('a.deleted',0)
            ->where(['b.deleted' => 0,'b.default' => 1])
            ->select();

        if ($order_master == null) { return null; }

        foreach ($order_master as $item) {

            $row[$item['order_num']]['before_time'] = $item['before_time'];
            $row[$item['order_num']]['address'] = $item['address'];
            $row[$item['order_num']]['username'] = $item['name'];
        }

        // 用户订单详情表和商品表
        $order_details = Db::name('order_details')
            ->alias('a')
            ->field('a.goods_name,a.order_num,a.price,a.num,b.images_url')
            ->join('goods_info b','a.gid = b.id')
            ->where('a.order_num','in',$order_str_num)
            ->where('a.deleted',0)
            ->where('b.deleted',0)
            ->select();

        foreach ($order_details as $detail) {
            $info['goods_name'] = $detail['goods_name'];
            $info['price'] = $detail['price'];
            $info['num'] = $detail['num'];
            $info['images_url'] = $detail['images_url'];
            $row[$detail['order_num']]['details'][] = $info;
        }

        return array_values($row);
    }

    // 待接单列表详情
    public function getOrderDetailsDjd($status, $pay_status, $order_num)
    {
        // 条件
        $where_details = [
            'status' => $status,
            'pay_status' => $pay_status,
            'order_num' => $order_num,
            'deleted' => 0,
        ];

        $res_master = \db('order_master')->field('user_id,order_id,order_num,address,mobile,before_time,
        make_time,deal_amount,total_amount,pay_type,note')
            ->where($where_details)
            ->select();

        if ($res_master == null) {
            return null;
        }

        $row = null;
        $rows = null;
        $data = null;
        $order_id = null;
        foreach ($res_master as $master) {
            $order_id[] = $master['order_id'];
            $res_user = Db::name('address')
                ->field('name')
                ->where(['user_id' => $master['user_id'],'default' => 1,'deleted' => 0])
                ->find();

            $rows[$master['order_id']]['order_num'] = $master['order_num'];
            $rows[$master['order_id']]['address'] = $master['address'];
            $rows[$master['order_id']]['address_name'] = $res_user['name'].' '.$master['mobile'];
            $rows[$master['order_id']]['before_time'] = $master['before_time'];
            $rows[$master['order_id']]['make_time'] = $master['make_time'];

            $rows[$master['order_id']]['total_amount'] = $master['deal_amount'];
            if ($master['pay_type'] == 2){

                // 砍价
                $res_bargain = Db::name('user_bargain')
                    ->field('present_price')
                    ->where(['order_num' => $master['order_num']])
                    ->find();

                $rows[$master['order_id']]['total_amount'] = $res_bargain['present_price'];
                $rows[$master['order_id']]['pay_type'] = '砍价支付';
            }
            if ($master['pay_type'] == 1) {
                $rows[$master['order_id']]['total_amount'] = $master['total_amount'];
                $rows[$master['order_id']]['pay_type'] = '直接支付';
            }

            $rows[$master['order_id']]['note'] = $master['note'];
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $res_goodsImg = Db::name('goods_info')->field('images_url')->where(['id' => $detail['gid'],'deleted' => 0])->find();
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['gid'] = $detail['gid'];
            $row['num'] = $detail['num'];
            $row['images_url'] = $res_goodsImg['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 待服务、待确认、已完成列表详情
    public function getOrderDetailsDfRw($status, $pay_status, $order_num)
    {
        // 条件
        $where_details = [
            'a.status' => $status,
            'a.pay_status' => $pay_status,
            'a.order_num' => $order_num,
            'a.deleted' => 0,
        ];

        // 用户订单主表和用户地址表
        $res_master = Db::name('order_master')->field('a.user_id,a.order_id,a.order_num,a.address,a.mobile,a.before_time,
        a.make_time,a.deal_amount,a.total_amount,a.pay_type,a.note,b.name')
            ->alias('a')
            ->join('address b','a.user_id = b.user_id')
            ->where($where_details)
            ->where(['b.default' => 1,'b.deleted' => 0])
            ->select();

        if ($res_master == null) { return null; }

        $row = null;
        $rows = null;
        $data = null;
        $order_id = null;
        foreach ($res_master as $master) {
            $order_id[] = $master['order_id'];

            $rows[$master['order_id']]['order_num'] = $master['order_num'];
            $rows[$master['order_id']]['address'] = $master['address'];
            $rows[$master['order_id']]['address_name'] = $master['name'].' '.$master['mobile'];
            $rows[$master['order_id']]['before_time'] = $master['before_time'];
            $rows[$master['order_id']]['make_time'] = $master['make_time'];

            $rows[$master['order_id']]['total_amount'] = $master['deal_amount'];
            if ($master['pay_type'] == 2){

                // 砍价
                $res_bargain = Db::name('user_bargain')
                    ->field('present_price')
                    ->where(['order_num' => $master['order_num']])
                    ->find();

                $rows[$master['order_id']]['total_amount'] = $res_bargain['present_price'];
                $rows[$master['order_id']]['pay_type'] = '砍价支付';
            }
            if ($master['pay_type'] == 1) {
                $rows[$master['order_id']]['total_amount'] = $master['total_amount'];
                $rows[$master['order_id']]['pay_type'] = '直接支付';
            }

            $rows[$master['order_id']]['note'] = $master['note'];
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')
            ->field('order_id,gid,goods_name,price,num')
            ->where('order_id','in',"$order_id_str")
            ->where(['deleted' => 0])
            ->select();
        foreach ($res_details as $detail) {
            $res_goodsImg = Db::name('goods_info')->field('images_url')->where(['id' => $detail['gid'],'deleted' => 0])->find();
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['gid'] = $detail['gid'];
            $row['num'] = $detail['num'];
            $row['images_url'] = $res_goodsImg['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }
}
