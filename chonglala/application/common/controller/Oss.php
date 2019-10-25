<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/21
 * Time: 15:27
 */
namespace app\common\controller;

use app\index\controller\AppController;
use think\console\Input;
use think\Request;
use Qiniu\Auth as Auth;
use think\Db;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use think\Page;
use think\Session;
use OSS\OssClient;
use OSS\Core\OssException;

class Oss extends Base
{
    //字符串上传
    public function charStringUpload()
    {
        $str = $_POST['str'];
        $str = "\xff\xfe" . iconv('utf-8', 'utf-16le', $str);
        // 文件名称
        $object = "testString".time().".txt";
        // 文件内容
        $content = $str;
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

            $ossClient->putObject($this->bucket, $object, $content);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        $url = "http://".$this->bucket.".oss-cn-hangzhou.aliyuncs.com/".$object;
        return $url;
    }

    //文件上传
    public function fileUpload()
    {
        $str = $_POST['str'];
        $str = "\xff\xfe" . iconv('utf-8', 'utf-16le', $str);
        /*$dir_path = realpath("./");
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777);
        }*/
        $dir_path = realpath(ROOT_PATH . 'public' . DS . 'uploads');
        if (!is_dir($dir_path)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads', 0777);
        }
        $new_file = $dir_path . time() . ".txt";
        file_put_contents($new_file, $str);
        $filePath = $new_file;
        $object = rand(1000, 9999) . time() . '.' . 'txt';
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

            $ossClient->uploadFile($this->bucket, $object, $filePath);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        unlink($filePath);
        $url = "http://".$this->bucket.".oss-cn-hangzhou.aliyuncs.com/".$object;
        return $url;
    }

    //文件上传
    public function fileUploadMore($file_name,$filePath,$ext)
    {
        $object = date("YmdHis").'-'.mt_rand(1000,9999).'-'.$file_name;
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

            $ossClient->uploadFile($this->bucket, $object, $filePath);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        unlink($filePath);
        $url = "http://".$this->bucket.".oss-cn-hangzhou.aliyuncs.com/".$object;
        return $url;
    }

    //图片上传
    public function imageUpload($filePath)
    {
        if (!$filePath){
            return "10018";
        }
        $object = 'scover'.rand(10000, 99999) . time() . '.' . 'png';
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

            $ossClient->uploadFile($this->bucket, $object, $filePath);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        unlink($filePath);
        $url = "http://".$this->bucket.".oss-cn-hangzhou.aliyuncs.com/".$object;
        return $url;
    }

    //图片上传-base64
    public function imageUploadByBase64($base64_code)
    {
        /*$dir_path = realpath("./");
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777);
        }*/

        $dir_path = realpath(ROOT_PATH . 'public' . DS . 'uploads');
        if (!is_dir($dir_path)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads', 0777);
        }

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_code, $result)) {
            if (!empty($result[2])) {
                $type = $result[2];
                $filePath = $dir_path . time() . ".{$type}";
                file_put_contents($filePath, base64_decode(str_replace($result[1], '', $base64_code)));
                $object = date("YmdHis").'cover'.rand(10000, 99999) .  '.' . 'png';
                try{
                    $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
                    $ossClient->uploadFile($this->bucket, $object, $filePath);
                } catch(OssException $e) {
                    printf(__FUNCTION__ . ": FAILED\n");
                    printf($e->getMessage() . "\n");
                    return "10019";
                }
            unlink($filePath);
                $url = "http://".$this->bucket.".oss-cn-hangzhou.aliyuncs.com/".$object;
                return $url;
            }
        }
        return 10019;
    }
    //学习文件上传
    public function courseContentfileUpload($str)
    {
        $length = $this->strLength($str);
        $str = "\xff\xfe" . iconv('utf-8', 'utf-16le', $str);
        $dir_path = realpath("./");
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777);
        }
        $new_file = $dir_path . time() . ".txt";
        file_put_contents($new_file, $str);
        $filePath = $new_file;
        $object = rand(1000, 9999) . time() . '.' . 'txt';
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

            $ossClient->uploadFile($this->bucket, $object, $filePath);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        unlink($filePath);
        $url = "http://".$this->bucket.".oss-cn-hangzhou.aliyuncs.com/".$object;
        $data['need_time'] = ceil($length/500);
        $data['url'] = $url;
        return $data;
    }
    function strLength($str, $charset = 'utf-8') {
        if ($charset == 'utf-8')
            $str = iconv ( 'utf-8', 'gb2312', $str );
        $num = strlen ( $str );
        $cnNum = 0;
        for($i = 0; $i < $num; $i ++) {
            if (ord ( substr ( $str, $i + 1, 1 ) ) > 127) {
                $cnNum ++;
                $i ++;
            }
        }
        $enNum = $num - ($cnNum * 2);
        $number = ($enNum / 2) + $cnNum;
        return ceil ( $number );
    }


}