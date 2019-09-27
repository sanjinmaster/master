<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\Hospital as HospitalModel;
use think\Controller;
use think\Request;

class Hospital extends Base
{
    // 医生列表
    public function hospitalList()
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
        $where_status = null;
        $where_search = null;
        $where_prove = null;
        if ($status != null) { $where_status = [ 'is_status' => ['=',"$status"] ]; }
        if ($mobile != null) { $where_search = [ 'mobile' => ['=',"$mobile"] ]; }

        $order = 'id desc';
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $HospitalModel = new HospitalModel();
        $res = $HospitalModel->hoList($order, $page, $pageSize,$where_status,$where_search);

        return $this->successReturn('200',$res);
    }

    // 冻结、恢复
    public function offNo()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['hospital_id', ['require','number'],''],
            ['is_off_no', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $hospital_id = $param['hospital_id'];
        $is_off_no = $param['is_off_no'];

        $HospitalModel = new HospitalModel();
        $res = $HospitalModel->isOffNo($hospital_id, $is_off_no);

        return $this->successReturn('200',$res);
    }

    // 删除(包含批量删除)
    public function delHospital()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['hospital_id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $hospital_id = $param['hospital_id'];
        $HospitalModel = new HospitalModel();
        $res = $HospitalModel->deleteHospital($hospital_id);

        return $this->successReturn('200',$res);
    }
}
