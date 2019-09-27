<?php

namespace app\petadmin\model;

use think\Db;
use think\Model;

class Service extends Model
{
    protected $table = 'goods_info';

    // 获取商品列表
    public function getGoodsList($where,$order,$pageSize,$page)
    {

        $page = $page - 1;
        $p = $page * $pageSize;
        $nums = $this->where(['deleted'=>0])->where($where)->count();

        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }

        $now_page_content = $this->field('id,cid,goods_name,price,buy_num,goods_status')->where(['deleted'=>0])->where($where)->order($order)->limit($p,$pageSize)->select();
        foreach ($now_page_content as $item) {
            $res_class = Db::name('goods_class')->field('pid')->where(['id' => $item['cid']])->find();
            if ($res_class['pid'] == 1) {
                $item['class_name'] = '猫咪';
            }
            if ($res_class['pid'] == 2) {
                $item['class_name'] = '狗狗';
            }
        }

        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;

        return $res;
    }

    // 添加商品
    public function createGoods($param, $cid)
    {
        $data = [
          'cid' => $cid,
            'goods_name' => $param['goods_name'],
            'price' => $param['goods_price'],
            'evaluate' => $param['evaluate'],
            'images_url' => $param['images'],
            'efficacy' => $param['efficacy'],
            'apply_pet' => $param['apply_pet'],
            'about_doctor' => $param['about_doctor'],
            'about_service' => $param['about_service'],
            'service_flow' => $param['service_flow'],
            'goods_status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
        ];

        $res = $this->insertGetId($data);
        return $res;
    }

    // 编辑查看
    public function editGetOne($goods_id)
    {
        $row = null;
        $res = $this->where(['id' => $goods_id])->find();

        switch ($res['cid']) {
            case '3':
                $goods_class = '猫咪';
                break;
            case '7':
                $goods_class = '狗狗';
                break;
            case '4':
                $goods_class = '猫咪';
                break;
            case '8':
                $goods_class = '狗狗';
                break;
            case '5':
                $goods_class = '猫咪';
                break;
            case '9':
                $goods_class = '狗狗';
                break;
            case '6':
                $goods_class = '猫咪';
                break;
            case '10':
                $goods_class = '狗狗';
                break;
        }
        $row['gid'] = $res['id'];
        $row['goods_name'] = $res['goods_name'];
        $row['goods_class'] = $goods_class;
        $row['cid'] = $res['cid'];
        $row['goods_price'] = $res['price'];
        $row['goods_images'] = $res['images_url'];
        $row['evaluate'] = $res['evaluate'];
        $row['efficacy'] = $res['efficacy'];
        $row['apply_pet'] = $res['apply_pet'];
        $row['about_doctor'] = $res['about_doctor'];
        $row['about_service'] = $res['about_service'];
        $row['service_flow'] = $res['service_flow'];

        return $row;
    }

    // 编辑保存
    public function editPublic($param, $cid, $goods_id)
    {
        $data_edit = [
            'cid' => $cid,
            'goods_name' => $param['goods_name'],
            'price' => $param['goods_price'],
            'evaluate' => $param['evaluate'],
            'images_url' => $param['images'],
            'efficacy' => $param['efficacy'],
            'apply_pet' => $param['apply_pet'],
            'about_doctor' => $param['about_doctor'],
            'about_service' => $param['about_service'],
            'service_flow' => $param['service_flow'],
            'create_time' => date('Y-m-d H:i:s'),
        ];

        $res = $this->where(['id' => $goods_id])->update($data_edit);

        return $res;
    }

    // 上、下架
    public function statusXjUpdate($goods_id, $goods_status)
    {
        $data = '';
        // 上架
        if ($goods_status == 1) {
            $data = ['goods_status' => 1];
        }
        // 下架
        if ($goods_status == 2) {
            $data = ['goods_status' => 2];
        }

        $res = $this->where(['id' => $goods_id])->update($data);

        return $res;
    }

    // 删除
    public function delPublic($goods_id)
    {
        $data = ['deleted' => 1];
        $res = $this->where('id','in',$goods_id)->update($data);

        return $res;
    }
}
