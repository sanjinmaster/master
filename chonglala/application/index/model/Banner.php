<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/10
 * Time: 11:40
 */
namespace app\index\model;

use think\Model;
use think\Db;
class Banner extends Model
{
    // 轮播图表
    protected $table = 'banner';

    // 获取首页轮播图
    public function getBanner()
    {
        return $this->field('url')->where(['deleted' => 0])->select();
    }
}