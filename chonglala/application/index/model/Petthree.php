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
        $res = $this->field('id,consultation_title,consultation_details_url')->where(['deleted' => 0,'is_up_down' => 1])->select();

        if ($res == null) {
            return null;
        }

        $row = null;
        $rows = null;
        foreach ($res as $item) {
            $row['id'] = $item['id'];
            $row['consultation_title'] = $item['consultation_title'];
            $row['consultation_details_url'] = $this->readTxtFile($item['consultation_details_url']);
            $rows[] = $row;
        }

        return $rows;
    }

    // 宠物字典详情
    public function Details($pet_id)
    {
        $res = $this->field('id,consultation_title,consultation_details_url')->where(['deleted' => 0,'id' => $pet_id])->find();

        $row['id'] = $res['id'];
        $row['consultation_title'] = $res['consultation_title'];
        $row['consultation_details_url'] = $this->readTxtFile($res['consultation_details_url']);

        return $row;
    }

    // 咨询列表
    public function speakList($order, $keyword, $page, $pageSize)
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

    // 字典列表
    public function zdList($keyword, $id)
    {
        $now_page_content = $res_title = $this->field('id,consultation_title')
            ->where('consultation_title','like',"%$keyword%")
            ->where(['deleted' => 0,'class_id' => $id])
            ->select();

        return $now_page_content;
    }

    /**
     * 读取txt文件内容
     */
    public function readTxtFile($url)
    {
        if (!empty($url)) {
            $str = file_get_contents($url);
            $str = iconv("utf-16le", "utf-8//IGNORE",$str);
            $str = substr($str,3);
            return $str;
        }
    }
}
