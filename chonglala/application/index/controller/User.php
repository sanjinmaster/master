<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11
 * Time: 10:27
 */
namespace app\index\controller;
use app\common\controller\Base;
use think\Request;

class User extends Base
{
    /**
     * 查询单个用户
     */
    public function user()
    {
        $param = $this->takeGetParam();
        $user_id = $param['user_id'];
        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user = new \app\index\model\User();
        $data = $user->getUser($user_id);
        if (!$data){
            return $this->successReturn('6001',$data);
        }
        return $this->successReturn('200',$data);
    }

    /**
     * 查看用户列表
     */
    public function users()
    {
        $param = $this->takeGetParam();
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $keyword = $param['keyword'];
        $where = [
            'nickname' => ['like',"%$keyword%"]
        ];
        $order = "user_id desc";
        $user = new \app\index\model\User();
        $data = $user->getUserList($where,$order,$pageSize,$page);
        if (!$data['now_page_content']){
            return $this->successReturn('6001',$data);
        }
        return $this->successReturn('200',$data);
    }

    /**
     * 添加用户
     */
    public function createUser()
    {
        $param = $this->takePostParam();
        $validate = new \think\Validate([
            ['openid', ['require'],''],
            ['headimg', ['require'],''],
            ['nickname', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }
        $auth = $this->doAuthentication($param['user_id'],Request::instance()->header('token'));
        if ($auth!=1){
            return $this->errorReturn('401',$auth,$param);
        }
        $user = new \app\index\model\User();
        $data = $user->createUser($param);
        return $this->successReturn('201',$data);
    }

    /**
     * 更新用户
     */
    public function updateUser()
    {
        $param = $this->takePutParam();
        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }
        $auth = $this->doAuthentication($param['user_id'],Request::instance()->header('token'));
        if ($auth!=1){
            return $this->errorReturn('401',$auth,$param);
        }
        $user = new \app\index\model\User();
        $data = $user->updateUser($param['user_id'],$param);
        return $this->successReturn('201',$data);
    }

    /**
     * 删除用户
     */
    public function deleteUser()
    {
        $param = $this->takeDeleteParam();
        $validate = new \think\Validate([
            ['user_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }
        $auth = $this->doAuthentication($param['user_id'],Request::instance()->header('token'));
        if ($auth!=1){
            return $this->errorReturn('401',$auth,$param);
        }
        $user = new \app\index\model\User();
        $data = $user->deleteUser($param['user_id']);
        return $this->successReturn('204',$data);
    }
}