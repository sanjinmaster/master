<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Settlement extends Model
{
    // 购物车表
    protected $table = 'shop_cart';

    /*// 去结算(发生在商品列表、购物车)
    public function makeAmount($user_id)
    {
        // 初始化数组
        $row = null;
        $rows = null;

        $where_cart = [
            'user_id' => $user_id
        ];
        // 购物车信息
        $res_cart = $this->field('gid,num')->where($where_cart)->select();
        if ($res_cart == null) {
            return false;
        }

        // 组装条件取商品表信息
        $gid = null;
        foreach ($res_cart as $item) {
            $gid[] = $item['gid'];
            $row[$item['gid']]['num'] = $item['num'];
        }
        $gid_str = join(',',$gid);
        $where_goods = ['id'=>['in',"$gid_str"]];

        $res_goods = Db::name('goods_info')->field('id,goods_name,price')->where($where_goods)->select();
        if ($res_goods == null) {
            return false;
        }

        $total_count = null;
        $total_amount = null;
        foreach ($res_goods as $goods) {
            $row[$goods['id']]['gid'] = $goods['id'];
            $row[$goods['id']]['goods_name'] = $goods['goods_name'];
            $row[$goods['id']]['price'] = $goods['price'];
            $total_count += count($goods['id']);
            $total_amount += sprintf('%0.2f',$row[$goods['id']]['num'] * $row[$goods['id']]['price']);
        }

        // 组装映射表
        $order_details = array_values($row);
        $rows['order_details'] = $order_details;
        $rows['total_amount'] = sprintf('%0.2f',$total_amount);
        $rows['total_count'] = $total_count;

        return $rows;
    }*/

    public function cartAmount($user_id, $gid, $cid_str)
    {
        // 初始化数组
        $row = null;
        $rows = null;

        $where_cart = [
            'user_id' => $user_id
        ];
        $where_gid = [
            'gid'=>['in',"$gid"],
            'cid' => ['in',"$cid_str"]
        ];

        // 购物车信息
        $res_cart = $this->field('gid,num')->where($where_cart)->where($where_gid)->select();
        if ($res_cart == null) {
            return false;
        }

        // 组装条件取商品表信息
        $cart_gid = null;
        foreach ($res_cart as $item) {
            $cart_gid[] = $item['gid'];
            $row[$item['gid']]['num'] = $item['num'];
        }
        $gid_str = join(',',$cart_gid);
        $where_goods = ['id'=>['in',"$gid_str"]];

        $res_goods = Db::name('goods_info')->field('id,goods_name,price,images_url')->where($where_goods)->select();
        if ($res_goods == null) {
            return false;
        }

        $total_count = null;
        $total_amount = null;
        $coupon_amount = null;
        foreach ($res_goods as $goods) {
            $row[$goods['id']]['gid'] = $goods['id'];
            $row[$goods['id']]['goods_name'] = $goods['goods_name'];
            $row[$goods['id']]['price'] = $goods['price'];
            $row[$goods['id']]['images_url'] = $goods['images_url'];
            $total_count += count($goods['id']);
            $total_amount += sprintf('%0.2f',$row[$goods['id']]['num'] * $row[$goods['id']]['price']);
            $coupon_amount = $this->getCoupon($user_id,$total_amount);
        }

        $order_details = array_values($row);
        $rows['order_details'] = $order_details;
        $rows['total_amount'] = sprintf('%0.2f',$total_amount);
        $rows['total_count'] = $total_count;
        $rows['coupon_amount'] = $coupon_amount;

        return $rows;
    }

    // 去结算(发生在商品详情)
    public function detailsSet($gid, $user_id)
    {
        // 商品信息
        $where_goods = ['id' => $gid];
        $res_goods = Db::name('goods_info')->field('id,goods_name,price,images_url')->where($where_goods)->find();

        $row['id'] = $res_goods['id'];
        $row['price'] = $res_goods['price'];
        $row['goods_name'] = $res_goods['goods_name'];
        $row['images_url'] = $res_goods['images_url'];
        $row['num'] = 1;

        $total_amount = $row['price'] * $row['num'];

        // 组装映射表
        $rows['order_details'] = $row;
        $rows['total_amount'] = sprintf('%0.2f',$total_amount);
        $rows['total_count'] = 1;
        $coupon_amount = $this->getCoupon($user_id,$total_amount);
        $rows['coupon_amount'] = $coupon_amount;

        return $rows;
    }

    // 结算、获取默认地址
    public function getAddress($user_id)
    {
        // 初始化数组
       $rows = null;
       $where_address = [
           'user_id' => $user_id,
           'deleted' => 0,
           'default' => 1
       ];

       // 地址
       $res_address = Db::name('address')->field('name,phone,area,address')->where($where_address)->find();
       if ($res_address == null) {
           return false;
       }

       $rows['username'] = $res_address['name'];
       $rows['mobile'] = $res_address['phone'];
       $rows['address'] = $res_address['area'].$res_address['address'];

       return $rows;
    }

    // 获取优惠券
    public function getCoupon($user_id, $total_amount)
    {
        $row = null;
        $rows = null;

        $coupon_id = $this->getCouId($total_amount);

        $res_user_coupon = Db::name('user_coupon')->field('coupon_id')->where(['user_id' => $user_id,'status' => 2])->where('coupon_id','in',$coupon_id)->select();
        foreach ($res_user_coupon as $value) {
            $res_coupon_id = $value['coupon_id'];
            $res_coupon = Db::name('coupon')->where(['coupon_id' => $res_coupon_id])->where(['deleted' => 0])->find();
            $row['coupon_name'] = $res_coupon['coupon_name'];
            $row['coupon_id'] = $res_coupon['coupon_id'];
            $row['full'] = $res_coupon['full'];
            $row['cut'] = $res_coupon['cut'];

            $create_time = date("Y-m-d",strtotime($res_coupon['create_time']));
            $row['create_time'] = $create_time;
            $row['guoqi_time'] = date("Y-m-d",strtotime("$create_time + 3 month"));
            $rows[] = $row;
        }

        return $rows;
    }

    // 获取coupon_id
    protected function getCouId($total_amount)
    {
        // 由于此优惠券不能叠加,故限制优惠券使用区间以达到不能叠加目的
        $coupon_id = null;
        if ($total_amount >= 100 && $total_amount < 200) {
            // 满100减10
            $coupon_id = "1";
        }
        if ($total_amount >= 200 && $total_amount < 300) {
            // 满200减20
            $coupon_id = "1,2";
        }
        if ($total_amount >= 300 && $total_amount < 400) {
            // 满300减30
            $coupon_id = "1,2,3";
        }
        if ($total_amount >= 400) {
            // 满400减40
            $coupon_id = "1,2,3,4";
        }

        return $coupon_id;
    }
}
