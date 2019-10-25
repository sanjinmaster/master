<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/27
 * Time: 10:28
 */
namespace app\common\controller;

use app\common\controller\Base;
use app\common\controller\Oss;
use app\index\controller\AppController;
use think\console\Input;
use think\Request;
use Qiniu\Auth as Auth;
use think\Db;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use think\Page;
use think\Session;

class Upload extends Base
{
    //文件上传
    public function uploadFile()
    {
        // 校验文件合法性
        if (!$_FILES['file']) {
            return $this->errorReturn("1001", $_FILES['file']);
        }

        if($_FILES['file']['error']>0){
            exit('附件有错误');
        }

        //附件上传逻辑
        //A. 附件存储目录 和 名字
        $dir = realpath(ROOT_PATH . 'public' . DS . 'uploads');
        if (!is_dir($dir)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads', 0777);
        }

        //附件后缀
        $ext = substr($_FILES['file']['name'],strrpos($_FILES['file']['name'],"."));
        $file_name = $_FILES['file']['name'];
        $name = date("YmdHis").'-'.mt_rand(1000,9999).$ext;
        $dir_name = $dir.$name;
        //B. move_uploaded_file() 把附件从"临时路径名"移动到"真实路径名"

        if(move_uploaded_file($_FILES['file']['tmp_name'],$dir_name)){
            $oss = new Oss();
            $url = $oss->fileUploadMore($file_name,$dir_name,$ext);
            $data['url'] = $url;
            return $this->successReturn('200',$data);
        }else{
            return $this->errorReturn('1005');
        }
    }

    // 字符串上传
    public function strFileUpload()
    {
        $oss = new Oss();
        $data = $oss->fileUpload();
        return $this->successReturn('200',$data);
    }

    // base64图片上传
    public function uploadBase64()
    {
        // 校验文件合法性
        $param = $this->takePostParam();

        $base64_code = $param['file'];

        if(!$base64_code){
            exit('附件有错误');
        }

        $oss = new Oss();
        $data = $oss->imageUploadByBase64($base64_code);
        return $this->successReturn('200',$data);
    }
}