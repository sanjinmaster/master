<?php

namespace app\api\controller;

use think\Controller;
use app\api\model\Evaluate as EvaluateModel;
use think\Request;

class Evaluate extends Base
{
    // 认证过的医生的累计评价
    public function getEvaluate()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = trim($param['id']);

        $EvaluateModel = new EvaluateModel();
        $res = $EvaluateModel->getDoctorPj($id);

        return $this->successReturn('200',$res);
    }

    // 认证过的医生回复用户的评价(2019-10-9目前情况下是只能回复一次)
    public function backEvaluate()
    {
        $param = $this->takePatchParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number'],
            ['back_note', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = trim($param['id']);
        $back_note = trim($param['back_note']);

        $EvaluateModel = new EvaluateModel();

        $res = $EvaluateModel->backPj($id, $back_note);

        return $this->successReturn('200',$res);
    }

    // 医院接单的累计评价
    public function getHosEvaluate()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = trim($param['id']);

        $EvaluateModel = new EvaluateModel();
        $res = $EvaluateModel->getHosPj($id);

        return $this->successReturn('200',$res);
    }

    // 医院回复用户的评价(2019-10-9目前情况下是只能回复一次)
    public function backHosEvaluate()
    {
        $param = $this->takePatchParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number'],
            ['back_note', ['require'],'']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $doctor_id = trim($param['id']);
        $back_note = trim($param['back_note']);

        $EvaluateModel = new EvaluateModel();
        $res = $EvaluateModel->backPj($doctor_id, $back_note);

        return $this->successReturn('200',$res);
    }
}
