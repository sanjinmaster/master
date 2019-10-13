<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Service extends Model
{
    // 商品表
    protected $table = 'goods_info';

    // 商品列表
    public function getGoods($cid_str, $user_id)
    {
        $res_goods = $this->field('id,cid,goods_name,price,buy_num,images_url')->where(['deleted' => 0])->where('cid','in',$cid_str)->select();

        $row = null;
        $rows = null;
        $data = null;
        foreach ($res_goods as $good) {
            $value = Db::name('shop_cart')->field('cid,gid,num')->where(['user_id' => $user_id,'gid' => $good['id'],'cid' => $good['cid']])->find();

            $row['id'] = $good['id'];
            $row['cid'] = $good['cid'];
            $row['goods_name'] = $good['goods_name'];
            $row['price'] = $good['price'];
            $row['num'] = $value['num'];
            $row['buy_num'] = $good['buy_num'];
            $row['images_url'] = $good['images_url'];
            $rows['total_num'] += $value['num'];
            $data[] = $row;
        }

        $rows['goods'] = $data;
        return $rows;
    }

    // 商品详情
    public function getGoodsDetails($gid)
    {
        $res = $this->field('id,cid,goods_name,price,images_url,
        buy_num,highest_bargain,evaluate,efficacy,apply_pet,about_doctor,about_service,service_flow')
        ->where(['id' => $gid])->find();
        return $res;
    }

    // 搜索商品
    public function searchGoods($user_id, $key_word)
    {
        $where = [
            'goods_name' => ['like',"%$key_word%"]
        ];
        $res = $this->field('id,cid,goods_name,price,buy_num,images_url')->where($where)->select();
        if ($res == null) {
            return null;
        }

        $row = null;
        $rows = null;
        $gid = null;
        foreach ($res as $item) {
            $gid[] = $item['id'];

            $row[$item['id']]['id'] = $item['id'];
            $row[$item['id']]['cid'] = $item['cid'];
            $row[$item['id']]['goods_name'] = $item['goods_name'];
            $row[$item['id']]['price'] = $item['price'];
            $row[$item['id']]['buy_num'] = $item['buy_num'];
            $row[$item['id']]['images_url'] = $item['images_url'];
        }
        $gid_str = join(',',$gid);

        $res_cart = Db::name('shop_cart')->field('gid,num')->where(['user_id' => $user_id])->where('gid','in',$gid_str)->select();
        foreach ($res_cart as $value) {
            $row[$value['gid']]['num'] = $value['num'];
        }

        $rows = array_values($row);
        return $rows;
    }
}
