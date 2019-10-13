<?php

namespace app\index\model;

use think\Db;
use think\Log;
use think\Model;

class Cart extends Model
{
    // 购物车表
    protected $table = 'shop_cart';

    // 商品加入购物车
    public function addCart($user_id, $cid, $gid)
    {
        $where_find_tal = [
            'user_id' => $user_id,
            'cid' => $cid,
            'gid' => $gid
        ];
        // 校验商品是否已经存在购物车
        $find_tal = $this->field('num')->where($where_find_tal)->find();
        if ($find_tal['num'] >= 1) {

            try{

                // 如果购物车存在商品,此时更新数量
                $num = $find_tal['num'] + 1;
                $data = [
                    'num' => $num,
                    'update_time' => date("Y-m-d H:i:s",time())
                ];
                $res = $this->where($where_find_tal)->update($data);

                return $res;
            }catch (\Exception $e) {
                return $e->getMessage();
            }
        }else {

            try{

                // 如果购物车不存在商品,此时新增
                $num = 1;
                $data = [
                    'user_id' => $user_id,
                    'cid' => $cid,
                    'gid' => $gid,
                    'num' => $num,
                    'create_time' => date("Y-m-d H:i:s",time())
                ];

                $res = $this->insertGetId($data);

                return $res;
            }catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }

    // 购物车减少商品数量
    public function reduceCart($user_id, $cid, $gid)
    {
        $where_find_tal = [
            'user_id' => $user_id,
            'cid' => $cid,
            'gid' => $gid
        ];

        // 校验商品是否已经存在购物车
        $find_tal = $this->field('num')->where($where_find_tal)->find();

        if ($find_tal['num'] > 1) {
            try{

                // 如果购物车存在商品,此时更新数量
                $num = $find_tal['num'] - 1;
                $data = [
                    'num' => $num,
                    'update_time' => date("Y-m-d H:i:s",time())
                ];
                $res = $this->where($where_find_tal)->update($data);

                return $res;
            }catch (\Exception $e) {
                return $e->getMessage();
            }
        }else {
            try{

                $res = $this->where($where_find_tal)->delete();

                return $res;
            }catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }

    // 购物车列表
    public function cartList($where)
    {
        $cart = $this->field('id,cid,gid,num')->where($where)->select();

        $data = null;
        $row = null;
        $rows = null;

        foreach ($cart as $value) {

            $where = ['id' => $value['gid'],'cid' => $value['cid']];
            $goods = Db::name('goods_info')->field('id,cid,goods_name,price,images_url')->where($where)->find();
            // 如果有上门疫苗
            if ($goods['cid'] == 3 || $goods['cid'] == 7) {
                $row_yimiao['gid'] = $goods['id'];
                $row_yimiao['cid'] = $goods['cid'];
                $row_yimiao['goods_name'] = $goods['goods_name'];
                $row_yimiao['price'] = $goods['price'];
                $row_yimiao['num'] = $value['num'];
                $row_yimiao['images_url'] = $goods['images_url'];
                $rows_yimiao[] = $row_yimiao;
                $data['yimiao'] = $rows_yimiao;
            }

            // 如果有上门体检
            if ($goods['cid'] == 4 || $goods['cid'] == 8) {
                $row_tijian['gid'] = $goods['id'];
                $row_tijian['cid'] = $goods['cid'];
                $row_tijian['goods_name'] = $goods['goods_name'];
                $row_tijian['price'] = $goods['price'];
                $row_tijian['num'] = $value['num'];
                $row_tijian['images_url'] = $goods['images_url'];
                $rows_tijian[] = $row_tijian;
                $data['tijian'] = $rows_tijian;
            }

            // 如果有上门美容
            if ($goods['cid'] == 5 || $goods['cid'] == 9) {
                $row_meirong['gid'] = $goods['id'];
                $row_meirong['cid'] = $goods['cid'];
                $row_meirong['goods_name'] = $goods['goods_name'];
                $row_meirong['price'] = $goods['price'];
                $row_meirong['num'] = $value['num'];
                $row_meirong['images_url'] = $goods['images_url'];
                $rows_meirong[] = $row_meirong;
                $data['meorong'] = $rows_meirong;
            }

            // 如果有上门火化
            if ($goods['cid'] == 6 || $goods['cid'] == 10) {
                $row_huohua['gid'] = $goods['id'];
                $row_huohua['cid'] = $goods['cid'];
                $row_huohua['goods_name'] = $goods['goods_name'];
                $row_huohua['price'] = $goods['price'];
                $row_huohua['num'] = $value['num'];
                $row_huohua['images_url'] = $goods['images_url'];
                $rows_huohua[] = $row_huohua;
                $data['huohua'] = $rows_huohua;
            }
        }

        return $data;
    }

    // 删除商品
    public function delCartGoods($where, $cid, $gid)
    {
        try{
            return $this->where($where)->where('cid','in',"$cid")->where('gid','in',"$gid")->delete();
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
