<?php

namespace app\index\model;

use think\Model;

class SearchZx extends Model
{
    // 咨询搜索
    public function getZxInfo($key_word)
    {
        $res = db('consultation_details')
            ->field('id,consultation_title')
            ->where('consultation_title','like',"%$key_word%")
            ->select();

        return $res;
    }
}
