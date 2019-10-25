<?php

namespace app\common\controller;

use think\Controller;
use think\Request;

class Code extends Base
{
    /**
     * @param $code
     * @return mixed|string
     * 接口调用成功状态码
     */
    public function getCodeInfo($code)
    {
        $code_arr = [
            '200' => "OK",                  //[GET]：服务器成功返回用户请求的数据，该操作是幂等的
            '201' => "CREATED",             //[POST/PUT/PATCH]：用户新建或修改数据成功
            '202' => "Accepted",            //[*]：表示一个请求已经进入后台排队（异步任务）
            '204' => "NO CONTENT",          //[DELETE]：用户删除数据成功
            '6001' => "No Result",          //没有符合条件的数据
            //用户2
            '2001' => "当前登录人数过多，请稍候再试",
        ];
        $msg = "状态码不存在";
        foreach ($code_arr as $key => $value){
            if ($code == $key){
                $msg = $value;
            }
        }
        return $msg;
    }

    /**
     * @param $code
     * @return mixed|string
     * 接口调用失败状态码
     */
    public function getErrorCodeInfo($code)
    {
        $code_arr = [
            '400' => "INVALID REQUEST",             //[POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的
            '401' => "Unauthorized",                //[*]：表示用户没有权限（令牌、用户名、密码错误）
            '403' => "Forbidden",                   //[*] 表示用户得到授权（与401错误相对），但是访问是被禁止的
            '404' => "NOT FOUND",                   //[*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的
            '406' => "Not Acceptable",              //[GET]：用户请求的格式不可得（比如用户请求JSON格式，但是只有XML格式）
            '410' => "Gone",                        //[GET]：用户请求的资源被永久删除，且不会再得到的
            '422' => "Unprocesable entity",         //[POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误
            '500' => "INTERNAL SERVER ERROR",       //[*]：服务器发生错误，用户将无法判断发出的请求是否成功
            '1001' => "Param Incomplete",           //请求参数不符合要求
            '1002' => "Param content error",        //无满足条件的返回
            '1005' => "文件上传失败",
            '9003' => "token过期",
        ];
        $msg = "状态码不存在";
        foreach ($code_arr as $key => $value){
            if ($code == $key){
                $msg = $value;
            }
        }
        return $msg;
    }
}
