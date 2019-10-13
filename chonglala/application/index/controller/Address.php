<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/5
 * Time: 17:46
 */
namespace app\index\controller;
use app\common\controller\Base;
use think\Request;

class Address extends Base
{
    /**
     * 查看单个地址
     */
    public function address()
    {
        $param = $this->takeGetParam();
        $id = $param['id'];
        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $address = new \app\index\model\Address();
        $data = $address->getAddress($id);
        if (!$data){
            return $this->successReturn('6001',$data);
        }
        return $this->successReturn('200',$data);
    }

    /**
     * 查看某用户的地址列表
     */
    public function addresses()
    {
        $param = $this->takeGetParam();
        $page = $param['page'];
        $pageSize = $param['pageSize'];

        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $address = new \app\index\model\Address();
        $data = $address->getAddressList(['user_id' => $param['user_id']],'',$pageSize,$page);
        
/*        if (!$data['now_page_content']){
            return $this->successReturn('6001',$data);
        }
        */
        return $this->successReturn('200',$data);
    }

    /**
     * 添加服务地址
     */
    public function createAddress()
    {
        $param = $this->takePostParam();
        $validate = new \think\Validate([
            ['name', ['require'],''],
            ['phone', ['require','length' => 11,'number'],''],
            ['area', ['require'],''],
            ['address', ['require'],''],
            ['default', ['require','in' => [0,1]],''],
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $address = new \app\index\model\Address();
        $data = $address->createAddress($param);
        return $this->successReturn('200',$data);
    }

    /**
     * 编辑服务地址
     */
    public function updateAddress()
    {
        $param = $this->takePutParam();
        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $address = new \app\index\model\Address();
        $data = $address->updateAddress($param['id'],$param);
        return $this->successReturn('200',$data);
    }

    /**
     * 删除服务地址
     */
    public function deleteAddress()
    {
        $param = $this->takeDeleteParam();
        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $address = new \app\index\model\Address();
        $data = $address->deleteAddress($param['id']);
        return $this->successReturn('200',$data);
    }
}