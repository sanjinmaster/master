<?php

namespace app\api\controller;

use app\admin\controller\Log;
use app\common\controller\CreditScore;
use app\common\model\CreditScore as CreditScoreModel;
use think\Controller;
use app\api\model\MeetOrder as MeetOrderModel;
use think\Request;
use JPush\Exceptions\APIConnectionException;
use JPush\Exceptions\APIRequestException;
use JPush\Client as JPush;

class MeetOrder extends Base
{
    // 待接单状态下认证过的医生抢单
    public function receiptOrder()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 认证过的医生
        $rz_doctor_id = $param['id'];
        $order_num = $param['order_num'];

        $MeetOrderModel = new MeetOrderModel();
        $res = $MeetOrderModel->receiptRzDoctor($rz_doctor_id, $order_num);

        if (!$res){
            return $this->successReturn('6001','您来晚一步,订单已被其他人抢到');
        }

        return $this->successReturn('200',$res);
    }

    // 待接单状态下-认证过的医生、医院弃单
    public function giveUpOrderDoctor()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['alias', ['require','number'],''],
            ['out_trade_no', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 如果认证过的医生弃单,继续选择另外两个进行派单
        $res = $this->pushApp($param);

        return $res;
    }

    // 如果有人弃单，继续派单给app
    public function pushApp($result)
    {
        $CreditScoreModel = new CreditScoreModel();

        // 获取三个满足派单条件的医生或者医院
        $credit = $CreditScoreModel->creditScore($result);

        // 去掉弃单的那个人
        foreach ($credit['three_person'] as $value) {
            if ($value['alias'] == $result['alias']) {
                unset($value['alias']);
            }
        }

        $first = $credit['three_person'][0]['alias'];
        $second = $credit['three_person'][1]['alias'];
        $three = $credit['three_person'][2]['alias'];

        // 引入极光sdk
        $this->jgSdk_inc();

        $client = new JPush($this->jg_app_key, $this->jg_master_secret);

        $push_payload = $client->push()
            ->setPlatform('all')
            // 多个别名
            ->addAlias(array('alias' => array($first, $second, $three)))
            //->addAllAudience()
            ->message('message content', array(
                'title' => '订单编号',
                // 'content_type' => 'text',
                'extras' => array(
                    'order_num' => $result['out_trade_no']
                ),
            ))
            //->addAllAudience()
            ->setNotificationAlert('您有个一张新的订单,赶快抢单吧!');

        try {

            $response = $push_payload->send();

            return $response;
        } catch (APIConnectionException $e) {
            \think\Log::error($e->getMessage());
        } catch (APIRequestException $e) {
            \think\Log::error($e->getMessage());
        }
    }

    // 待接单状态下医院抢单
    public function receiptHospital()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 认证过的医生
        $hospital_id = $param['id'];
        $order_num = $param['order_num'];

        $MeetOrderModel = new MeetOrderModel();
        $res = $MeetOrderModel->receiptHospital($hospital_id, $order_num);

        if (!$res){
            return $this->successReturn('6001','您来晚一步,订单已被其他人抢到');
        }

        return $this->successReturn('200',$res);
    }

    // 医院下面的医生列表
    public function doctorList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 认证过的医生
        $hospital_id = $param['id'];

        $MeetOrderModel = new MeetOrderModel();
        $res = $MeetOrderModel->getDoctor($hospital_id);

        if (!$res){
            return $this->successReturn('6001','您还没有医生');
        }

        return $this->successReturn('200',$res);
    }

    // 医院派单给下面的医生
    public function makeOrderDoctor()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['order_num', ['require','number'],''],
            ['alias', ['require','']],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 认证过的医生
        $order_num = $param['order_num'];
        $alias = $param['alias'];

        // 引入极光sdk
        $this->jgSdk_inc();

        $client = new JPush($this->jg_app_key, $this->jg_master_secret);

        $push_payload = $client->push()
            ->setPlatform('all')
            // 多个别名
            ->addAlias(array('alias' => array($alias)))
            //->addAllAudience()
            ->message('message content', array(
                'title' => '订单编号',
                // 'content_type' => 'text',
                'extras' => array(
                    'order_num' => $order_num
                ),
            ))
            //->addAllAudience()
            ->setNotificationAlert('您有个一张新的订单,赶快抢单吧!');

        try {

            $response = $push_payload->send();

            return $response;
        } catch (APIConnectionException $e) {
            \think\Log::error($e->getMessage());
        } catch (APIRequestException $e) {
            \think\Log::error($e->getMessage());
        }
    }

    // 医院下面的医生接单(确认)
    public function confirmOrder()
    {
        $param = $this->takePutParam();

        $validate = new \think\Validate([
            ['id', ['require','number'],''],
            ['order_num', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        // 认证过的医生
        $doctor_id = $param['id'];
        $order_num = $param['order_num'];

        $MeetOrderModel = new MeetOrderModel();
        $res = $MeetOrderModel->receiptDoctor($doctor_id, $order_num);

        return $this->successReturn('200',$res);
    }
}
