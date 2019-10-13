<?php

namespace app\common\controller;

use app\api\model\Register as RegisterModel;
use think\Cache;
use think\Controller;
use think\Request;

class SendSms extends Base
{
    // 签名名称
    protected $SignName = '宠啦啦';
    // 用户注册模板
    protected $Register_TemplateCode = 'SMS_174985904';
    // 用户修改密码模板
    protected $Forget_TemplateCode = 'SMS_174987023';
    // 服务确认模板
    protected $Service_TemplateCode = 'SMS_174987142';

    private function inc_sms()
    {
        require_once EXTEND_PATH.'/aliyun-dysms-php-sdk/api_demo/SmsDemo.php';
    }

    // 发送短信验证码
    protected static function Sms($PhoneNumbers, $SignName, $TemplateCode, $templateParam)
    {
        SendSms::inc_sms();
        $ali_sms = new \SmsDemo();
        return $ali_sms::sendSms($PhoneNumbers, $SignName, $TemplateCode, $templateParam);
    }

    // 用于注册、忘记密码、服务确认
    public function sendVerifyCode($PhoneNumbers, $Sms_type, $templateParam)
    {
        // 获取redis中以key=>val存储的用户手机号验证码
        $cache_code = Cache::store('redis')->get("$PhoneNumbers");

        // 校验有无在验证码有效期反复请求发送验证码
        if ($cache_code) {
            return ['message' => '已发送验证码到您的手机,请勿在验证码有效期内反复发送!'];
        }

        switch ($Sms_type) {
            case '1' :
                // 注册
                $verify_code = SendSms::Sms($PhoneNumbers, $this->SignName, $this->Register_TemplateCode, $templateParam);
                break;
            case '2' :
                // 修改、忘记密码
                $verify_code = SendSms::Sms($PhoneNumbers, $this->SignName, $this->Forget_TemplateCode, $templateParam);
                break;
            case '3' :
                // 服务确认
                $verify_code = SendSms::Sms($PhoneNumbers, $this->SignName, $this->Service_TemplateCode, $templateParam);
                break;
            default:
                return $this->errorReturn('1002','类型错误',$Sms_type);
                break;
        }
        // 将对象转为数组
        $res_send = SendSms::object_switch_array($verify_code);

        return $res_send;
    }
}
