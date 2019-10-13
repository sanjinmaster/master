<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Evaluate extends Base
{
    // 获取用户对医生的评价
    public function getDoctorPj($doctor_id)
    {
        $row = null;
        $rows = null;

        // 首先获取医生信息,头像、姓名、信用分、所属医院、电话、医生证明
        $res_doctor = Db::name('app_user_doctor')->field('head_images,username,credit,id,mobile,qualifications_book')->where(['id' => $doctor_id])->find();
        $rows['head_images'] = $res_doctor['head_images'];
        $rows['username'] = $res_doctor['username'];
        $rows['credit'] = $res_doctor['credit'];

        // 取出所有评价
        $res_evaluate = Db::name('user_order_evaluate')->field('user_id,order_num,evaluate_note,evaluate_time,images,back_note')->where(['doctor_id' => $doctor_id])->select();
        if ($res_evaluate == null) {
            return false;
        }

        $count_evaluate = count($res_evaluate);
        $rows['count_evaluate'] = $count_evaluate;

        foreach ($res_evaluate as $item) {
            $user_id = $item['user_id'];
            $res_user = Db::name('user')->field('headimg,nickname')->where(['user_id' => $user_id])->find();
            $row['headimg'] = $res_user['headimg'];
            $row['nickname'] = $res_user['nickname'];

            $row['order_num'] = $item['order_num'];
            $row['evaluate_note'] = $item['evaluate_note'];
            $row['evaluate_time'] = $item['evaluate_time'];
            $row['images'] = $item['images'];
            $row['back_note'] = $item['back_note'];
            $row['back_time'] = $item['back_time'];
            $rows['evaluate_details'][] = $row;
        }

        return $rows;

    }

    // 回复评价、医院、医生
    public function backPj($id, $back_note)
    {
        $where = [
            'doctor_id' => $id
        ];
        $data = [
            'back_note' => $back_note,'back_time' => date("Y-m-d H:i:s",time())
        ];

        $res = Db::name('user_order_evaluate')->where($where)->update($data);

        return $res;
    }

    // 获取用户对医院的评价
    public function getHosPj($id)
    {
        $row = null;
        $rows = null;

        $q = Db::name('app_user_hospital')->field('store_images,hospital_name,mobile')->where(['id' => $id])->find();
        $rows['store_images'] = $q['store_images'];
        $rows['hospital_name'] = $q['hospital_name'];
        $rows['mobile'] = $q['mobile'];

        $res = Db::name('app_hospital_doctor')
            ->field('b.doctor_id,b.order_num,b.evaluate_note,b.evaluate_time,b.grade,images,b.doctor_id,b.back_note,b.back_time')
            ->alias('a')
            ->join('user_order_evaluate b','a.doctor_id = b.doctor_id')
            ->select();

        foreach ($res as $value) {

            $row['order_num'] = $value['order_num'];
            $row['evaluate_note'] = $value['evaluate_note'];
            $row['order_num'] = $value['order_num'];
            $row['evaluate_time'] = $value['evaluate_time'];
            $row['grade'] = $value['grade'];
            $row['images'] = $value['images'];
            $row['doctor_id'] = $value['doctor_id'];
            $row['back_note'] = $value['back_note'];
            $row['back_time'] = $value['back_time'];
            $rows['details'][] = $row;
        }

        return $rows;
    }
}
