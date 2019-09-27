<?php

namespace app\petadmin\model;

use think\Model;

class Doctor extends Model
{
    // 医生表
    protected $table = 'app_user_doctor';

    // 医生列表
    public function docList($order, $page, $pageSize,$where_status,$where_search,$where_prove)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where($where_status)
            ->where($where_search)
            ->where($where_prove)
            ->where(['deleted'=>0])
            ->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->field('id,username,mobile,is_prove,is_status,
        credit')
            ->where($where_status)
            ->where($where_search)
            ->where($where_prove)
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
            if ($item['is_prove'] == 1) {
                $item['is_prove'] = '已认证';
            }
            if ($item['is_prove'] == 2) {
                $item['is_prove'] = '未认证';
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
    public function isOffNo($doctor_id, $is_off_no)
    {
        try{
            $data = ['is_status' => $is_off_no];
            // 此时为激活
            if ($is_off_no == 1) {
                $res = $this->where(['id' => $doctor_id])->update($data);
                return $res;
            }
            // 此时为禁用
            if ($is_off_no == 2) {
                $res = $this->where(['id' => $doctor_id])->update($data);
                return $res;
            }
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 删除
    public function deleteDoctor($doctor_id)
    {
        try{
            $data = ['deleted' => 1];
            return $this->where('id','in',$doctor_id)->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
