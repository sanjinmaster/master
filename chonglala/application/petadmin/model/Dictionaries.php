<?php

namespace app\petadmin\model;

use think\Db;
use think\Model;

class Dictionaries extends Model
{
    // 知识点分类表
    protected $table = 'consultation_class';

    // 添加字典咨询分类
    public function addZdClass($zd_class_name)
    {
        try{
            $data = [
                'class_name' => $zd_class_name,
                'create_time' => date("Y-m-d H:i:s",time()),
            ];
            $res = $this->insertGetId($data);
            return $res;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    // 分类列表
    public function zdClassList($order, $page, $pageSize)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted'=>0])->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }
        $now_page_content = $this->where(['deleted'=>0])->order($order)->limit($p,$pageSize)->select();
        foreach ($now_page_content as $item) {
            $details = Db::name('consultation_details')->where(['deleted'=>0])->where(['class_id' => $item['id']])->count();
            $item['content_num'] = $details;
        }
        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;
        return $res;
    }

    // 编辑查看
    public function findZd($id)
    {
        return $this->where(['id' => $id])->find();
    }

    // 编辑保存
    public function updateZd($id, $class_name)
    {
        try{
            $data = ['class_name' => $class_name];
            return $this->where(['id' => $id])->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 删除
    public function deleteZd($id)
    {
        try{
            $data = ['deleted' => 1];
            $res = $this->where('id','in',$id)->update($data);
            Db::name('consultation_details')->where('class_id','in',$id)->update($data);
            return $res;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
