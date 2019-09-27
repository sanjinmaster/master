<?php

namespace app\petadmin\model;

use think\Model;

class User extends Model
{
    // 用户表
    protected $table = 'user';

    // 用户列表
    public function userList($order, $page, $pageSize,$where_status,$where_search)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where($where_status)
            ->where($where_search)
            ->where(['deleted'=>0])
            ->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->field('user_id,nickname,is_status,
        last_login_time')
            ->where($where_status)
            ->where($where_search)
            ->where(['deleted'=>0])
            ->order($order)
            ->limit($p,$pageSize)
            ->select();

        foreach ($now_page_content as $item) {
            if ($item['is_status'] == 1) {
                $item['is_status'] = '正常';
            }else {
                $item['is_status'] = '冻结';
            }
        }
        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }

    // 激活、禁用
    public function isOffNo($user_id, $is_off_no)
    {
        try{
            $data = ['is_status' => $is_off_no];
            // 此时为激活
            if ($is_off_no == 1) {
                $res = $this->where(['user_id' => $user_id])->update($data);
                return $res;
            }
            // 此时为禁用
            if ($is_off_no == 2) {
                $res = $this->where(['user_id' => $user_id])->update($data);
                return $res;
            }
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 删除
    public function deleteUser($user_id)
    {
        try{
            $data = ['deleted' => 1];
            return $this->where('user_id','in',$user_id)->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
