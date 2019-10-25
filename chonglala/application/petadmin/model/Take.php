<?php

namespace app\petadmin\model;

use think\Db;
use think\Model;

class Take extends Model
{
    // 奖励金提现表
    protected $table = 'reward_take_out';

    // 提现列表
    public function taList($order, $page, $pageSize, $where_status, $where_ali_pay)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted' => 0])
            ->alias('a')
            ->join('user_alipay b','a.user_id = b.user_id')
            ->where($where_status)
            ->where($where_ali_pay)
            ->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->field('a.id,a.user_id,b.alipay,a.take_out_time,a.status')
            ->alias('a')
            ->join('user_alipay b','a.user_id = b.user_id')
            ->where(['a.deleted' => 0])
            ->where($where_status)
            ->where($where_ali_pay)
            ->order($order)
            ->limit($p,$pageSize)
            ->select();

        foreach ($now_page_content as $value) {
            $res_user = Db::name('user')->field('nickname')->where(['user_id' => $value['user_id']])->where(['deleted' => 0])->find();
            $value['nickname'] = $res_user['nickname'];
            if ($value['status'] == 1) {
                $value['status'] = '提现成功';
            }
            if ($value['status'] == 2) {
                $value['status'] = '拒绝提现';
            }
            if ($value['status'] == 3) {
                $value['status'] = '待操作';
            }
        }

        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }

    // 同意
    public function agr($take_out_id)
    {
        try {
            return $this->where(['id' => $take_out_id])->update(['status' => 1]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 拒绝
    public function agrNo($take_out_id)
    {
        try {
            return $this->where(['id' => $take_out_id])->update(['status' => 2]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 删除
    public function deleteTake($take_put_id)
    {
        try{
            $data = ['deleted' => 1];
            return $this->where(['id' => $take_put_id])->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
