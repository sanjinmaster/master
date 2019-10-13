<?php

namespace app\api\controller;

use app\api\model\Bind as BindMyModel;
use think\Controller;
use think\Request;

class Bind extends Base
{
    // 医生加入、解绑医院
    public function isJoinHospital()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['hospital_id', ['require'],'number'],
            ['is_join', ['require'],'number'],
            ['doctor_id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $hospital_id = trim($param['hospital_id']);
        $is_join = trim($param['is_join']);
        $doctor_id = trim($param['doctor_id']);

        $BindMyModel = new BindMyModel();

        // 加入还是解绑
        switch ($is_join) {
            case '1' :
                // 加入
                $res = $BindMyModel->joinHospital($hospital_id, '1', $doctor_id);
                break;
            case '2' :
                // 解绑
                $res = $BindMyModel->joinHospital($hospital_id, '2', $doctor_id);
                break;
            default :
                return $this->errorReturn('1001','请求类型不符合要求',$is_join);
                break;
        }

        return $this->successReturn('200',$res);
    }
}
