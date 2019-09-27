<?php

namespace app\petadmin\model;

use think\Model;

class Login extends Model
{
    // 后台管理员表
    protected $table = 'admin_user';

    /**
     * @param $name
     * 根据用户名查询信息
     */
    public function findUserByName($name)
    {
        return $this->where('username', $name)->find();
    }

    /**
     * @param $uid
     * @param array $param
     * @return int|string
     * 登录成功更新用户信息
     */
    public function updateStatus($uid, $param = [])
    {
        try{

            $this->where('id', $uid)->update($param);
            return 1;
        }catch (\Exception $e){

            return $e->getMessage();
        }
    }
}
