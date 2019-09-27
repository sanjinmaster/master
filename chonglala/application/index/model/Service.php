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
        $res_goods = $this->field('id,cid,goods_name,price,buy_num,images_url')->where('cid','in',$cid_str)->select();

        $row = null;
        $rows = null;
        foreach ($res_goods as $good) {
            $row[$good['id']]['id'] = $good['id'];
            $row[$good['id']]['cid'] = $good['cid'];
            $row[$good['id']]['goods_name'] = $good['goods_name'];
            $row[$good['id']]['price'] = $good['price'];
            $row[$good['id']]['buy_num'] = $good['buy_num'];
            $row[$good['id']]['images_url'] = $good['images_url'];
        }

        $res_cart = Db::name('shop_cart')->field('cid,gid,num')->where(['user_id' => $user_id])->select();

        foreach ($res_cart as $value) {
            $row[$value['gid']]['num'] = $value['num'];
            if ($value['cid'] == 3 || $value['cid'] == 7) {
                $rows['ym_num'] += $value['num'];
            }
            if ($value['cid'] == 4 || $value['cid'] == 8) {
                $rows['tj_num'] += $value['num'];
            }
            if ($value['cid'] == 5 || $value['cid'] == 9) {
                $rows['mr_num'] += $value['num'];
            }
            if ($value['cid'] == 6 || $value['cid'] == 10) {
                $rows['hh_num'] += $value['num'];
            }
        }
        $rows['goods'] = array_values($row);
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
    public function searchGoods($key_word)
    {
        $where = [
            'goods_name' => ['like',"%$key_word%"]
        ];
        $res = $this->field('id,cid,goods_name,price,buy_num,images_url')->where($where)->select();
        return $res;
    }
}
