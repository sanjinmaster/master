<?php

namespace app\index\model;

use think\Db;
use think\Log;
use think\Model;

class My extends Model
{
    // 用户表
    protected $table = 'user';

    // 获取我的头部信息
    public function getHeadInfo($user_id)
    {
        $where_user = [
            'user_id' => $user_id
        ];

        $res = $this->field('headimg,nickname,total_reward,next_num,coupon_num')->where($where_user)->select();
        return $res;
    }

    // 下级详情
    public function getNextInfo($user_id)
    {
        $where_user = [
            'user_id' => $user_id
        ];

        $res = Db::name('user_relation')->field('next_id,create_time')->where($where_user)->select();

        if ($res == null) {
            return null;
        }

        $next_id = null;
        $row = null;
        foreach ($res as $item) {
            $next_id[] = $item['next_id'];
            $row[$item['next_id']]['create_time'] = $item['create_time'];
        }
        $next_str_id = join(',',$next_id);

        $res_user = Db::name('user')->field('nickname,headimg')->where('id','in',$next_str_id)->select();
        foreach ($res_user as $value) {
            $row[$value['id']]['nickname'] = $value['nickname'];
            $row[$value['id']]['headimg'] = $value['headimg'];
        }

        return array_values($row);
    }

    // 分享
    public function shareInfo()
    {

    }

    // 优惠券列表
    public function getCouponInfo($user_id, $status)
    {
        $res_user_coupon = Db::name('user_coupon')->where(['user_id' => $user_id,'status' => $status])->select();
        if ($res_user_coupon == null) {
            return null;
        }

        $coupon_id = null;
        foreach ($res_user_coupon as $item) {
            $coupon_id[] = $item['coupon_id'];
        }

        $coupon_id_str = join(',',$coupon_id);

        $res_coupon = Db::name('coupon')->where('coupon_id' ,'in', "$coupon_id_str")->where(['deleted' => 0])->select();

        $row = null;
        $rows = null;
        foreach ($res_coupon as $value) {
            $row['coupon_name'] = $value['coupon_name'];
            $row['coupon_id'] = $value['coupon_id'];
            $row['full'] = $value['full'];
            $row['cut'] = $value['cut'];

            $create_time = date("Y-m-d",strtotime($value['create_time']));
            $row['create_time'] = $create_time;
            $row['guoqi_time'] = date("Y-m-d",strtotime("$create_time + 3 month"));
            // 当状态为未使用时去更新
            if ($status == 2) {
                if ($row['guoqi_time'] == date("Y-m-d")) {
                    // 如果过期时间等于当前时间更新优惠券状态为过期
                    Db::name('user_coupon')->where(['user_id' => $user_id,'coupon_id' => $value['coupon_id']])->update(['status' => 3]);
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    // 奖励金详情
    public function getRewardInfo($user_id)
    {
        $row = null;
        $rows = null;

        $res_user = Db::name('user')->field('total_reward')->where(['user_id' => $user_id])->find();
        $res_alipay = Db::name('user_alipay')->field('alipay')->where(['user_id' => $user_id])->find();
        // 可提现奖励金
        $total_reward = $res_user['total_reward'];
        $rows['alipay'] = $res_alipay['alipay'];
        $rows['total_reward'] = $total_reward;

        // 提现记录
        $res_reward_take = Db::name('reward_take_out')->field('take_out_amount,take_out_time,status')->where(['user_id' => $user_id,'deleted' => 0])->limit(0,3)->select();
        if ($res_reward_take == null) {
            return $rows;
        }

        foreach ($res_reward_take as $item) {
            if ($item['status'] == 3) {
                $row['take_amount_info'] = "获得奖励金";
                $row['take_out_amount'] = $item['take_out_amount'];
                $row['take_out_time'] = $item['take_out_time'];
            }

            if ($item['status'] == 1) {
                $row['take_amount_info'] = "提现至支付宝";
                $row['take_out_amount'] = $item['take_out_amount'];
                $row['take_out_time'] = $item['take_out_time'];
            }
            $rows['reward_take'][] = $row;
        }

        return $rows;
    }

    // 提现奖励金
    public function takeRewardInfo()
    {

    }

    // 奖励金收支明细
    public function getMoneyInfo($user_id)
    {
        // 提现记录
        $res_reward_take = Db::name('reward_take_out')->where(['user_id' => $user_id,'deleted' => 0])->select();
        $row = null;
        $rows = null;
        foreach ($res_reward_take as $item) {
            if ($item['status'] == 3) {
                $row['take_amount_info'] = "获得奖励金";
                $row['take_out_amount'] = $item['take_out_amount'];
                $row['take_out_time'] = $item['take_out_time'];
            }

            if ($item['status'] == 1) {
                $row['take_amount_info'] = "提现至支付宝";
                $row['take_out_amount'] = $item['take_out_amount'];
                $row['take_out_time'] = $item['take_out_time'];
            }

            $rows['reward_take'][] = $row;
        }

        return $rows;
    }

    // 绑定支付宝账号
    public function addAliPay($user_id, $alipay)
    {
        // 添加之前检验用户是否已经添加过支付宝账号
        $res_ver = Db::name('user_alipay')->where(['user_id' => $user_id])->find();
        if ($res_ver != null) {
            return false;
        }

        try{

            $data = [
                'user_id' => $user_id,
                'alipay' => $alipay,
                'create_time' => date("Y-m-d H:i:s",time())
            ];

            $res_ali = Db::name('user_alipay')->insertGetId($data);

            return $res_ali;
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public function editAliPay($user_id, $alipay)
    {
        return Db::name('user_alipay')->where(['user_id' => $user_id])->update(['alipay' => $alipay,'update_time' => date("Y-M-D h:i:s",time())]);
    }

    // 意见反馈
    public function feedback($user_id, $feedback_note)
    {
        try{

            $data = [
                'user_id' => $user_id,
                'feedback_note' => $feedback_note,
                'create_time' => date("Y-m-d H:i:s",time())
            ];

            return Db::name('feedback')->insertGetId($data);
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }
}
