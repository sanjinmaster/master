<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11
 * Time: 15:13
 */
namespace app\index\controller;

use app\common\controller\Base;
use think\Cache;
use think\cache\driver\Redis;
use think\Db;
use think\Request;
use think\Validate;

class Login extends Base
{
    public function wxLogin()
    {
        $param = $this->takeGetParam();
        $openid = $param['openid'];
        $validate = new Validate([
            ['openid', ['require'],''],
        ]);

        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $user = new \app\index\model\User();
        $user_msg = $user->getUserByOpenid($openid);

        if (!$user_msg){
            return $this->errorReturn('410','openid未保存',$param);
        }
        if ($user_msg['deleted']==1){
            return $this->errorReturn('410','该用户已被禁用',$param);
        }

        return $this->successReturn('200',$user_msg);
    }

    /**
     * 获取用户openid
     */
    public function getWxOpenid()
    {
        $param = $this->takeGetParam();
        $code = $param['code'];

        $validate = new Validate([
            ['code', ['require'],''],
            ['headimg', ['require'],''],
            ['nickname', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $appId = 'wx152de2fb5c169fd2';
        $appSecret = 'c33b0422e8ec5b322d9a032588a99961';
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appSecret&js_code=$code&grant_type=authorization_code";

        if($result=file_get_contents($url))
        {
            $res = json_decode($result,true);
            $openid = $res['openid'];

            if ($openid){
                $param['openid'] = $openid;

                //第一次获取openid时，同时创建用户
                $user = new \app\index\model\User();
                $user_msg = $user->getUserByOpenid($openid);

                if (!$user_msg){
                    $user->createUser($param);      //创建用户
                }

                $data['openid'] = $openid;

                /*Cache::set('code',$code);
                Cache::set('openid',$openid);*/

                return $this->successReturn('200',$data);

            }

            /*if (Cache::get('code') == $code) {
                $data['openid'] = Cache::get('openid',$openid);
                return $this->successReturn('200',$data);
            }*/

            return $this->errorReturn('1002','code错误，获取openid失败',$param);
        }
    }
}