<?php

namespace app\api\model;

use think\Db;
use think\Log;
use think\Model;

class MeetOrder extends Base
{
    // 医院或者医生,抢单
    public function receiptRzDoctor($rz_doctor_id, $order_num)
    {
        // 校验此订单是否被医院或者其他认证过的医生抢了(校验节点为此张订单有医生id且已经接单)
        $res_check = Db::name('app_doctor_order')
            ->field('doctor_id,is_accept')
            ->where(['order_num' => $order_num])
            ->find();

        // 如果被其他人抢到,返回false
        if ($res_check['doctor_id'] != null && $res_check['is_accept'] == 1) {
            return false;
        }

        $doctor_name = Db::name('app_user_doctor')
            ->field('username')
            ->where(['id' => $rz_doctor_id])
            ->find();

        if ($doctor_name) {
            $data = [
                'doctor_id' => $rz_doctor_id,
                // 状态 1 待接单  2 待服务  3 待确认  4 已完成
                'status' => 2,
                'is_accept' => 1,
                'door_type' => 1,
                'door_doctor' => $doctor_name['username'],
                'receipt_time' => date("Y-m-d H:i:s",time())
            ];

            try {
                // 开启事务
                $this->startTrans();
                // 加锁
                $this->lock(true)->where('order_num',$order_num)->find();

                // 如果没有人抢,更新这个订单记录
                $res = Db::name('app_doctor_order')
                    ->where(['order_num' => $order_num])
                    ->update($data);

                // 提交事务
                $this->commit();
                return $res;
            } catch (\Exception $e) {
                $this->rollback();
                Log::error($e->getMessage());
            }
        }
    }
}
