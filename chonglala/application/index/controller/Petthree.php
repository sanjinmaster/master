<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Petthree as PetthreeModel;
use think\Controller;
use think\Request;

class Petthree extends Base
{
    // 首页宠物字典默认三个
    public function indexThree()
    {
        $Petthree = new PetthreeModel();
        $res = $Petthree->indexDefault();

        return $this->successReturn('200',$res);
    }

    // 首页宠物字典查看详情
    public function petDetails()
    {
        $param = $this->takeGetParam();
        $pet_id = $param['id'];

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $Petthree = new PetthreeModel();
        $res = $Petthree->Details($pet_id);
        if (!$res){
            return $this->successReturn('6001',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 咨询列表,咨询分类
    public function petList()
    {
        $param = $this->takeGetParam();

        // 检验参数
        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $keyword = $param['keyword'];
        $page = $param['page'];
        $pageSize = $param['pageSize'];

        $order = 'id desc';
        $Petthree = new PetthreeModel();
        $res = $Petthree->speakList($order, $keyword, $page, $pageSize);

        if (!$res) {
            return $this->errorReturn('1002','没有默认数据',$res);
        }

        return $this->successReturn('200',$res);
    }

    // 咨询分类,咨询详情
    public function zxList()
    {
        $param = $this->takeGetParam();

        // 检验参数
        $validate = new \think\Validate([
            ['id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $keyword = $param['keyword'];

        $id = $param['id'];

        $Petthree = new PetthreeModel();
        $res = $Petthree->zdList($keyword, $id);

        if (!$res) {
            return $this->errorReturn('1002','没有默认数据',$res);
        }

        return $this->successReturn('200',$res);
    }
}
