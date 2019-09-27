<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\Dictionaries as DictionariesModel;
use app\petadmin\model\Means as MeansModel;
use think\Controller;
use think\Db;
use app\petadmin\controller\Base as BaseController;
use think\Request;

class Means extends Base
{
    // 添加知识点资料--显示分类
    public function getClassMeans()
    {
        $OrderModel = new DictionariesModel();
        $res = $OrderModel->name('consultation_class')
            ->field('id,class_name')
            ->where(['deleted' => 0])
            ->select();

        if ($res == null) {
            $this->errorReturn('1001','false');
        }
        return $this->successReturn('200',$res);
    }

    // 添加知识点资料
    public function addMeans()
    {
        $param = $this->takePostParam();
        $validate = new \think\Validate([
            ['class_id', ['require','number'],''],
            ['consultation_title', ['require'],''],
            ['consultation_details', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $class_id = $param['class_id'];
        $consultation_title = $param['consultation_title'];
        $consultation_details = $param['consultation_details'];
        $MeansModel = new MeansModel();
        $res = $MeansModel->addZl($class_id, $consultation_title, $consultation_details);

        return $this->successReturn('200',$res);
    }

    // 知识点资料列表
    public function meansList()
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
        $MeansModel = new MeansModel();
        $res = $MeansModel->MeansZsList($order, $page, $pageSize);

        return $this->successReturn('200',$res);
    }

    // 上、下架首页
    public function upDownFrame()
    {
        $param = $this->takePutParam();
        $validate = new \think\Validate([
            ['id', ['require'],''],
            ['is_up_down', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $is_up_down = $param['is_up_down'];
        switch ($is_up_down) {
            case '1':
                // 此时为上架
                $MeansModel = new MeansModel();
                $res = $MeansModel->isFrame($id, $is_up_down);

                if (!$res) {
                    return $this->errorReturn('1001','已有三个上架',$res);
                }
                return $this->successReturn('200',$res);
                break;
            case '2':
                // 此时为下架
                $MeansModel = new MeansModel();
                $res = $MeansModel->isFrame($id, $is_up_down);

                return $this->successReturn('200',$res);
                break;
            default:
                return $this->errorReturn('1001','请求参数不符合要求',$param);
                break;
        }
    }

    // 查看编辑
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
        $MeansModel = new MeansModel();
        $res = $MeansModel->getEdit($id);

        return $this->successReturn('200',$res);
    }

    // 编辑保存
    public function editSave()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['consultation_title', ['require'],''],
            ['consultation_class', ['require','number'],''],
            ['consultation_details', ['require'],''],
        ]);

        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $data = [
            'class_id' => $param['consultation_class'],
            'consultation_title' => $param['consultation_title'],
            'consultation_details_url' => $param['consultation_details'],
            'create_time' => date("Y-m-d H:i:s",time()),
        ];

        $MeansModel = new MeansModel();
        $res = $MeansModel->saveEdit($id, $data);

        return $this->successReturn('200',$res);
    }

    // 删除(包含了批量删除)
    public function delMeans()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $id = $param['id'];
        $MeansModel = new MeansModel();
        $res = $MeansModel->delZl($id);

        return $this->successReturn('200',$res);
    }

}
