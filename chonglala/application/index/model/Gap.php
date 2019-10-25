<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Gap extends Model
{
    // 获取医生位置信息
    public function getGapInfo($order_num)
    {
        // 经纬度
        $lng_lat = Db::name('app_doctor_order')
            ->field('lng,lat,doctor_id')
            ->where(['order_num' => $order_num])
            ->find();

        if ($lng_lat == null) {
            return null;
        }

        $row['lng'] = $lng_lat['lng'];
        $row['lat'] = $lng_lat['lat'];

        // 医生信息
        $doctor_info = Db::name('app_user_doctor')
            ->field('head_images,username,credit,is_prove,mobile')
            ->where(['id' => $lng_lat['doctor_id']])
            ->find();

        $row['head_images'] = $doctor_info['head_images'];
        $row['username'] = $doctor_info['username'];
        $row['credit'] = $doctor_info['credit'];
        $row['mobile'] = $doctor_info['mobile'];
        if ($doctor_info['is_prove'] == 2) {
            // 如果没有认证,查所属医院
            $doctor_hospital = Db::name('app_hospital_doctor')
                ->alias('a')
                ->field('b.hospital_name')
                ->join('app_user_hospital b','a.hospital_id = b.hospital_id')
                ->where(['b.doctor_id' => $doctor_info['doctor_id'],'b.is_join' => 1])
                ->find();
            $row['hospital'] = $doctor_hospital['hospital_name'];
        }else {
            $row['hospital'] = null;
        }

        return $row;
    }
}
