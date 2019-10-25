<?php

namespace app\api\model;

use think\Db;
use think\Log;
use think\Model;

class HospitalMy extends Base
{
    // 医院表
    protected $table = 'app_user_hospital';

    // 医院我的
    public function getMyInfo($id)
    {
        return $this->field('id,mobile,hospital_name,address,store_images,hospital_name,credit,is_work,account_amount')->where(['id' => $id])->find();
    }

    // 更换头像
    public function updateHeadImg($id, $store_images)
    {
        try{
            $data = [
                'store_images' => $store_images,
                'update_time' => date("Y-m-d H:i:s",time())
            ];

            return $this->where(['id' => $id])->update($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 更新密码
    public function updatePass($mobile, $password)
    {
        try{
            $data = [
                'password' => md5($password),
                'update_time' => date("Y-m-d H:i:s",time())
            ];

            return $this->where(['mobile' => $mobile])->update($data);
        } catch (\Exception $e) {
            Log::write($e->getMessage());
            return $e->getMessage();
        }
    }

    // 更换手机号
    public function updateMobile($mobile)
    {
        try{
            $data = [
                'mobile' => $mobile,
                'update_time' => date("Y-m-d H:i:s",time())
            ];

            return $this->where(['mobile' => $mobile])->update($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 医院信息
    public function HospitalInfo($id)
    {
        $res = $this->field('address')->where(['id' => $id])->find();

        if ($res == null) {
            return null;
        }

        return $res;
    }

    // 添加医生
    public function createDoctor($mobile, $hospital_id)
    {
        $where_doctor = [
            'mobile' => $mobile,
            // 未认证的医生
            'is_prove' => 2,
        ];

        // 取出医生id
        $res_doctor = Db::name('app_user_doctor')->field('id')->where($where_doctor)->find();
        if ($res_doctor == null) {
            return null;
        }

        $data = [
            'hospital_id' => $hospital_id,
            'doctor_id' => $res_doctor['id'],
            'create_time' => date("Y-m-d H:i:s",time())
        ];

        // 新增医院、医生关系表
        try{
            $res = Db::name('app_hospital_doctor')->insertGetId($data);
            return $res;
        } catch (\Exception $e) {
            Log::write($e->getMessage());
            return $e->getMessage();
        }
    }

    // 我的医生
    public function doctorInfo($id)
    {
        $where = [
            'hospital_id' => $id,
            // is_join = 1,指医院发送过邀请医生已经绑定了医院
            'is_join' => 1,
        ];

        $res_hos_doc = Db::name('app_hospital_doctor')->field('hospital_id,doctor_id')->where($where)->select();
        if ($res_hos_doc == null) {
            return null;
        }

        $row = null;
        $doctor_id = null;
        foreach ($res_hos_doc as $value) {
            $doctor_id[] = $value['doctor_id'];
        }
        $doctor_str_id = join(',',$doctor_id);

        $res_doctor = Db::name('app_user_doctor')->field('id,username,mobile,head_images')->where('id','in',$doctor_str_id)->select();
        if ($res_doctor == null) {
            return null;
        }

        return $res_doctor;
    }
}
