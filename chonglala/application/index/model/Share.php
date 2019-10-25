<?php

namespace app\index\model;

use think\Log;
use think\Model;

class Share extends Model
{
    // 产生上下级
    public function addNextShare($data)
    {
        try {

            $res = db('user_relation')->insertGetId($data);

            // 如果下级产生成功,更新user表next_num下级数量
            if ($res) {
                $res_next_num = db('user')->field('next_num')->where('user_id',$data['user_id'])->find();
                $next_num = $res_next_num['next_num'];
                db('user')->where('user_id',$data['user_id'])->setField('next_num',$next_num + 1);
            }
            return $res;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    // 校验只能有一个上级
    public function getOne($next_id)
    {
        return db('user_relation')->field('user_id')->where('next_id',$next_id)->find();
    }
}
