<?php

namespace app\petadmin\model;

use think\Db;
use think\Exception;
use think\Model;

class Order extends Model
{
    protected $table = 'order_master';

    // 获取订单列表
    public function getOrderList($where_status,$where_make,$where_before,$where_search,$order,$pageSize,$page)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted'=>0])
            ->where($where_status)
            ->where($where_make)
            ->where($where_before)
            ->where($where_search)
            ->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->where(['deleted'=>0])
            ->where($where_status)
            ->where($where_make)
            ->where($where_before)
            ->where($where_search)
            ->field('order_id,order_num,user_id,mobile,`status`,make_time,before_time')
            ->order($order)
            ->limit($p,$pageSize)
            ->select();

        foreach ($now_page_content as $item) {
            $user_id = $item['user_id'];
            $user_res = Db::name('address')->field('name')->where(['user_id' => $user_id])->select();
            foreach ($user_res as $value) {
                $item['name'] = $value['name'];
            }
        }

        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }

    // 订单列表详情
    public function listDetails($order_id)
    {
        // 订单主表信息(订单编号、下单时间、预约时间、支付方式、联系电话、服务地址)
        $order_master = $this->field('order_id,doctor_id,
        order_num,make_time,before_time,pay_type,
        user_id,doctor_id,mobile,address')
            ->where(['order_id' => $order_id])
            ->find();

        // 根据用户id查找address
        $user_id = $order_master['user_id'];
        $user = null;
        if ($user_id != null) {
            $user = Db::name('address')
                ->field('name')
                ->where(['user_id' => $user_id,'default' => 1])
                ->find();
        }
        $name = $user['name'];

        // 根据医生id查找接单医生姓名
        $doctor_id = $order_master['doctor_id'];
        $doctor = null;
        if ($user_id != null) {
            $doctor = Db::name('app_user_doctor')->
            field('username')
                ->where(['id' => $doctor_id])
                ->find();
        }
        $doctor_name = $doctor['username'];

        // 根据订单id查找其下商品详情
        $order_id = $order_master['order_id'];
        $order_details = null;
        $row = null;
        $gid = null;
        if ($order_id != null) {
            $order_details = Db::name('order_details')
                ->field('gid,goods_name,price,num')
                ->where(['order_id' => $order_id])
                ->select();
            foreach ($order_details as $item) {
                $gid[] = $item['gid'];
                $row[$item['gid']]['goods_name'] = $item['goods_name'];
                $row[$item['gid']]['price'] = $item['price'];
                $row[$item['gid']]['num'] = $item['num'];
            }

            $gid_str = join(',',$gid);
            $res_goods_info = Db::name('goods_info')->where('id','in',$gid_str)->select();
            foreach ($res_goods_info as $value) {
                $row[$value['id']]['images_url'] = $value['images_url'];
            }
        }


        $data = null;
        $pay_type_str = null;

        $data['order_num'] = $order_master['order_num'];
        $data['make_time'] = $order_master['make_time'];
        $data['before_time'] = $order_master['before_time'];
        $pay_type = $order_master['pay_type'];
        if ($pay_type == 1) { $pay_type_str = '直接支付'; }
        if ($pay_type == 2) { $pay_type_str = '砍价支付'; }
        $data['pay_type'] = $pay_type_str;
        $data['mobile'] = $order_master['mobile'];
        $data['address'] = $order_master['address'];
        $data['name'] = $name;
        $data['doctor_name'] = $doctor_name;
        $data['goods'] = array_values($row);

        return $data;
    }

    // 删除订单
    public function delOrder($order_id)
    {
        $res = $this->where('order_id','in',"$order_id")->update(['deleted' => 1]);
        if ($res != null) {
            try {
                Db::name('order_details')->where('order_id','in',$order_id)->update(['deleted' => 1]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        return $order_id;
    }
}
