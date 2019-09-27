<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/5
 * Time: 17:47
 */
namespace app\index\model;

use think\Model;
use think\Db;
class Address extends Model
{
    public function getAddress($id)
    {
        $res =  Db::name('address')->where(['id' => $id,'deleted' => 0])->find();
        return $res;
    }

    public function getAddressList($where,$order,$pageSize,$page)
    {
        $page = $page-1;
        $p = $page*$pageSize;
        $nums = Db::name('address')->where(['deleted'=>0])->where($where)->count();
        $load_state = 'load_more';
        if (($p + $pageSize) >= $nums) {
            $load_state = 'load_finish';
        }
        $now_page_content = Db::name('address')->where(['deleted'=>0])->where($where)->order($order)->limit($p,$pageSize)->select();
        $res['load_state'] = $load_state;
        $res['total_nums'] = $nums;
        $res['pageSize'] = $pageSize;
        $res['total_page'] = ceil($nums/$pageSize);
        $res['now_page_content'] = $now_page_content;
        return $res;

    }

    public function createAddress($postdata)
    {
        $data = [
            'name' => $postdata['name'],
            'phone' => $postdata['phone'],
            'area' => $postdata['area'],
            'address' => $postdata['address'],
            'default' => $postdata['default'],
            'user_id' => $postdata['user_id'],
            'create_time' => date("Y-m-d H:i:s",time()),
        ];
        if ($postdata['default']==1){
            $this->initialAddressDefaultState($postdata['user_id']);
        }
        $res = Db::name('address')->insertGetId($data);
        return $res;
    }

    public function updateAddress($id,$postdata)
    {
        $field = "";
        foreach ($postdata as $key => $value){
            $field = $field.$key.",";
        }
        $field = substr($field,0,strlen($field)-1);
        $data = [
            'name' => $postdata['name'],
            'phone' => $postdata['phone'],
            'area' => $postdata['area'],
            'address' => $postdata['address'],
            'default' => $postdata['default'],
            'user_id' => $postdata['user_id'],
            'update_time' => date("Y-m-d H:i:s",time()),
        ];
        if ($postdata['default']==1){
            $this->initialAddressDefaultState($postdata['user_id']);
        }
        $res = Db::name('address')->where(['id' => $id,'user_id' => $postdata['user_id']])->field($field)->update($data);
        return $id;
    }

    public function deleteAddress($id)
    {
        $data = [
            'deleted' => 1,
        ];
        $res = Db::name('address')->where(['id' => $id])->update($data);
        return $id;
    }

    public function initialAddressDefaultState($user_id)
    {
        Db::name('address')->where(['user_id' => $user_id])->field('default')->update(['default' => 0]);
        return true;
    }
}