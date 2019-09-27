<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\Dictionaries as DictionariesModel;
use think\Controller;
use think\Request;

class Dictionaries extends Base
{
    // 添加字典分类
    public function addZdClass()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['class_name', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $zd_class_name = $param['class_name'];
        $OrderModel = new DictionariesModel();
        $res = $OrderModel->addZdClass($zd_class_name);

        return $this->successReturn('200',$res);
    }

    // 分类列表
    public function zdList()
    {
        $param = $this->takeGetParam();
        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order = 'id desc';
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $OrderModel = new DictionariesModel();
        $res = $OrderModel->zdClassList($order, $page, $pageSize);

        return $this->successReturn('200',$res);
    }

    // 编辑查看
    public function editLook()
    {
        $param = $this->takeGetParam();
        $validate = new \think\Validate([
            ['id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $OrderModel = new DictionariesModel();
        $res = $OrderModel->findZd($id);

        return $this->successReturn('200',$res);
    }

    // 编辑保存
    public function editSave()
    {
        $param = $this->takePutParam();
        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['class_name', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $class_name = $param['class_name'];
        $OrderModel = new DictionariesModel();
        $res = $OrderModel->updateZd($id, $class_name);

        return $this->successReturn('200',$res);
    }

    // 删除
    public function delZd()
    {
        $param = $this->takeDeleteParam();
        $validate = new \think\Validate([
            ['id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $OrderModel = new DictionariesModel();
        $res = $OrderModel->deleteZd($id);

        return $this->successReturn('200',$res);
    }
}
