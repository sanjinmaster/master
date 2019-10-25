<?php

namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Share as ShareModel;
use think\Cache;
use think\Controller;
use think\Request;

class Share extends Base
{
    // 分享产生上下级
    public function sharePet()
    {
        $param = $this->takePostParam();

        $validate = new \think\Validate([
            ['up_id', ['require','number'],''],
            ['next_id', ['require','number'],''],
        ]);
        if (!$validate->check($param)) {
            return $this->errorReturn('1001','请求参数不符合要求',$param);
        }

        $up_id = $param['up_id'];
        $next_id = $param['next_id'];

        $data = [
            'user_id' => $up_id,
            'next_id' => $next_id,
            'create_time' => date("Y-m-d H:i:s",time())
        ];

        $MyModel = new ShareModel();

        // 校验每个人只能有一个上级
        $check_one = $MyModel->getOne($next_id);
        if ($check_one) {
            return $this->errorReturn('1001','分享失败',$check_one);
        }

        // 校验是否重复分享给一个人
        $check = Cache::get(md5($up_id.$next_id));
        if ($check['up_id'] == $up_id && $check['next_id'] == $next_id) {
            return $this->errorReturn('1001','请勿重复分享给一个人',$check);
        }

        $res = $MyModel->addNextShare($data);

        if (!$res){
            return $this->successReturn('6001',$res);
        }

        // 添加成功将用户关系组放redis
        $relation['up_id'] = $up_id;
        $relation['next_id'] = $next_id;
        Cache::store('redis')->set(md5($up_id.$next_id),$relation,0);

        return $this->successReturn('200',$res);
    }
}
