<?php

namespace app\petadmin\model;

use think\Model;

class Banner extends Model
{
    // 轮播图表
    protected $table = 'banner';

    // 添加轮播图
    public function createBanner($img_url)
    {
        $data = [
            'url' => $img_url,
            'create_time' => date('Y-m-d H:i:s')
        ];
        try{
            $res_id = $this->insertGetId($data);
            return $res_id;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 轮播图列表
    public function banList($order, $page, $pageSize)
    {
        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted' => 0])
            ->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->field('id,url,create_time')
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

    // 更换轮播图
    public function udBanner($id, $url)
    {
        $data = [
            'url' => $url,
            'update_time' => date('Y-m-d H:i:s')
        ];
        try{
            $res_id = $this->where(['id' => $id])->update($data);
            return $res_id;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 删除轮播图
    public function deleteBanner($id)
    {
        $data = [
            'deleted' => 1,
            'update_time' => date('Y-m-d H:i:s')
        ];
        try{
            $res_id = $this->where(['id' => $id])->update($data);
            return $res_id;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
