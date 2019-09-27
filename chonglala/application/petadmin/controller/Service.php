<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use think\Controller;
use think\Request;
use app\petadmin\model\Service as ServiceModel;

class Service extends Base
{
    // 服务管理(上门疫苗、体检、美容、火化)
    public function serviceRun()
    {
        $param = $this->takeGetParam();
        // 类型,上门疫苗、体检、美容、火化
        $service_type = $param['service_type'];

        // 校验服务类型
        $validate = new \think\Validate([
            ['service_type', ['require','number'],''],
            ['type', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $where = '';
        switch ($service_type) {
            case '1':
                // 关键词
                $keyword = $param['keyword'];
                // 分类
                $type = $param['type'];
                // 当选择了筛选条件
                if ($type != null) {
                    if ($type == 7 || $type == 3)
                        $where = [
                            'goods_name' => ['like',"%$keyword%"],
                            'cid' => ['=',"$type"]
                        ];
                }

                // 没有选择筛选条件
                if ($type == 0) {
                    $where = 'cid = 7 or cid = 3';
                }
                    return $this->doorPublic($param, $where);
                break;
            case '2':
                // 关键词
                $keyword = $param['keyword'];
                // 分类
                $type = $param['type'];
                // 当选择了筛选条件
                if ($type != null) {
                    if ($type == 4 || $type == 8)
                        $where = [
                            'goods_name' => ['like',"%$keyword%"],
                            'cid' => ['=',"$type"]
                        ];
                }

                // 没有选择筛选条件
                if ($type == 0) {
                    $where = 'cid = 4 or cid = 8';
                }
                    return $this->doorPublic($param, $where);
                break;
            case '3':
                // 关键词
                $keyword = $param['keyword'];
                // 分类
                $type = $param['type'];
                // 当选择了筛选条件
                if ($type != null) {
                    if ($type == 5 || $type == 9)
                        $where = [
                            'goods_name' => ['like',"%$keyword%"],
                            'cid' => ['=',"$type"]
                        ];
                }

                // 没有选择筛选条件
                if ($type == 0) {
                    $where = 'cid = 5 or cid = 9';
                }
                    return $this->doorPublic($param, $where);
                break;
            case '4':
                // 关键词
                $keyword = $param['keyword'];
                // 分类
                $type = $param['type'];
                // 当选择了筛选条件
                if ($type != null) {
                    if ($type == 6 || $type == 10)
                        $where = [
                            'goods_name' => ['like',"%$keyword%"],
                            'cid' => ['=',"$type"]
                        ];
                }

                // 没有选择筛选条件
                if ($type == 0) {
                    $where = 'cid = 6 or cid = 10';
                }
                    return $this->doorPublic($param, $where);
                break;
            default:
                    return $this->errorReturn('1001','请求参数不符合要求','service_type');
                break;
        }
    }

    // 上门疫苗列表(上门疫苗、体检、美容、火化)
    public function doorPublic($param, $where)
    {
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order = "id desc";
        $ServiceModel = new ServiceModel();

        $data = $ServiceModel->getGoodsList($where,$order,$pageSize,$page);

        if (!$data['now_page_content']){
            return $this->successReturn('200',$data);
        }
        return $this->successReturn('200',$data);
    }

    // 添加商品(上门疫苗、体检、美容、火化)
    public function addGoods()
    {
        $param = $this->takePostParam();
        $service_type = $param['service_type'];

        // 校验参数合法性
        $validate = new \think\Validate([
            ['service_type', ['require'],''],
            ['goods_class', ['require'],''],
            ['goods_name', ['require'],''],
            ['goods_price', ['require'],''],
            ['images', ['require'],''],
            ['evaluate', ['require'],''],
            ['efficacy', ['require'],''],
            ['apply_pet', ['require'],''],
            ['about_doctor', ['require'],''],
            ['about_service', ['require'],''],
            ['service_flow', ['require'],''],
        ]);

        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        switch ($service_type) {
            case '1':
                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为3
                    $cid = 3;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是喵咪id为7
                    $cid = 7;
                }
                return $this->addPublic($param, $cid);
                break;
            case '2':
                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为4
                    $cid = 4;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是喵咪id为8
                    $cid = 8;
                }
                return $this->addPublic($param, $cid);
                break;
            case '3':
                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为5
                    $cid = 5;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是喵咪id为9
                    $cid = 9;
                }
                return $this->addPublic($param, $cid);
                break;
            case '4':
                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为6
                    $cid = 6;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是喵咪id为10
                    $cid = 10;
                }
                return $this->addPublic($param, $cid);
                break;
            default:
                return $this->errorReturn('1001','请求参数不符合要求','service_type');
                break;
        }
    }

    // 上门添加商品
    public function addPublic($param, $cid)
    {
        $ServiceModel = new ServiceModel();
        $goods = $ServiceModel->createGoods($param, $cid);

        return $this->successReturn('200',$goods);
    }

    // 编辑查看(上门疫苗、体检、美容、火化)
    public function editLook()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['goods_id', ['require'],''],
        ]);

        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $goods_id = $param['goods_id'];
        $ServiceModel = new ServiceModel();
        $edit = $ServiceModel->editGetOne($goods_id);

        return $this->successReturn('200',$edit);
    }

    // 编辑保存(上门疫苗、体检、美容、火化)
    public function editAction()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['service_type', ['require'],''],
            ['goods_id', ['require'],''],
            ['goods_name', ['require'],''],
            ['goods_class', ['require'],''],
            ['goods_price', ['require'],''],
            ['images', ['require'],''],
            ['evaluate', ['require'],''],
            ['efficacy', ['require'],''],
            ['apply_pet', ['require'],''],
            ['about_doctor', ['require'],''],
            ['about_service', ['require'],''],
            ['service_flow', ['require'],''],
        ]);

        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $service_type = $param['service_type'];
        $goods_id = $param['goods_id'];
        $ServiceModel = new ServiceModel();

        switch ($service_type) {
            case '1':

                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为3
                    $cid = 3;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是狗狗id为7
                    $cid = 7;
                }

                $res = $ServiceModel->editPublic($param, $cid, $goods_id);
                return $this->successReturn('200',$res);
                break;
            case '2':

                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为4
                    $cid = 4;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是喵咪id为8
                    $cid = 8;
                }

                $res = $ServiceModel->editPublic($param, $cid, $goods_id);
                return $this->successReturn('200',$res);
                break;
            case '3':
                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为5
                    $cid = 5;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是喵咪id为9
                    $cid = 9;
                }

                $res = $ServiceModel->editPublic($param, $cid, $goods_id);
                return $this->successReturn('200',$res);
                break;
            case '4':
                $class_id = $param['goods_class'];

                $cid = '';
                if ($class_id == 1) {
                    // 此时属于商品的分类是喵咪id为6
                    $cid = 6;
                }
                if ($class_id == 2) {
                    // 此时属于商品的分类是喵咪id为10
                    $cid = 10;
                }
                $res = $ServiceModel->editPublic($param, $cid, $goods_id);
                return $this->successReturn('200',$res);
                break;
            default:
                return $this->errorReturn('1001','请求参数不符合要求','service_type');
                break;
        }

    }

    // 上、下架(上门疫苗、体检、美容、火化)
    public function lowerXj()
    {
        $param = $this->takePatchParam();

        $validate = new \think\Validate([
            ['goods_id', ['require'],''],
            ['goods_status', ['require'],''],
        ]);

        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $goods_id = $param['goods_id'];
        $goods_status = $param['goods_status'];
        $ServiceModel = new ServiceModel();
        $xj = $ServiceModel->statusXjUpdate($goods_id, $goods_status);

        return $this->successReturn('200',$xj);

    }

    // 删除商品(上门疫苗、体检、美容、火化)
    public function delGoods()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['goods_id', ['require'],''],
        ]);

        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $goods_id = $param['goods_id'];
        $ServiceModel = new ServiceModel();
        $res = $ServiceModel->delPublic($goods_id);

        return $this->successReturn('200',$res);
    }
}
