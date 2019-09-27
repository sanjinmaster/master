<?php

namespace app\petadmin\model;

use think\Model;

class Index extends Model
{
    // 订单主表
    protected $table = 'order_master';

    // 订单统计
    public function getReportDate($start_date, $end_date)
    {
        $sql = sprintf("SELECT pay_date,SUM(deal_amount) AS deal_amount FROM `order_master`
    WHERE `pay_date` >= '%s' AND `pay_date` <= '%s' GROUP BY `pay_date`", $start_date, $end_date);
        return $this->query($sql);
    }

    // 资金流动--总支出
    public function reportTakeOut($start_date, $end_date)
    {
        $sql = sprintf("SELECT take_out_date,SUM(take_out_amount) AS take_out_amount FROM `reward_take_out`
    WHERE `take_out_date` >= '%s' AND `take_out_date` <= '%s' GROUP BY `take_out_date`", $start_date, $end_date);
        return $this->query($sql);
    }

}
