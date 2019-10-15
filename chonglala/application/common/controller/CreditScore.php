<?php

namespace app\common\controller;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use think\Controller;
use app\common\model\CreditScore as CreditScoreModel;
use think\Log;
use think\Request;
use JPush\Exceptions\APIConnectionException;
use JPush\Exceptions\APIRequestException;
use JPush\Client as JPush;

class CreditScore extends Base
{
    // 派单给app
    public function pushApp($result)
    {
        $CreditScoreModel = new CreditScoreModel();

        // 获取三个满足派单条件的医生或者医院
        $credit = $CreditScoreModel->creditScore($result);

        $first = $credit['three_person'][0]['alias'];
        $second = $credit['three_person'][1]['alias'];
        $three = $credit['three_person'][2]['alias'];

        // 引入极光sdk
        $this->jgSdk_inc();

        $client = new JPush($this->jg_app_key, $this->jg_master_secret);

        $push_payload = $client->push()
            ->setPlatform('all')
            // 多个别名
            //->addAlias(array('alias' => array($first, $second, $three)))
                ->addAllAudience()
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
            if ($response['http_code'] == 200) {
                // 如果推送成功
                $CreditScoreModel->makeOrder($result['out_trade_no']);
            }

            return $response;
        } catch (APIConnectionException $e) {
            Log::error($e->getMessage());
        } catch (APIRequestException $e) {
            Log::error($e->getMessage());
        }
    }

}
