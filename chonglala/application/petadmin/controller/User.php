<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\User as UserModel;
use think\Controller;
use think\Request;

class User extends Base
{
    // 用户列表
    public function userList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $status = $param['status'];
        $nickname = $param['search_content'];
        $where_status = null;
        $where_search = null;
        if ($status != null) { $where_status = [ 'is_status' => ['=',"$status"] ]; }
        if ($nickname != null) { $where_search = [ 'nickname' => ['like',"%$nickname%"] ]; }

        $order = 'user_id desc';
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $MeansModel = new UserModel();
        $res = $MeansModel->userList($order, $page, $pageSize,$where_status,$where_search);

        return $this->successReturn('200',$res);
    }

    // 冻结、恢复
    public function offNo()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
            ['is_off_no', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $is_off_no = $param['is_off_no'];

        $UserModel = new UserModel();
        $res = $UserModel->isOffNo($user_id, $is_off_no);

        return $this->successReturn('200',$res);
    }

    // 删除(包含批量删除)
    public function delUser()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['user_id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user_id = $param['user_id'];
        $UserModel = new UserModel();
        $res = $UserModel->deleteUser($user_id);

        return $this->successReturn('200',$res);
    }
}
