<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Petthree extends Model
{
    // 宠物字典表
    protected $table = 'consultation_details';

    // 首页默认三个
    public function indexDefault()
    {
        return $this->field('id,consultation_title,consultation_details_url')->where(['deleted' => 0,'is_up_down' => 1])->select();
    }

    // 宠物字典详情
    public function Details($pet_id)
    {
        return $this->field('id,consultation_title,consultation_details_url')->where(['deleted' => 0,'id' => $pet_id])->find();
    }

    // 咨询列表
    public function speakList($keyword, $page, $pageSize)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted'=>0])->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $res_title = Db::name('consultation_class')->field('id,class_name')
            ->where('class_name','like',"%$keyword%")
            ->where(['deleted' => 0])
            ->select();

        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }

    // 字典列表
    public function zdList($keyword, $page, $pageSize, $id)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted'=>0])->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $res_title = $this->field('id,consultation_title')
            ->where('consultation_title','like',"%$keyword%")
            ->where(['deleted' => 0,'class_id' => $id])
            ->select();

        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }
}
