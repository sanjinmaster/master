<?php

namespace app\index\model;

use think\Cache;
use think\Db;
use think\Exception;
use think\Log;
use think\Model;

class WxNotifyBack extends Model
{
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
            'make_date' => date("Y-m-d",time()),
            'before_time' => $param['before_time'],
            'pay_type' => 1,
            'coupon_amount' => $param['coupon_amount'],
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
            $shop_cart = Db::name('shop_cart')
                ->field('gid,num')
                ->where(['user_id' => $param['user_id']])
                ->where('gid','in',"$gid_str")
                ->select();

            $row = null;
            $rows = null;
            foreach ($shop_cart as $value) {
                // 放入购物车中商品的数量
                $row[$value['gid']]['num'] = $value['num'];
            }

            // 取出商品信息
            $goods_info = Db::name('goods_info')
                ->field('id,goods_name,price')
                ->where('id','in',"$gid_str")
                ->select();

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

            if ($param['coupon_id']) {
                // 如果领取了优惠券并使用了就更改此优惠券状态
                $this->updateCouponStatus($param['coupon_id'], $param['user_id']);
            }

            // 提交事务
            $this->commit();
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
            'make_date' => date("Y-m-d",time()),
            'before_time' => $param['before_time'],
            'pay_type' => 1,
            'coupon_amount' => $param['coupon_amount'],
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

            if ($param['coupon_id']) {
                // 如果领取了优惠券并使用了就更改此优惠券状态
                $this->updateCouponStatus($param['coupon_id'], $param['user_id']);
            }

            // 提交事务
            $this->commit();
        }catch (\Exception $e) {
            // 回滚
            $this->rollback();
            Log::write($e->getMessage());
            return $e->getMessage();
        }

    }

    // 检查有没有生成过订单、避免重复付款
    public function checkPay($order_num)
    {
        $res = Db::name('order_master')->field('pay_status')->where(['order_num' => $order_num])->find();
        return $res['pay_status'];
    }

    // 更改优惠券状态
    protected function updateCouponStatus($coupon_id, $user_id)
    {
        $res = Db::name('user_coupon')->where(['coupon_id' => $coupon_id,'user_id' => $user_id])->update(['status' => 1,'update_time' => date("Y-m-d H:i:s",time())]);
        if ($res) {
            // 更新优惠券数量
            $count = Db::name('user')->where(['user_id' => $user_id,'status' => 1])->count();
            return Db::name('user')->where(['user_id' => $user_id])->update(['coupon_num' => $count - 1]);
        }
    }

    // 支付回调、修改订单状态
    public function updateOrderStatus($result)
    {
        // 判断是否支付成功
        if ($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS') {
            // 修改订单状态
            // 订单编号
            $out_trade_no = $result['out_trade_no'];
            // 成交金额
            $deal_amount = $result['total_fee']/100;

            $data = [
                'status' => 2,
                'pay_status' => 1,
                'deal_amount' => $deal_amount,
                'pay_time' => date("Y-m-d H:i:s",time()),
                'pay_date' => date('Y-m-d'),
            ];

            try{
                // 更新订单主表状态
                $res = Db::name('order_master')->where(['order_num' => $out_trade_no])->update($data);

                // 如果支付用户有上级,则给上级更新奖励金
                $next_id = Cache::get("$out_trade_no");
                $res_id = Db::name('user_relation')->field('user_id')->where('next_id',$next_id)->find();
                $up_id = $res_id['user_id'];

                // 产生奖励金逻辑
                if ($up_id) {

                    // 新增奖励金记录
                    $data_reward = [
                        'user_id' => $next_id,
                        'order_num' => $out_trade_no,
                        'reward' => sprintf('%0.2f',$deal_amount * 0.02),
                        'up_id' => $up_id,
                        'create_time' => date("Y-m-d H:i:s",time()),
                    ];

                    $res_reward = Db::name('user_reward')->insertGetId($data_reward);

                    if ($res_reward) {
                        // 更新上级user表奖励金字段
                        $res_before_reward = Db::name('user')->field('total_reward')->where('user_id',$up_id)->find();

                        // 之前的奖励金
                        $before_reward = $res_before_reward['total_reward'];
                        // 获得的奖励金
                        $get_reward = sprintf('%0.2f',$deal_amount * 0.02);

                        Db::name('user')->where('user_id',$up_id)->setField('total_reward',sprintf('%0.2f',$before_reward + $get_reward));

                    }

                }

                if ($res) {

                    return "<xml>
                <return_code><![CDATA[SUCCESS]]></return_code>
                <return_msg><![CDATA[OK]]></return_msg>
                </xml>";
                }else {

                    return "<xml>
				<return_code><![CDATA[FAIL]]></return_code>
				<return_msg><![CDATA[未找到订单号]]></return_msg>
				</xml>";
                }
            }catch (\Exception $e) {
                Log::error($e->getMessage());
                return $e->getMessage();
            }
        }
    }

    // 退款回调
    public function updateCancelOrder($result)
    {
        // 判断是否支付成功
        if ($result['return_code'] == 'SUCCESS') {
            // 修改订单状态
            // 订单编号
            $out_trade_no = $result['out_refund_no'];
            // 成交金额
            $refund_amount = $result['refund_fee']/100;

            $data = [
                'status' => 6,
                'pay_status' => 3,
                'refund_amount' => $refund_amount,
                'refund_time' => date("Y-m-d H:i:s",time()),
                'deleted' => 1,
            ];

            try{
                // 更新订单主表
                $res = Db::name('order_master')->where(['order_num' => $out_trade_no])->update($data);

                if ($res) {

                    // 更新订单详情表
                    $res_xq = Db::name('order_details')
                        ->where(['order_num' => $out_trade_no])
                        ->update(['deleted' => 1,['update_time' => date("Y-m-d H:i:s",time())]]);

                    if ($res_xq) {
                        return "<xml> 
                  <return_code><![CDATA[SUCCESS]]></return_code>
                  <return_msg><![CDATA[OK]]></return_msg>
                </xml>";
                    }
                }

            } catch (\Exception $e) {
                Log::write($e->getMessage());
                return $e->getMessage();
            }
        }
    }
}
