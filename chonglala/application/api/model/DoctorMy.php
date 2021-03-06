<?php

namespace app\api\model;

use think\Db;
use think\Log;
use think\Model;

class DoctorMy extends Base
{
    // 医生表
    protected $table = 'app_user_doctor';

    // 医生我的
    public function getMyInfo($id)
    {
        $row = null;
        $res_doctor = $this->field('id,mobile,head_images,address,
        username,credit,is_work,is_prove,account_amount')
            ->where(['id' => $id])
            ->find();

        $row['id'] = $res_doctor['id'];
        $row['doctor_mobile'] = $res_doctor['mobile'];
        $row['doctor_address'] = $res_doctor['address'];
        $row['head_images'] = $res_doctor['head_images'];
        $row['username'] = $res_doctor['username'];
        $row['credit'] = $res_doctor['credit'];
        $row['is_work'] = $res_doctor['is_work'];
        $row['is_prove'] = $res_doctor['is_prove'];
        $row['account_amount'] = $res_doctor['account_amount'];

        $res_hospital = Db::name('app_hospital_doctor')->field('id,hospital_id,doctor_id,is_join')->where(['doctor_id' => $id])->find();
        $hospital_id = $res_hospital['hospital_id'];

        $row['hospital_id'] = $res_hospital['hospital_id'];
        $row['doctor_id'] = $res_hospital['doctor_id'];
        $row['is_join'] = $res_hospital['is_join'];

        if ($hospital_id) {
            $res_address = Db::name('app_user_hospital')->field('address,hospital_name,store_images,mobile')->where(['id' => $hospital_id])->find();
            $row['hospital_address'] = $res_address['address'];
            $row['hospital_name'] = $res_address['hospital_name'];
            $row['store_images'] = $res_address['store_images'];
            $row['hospital_mobile'] = $res_address['mobile'];
        }else {
            $row['hospital_address'] = null;
            $row['hospital_name'] = null;
            $row['store_images'] = null;
            $row['hospital_mobile'] = null;
        }

        return $row;

    }

    // 更换头像
    public function updateHeadImg($id, $head_images)
    {
        try{
            $data = [
                'head_images' => $head_images,
                'update_time' => date("Y-m-d H:i:s",time())
            ];

            return $this->where(['id' => $id])->update($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // 更换昵称
    public function updateNickname($id, $nickname)
    {
        try{
            $data = [
                'nickname' => $nickname,
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
        $row = null;

        $res_hospital = Db::name('app_hospital_doctor')->field('id,hospital_id,doctor_id,is_join')->where(['doctor_id' => $id])->find();
        $hospital_id = $res_hospital['hospital_id'];

        $row['hospital_id'] = $res_hospital['hospital_id'];
        $row['doctor_id'] = $res_hospital['doctor_id'];
        $row['is_join'] = $res_hospital['is_join'];

        if ($hospital_id) {
            $res_address = Db::name('app_user_hospital')->field('address,hospital_name,store_images,mobile')->where(['id' => $hospital_id])->find();
            $row['address'] = $res_address['address'];
            $row['hospital_name'] = $res_address['hospital_name'];
            $row['store_images'] = $res_address['store_images'];
            $row['mobile'] = $res_address['mobile'];
        }

        return $row;
    }
}
