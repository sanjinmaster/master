<?php

namespace app\petadmin\model;

use think\Model;

class Idea extends Model
{
    // 意见表
    protected $table = 'feedback';

    // 意见列表
    public function idList($order, $page, $pageSize)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted' => 0])
            ->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->field('id,feedback_note,create_time')
            ->where(['deleted' => 0])
            ->order($order)
            ->limit($p,$pageSize)
            ->select();

        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }

    // 获取一条意见内容
    public function getOneIdea($feedback_id)
    {
        $res = $this->field('feedback_note')->where(['id' => $feedback_id])->find();
        if ($res == null) { return false; }
        return $res;
    }

    // 删除
    public function deleteIdea($feedback_id)
    {
        try{
            $data = ['deleted' => 1];
            return $this->where('id','in',$feedback_id)->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
