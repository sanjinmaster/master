<?php

namespace app\api\controller;

use think\Controller;
use app\api\model\Work as WorkModel;
use think\Request;

class Work extends Base
{
    // 医生工作中
    public function doctorWorkIng()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $Work = new WorkModel();
        $id = trim($param['id']);

        // 校验医生是否为认证医生,只有认证过的医生才有开启工作中的权限
        $is_prove = $Work->isProve($id);
        if (!$is_prove) {
            return $this->errorReturn('1001','您还没成为认证医生,不能开启工作状态',$is_prove);
        }

        // 更新医生工作状态
        $res = $Work->workIng($id);

        return $this->successReturn('200',$res);
    }

    // 医生休息中
    public function doctorWorkOut()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = trim($param['id']);

        $Work = new WorkModel();
        $res = $Work->workOut($id);

        return $this->successReturn('200',$res);
    }

    // 医院工作中
    public function hospitalWorkIng()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = trim($param['id']);

        $Work = new WorkModel();
        $res = $Work->hospitalWorkIng($id);

        return $this->successReturn('200',$res);
    }

    // 医院休息中
    public function hospitalWorkOut()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require'],'number']
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = trim($param['id']);

        $Work = new WorkModel();
        $res = $Work->hospitalWorkOut($id);

        return $this->successReturn('200',$res);
    }
}
