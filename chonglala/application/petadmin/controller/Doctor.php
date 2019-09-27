<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\Doctor as DoctorModel;
use think\Controller;
use think\Request;

class Doctor extends Base
{
    // 医生列表
    public function doctorList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 状态
        $status = $param['status'];
        // 搜索内容
        $mobile = $param['search_content'];
        // 是否认证
        $is_prove = $param['is_prove'];
        $where_status = null;
        $where_search = null;
        $where_prove = null;
        if ($status != null) { $where_status = [ 'is_status' => ['=',"$status"] ]; }
        if ($mobile != null) { $where_search = [ 'mobile' => ['=',"$mobile"] ]; }
        if ($is_prove != null) { $where_prove = [ 'is_prove' => ['=',"$is_prove"] ]; }

        $order = 'id desc';
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $DoctorModel = new DoctorModel();
        $res = $DoctorModel->docList($order, $page, $pageSize,$where_status,$where_search,$where_prove);

        return $this->successReturn('200',$res);
    }

    // 冻结、恢复
    public function offNo()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['doctor_id', ['require','number'],''],
            ['is_off_no', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $doctor_id = $param['doctor_id'];
        $is_off_no = $param['is_off_no'];

        $DoctorModel = new DoctorModel();
        $res = $DoctorModel->isOffNo($doctor_id, $is_off_no);

        return $this->successReturn('200',$res);
    }

    // 删除(包含批量删除)
    public function delDoctor()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['doctor_id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $doctor_id = $param['doctor_id'];
        $DoctorModel = new DoctorModel();
        $res = $DoctorModel->deleteDoctor($doctor_id);

        return $this->successReturn('200',$res);
    }
}
