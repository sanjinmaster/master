<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Work extends Base
{
    // 医生表
    protected $table = 'app_user_doctor';

    // 校验医生是否为认证医生
    public function isProve($id)
    {
        $res = $this->field('is_prove')->where(['id' => $id])->find();
        if ($res['is_prove'] == 2) {
            return false;
        }
        return true;
    }

    // 医生工作中
    public function workIng($id)
    {
        try{
            $data = [
                'is_work' => 1,
                'update_time' => date("Y-m-d H:i:s",time()),
            ];
            return $this->where(['id' => $id])->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 医生休息中
    public function workOut($id)
    {
        try{
            $data = [
                'is_work' => 2,
                'update_time' => date("Y-m-d H:i:s",time()),
            ];
            return $this->where(['id' => $id])->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 医生工作中
    public function hospitalWorkIng($id)
    {
        try{
            $data = [
                'is_work' => 1,
                'update_time' => date("Y-m-d H:i:s",time()),
            ];
            return Db::name('app_user_hospital')->where(['id' => $id])->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 医生休息中
    public function hospitalWorkOut($id)
    {
        try{
            $data = [
                'is_work' => 2,
                'update_time' => date("Y-m-d H:i:s",time()),
            ];
            return Db::name('app_user_hospital')->where(['id' => $id])->update($data);
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
