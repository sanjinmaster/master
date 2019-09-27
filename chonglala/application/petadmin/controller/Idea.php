<?php

namespace app\petadmin\controller;

use app\common\controller\Base;
use app\petadmin\model\Idea as IdeaModel;
use think\Controller;
use think\Request;

class Idea extends Base
{
    // 意见反馈列表
    public function ideaList()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['page', ['require','number'],''],
            ['pageSize', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $order = 'id desc';
        $page = $param['page'];
        $pageSize = $param['pageSize'];
        $IdeaModel = new IdeaModel();
        $res = $IdeaModel->idList($order, $page, $pageSize);

        return $this->successReturn('200',$res);
    }

    // 查看意见反馈
    public function lookIdea()
    {
        $param = $this->takeGetParam();

        $validate = new \think\Validate([
            ['feedback_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $feedback_id = $param['feedback_id'];
        $IdeaModel = new IdeaModel();
        $res = $IdeaModel->getOneIdea($feedback_id);

        return $this->successReturn('200',$res);

    }

    // 删除
    public function delIdea()
    {
        $param = $this->takeDeleteParam();

        $validate = new \think\Validate([
            ['feedback_id', ['require'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $feedback_id = $param['feedback_id'];
        $IdeaModel = new IdeaModel();
        $res = $IdeaModel->deleteIdea($feedback_id);

        return $this->successReturn('200',$res);
    }
}
