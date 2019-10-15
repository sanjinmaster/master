<?php

namespace app\index\model;

use think\Db;
use think\Log;
use think\Model;

class Bargain extends Model
{
    // 砍价表
    protected $table = '';

    // 用于商品列表、购物车
    public function goodsCart($order_num, $param)
    {
        $data = [
            'order_num' => $order_num,
            'user_id' => $param['user_id'],
            'mobile' => $param['mobile'],
            'address' => $param['address'],
            'status' => 1,
            'pay_status' => 2,
            'total_amount' => $param['total_amount'],
            'make_time' => date("Y-m-d H:i:s",time()),
            'before_time' => $param['before_time'],
            'pay_type' => 2,
            'note' => $param['order_note'],
            'type' => 1,
        ];

        try{
            // 开启事务
            $this->startTrans();

            // 生成待支付订单
            $order_id = Db::name('order_master')->insertGetId($data);

            // 获取商品id,字符串 1,2,3...
            $gid_str = trim($param['gid']);

            // 拿出购物车中商品数量
            $shop_cart = Db::name('shop_cart')->field('gid,num')->where(['user_id' => $param['user_id']])->where('gid','in',"$gid_str")->select();

            $row = null;
            $rows = null;
            foreach ($shop_cart as $value) {
                // 放入购物车中商品的数量
                $row[$value['gid']]['num'] = $value['num'];
            }

            // 取出商品信息
            $goods_info = Db::name('goods_info')->field('id,goods_name,price')->where('id','in',"$gid_str")->select();
            foreach ($goods_info as $item) {
                // 放入商品信息
                $row[$item['id']]['goods_name'] = $item['goods_name'];
                $row[$item['id']]['order_num'] = $order_num;
                $row[$item['id']]['order_id'] = $order_id;
                $row[$item['id']]['price'] = $item['price'];
                $row[$item['id']]['gid'] = $item['id'];
            }

            // 组装order_details表所需信息
            $rows = array_values($row);

            // 遍历插入订单详情表
            foreach ($rows as $details) {
                $order_data = [
                    'order_id' => $details['order_id'],
                    'gid' => $details['gid'],
                    'goods_name' => $details['goods_name'],
                    'price' => sprintf('%0.2f',$details['price']),
                    'num' => $details['num'],
                    'order_num' => $details['order_num'],
                    'create_time' => date("Y-m-d H:i:s",time()),
                ];
                Db::name('order_details')->insertGetId($order_data);
            }

            // 生成订单,删除购物车中商品
            Db::name('shop_cart')->where(['user_id' => $param['user_id']])->where('gid','in',"$gid_str")->delete();

            $bar_data = [
                'user_id' => $param['user_id'],
                'order_num' => $order_num,
                'before_price' => sprintf('%0.2f', $param['total_amount']),
                'present_price' => sprintf('%0.2f', $param['total_amount']),
                'already_price' => 0,
                'highest_price' => sprintf('%0.2f', $param['total_amount'] * 0.2),
                'create_time' => date("Y-m-d H:i:s",time())
            ];
            // 新增用户砍价记录
            $bargain_id = Db::name('user_bargain')->insertGetId($bar_data);

            // 提交事务
            $this->commit();
            $q['bargain_id'] = $bargain_id;
            $q['order_num'] = $order_num;
            return $q;
        }catch (\Exception $e) {
            // 回滚
            $this->rollback();
            Log::error($e->getMessage());
            return $e->getMessage();
        }

    }

    // 用于商品详情
    public function goodsDetails($order_num, $param)
    {
        $data = [
            'order_num' => $order_num,
            'user_id' => $param['user_id'],
            'mobile' => $param['mobile'],
            'address' => $param['address'],
            'status' => 1,
            'pay_status' => 2,
            'total_amount' => $param['total_amount'],
            'make_time' => date("Y-m-d H:i:s",time()),
            'before_time' => $param['before_time'],
            'pay_type' => 2,
            'note' => $param['order_note'],
            'type' => 2,
        ];

        try{
            // 开启事务
            $this->startTrans();

            // 生成待支付订单
            $order_id = Db::name('order_master')->insertGetId($data);
            if ($order_id) {
                $gid = trim($param['gid']);

                // 取出商品信息
                $goods_info = Db::name('goods_info')->field('id,goods_name,price')->where(['id' => $gid])->find();

                if ($goods_info != null) {
                    $row['order_id'] = $order_id;
                    $row['gid'] = $goods_info['id'];
                    $row['goods_name'] = $goods_info['goods_name'];
                    $row['price'] = $goods_info['price'];
                    $row['num'] = 1;
                    $row['order_num'] = $order_num;
                    $row['create_time'] = date("Y-m-d H:i:s",time());

                    Db::name('order_details')->insertGetId($row);
                }
            }

            $bar_data = [
                'user_id' => $param['user_id'],
                'order_num' => $order_num,
                'before_price' => $param['total_amount'],
                'present_price' => $param['total_amount'],
                'already_price' => 0,
                'highest_price' => sprintf('%0.2f',$param['total_amount'] * 0.2),
                'create_time' => date("Y-m-d H:i:s",time())
            ];
            // 新增用户砍价记录
            $bargain_id = Db::name('user_bargain')->insertGetId($bar_data);

            // 提交事务
            $this->commit();
            $q['bargain_id'] = $bargain_id;
            $q['order_num'] = $order_num;
        }catch (\Exception $e) {
            // 回滚
            $this->rollback();
            Log::error($e->getMessage());
            return $e->getMessage();
        }

    }

    // 获取砍价页面所需信息
    public function getBargainInfo($user_id, $bargain_id)
    {
        $row = null;
        // 根据user_id获取头像、昵称
        $res_user = Db::name('user')->field('headimg,nickname')->where(['user_id' => $user_id])->find();

        if ($res_user == null) {
            return null;
        }

        // 头像
        $row['headimg'] = $res_user['headimg'];
        // 昵称
        $row['nickname'] = $res_user['nickname'];

        // 根据order_num获取商品订单信息
        $res_order = Db::name('user_bargain')->field('id,order_num,before_price,present_price,already_price,highest_price')->where(['id' => $bargain_id])->find();

        if ($res_order == null) {
            return null;
        }

        // 砍价榜
        $bargain_details = Db::name('bargain_details')
            ->field('a.user_id,a.bargain_amount,b.nickname,b.headimg')
            ->alias('a')
            ->join('user b','a.user_id = b.user_id')
            ->where('a.bargain_id',$res_order['id'])
            ->select();

        if ($res_order == null) {
            return null;
        }

        foreach ($bargain_details as $detail) {
            $rows['headimg'] = $detail['headimg'];
            $rows['nickname'] = $detail['nickname'];
            $rows['bargain_amount'] = $detail['bargain_amount'];

            $row['bargain_bang'][] = $rows;
        }

        // 原价
        $row['before_price'] = sprintf('%0.2f',$res_order['before_price']);
        // 砍价过后金额，此时还没有砍价
        $row['present_price'] = sprintf('%0.2f',$res_order['present_price']);
        // 最高可砍
        $row['highest_price'] = sprintf('%0.2f',$res_order['highest_price']);
        // 已砍金额,此时还没有产生砍价
        $row['already_price'] = sprintf('%0.2f',$res_order['already_price']);
        $row['order_num'] = $res_order['order_num'];
        $row['id'] = $res_order['id'];

        return $row;
    }

    // 分享砍价
    public function makeBar($openid, $order_num, $user_bargain_id, $before_price, $highest_price)
    {
        // 根据openid去user表找到参与砍价用户
        $res_user = Db::name('user')->field('user_id')->where(['openid' => $openid])->find();

        if ($res_user) {
            $user_id = $res_user['user_id'];

            // 校验此用户是否已经砍过一次
            $res_kj = Db::name('bargain_details')->field('user_id')->where(['user_id' => $user_id,'bargain_id' => $user_bargain_id,])->find();

            if ($res_kj == null) {
                // 根据规则,每次随机产生的砍价金额
                $bargain_amount = $this->onceMakeBargain($user_bargain_id, $highest_price);

                // 新增砍价记录bargain_details表
                $data_bargain = [
                    'bargain_id' => $user_bargain_id,
                    'user_id' => $user_id,
                    'bargain_amount' => $bargain_amount,
                    'create_time' => date("Y-m-d H:i:s",time())
                ];

                $res_bar_details = Db::name('bargain_details')->insertGetId($data_bargain);
                if ($res_bar_details) {

                    $res_make = Db::name('bargain_details')->field('user_id,bargain_id,bargain_amount')->where(['bargain_id' => $user_bargain_id])->select();
                    $already_price = null;
                    $row = null;
                    $rows = null;

                    foreach ($res_make as $value) {
                        // 累加得到已砍的金额
                        $already_price += $value['bargain_amount'];
                        $row[$value['user_id']]['bargain_amount'] = $value['bargain_amount'];
                    }
                    // 现价
                    $present_price = sprintf('%0.2f',$before_price - $already_price);
                    $data_price = [
                        'already_price' => $already_price,
                        'present_price' => sprintf('%0.2f',$present_price)
                    ];

                    // 更新user_bargain表信息
                    $res_bargain = Db::name('user_bargain')->where(['order_num' => $order_num])->update($data_price);

                    if ($res_bargain) {

                        $rows['already_price'] = $already_price;
                        $rows['present_price'] = $present_price;

                        // 砍价榜
                        $user = Db::name('user')->field('user_id,nickname,headimg')->select();

                        foreach ($user as $item) {
                            if ($row[$item['user_id']]['bargain_amount']) {
                                $row[$item['user_id']]['nickname'] = $item['nickname'];
                                $row[$item['user_id']]['headimg'] = $item['headimg'];
                            }
                        }

                        $rows['bargain_bang'] = array_values($row);

                        return $rows;
                    }
                }
            }else {
                return 'fail';
            }
        }
    }

    // 每次砍价随机金额
    protected function onceMakeBargain($user_bargain_id, $highest_price)
    {
        // 前7个好友随机分2%，第8刀砍大红包2%，9刀-27刀随机分2%，第28刀4%，28刀-57刀随机分2%，第58刀3%，后面随机88刀砍5%。最高可减免20%。
        $res_user_count = Db::name('bargain_details')->where(['bargain_id' => $user_bargain_id])->count();

        if ($res_user_count >= 0 && $res_user_count <= 6) {
            // 本次条件下最多发生金额
            $total_once_amount = sprintf('%0.2f',($highest_price * 0.2));
            $amount = $this->bargainBl($res_user_count,$total_once_amount);
            return $amount;
        }

        if ($res_user_count == 7) {
            // 本次条件下最多发生金额
            $total_once_amount = sprintf('%0.2f',($highest_price * 0.2));
            return $total_once_amount;
        }

        if ($res_user_count >= 8 && $res_user_count <= 26) {
            // 本次条件下最多发生金额
            $total_once_amount = sprintf('%0.2f',($highest_price * 0.2));
            $amount = $this->bargainBl($res_user_count,$total_once_amount);
            return $amount;
        }

        if ($res_user_count == 27) {
            // 本次条件下最多发生金额
            $total_once_amount = sprintf('%0.2f',($highest_price * 0.4));
            return $total_once_amount;
        }
        if ($res_user_count >= 28 && $res_user_count < 56) {
            // 本次条件下最多发生金额
            $total_once_amount = sprintf('%0.2f',($highest_price * 0.2));
            $amount = $this->bargainBl($res_user_count,$total_once_amount);
            return $amount;
        }

        if ($res_user_count == 57) {
            // 本次条件下最多发生金额
            $total_once_amount = sprintf('%0.2f',($highest_price * 0.3));
            return $total_once_amount;
        }

        if ($res_user_count >= 58 && $res_user_count <= 87) {
            // 本次条件下最多发生金额
            $total_once_amount = sprintf('%0.2f',($highest_price * 0.5));
            $amount = $this->bargainBl($res_user_count,$total_once_amount);
            return $amount;
        }
    }

    // 砍价比例
    protected function bargainBl($res_user_count,$total_once_amount)
    {
        // 前7个好友随机分2%
        if ($res_user_count == 0) {
            return $total_once_amount * 0.2;
        }
        if ($res_user_count == 1) {
            return $total_once_amount * 0.01;
        }
        if ($res_user_count == 2) {
            return $total_once_amount * 0.4;
        }
        if ($res_user_count == 3) {
            return $total_once_amount * 0.07;
        }
        if ($res_user_count == 4) {
            return $total_once_amount * 0.04;
        }
        if ($res_user_count == 5) {
            return $total_once_amount * 0.09;
        }
        if ($res_user_count == 6) {
            return $total_once_amount * 0.19;
        }

        // 9刀-27刀随机分2%
        if ($res_user_count == 8) {
            return $total_once_amount * 0.03;
        }
        if ($res_user_count == 9) {
            return $total_once_amount * 0.006;
        }
        if ($res_user_count == 10) {
            return $total_once_amount * 0.007;
        }
        if ($res_user_count == 11) {
            return $total_once_amount * 0.002;
        }
        if ($res_user_count == 12) {
            return $total_once_amount * 0.007;
        }
        if ($res_user_count == 13) {
            return $total_once_amount * 0.009;
        }
        if ($res_user_count == 14) {
            return $total_once_amount * 0.001;
        }
        if ($res_user_count == 15) {
            return $total_once_amount * 0.003;
        }
        if ($res_user_count == 16) {
            return $total_once_amount * 0.007;
        }
        if ($res_user_count == 17) {
            return $total_once_amount * 0.01;
        }
        if ($res_user_count == 18) {
            return $total_once_amount * 0.004;
        }
        if ($res_user_count == 19) {
            return $total_once_amount * 0.02;
        }
        if ($res_user_count == 20) {
            return $total_once_amount * 0.03;
        }
        if ($res_user_count == 21) {
            return $total_once_amount * 0.01;
        }
        if ($res_user_count == 22) {
            return $total_once_amount * 0.1;
        }
        if ($res_user_count == 23) {
            return $total_once_amount * 0.06;
        }
        if ($res_user_count == 24) {
            return $total_once_amount * 0.05;
        }
        if ($res_user_count == 25) {
            return $total_once_amount * 0.5;
        }
        if ($res_user_count == 26) {
            return $total_once_amount * 0.144;
        }

        // 28刀-57刀随机分2%
        if ($res_user_count == 27) {
            return sprintf('%0.2f',$total_once_amount * 0.03);
        }
        if ($res_user_count == 28) {
            return sprintf('%0.2f',$total_once_amount * 0.006);
        }
        if ($res_user_count == 29) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 30) {
            return sprintf('%0.2f',$total_once_amount * 0.002);
        }
        if ($res_user_count == 31) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 32) {
            return sprintf('%0.2f',$total_once_amount * 0.009);
        }
        if ($res_user_count == 33) {
            return sprintf('%0.2f',$total_once_amount * 0.001);
        }
        if ($res_user_count == 34) {
            return sprintf('%0.2f',$total_once_amount * 0.003);
        }
        if ($res_user_count == 35) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 36) {
            return sprintf('%0.2f',$total_once_amount * 0.01);
        }
        if ($res_user_count == 37) {
            return sprintf('%0.2f',$total_once_amount * 0.004);
        }
        if ($res_user_count == 38) {
            return sprintf('%0.2f',$total_once_amount * 0.02);
        }
        if ($res_user_count == 39) {
            return sprintf('%0.2f',$total_once_amount * 0.03);
        }
        if ($res_user_count == 40) {
            return sprintf('%0.2f',$total_once_amount * 0.01);
        }
        if ($res_user_count == 41) {
            return sprintf('%0.2f',$total_once_amount * 0.1);
        }
        if ($res_user_count == 42) {
            return sprintf('%0.2f',$total_once_amount * 0.06);
        }
        if ($res_user_count == 43) {
            return sprintf('%0.2f',$total_once_amount * 0.05);
        }
        if ($res_user_count == 44) {
            return sprintf('%0.2f',$total_once_amount * 0.5);
        }
        if ($res_user_count == 45) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 46) {
            return sprintf('%0.2f',$total_once_amount * 0.017);
        }
        if ($res_user_count == 47) {
            return sprintf('%0.2f',$total_once_amount * 0.012);
        }
        if ($res_user_count == 48) {
            return sprintf('%0.2f',$total_once_amount * 0.012);
        }
        if ($res_user_count == 49) {
            return sprintf('%0.2f',$total_once_amount * 0.018);
        }
        if ($res_user_count == 50) {
            return sprintf('%0.2f',$total_once_amount * 0.006);
        }
        if ($res_user_count == 51) {
            return sprintf('%0.2f',$total_once_amount * 0.019);
        }
        if ($res_user_count == 52) {
            return sprintf('%0.2f',$total_once_amount * 0.005);
        }
        if ($res_user_count == 53) {
            return sprintf('%0.2f',$total_once_amount * 0.021);
        }
        if ($res_user_count == 54) {
            return sprintf('%0.2f',$total_once_amount * 0.003);
        }
        if ($res_user_count == 55) {
            return sprintf('%0.2f',$total_once_amount * 0.022);
        }
        if ($res_user_count == 56) {
            return sprintf('%0.2f',$total_once_amount * 0.002);
        }


        // 58-88刀砍5%
        if ($res_user_count == 57) {
            return sprintf('%0.2f',$total_once_amount * 0.03);
        }
        if ($res_user_count == 58) {
            return sprintf('%0.2f',$total_once_amount * 0.006);
        }
        if ($res_user_count == 59) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 60) {
            return sprintf('%0.2f',$total_once_amount * 0.002);
        }
        if ($res_user_count == 61) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 62) {
            return sprintf('%0.2f',$total_once_amount * 0.009);
        }
        if ($res_user_count == 63) {
            return sprintf('%0.2f',$total_once_amount * 0.001);
        }
        if ($res_user_count == 64) {
            return sprintf('%0.2f',$total_once_amount * 0.003);
        }
        if ($res_user_count == 65) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 66) {
            return sprintf('%0.2f',$total_once_amount * 0.01);
        }
        if ($res_user_count == 67) {
            return sprintf('%0.2f',$total_once_amount * 0.004);
        }
        if ($res_user_count == 68) {
            return sprintf('%0.2f',$total_once_amount * 0.02);
        }
        if ($res_user_count == 69) {
            return sprintf('%0.2f',$total_once_amount * 0.03);
        }
        if ($res_user_count == 70) {
            return sprintf('%0.2f',$total_once_amount * 0.01);
        }
        if ($res_user_count == 71) {
            return sprintf('%0.2f',$total_once_amount * 0.1);
        }
        if ($res_user_count == 72) {
            return sprintf('%0.2f',$total_once_amount * 0.06);
        }
        if ($res_user_count == 73) {
            return sprintf('%0.2f',$total_once_amount * 0.05);
        }
        if ($res_user_count == 74) {
            return sprintf('%0.2f',$total_once_amount * 0.5);
        }
        if ($res_user_count == 75) {
            return sprintf('%0.2f',$total_once_amount * 0.007);
        }
        if ($res_user_count == 76) {
            return sprintf('%0.2f',$total_once_amount * 0.017);
        }
        if ($res_user_count == 77) {
            return sprintf('%0.2f',$total_once_amount * 0.012);
        }
        if ($res_user_count == 78) {
            return sprintf('%0.2f',$total_once_amount * 0.012);
        }
        if ($res_user_count == 79) {
            return sprintf('%0.2f',$total_once_amount * 0.018);
        }
        if ($res_user_count == 80) {
            return sprintf('%0.2f',$total_once_amount * 0.006);
        }
        if ($res_user_count == 81) {
            return sprintf('%0.2f',$total_once_amount * 0.019);
        }
        if ($res_user_count == 82) {
            return sprintf('%0.2f',$total_once_amount * 0.005);
        }
        if ($res_user_count == 83) {
            return sprintf('%0.2f',$total_once_amount * 0.021);
        }
        if ($res_user_count == 84) {
            return sprintf('%0.2f',$total_once_amount * 0.003);
        }
        if ($res_user_count == 85) {
            return sprintf('%0.2f',$total_once_amount * 0.013);
        }
        if ($res_user_count == 86) {
            return sprintf('%0.2f',$total_once_amount * 0.006);
        }
        if ($res_user_count == 87) {
            return sprintf('%0.2f',$total_once_amount * 0.002);
        }
        if ($res_user_count == 87) {
            return sprintf('%0.2f',$total_once_amount * 0.003);
        }
    }

    // 校验用户是否二次砍价
    public function checkBar($user_id, $order_num)
    {
        $res = Db::name('user_bargain')->where(['user_id' => $user_id,'order_num' => $order_num])->count();
        return $res;
    }
}
