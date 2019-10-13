<?php

namespace app\api\model;

use think\Log;
use think\Model;

class Bind extends Base
{
    // 医院、医生关系表
    protected $table = 'app_hospital_doctor';

    // 加入、解绑医院
    public function joinHospital($hospital_id, $is_join, $doctor_id)
    {
        $where = [
            'hospital_id' => $hospital_id,
            'doctor_id' => $doctor_id
        ];

        $data = [
            'is_join' => $is_join,
            'update_time' => date("Y-m-d H:i:s",time()),
        ];

        try{
            $res = $this->where($where)->update($data);
            return $res;
        }catch (\Exception $e) {
            Log::write($e->getMessage());
            return $e->getMessage();
        }
    }
}
