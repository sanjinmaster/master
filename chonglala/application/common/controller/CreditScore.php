<?php

namespace app\common\controller;

use think\Controller;
use app\common\model\CreditScore as CreditScoreModel;
use think\Request;

class CreditScore extends Base
{
    // 根据规则计算出医院和医生的信用分,用于派单

    // 获取医院、医生的信用分
    public static function getCreditScore($result)
    {
        $CreditScoreModel = new CreditScoreModel();
        $res = $CreditScoreModel->creditScore($result);
        return $res;
    }
}
