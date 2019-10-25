<?php

namespace app\common\controller;

require '../vendor/aliyun-oss-php-sdk-master/autoload.php';

use app\index\controller\Code;
use think\Controller;
use think\Loader;
use think\Request;
use think\db;

//安全公钥和私钥
define('PRIVATE_KEY','-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCq38pvqT//Luuuw105mQ5sDDIV13rgdQzTr8Ccik1YqpuaJE0K
59dOcl2KdAd7e50O1TZlh9bwOp9MPpVvIDzdlGsQEDRV7kxgsxTpF4dqI4SYUBYg
6B31Wh0TpYAEG95FIweF9P/7Q9fH2oSpveL0jPYyhvPvzXtq5K8qj6cL3QIDAQAB
AoGAM99B/gm8MsRUqbYG+/A5z5UYM0c5tx/xZ+XHq/3UIyGPoQh6FuBwnRDc0qYM
i3DoKdOR8tp85mp1Z1jsVlLMBtzsqUIxv5uqj8kDwBdZnNunZ8dvC1TU3UUwPaYJ
xcDd9tMTm7KisDAZ7bOJc2aCuhtQ1D0EYPXSZT+KcGDW/IECQQDZnzJgUUBQmAxs
zCGoH8DxznxyYjQjJxtKbToMVMoP0VZfXIPd3UfjL5AIrhI1InS/kSxRlcNI5gay
kNUkyLw9AkEAyQIc/ALr2wDVeGI3gqIDOZvQmxOwMMdrshGh9JZgp4UbUvszoJg8
Nr74bZkZQ+9DzOTEJf7+LePohsqAqWZoIQJAMyP3Ka1OaOIiYVrjOegkZm64zgSH
7g7dmfLrJkSyq17tZkGOd4/tudTOi0uk2bm8J9yMxqtkFfiAcGwauqc1nQJBAKN0
aXdxNLQxeGXdkIBVGMRG9Zq1pufzsprqBcYsZsqyzeZrya7FPOnT35bYEZiRv5Ol
T/AJ7E4K7/J0R635TaECQQCxSiG5stx4f+Oev2tPXvuyoGLD9Up7c8TNSpKM0MK4
OIKoNavobvk1sLTugzenVuPH3KjbyFzO++vvlYTHp2cn
-----END RSA PRIVATE KEY-----
');

define('PUBLIC_SRV_KEY','-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCq38pvqT//Luuuw105mQ5sDDIV
13rgdQzTr8Ccik1YqpuaJE0K59dOcl2KdAd7e50O1TZlh9bwOp9MPpVvIDzdlGsQ
EDRV7kxgsxTpF4dqI4SYUBYg6B31Wh0TpYAEG95FIweF9P/7Q9fH2oSpveL0jPYy
hvPvzXtq5K8qj6cL3QIDAQAB
-----END PUBLIC KEY-----
');

//用于app端的公钥
define('PUBLIC_APP_KEY','-----BEGIN PUBLIC KEY----- MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCq38pvqT//Luuuw105mQ5sDDIV 13rgdQzTr8Ccik1YqpuaJE0K59dOcl2KdAd7e50O1TZlh9bwOp9MPpVvIDzdlGsQ EDRV7kxgsxTpF4dqI4SYUBYg6B31Wh0TpYAEG95FIweF9P/7Q9fH2oSpveL0jPYy hvPvzXtq5K8qj6cL3QIDAQAB -----END PUBLIC KEY-----');


class Base extends Controller
{
    // 阿里云OSS
    public $accessKeyId = "LTAI2t2tI0OSHAdV";
    public $accessKeySecret = "JsOOIJzQSAquMLX1mBQZ9OIs8mSeGl";
    public $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
    public $bucket = "exam-181212";

    // 极光推送
    public $jg_app_key = '205fb49ba887fdf43512ca57';
    public $jg_master_secret = 'b3967d944c43c5bcc80973a0';

    // 百度地图api
    public $ak = 'LlAFZ1WtwIYOwCxAcVp96luiWoH651yn';  //改版后，服务端和客户端key值不能共用

    // 引入极光推送sdk
    protected function jgSdk_inc()
    {
        require_once  EXTEND_PATH.'/jpush-api-php-client-master/src/JPush/Client.php';
        require_once  EXTEND_PATH.'/jpush-api-php-client-master/src/JPush/Config.php';
        require_once  EXTEND_PATH.'/jpush-api-php-client-master/src/JPush/PushPayload.php';
        require_once  EXTEND_PATH.'/jpush-api-php-client-master/src/JPush/Http.php';
    }

    /**  为医生用户生成别名
     * @return string
     */
    protected function createAlias()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = substr($charid, 0, 8).substr($charid, 8, 4).substr($charid,12, 4).substr($charid,16, 4).substr($charid,20,12);
        return $uuid;
    }

    /**  用rsa加密
     * @param $data
     * @return string
     */
    public static function rsa_encode($data)
    {
        //$pi_key =  openssl_pkey_get_private(PRIVATE_KEY);
        $pu_key = openssl_pkey_get_public(PUBLIC_SRV_KEY);

        if(is_array($data)){ $data = json_encode($data); }
        openssl_public_encrypt($data, $encrypted, $pu_key);//公钥加密
        $encrypted = base64_encode($encrypted);// base64传输
        //echo $encrypted,"<br/>";

        return $encrypted;
    }

    /**  用rsa解密
     * @param $encrypted
     * @return mixed
     */
    public static function rsa_decode($encrypted)
    {
        $pi_key =  openssl_pkey_get_private(PRIVATE_KEY);
        //$pu_key = openssl_pkey_get_public(PUBLIC_SRV_KEY);
        openssl_private_decrypt(base64_decode($encrypted), $decrypted, $pi_key);//私钥解密

        return $decrypted;
    }

    /**  对象转数组
     * @param $array
     * @return array
     */
    public static function object_switch_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        }
        if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] =self::object_switch_array($value);
            }
        }
        return $array;
    }

    /**  校验密码是否为弱口令及密码位数
     * @param $pwd
     * @return false|int
     */
    public static function checkPwd($pwd)
    {
        $res = preg_match('/(?![A-Z]+$)(?![a-z]+$)(?!\d+$)(?![\W_]+$)\S{8,16}$/', $pwd);
        return $res;
    }

    /**  获取app端token
     * @param $mobile
     * @param $password
     * @param $time
     * @return string
     */
    public static function getToken($mobile, $password, $time)
    {
        $str = md5(uniqid(md5($mobile.$password.$time)),true);  //生成一个不会重复的字符串
        $str = sha1($str);  //SHA1加密
        return $str;
    }

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

    /**  检验不同分类是否一起下单
     * @param $cid
     * @return bool
     */
    public function checkMakePublic($cid)
    {
        // 校验选择的商品是否包含了不同的分类,以下是所有可能的组合
        if (in_array('3',$cid) && in_array('7',$cid) && count($cid) <= 2) {
            return true;
        }
        if (in_array('4',$cid) && in_array('8',$cid) && count($cid) <= 2) {
            return true;
        }
        if (in_array('5',$cid) && in_array('9',$cid) && count($cid) <= 2) {
            return true;
        }
        if (in_array('6',$cid) && in_array('10',$cid) && count($cid) <= 2) {
            return true;
        }

        return false;
    }

    /**  Base64Img
     * @param $img
     * @param string $path
     * @return array|false|string
     */
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

    /** 生成唯一订单号
     * @return string
     */
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

    /**  获取header头部token
     * @return mixed|string
     */
    protected function takeHeaderToken()
    {
        if (!Request::instance()->header('token')){
            return $this->requestData();
        }
        return Request::instance()->header('token');
    }

    /**
     * 根据地址获取经纬度
     */
    public function getLatLong($address){
        $key = $this->ak;
        $address = urlencode($address);
        $url='http://api.map.baidu.com/geocoder/v2/?address='.$address.'&output=json&ak='.$key;
        if($result=file_get_contents($url))
        {
            $res= explode(',"lat":', substr($result, 40,36));
            return  $res;
        }
    }



    /**
     * 根据起点坐标和终点坐标测距离
     * @param  [array]   $from     [起点坐标(经纬度),例如:array(118.012951,36.810024)]
     * @param  [array]   $to     [终点坐标(经纬度)]
     * @param  [bool]    $km        是否以公里为单位 false:米 true:公里(千米)
     * @param  [int]     $decimal   精度 保留小数位数
     * @return [string]  距离数值
     */
    function getDistance($from,$to,$km=true,$decimal=2){
        sort($from);
        sort($to);
        $EARTH_RADIUS = 6370.996; // 地球半径系数

        $distance = $EARTH_RADIUS*2*asin(sqrt(pow(sin( ($from[0]*pi()/180-$to[0]*pi()/180)/2),2)+cos($from[0]*pi()/180)*cos($to[0]*pi()/180)* pow(sin( ($from[1]*pi()/180-$to[1]*pi()/180)/2),2)))*1000;

        if($km){
            $distance = $distance / 1000;
        }

        return round($distance, $decimal);
    }

    /**
     * 根据经纬度获取城市
     */
    public function getCityByLng($lng,$lat)
    {
        $key = $this->ak;
        $url='http://api.map.baidu.com/geocoder?location='.$lat.','.$lng.'&output=json&ak='.$key;
        if($result=file_get_contents($url))
        {
            return $result;
        }
    }

    /**  解密req_info,用于微信退款的回调
     * @param $mch_key
     * @param $req_info
     * @return mixed
     */
    public static function decipheringReqInfo($mch_key, $req_info)
    {
        $xml = openssl_decrypt(base64_decode($req_info),'aes-256-ecb',md5($mch_key),OPENSSL_RAW_DATA);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}
