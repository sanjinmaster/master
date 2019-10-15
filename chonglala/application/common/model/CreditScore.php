<?php

namespace app\common\model;

use app\common\controller\Base;
use think\Db;
use think\Log;
use think\Model;

class CreditScore extends Model
{
    // 派单三个人
    public function creditScore($result)
    {
        // 派单三个人
        $three_person = CreditScore::getCredit($result);
        // 订单编号
        $order_num = $result['out_trade_no'];
        $row['three_person'] = $three_person;
        $row['order_num'] = $order_num;
        return $row;
    }

    // 根据规则计算出医院和医生的距离、信用分,用于派单
    public static function getCredit($result)
    {
        $row = null;
        $rows = null;
        $data = null;

        // 分别获取用户的地址和医生的地址计算之间的距离

        // 订单编号
        $out_trade_no = $result['out_trade_no'];
        $res_user_id = Db::name('order_master')->field('before_time,user_id')->where(['order_num' => $out_trade_no,'deleted' => 0])->find();

        if ($res_user_id) {
            $user_id = $res_user_id['user_id'];
            $res_address = Db::name('address')->field('name,area,address')->where(['user_id' => $user_id,'default' => 1,'deleted' => 0])->find();
            // 下单用户的服务地址
            $user_address = $res_address['area'].$res_address['address'];
            $user_city = $res_address['area'];

            $Base = new Base();
            $user_gap = $Base->getLatLong($user_address);

            $where_doctor = [
                // 工作中
                'is_work' => 1,
                // 用户状态为正常
                'is_status' => 1,
                // 已认证
                'is_prove' => 1,
                'deleted' => 0
            ];

            $like = "`address` like '%$user_city%' ";

            // 取信用分最高的前两个医生
            $res_doctor = Db::name('app_user_doctor')->field('id,credit,address,alias')->where($where_doctor)->where($like)->limit(0,2)->select();
            foreach ($res_doctor as $value) {
                $row['doctor_id'] = $value['id'];
                $row['credit'] = $value['credit'];
                $row['alias'] = $value['alias'];
                $doctor_gap = $Base->getLatLong($value['address']);
                $row['gap'] = $Base->getDistance($doctor_gap,$user_gap);
                $data[] = $row;
            }

            $where_hospital = [
                // 工作中
                'is_work' => 1,
                // 用户状态为正常
                'is_status' => 1,
                'deleted' => 0
            ];

            // 取信用分最高的前两个医院
            $res_hospital = Db::name('app_user_hospital')->field('id,credit,address,alias')->where($where_hospital)->where($like)->limit(0,2)->select();
            foreach ($res_hospital as $item) {
                $rows['hospital_id'] = $item['id'];
                $rows['credit'] = $item['credit'];
                $rows['alias'] = $item['alias'];
                $hospital_gap = $Base->getLatLong($item['address']);
                $rows['gap'] = $Base->getDistance($hospital_gap,$user_gap);
                $data[] = $rows;
            }

            // 先进行距离排序
            $gap = array_column($data, 'gap');
            $desc_gap = array_multisort($gap,SORT_DESC,$data);
            if ($desc_gap) {
                // 在进行信用分排序
                $credit = array_column($data, 'credit');
                array_multisort($credit,SORT_DESC,$data);
                // 取出的是四个,去掉多余的一个
                unset($data[3]);

                // 去除多余数组,只要医生或者医院id
                unset($data[0]['credit']);
                unset($data[0]['gap']);
                unset($data[1]['credit']);
                unset($data[1]['gap']);
                unset($data[2]['credit']);
                unset($data[2]['gap']);
            }

            return $data;
        }
    }

    // 派单表
    public function makeOrder($order_num)
    {
        try{
            $res = Db::name('app_doctor_order')
                ->insertGetId(['order_num' => $order_num, 'status' => 1,'door_type' => 1,'create_time' => date("Y-m-d H:i:s",time())]);
            return $res;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

}
