<?php

namespace app\index\model;

use think\Db;
use think\Log;
use think\Model;

class Order extends Model
{
    // 订单表
    protected $table = 'order_master';

    // 待付款
    public function orderDataDfk($type, $user_id)
    {
        $where_master = [
            'user_id' => $user_id,
            'status' => $type,
            'pay_status' => '2',
            'deleted' => '0',
        ];

        $res_master = $this->field('order_id,order_num,pay_status,before_time,pay_status,pay_type')->where($where_master)->select();

        $order_id = null;
        $row = null;
        $rows = null;
        $data = null;
        foreach ($res_master as $value) {
            $order_id[] = $value['order_id'];
            $order_num = $value['order_num'];

            // 帮砍表
            $res_bargain = Db::name('user_bargain')->field('present_price,already_price,highest_price')->where(['order_num' => "$order_num"])->find();
            // 如果一张订单有砍价记录
            if ($res_bargain != null) {
                $rows[$value['order_id']]['present_price'] = $res_bargain['present_price'];
                if ($res_bargain['already_price'] < $res_bargain['highest_price']) {
                    $rows[$value['order_id']]['bargain_type'] = '砍价进行中...';
                }
                if ($res_bargain['already_price'] == $res_bargain['highest_price']) {
                    $rows[$value['order_id']]['bargain_type'] = '砍价成功';
                }
            }

            $rows[$value['order_id']]['order_id'] = $value['order_id'];
            $rows[$value['order_id']]['order_num'] = $value['order_num'];
            $rows[$value['order_id']]['pay_status'] = $value['pay_status'];
            $rows[$value['order_id']]['before_time'] = $value['before_time'];
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $gid = $detail['gid'];
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['num'] = $detail['num'];

            // 如果有砍价记录,此张订单总价格取当前砍价过的总金额,否则就计算返回
            if ($rows[$detail['order_id']]['present_price'] != null) {
                $rows[$detail['order_id']]['total_amount'] = sprintf('%0.2f',$rows[$detail['order_id']]['present_price']);
            }else {
                $rows[$detail['order_id']]['total_amount'] += sprintf('%0.2f',$detail['price'] * $detail['num']);
            }

            $res_goods = Db::name('goods_info')->field('images_url')->where(['id' => $gid])->find();
            $row['goods_img'] = $res_goods['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 待接单
    public function orderDataDjd($type, $user_id)
    {
        $where_master = [
            'user_id' => $user_id,
            'status' => $type,
            'pay_status' => '1',
            'deleted' => '0',
        ];

        $res_master = $this->field('order_id,order_num,pay_status,before_time,pay_status,pay_type,deal_amount')->where($where_master)->select();

        $order_id = null;
        $row = null;
        $rows = null;
        $data = null;
        foreach ($res_master as $value) {
            $order_id[] = $value['order_id'];

            $rows[$value['order_id']]['total_amount'] = sprintf('%0.2f',$value['deal_amount']);

            $rows[$value['order_id']]['order_id'] = $value['order_id'];
            $rows[$value['order_id']]['order_num'] = $value['order_num'];
            $rows[$value['order_id']]['pay_status'] = $value['pay_status'];
            $rows[$value['order_id']]['before_time'] = $value['before_time'];
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $gid = $detail['gid'];
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['num'] = $detail['num'];

            $res_goods = Db::name('goods_info')->field('images_url')->where(['id' => $gid])->find();
            $row['goods_img'] = $res_goods['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 待服务
    public function orderDataDfw($type, $user_id)
    {
        $where_master = [
        'user_id' => $user_id,
        'status' => $type,
        'pay_status' => '1',
        'deleted' => '0',
    ];

        $res_master = $this->field('order_id,doctor_id,hospital_id,order_num,pay_status,before_time,pay_status,pay_type,deal_amount')->where($where_master)->select();

        $order_id = null;
        $row = null;
        $rows = null;
        $data = null;
        foreach ($res_master as $value) {
            $order_id[] = $value['order_id'];

            $rows[$value['order_id']]['total_amount'] = sprintf('%0.2f',$value['deal_amount']);

            $rows[$value['order_id']]['order_id'] = $value['order_id'];
            $rows[$value['order_id']]['doctor_id'] = $value['doctor_id'];
            $rows[$value['order_id']]['hospital_id'] = $value['hospital_id'];
            $rows[$value['order_id']]['order_num'] = $value['order_num'];
            $rows[$value['order_id']]['pay_status'] = $value['pay_status'];
            $rows[$value['order_id']]['before_time'] = $value['before_time'];
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $gid = $detail['gid'];
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['num'] = $detail['num'];

            $res_goods = Db::name('goods_info')->field('images_url')->where(['id' => $gid])->find();
            $row['goods_img'] = $res_goods['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 待确认
    public function orderDataDqr($type, $user_id)
    {
        $where_master = [
            'user_id' => $user_id,
            'status' => $type,
            'pay_status' => '1',
            'deleted' => '0',
        ];

        $res_master = $this->field('order_id,doctor_id,hospital_id,order_num,pay_status,before_time,pay_status,pay_type,deal_amount')->where($where_master)->select();

        $order_id = null;
        $row = null;
        $rows = null;
        $data = null;
        foreach ($res_master as $value) {
            $order_id[] = $value['order_id'];

            $rows[$value['order_id']]['total_amount'] = sprintf('%0.2f',$value['deal_amount']);

            $rows[$value['order_id']]['order_id'] = $value['order_id'];
            $rows[$value['order_id']]['doctor_id'] = $value['doctor_id'];
            $rows[$value['order_id']]['hospital_id'] = $value['hospital_id'];
            $rows[$value['order_id']]['order_num'] = $value['order_num'];
            $rows[$value['order_id']]['pay_status'] = $value['pay_status'];
            $rows[$value['order_id']]['before_time'] = $value['before_time'];
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $gid = $detail['gid'];
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['num'] = $detail['num'];

            $res_goods = Db::name('goods_info')->field('images_url')->where(['id' => $gid])->find();
            $row['goods_img'] = $res_goods['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 已完成
    public function orderDataYwc($type, $user_id)
    {
        $where_master = [
            'user_id' => $user_id,
            'status' => $type,
            'pay_status' => '1',
            'deleted' => '0',
        ];

        $res_master = $this->field('order_id,doctor_id,hospital_id,order_num,pay_status,before_time,pay_status,pay_type,deal_amount')->where($where_master)->select();

        $order_id = null;
        $row = null;
        $rows = null;
        $data = null;
        foreach ($res_master as $value) {
            $order_id[] = $value['order_id'];

            $rows[$value['order_id']]['total_amount'] = sprintf('%0.2f',$value['deal_amount']);

            $rows[$value['order_id']]['order_id'] = $value['order_id'];
            $rows[$value['order_id']]['doctor_id'] = $value['doctor_id'];
            $rows[$value['order_id']]['hospital_id'] = $value['hospital_id'];
            $rows[$value['order_id']]['order_num'] = $value['order_num'];
            $rows[$value['order_id']]['pay_status'] = $value['pay_status'];
            $rows[$value['order_id']]['before_time'] = $value['before_time'];
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $gid = $detail['gid'];
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['num'] = $detail['num'];

            $res_goods = Db::name('goods_info')->field('images_url')->where(['id' => $gid])->find();
            $row['goods_img'] = $res_goods['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 订单详情:待付款、待接单
    public function orderDataDetails($status, $user_id, $pay_status, $order_num)
    {
        // 条件
        $where_details = [
            'status' => $status,
            'user_id' => $user_id,
            'pay_status' => $pay_status,
            'order_num' => $order_num,
            'deleted' => 0,
        ];

        $res_master = $this->field('user_id,order_id,order_num,address,mobile,before_time,make_time,deal_amount,pay_type,note')
            ->where($where_details)->select();
        $row = null;
        $rows = null;
        $data = null;
        $order_id = null;
        foreach ($res_master as $master) {
            $order_id[] = $master['order_id'];
            $res_user = Db::name('address')->field('name')->where(['user_id' => $user_id,'default' => 1,'deleted' => 0])->find();

            $rows[$master['order_id']]['order_id'] = $master['order_id'];
            $rows[$master['order_id']]['order_num'] = $master['order_num'];
            $rows[$master['order_id']]['address'] = $master['address'];
            $rows[$master['order_id']]['address_name'] = $res_user['name'].' '.$master['mobile'];
            $rows[$master['order_id']]['before_time'] = $master['before_time'];
            $rows[$master['order_id']]['make_time'] = $master['make_time'];
            $rows[$master['order_id']]['deal_amount'] = $master['deal_amount'];
            $rows[$master['order_id']]['note'] = $master['note'];

            if ($master['pay_type'] == 1) {
                $rows[$master['order_id']]['pay_type'] = '直接支付';
            }
            if ($master['pay_type'] == 2) {
                $rows[$master['order_id']]['pay_type'] = '砍价支付';
            }
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $res_goodsImg = Db::name('goods_info')->field('images_url')->where(['id' => $detail['gid'],'deleted' => 0])->find();
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['num'] = $detail['num'];
            $row['images_url'] = $res_goodsImg['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 订单详情:待服务、待确认、已完成
    public function orderDataDetailsDoctor($status, $user_id, $pay_status, $order_num)
    {
        // 条件
        $where_details = [
            'status' => $status,
            'user_id' => $user_id,
            'pay_status' => $pay_status,
            'order_num' => $order_num,
            'deleted' => 0,
        ];

        $res_master = $this->field('user_id,order_id,order_num,address,mobile,before_time,make_time,deal_amount,pay_type,note')
            ->where($where_details)->select();

        $row = null;
        $rows = null;
        $data = null;
        $order_id = null;
        foreach ($res_master as $master) {
            $order_id[] = $master['order_id'];
            $res_user = Db::name('address')->field('name')->where(['user_id' => $user_id,'default' => 1,'deleted' => 0])->find();

            $where_doctor = ['order_num' => $master['order_num'],'status' => 2,'is_accept' => 1];
            $res_doctor = Db::name('app_doctor_order')->field('door_doctor')->where($where_doctor)->find();

            $rows[$master['order_id']]['door_doctor'] = $res_doctor['door_doctor'];
            $rows[$master['order_id']]['order_id'] = $master['order_id'];
            $rows[$master['order_id']]['order_num'] = $master['order_num'];
            $rows[$master['order_id']]['address'] = $master['address'];
            $rows[$master['order_id']]['address_name'] = $res_user['name'].' '.$master['mobile'];
            $rows[$master['order_id']]['before_time'] = $master['before_time'];
            $rows[$master['order_id']]['make_time'] = $master['make_time'];
            $rows[$master['order_id']]['deal_amount'] = $master['deal_amount'];
            $rows[$master['order_id']]['note'] = $master['note'];

            if ($master['pay_type'] == 1) {
                $rows[$master['order_id']]['pay_type'] = '直接支付';
            }
            if ($master['pay_type'] == 2) {
                $rows[$master['order_id']]['pay_type'] = '砍价支付';
            }
        }
        $order_id_str = join(',',$order_id);

        $res_details = Db::name('order_details')->field('order_id,gid,goods_name,price,num')->where('order_id','in',"$order_id_str")->where(['deleted' => 0])->select();
        foreach ($res_details as $detail) {
            $res_goodsImg = Db::name('goods_info')->field('images_url')->where(['id' => $detail['gid'],'deleted' => 0])->find();
            $row['goods_name'] = $detail['goods_name'];
            $row['price'] = $detail['price'];
            $row['num'] = $detail['num'];
            $row['images_url'] = $res_goodsImg['images_url'];
            $rows[$detail['order_id']]['details'][] = $row;
        }
        $data = array_values($rows);

        return $data;
    }

    // 取消订单、待付款
    public function delOrderDfk($user_id, $order_num)
    {
        $where_del = [
            'user_id' => $user_id,
            'order_num' => $order_num
        ];

        $res = $this->where($where_del)->update(['deleted' => 1]);
        return $res;
    }

    // 确认订单、待确认
    public function confirmDqr($user_id, $order_num)
    {
        $where_del = [
            'user_id' => $user_id,
            'order_num' => $order_num
        ];

        $res = $this->where($where_del)->update(['status' => 5]);
        return $res;
    }

    // 已完成、评价
    public function evalNote($user_id, $order_num, $evaluate_note, $grade, $images, $doctor_id)
    {
        try{
            $data = [
                'user_id' => $user_id,
                'order_num' => $order_num,
                'evaluate_note' => $evaluate_note,
                'grade' => $grade,
                'images' => $images,
                'evaluate_time' => date("Y-m-d H:i:s",time()),
                'doctor_id' => $doctor_id,
            ];

            $res = Db::name('user_order_evaluate')->insertGetId($data);
            return $res;
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    // 获取用户对医生的评价
    public function getDoctorPj($doctor_id)
    {
        $row = null;
        $rows = null;
        // 首先获取医生信息,头像、姓名、信用分、所属医院、电话、医生证明
        $res_doctor = Db::name('app_user_doctor')->field('head_images,username,credit,id,mobile,qualifications_book')->where(['id' => $doctor_id])->find();
        $rows['head_images'] = $res_doctor['head_images'];
        $rows['username'] = $res_doctor['username'];
        $rows['credit'] = $res_doctor['credit'];
        $rows['mobile'] = $res_doctor['mobile'];
        $rows['qualifications_book'] = $res_doctor['qualifications_book'];

        $hospital_id = $res_doctor['id'];
        $res_hospital = Db::name('app_user_hospital')->field('hospital_name')->where(['id' => $hospital_id])->find();
        $rows['hospital_name'] = $res_hospital['hospital_name'];

        // 取出所有评价
        $res_evaluate = Db::name('user_order_evaluate')->field('user_id,order_num,evaluate_note,evaluate_time,images,back_note')->where(['doctor_id' => $doctor_id])->select();
        if ($res_evaluate == null) {
            return false;
        }

        $count_evaluate = count($res_evaluate);
        $rows['count_evaluate'] = $count_evaluate;
        foreach ($res_evaluate as $item) {
            $user_id = $item['user_id'];
            $res_user = Db::name('user')->field('headimg,nickname')->where(['user_id' => $user_id])->find();
            $row['headimg'] = $res_user['headimg'];
            $row['nickname'] = $res_user['nickname'];

            $row['order_num'] = $item['order_num'];
            $row['evaluate_note'] = $item['evaluate_note'];
            $row['evaluate_time'] = $item['evaluate_time'];
            $row['images'] = $item['images'];
            $row['back_note'] = $item['back_note'];
            $row['back_time'] = $item['back_time'];
            $rows['evaluate_details'][] = $row;
        }

        return $rows;

    }
}
