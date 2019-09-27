<?php

namespace app\petadmin\model;

use think\Db;
use think\Model;

class Means extends Model
{
    // 知识点资料表
    protected $table = 'consultation_details';

    //添加知识点资料
    public function addZl($class_id, $consultation_title, $consultation_details)
    {
        try{
            $data = [
              'class_id' => $class_id,
              'consultation_title' => $consultation_title,
              'consultation_details_url' => $consultation_details,
              'create_time' => date("Y-m-d H:i:s",time()),
            ];
            $res = $this->insertGetId($data);
            return $res;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 知识点列表
    public function MeansZsList($order, $page, $pageSize)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted'=>0])->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->field('id,class_id,consultation_title,
        consultation_details_url,create_time,is_up_down')
            ->where(['deleted'=>0])
            ->order($order)
            ->limit($p,$pageSize)
            ->select();

        $row = null;
        $class_id = null;
        foreach ($now_page_content as $item) {
            $details = Db::name('consultation_class')
                ->field('class_name')
                ->where(['id' => $item['class_id']])
                ->find();
            $item['class_name'] = $details['class_name'];
        }

        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }

    // 上、下架
    public function isFrame($id, $is_up_down)
    {
        $is_frame = $this->where(['is_up_down' => 1,'deleted' => 0])->count();
        // 校验首页上架商品是否已有三个
        if ($is_up_down == 1) {

            if ($is_frame < 3) {

                $data = ['is_up_down' => 1];
                $res = $this->where(['id' => $id])->update($data);

                return $res;
            }else {
                return false;
            }
        }

        if ($is_up_down == 2) {
            $data = ['is_up_down' => 2];
            $res = $this->where(['id' => $id])->update($data);

            return $res;
        }
    }

    // 编辑查看
    public function getEdit($id)
    {
        $res = $this->field('class_id,consultation_title,consultation_details_url')
            ->where(['id' => $id])
            ->find();

        $class = Db::name('consultation_class')
            ->field('class_name')
            ->where(['id' => $res['class_id']])
            ->find();
        $res['class_name'] = $class['class_name'];
        $res['consultation_details_url'] = $this->readTxtFile($res['consultation_details_url']);

        return $res;
    }

    // 编辑保存
    public function saveEdit($id, $data)
    {
        try{
            $res = $this->where(['id' => $id])->update($data);
            return $res;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 删除
    public function delZl($id)
    {
        try{
            $data = ['deleted' => 1];
            return $this->where('id','in',$id)->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 读取txt文件内容
     */
    public function readTxtFile($url)
    {
        $str = file_get_contents($url);
        $str = iconv("utf-16le", "utf-8//IGNORE",$str);
        $str = substr($str,3);
        return $str;
    }
}
