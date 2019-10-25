<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\SearchZx as SearchZxModel;
use think\Controller;
use think\Request;

class SearchZx extends Base
{
    // 咨询搜索
    public function searchZxInfo()
    {
        $param = $this->takeGetParam();

        $key_word = $param['key_word'];
        if ($key_word == null) {
            return $this->successReturn('200',null);
        }

        $SearchZxModel = new SearchZxModel();
        $res = $SearchZxModel->getZxInfo($key_word);

        return $this->successReturn('200',$res);
    }
}
