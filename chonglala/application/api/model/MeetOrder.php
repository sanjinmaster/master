<?php

namespace app\api\model;

use app\common\model\DutyAction;
use think\Db;
use think\Log;
use think\Model;

class MeetOrder extends Base
{
    // 认证过的医生,抢单
    public function receiptRzDoctor($rz_doctor_id, $order_num)
    {
        // 校验此订单是否被医院或者其他认证过的医生抢了(校验节点为此张订单有医生id且已经接单)
        $res_check = Db::name('app_doctor_order')
            ->field('is_accept')
            ->where(['order_num' => $order_num])
            ->find();

        // 如果被其他人抢到,返回false
        if ($res_check['is_accept'] != null) {
            return false;
        }

        $doctor_name = Db::name('app_user_doctor')
            ->field('username')
            ->where(['id' => $rz_doctor_id])
            ->find();

        if ($doctor_name) {
            $data = [
                // 认证过的医生id
                'doctor_id' => $rz_doctor_id,
                // 状态 1 待接单  2 待服务  3 待确认  4 已完成
                'status' => 2,
                // 1 接单  2 弃单  3 超时未接单  4 未接单
                'is_accept' => 1,
                // 1 上门
                'door_type' => 1,
                // 上门医生
                'door_doctor' => $doctor_name['username'],
                // 抢单时间
                'receipt_time' => date("Y-m-d H:i:s",time())
            ];

            try {
                // 开启事务
                $this->startTrans();

                // 加锁
                Db::name('app_doctor_order')->lock(true)->where('order_num',$order_num)->find();

                // 如果没有人抢,更新这个订单记录
                $res = Db::name('app_doctor_order')
                    ->where(['order_num' => $order_num])
                    ->update($data);

                // 更新成功医院-医生订单表，更新用户订单主表
                if ($res) {

                    $order_master = [
                        // 订单状态   1 待付款  2 待接单  3 待服务  4 待确认  5 已完成  6 有退款
                        'status' => 3,
                        // 医生接单时间
                        'receipt_time' => date("Y-m-d H:i:s",time())
                    ];

                    $q = Db::name('order_master')
                        ->where(['order_num' => $order_num])
                        ->update($order_master);

                }

                // 提交事务
                $this->commit();

                return $q;
            } catch (\Exception $e) {
                $this->rollback();
                Log::error($e->getMessage());
            }
        }
    }

    // 医院抢单
    public function receiptHospital($hospital_id, $order_num)
    {
        // 校验此订单是否被医院或者其他认证过的医生抢了(校验节点为此张订单有医生id且已经接单)
        $res_check = Db::name('app_doctor_order')
            ->field('is_accept')
            ->where(['order_num' => $order_num])
            ->find();

        // 如果被其他人抢到,返回false
        if ($res_check['is_accept'] != null) {
            return false;
        }

        $data = [
            // 认证过的医生id
            'hospital_id' => $hospital_id,
            // 状态 1 待接单  2 待服务  3 待确认  4 已完成
            'status' => 2,
            // 1 接单  2 弃单  3 超时未接单  4 未接单
            'is_accept' => 1,
            // 抢单时间
            'receipt_time' => date("Y-m-d H:i:s",time())
        ];

        try {
            // 开启事务
            $this->startTrans();

            // 加锁
            Db::name('app_doctor_order')->lock(true)->where('order_num',$order_num)->find();

            // 如果没有人抢,更新这个订单记录
            $res = Db::name('app_doctor_order')
                ->where('order_num', $order_num)
                ->update($data);

            // 更新成功医院-医生订单表，更新用户订单主表
            if ($res) {

                $order_master = [
                    // 订单状态   1 待付款  2 待接单  3 待服务  4 待确认  5 已完成  6 有退款
                    'status' => 3,
                    // 医生接单时间
                    'receipt_time' => date("Y-m-d H:i:s",time())
                ];

                $q = Db::name('order_master')
                    ->where(['order_num' => $order_num])
                    ->update($order_master);

            }

            // 提交事务
            $this->commit();

            return $q;
        } catch (\Exception $e) {
            $this->rollback();
            Log::error($e->getMessage());
        }
    }

    // 医院下面的医生列表
    public function getDoctor($hospital_id)
    {
        $res = \db('app_hospital_doctor')
            ->alias('a')
            ->field('a.doctor_id,b.username,b.alias')
            ->join('app_user_doctor b','a.doctor_id = b.id')
            ->where(['a.hospital_id' => $hospital_id,'a.is_join' => 1,'b.is_status' => 1])
            ->select();

        return $res;
    }

    // 医院下面的医生接单
    public function receiptDoctor($doctor_id, $order_num)
    {
        try {
            // 开启事务
            $this->startTrans();

            $doctor_name = \db('app_user_doctor')
                ->field('username')
                ->where(['id' => $doctor_id])
                ->find();

            $data = [
                // 认证过的医生id
                'doctor_id' => $doctor_id,
                // 状态 1 待接单  2 待服务  3 待确认  4 已完成
                'status' => 2,
                // 1 上门
                'door_type' => 1,
                // 上门医生
                'door_doctor' => $doctor_name['username'],
                // 医生接单时间
                'make_doctor_time' => date("Y-m-d H:i:s",time())
            ];

            $res = \db('app_doctor_order')
                ->where('order_num', $order_num)
                ->update($data);

            // 提交
            $this->commit();
            return $res;
        } catch (\Exception $e) {
            // 回滚
            $this->rollback();
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }
}
