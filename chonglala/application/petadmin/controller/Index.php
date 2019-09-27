<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use think\Controller;
use think\Log;
use think\Request;
use app\petadmin\model\Index as IndexModel;

class Index extends Base
{
    // 报表管理--订单统计
    public function orderReport()
    {
        $param = $this->takeGetParam();
        // 订单统计月份
        $order_report_time = $param['order_report_time'];
        // 如果没有选择筛选月份,拿当前月份
        $screen_time = $order_report_time ? $order_report_time : date('Y-m');

        // 返回当月的第一天和最后一天
        $start_date = $this->startTimeReturn($screen_time);
        $end_date = $this->endTimeReturn($screen_time);

        $IndexModel = new IndexModel();
        $reportData = $IndexModel->getReportDate($start_date, $end_date);

        $total_amount = null;
        $rows = null;
        $data = null;
        $date = null;
        foreach ($reportData as $v) {
            $total_amount += $v['deal_amount'];
            $rows['pay_date'] = $v['pay_date'];
            $rows['deal_amount'] = $v['deal_amount'];
            $data[] = $rows;
        }

        $data['total_amount'] = sprintf('%0.2f',$total_amount);

        if (is_array($data)) {
            return $this->successReturn('200',$data);
        }
    }

    // 报表管理--资金流动
    public function amountReport()
    {
        $param = $this->takeGetParam();
        // 订单统计月份
        $capital_report_time = $param['capital_report_time'];
        // 如果没有选择筛选月份,拿当前月份
        $screen_time = $capital_report_time ? $capital_report_time : date('Y-m');

        // 返回当月的第一天和最后一天
        $start_date = $this->startTimeReturn($screen_time);
        $end_date = $this->endTimeReturn($screen_time);

        $IndexModel = new IndexModel();
        // 收入
        $reportData = $IndexModel->getReportDate($start_date, $end_date);
        // 支出
        $reportTakeOut = $IndexModel->reportTakeOut($start_date, $end_date);

        $total_amount = null;
        $take_amount = null;
        $row = null;
        $rows = null;
        $data = null;
        $date = null;
        foreach ($reportData as $v) {
            $total_amount += $v['deal_amount'];
            $row['pay_date'] = $v['pay_date'];
            $row['deal_amount'] = $v['deal_amount'];
            $rows[$row['pay_date']] = $row;
        }

        foreach ($reportTakeOut as $item) {
            $take_amount += $item['take_out_amount'];
            $row['take_out_date'] = $item['take_out_date'];
            $row['take_out_amount'] = $item['take_out_amount'];
            $rows[$row['take_out_date']] = $row;
        }
        $data['daily_details'] = $rows;
        $data['total_amount'] = sprintf('%0.2f',$total_amount);
        $data['take_out_amount'] = sprintf('%0.2f',$take_amount);

        if (is_array($data)) {
            return $this->successReturn('200',$data);
        }
    }

    // 报表管理--流量统计
    public function flowReport()
    {

    }

}
