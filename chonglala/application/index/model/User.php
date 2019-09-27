<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/10
 * Time: 11:40
 */
namespace app\index\model;

use think\Model;
use think\Db;
class User extends Model
{
    public function getUser($user_id)
    {
        $res =  Db::name('user')->where(['openid' => $user_id,'deleted' => 0])->find();
        return $res;
    }

    public function getUserByOpenid($openid)
    {
        $res =  Db::name('user')->where(['openid' => $openid])->find();
        return $res;
    }

    public function getUserList($where,$order,$pageSize,$page)
    {
        $page = $page-1;
        $p = $page*$pageSize;
        $nums = Db::name('user')->where(['deleted'=>0])->where($where)->count();
        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }
        $now_page_content = Db::name('user')->where(['deleted'=>0])->where($where)->order($order)->limit($p,$pageSize)->select();
        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;
        return $res;
    }

    public function createUser($postdata)
    {
        $data = [
            'openid' => $postdata['openid'],
            'headimg' => $postdata['headimg'],
            'nickname' => $postdata['nickname'],
            'coupon_num' => 4,
            'last_login_time' => date("Y-m-d H:i:s",time()),
        ];
        $if_openid_exit = Db::name('user')->where(['openid' => $postdata['openid']])->find();
        if ($if_openid_exit){
            return false;       //该openid已存在
        }
        $user_id = Db::name('user')->insertGetId($data);

        // 注册即送优惠券
        $data_coupon = [
            [
                'user_id' => $user_id,
                'coupon_id' => 1,
                'status' => 2,
                'create_time' => date("Y-m-d H:i:s",time())
            ],
            [
                'user_id' => $user_id,
                'coupon_id' => 2,
                'status' => 2,
                'create_time' => date("Y-m-d H:i:s",time())
            ],
            [
                'user_id' => $user_id,
                'coupon_id' => 3,
                'status' => 2,
                'create_time' => date("Y-m-d H:i:s",time())
            ],
            [
                'user_id' => $user_id,
                'coupon_id' => 4,
                'status' => 2,
                'create_time' => date("Y-m-d H:i:s",time())
            ]
        ];
        foreach ($data_coupon as $value) {
            Db::name('user_coupon')->insertGetId($value);
        }

        $param = time() . "cll" . $user_id . rand(1, 1000);
        $token = md5($param);
        $data = [
            'user_id' => $user_id,
            'token' => $token,
            'create_time' => date("Y-m-d H:i:s",time()),
            'refresh_time' => date("Y-m-d H:i:s",time()),
        ];
        $res = Db::name('user_token')->insertGetId($data);
        return $res;
    }

    public function updateUser($user_id,$postdata)
    {
        $field = "";
        foreach ($postdata as $key => $value){
            $field = $field.$key.",";
        }
        $field = substr($field,0,strlen($field)-1);
        $data = [
            'openid' => $postdata['openid'],
            'headimg' => $postdata['headimg'],
            'nickname' => $postdata['nickname'],
        ];
        $res = Db::name('user')->where(['openid' => $user_id])->field($field)->update($data);
        return $user_id;
    }

    public function deleteUser($user_id)
    {
        $data = [
            'deleted' => 1,
        ];
        $res = Db::name('user')->where(['openid' => $user_id])->update($data);
        return $user_id;
    }

}