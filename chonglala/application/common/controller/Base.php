<?php

namespace app\common\controller;

require '../vendor/aliyun-oss-php-sdk-master/autoload.php';

use app\index\controller\Code;
use think\Controller;
use think\Loader;
use think\Request;
use think\db;

class Base extends Controller
{
    //阿里云OSS
    public $accessKeyId = "LTAI2t2tI0OSHAdV";
    public $accessKeySecret = "JsOOIJzQSAquMLX1mBQZ9OIs8mSeGl";
    public $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
    public $bucket = "exam-181212";

    /**
     * 用户登录token
     */
    public function getUserToken($user_id)
    {
        $time = time();
        $param = $time . "cll" . $user_id . rand(1, 1000);
        $token = md5($param);
        Db::name('user_token')->where(['user_id' => $user_id])->update([
            'token' => $token,
            'refresh_time' => date("Y-m-d H:i:s",$time + 60*60*24*7),
        ]);
        return $token;
    }
    /**
     * 鉴权
     */
    public function doAuthentication($user_id, $token)
    {
        if (!$user_id){
            return "缺少user_id";
        }
        return 1;
        $toekn = Db::name('user_token')->where(['user_id' => $user_id])->find();
        $time = time();
        if ($toekn['token'] != $token) {
            return "token错误";
        }
        if ($toekn['refresh_time'] < date("Y-m-d H:i:s",$time)) {
            return "token已过期";
        }
        Db::name('user_token')->where(['user_id' => $user_id])->update([
            'refresh_time' => date("Y-m-d H:i:s",$time + 60*60*24*7),
        ]);
        return 1;
    }
    /**
     * 获取前端传过来的json数据
     */
    protected function requestData()
    {
        $data = file_get_contents('php://input');
        return json_decode($data, true);
    }

    /**
     * 获取get请求数据
     * GET
     */
    protected function takeGetParam()
    {
        if (!Request::instance()->get()){
            return $this->requestData();
        }
        return Request::instance()->get();
    }

    /**
     * 获取post请求数据
     * POST
     */
    protected function takePostParam()
    {
        if (!Request::instance()->post()){
            return $this->requestData();
        }
        return Request::instance()->post();
    }

    /**
     * 获取更新请求数据
     * PUT
     */
    protected function takePutParam()
    {
        if (!Request::instance()->put()){
            return $this->requestData();
        }
        return Request::instance()->put();
    }

    /**
     * 获取部分更新请求数据
     * PATCH
     */
    protected function takePatchParam()
    {
        if (!Request::instance()->patch()){
            return $this->requestData();
        }
        return Request::instance()->patch();
    }

    /**
     * 获取删除请求数据
     * DELETE
     */
    protected function takeDeleteParam()
    {
        if (!Request::instance()->delete()){
            return $this->requestData();
        }
        return Request::instance()->delete();
    }

    ///////////////////////////////常用函数////////////////////////////////////
    /**
     * 返回成功数据
     */
    protected function successReturn($code,$data){
        $return["status"] = 1;
        $return["code"] = $code;
        $code_c = new \app\common\controller\Code();
        $return["code_info"] = $code_c->getCodeInfo($code);
        $return["time"] = date("Y-m-d H:i:s",time());
        $return["data"] = $data;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 返回失败数据
     */
    protected function errorReturn($code,$error_msg,$param){
        $return["status"] = 0;
        $return["code"] = $code;
        $code_c = new \app\common\controller\Code();
        $return["code_info"] = $code_c->getErrorCodeInfo($code);
        $return["error_msg"] = $error_msg;
        $return["time"] = date("Y-m-d H:i:s",time());
        $return["param"] = $param;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 给定月份返回该月第一天
     */
    protected function startTimeReturn($screen_time)
    {
        return date('Y-m-d', mktime(00, 00, 00, date('m', strtotime($screen_time)), 01));
    }

    /**
     * 给定月份返回该月最后一天
     */
    protected function endTimeReturn($screen_time)
    {
        return date('Y-m-d', mktime(23, 59, 59, date('m', strtotime($screen_time))+1, 00));
    }

    /**
     * 获取post过来的json数据
     */
    protected function getPostData()
    {
        $data = file_get_contents('php://input');
        $arr = json_decode($data, true);
        return $arr;
    }

    // 检验不同分类是否一起下单
    public function checkMakePublic($cid)
    {
        // 校验选择的商品是否包含了不同的分类,以下是所有可能的组合
        if (in_array('3',$cid) && in_array('7',$cid)) {
            return true;
        }
        if (in_array('4',$cid) && in_array('8',$cid)) {
            return true;
        }
        if (in_array('5',$cid) && in_array('9',$cid)) {
            return true;
        }
        if (in_array('6',$cid) && in_array('10',$cid)) {
            return true;
        }

        return false;
    }

    public function Base64Img($img, $path = 'AppUser/')
    {
        $head = 'data:image/jpeg;base64,' . $img;
        $types = empty($types) ? array('jpg', 'gif', 'png', 'jpeg') : $types;

        $img = str_replace(array('_', '-'), array('/', '+'), $head);

        $base64img = substr($img, 0, 100);

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64img, $matches)) {
            $type = $matches[2];
            if (!in_array($type, $types)) {
                return array('code' => 1, 'info' => '图片格式不正确，只支持 jpg、gif、png、jpeg哦！', 'url' => '');
            }
            $img = str_replace($matches[1], '', $img);
            $img = base64_decode($img);
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $url = md5(date('Y-m-d H:i:s') . rand(1000, 9999)) . '.jpg';
            file_put_contents($path . $url, $img);
            $ary['code'] = 0;
            $ary['info'] = '保存图片成功';
            $ary['url'] = 'http://' . $_SERVER['HTTP_HOST'].'/'.$path.$url;
        }else {
            $ary['code'] = 1;
            $ary['info'] = '请选择要上传的图片';
        }
        return json_encode($ary);
    }

    // 生成唯一订单号
    protected function order_num()
    {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 11);
    }

    /**
     * 读取txt文件内容
     */
    public function readTxtFile()
    {
        if (!$_POST) {
            $_POST = $this->getPostData();
        }
        $user_id = $_POST['user_id'];
        $user_token = Request::instance()->header('userToken');
        $validate = new \think\Validate([
            ['user_id', ['require', 'number'], ''],
            ['url', ['require'], ''],
        ]);
        if (!$validate->check($_POST)) {
            return $this->errorReturn("1001", $_POST);
        }
        $auth = $this->doAuthentication($user_id, $user_token);
        if ($auth != 1) {
            return $this->successReturn($auth);
        }
        $url = $_POST['url'];
        $str = file_get_contents($url);
        $str = iconv("utf-16le", "utf-8//IGNORE",$str);
        $str = substr($str,3);
        $data['str'] = $str;
        return $this->successReturn('200',$data);
    }
}
