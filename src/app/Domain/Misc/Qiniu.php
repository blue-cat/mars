<?php
namespace App\Domain\Misc;

use App\Common\Common\Util;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use PhalApi\Exception;


class Qiniu {

    protected $accessKey = '';

    protected $secretKey = '';

    protected $bucket = '';

    protected $confBucket = '';

    public function __construct()
    {
        $di = \PhalApi\DI()->config->get('vendor.qiniu_store');
        $this->accessKey = $di['accessKey'];
        $this->secretKey = $di['secretKey'];
        $this->bucket = $di['bucket'];
        $this->confBucket = $di['confBucket'];
    }

    /**
     * @param $name 存在name的时候文件可覆写
     * @return string
     */
    public function getUploadToken($name = null, $isConf = false) {

        $auth = new Auth($this->accessKey, $this->secretKey);

        $token = $auth->uploadToken($isConf ? $this->confBucket : $this->bucket, $name);

        return $token;
    }

    /**
     * 上传二进制数据到文件, 可复写的
     * @param $name
     * @param $data
     * @return mixed
     */
    public function uploadData($name, $data, $isOverWrite = false, $isConf = false) {
        $uploadMgr = new UploadManager();
        $token = $this->getUploadToken($isOverWrite ? $name : null, $isConf);
        list($ret, $error) = $uploadMgr->put($token, $name, $data);
        if ($error) {
            $err = "生成错误: $name " . $error->message() . ", 退出\n";
            throw new Exception($err);
        }
        return $ret;
    }

    public function uploadFile($name, $filePath, $isOverWrite = false, $isConf = false) {
        $uploadMgr = new UploadManager();
        $token = $this->getUploadToken($isOverWrite ? $name : null, $isConf);
        list($ret, $error) = $uploadMgr->putFile($token, $name, $filePath);
        if ($error) {
            $err = "生成错误: $name " . $error->message() . ", 退出\n";
            throw new Exception($err);
        }
        return $ret;
    }

    public function savePic27niu($urlOrBase64, $path = 'avatar', $fileName = '', $poster = 'jpg') {
        $baseName = ($fileName ?: time() . md5($urlOrBase64)) . '.' . $poster;
        $name = $path . '/' . $baseName;

        $localTmpDir = '/tmp/' . $baseName;

        $res = '';

        //先下载到本地
        if (filter_var($urlOrBase64, FILTER_VALIDATE_URL)) {
            Util::fetchContent($urlOrBase64, $localTmpDir);
            if (file_exists($localTmpDir)) {
                $imageInfo = getimagesize($localTmpDir);
                if (!$imageInfo[0]) {
                    return $res;
                }
//            $typeInfo = explode('/', $imageInfo['mime']);
//            $isVideoX = $typeInfo[0] != 'image';

                $ret = $this->uploadFile($name, $localTmpDir, (bool)$fileName);
//            if ($ret && $ret['key']) {
//                $res = [
//                    'imageInfo' => $imageInfo,
//                    'ret' => $ret,
//                ];
//            }
                $res = $ret;
                unlink($localTmpDir);
            }
        } else if(strpos($urlOrBase64, 'base64') !== false) {
            $array = explode(',', $urlOrBase64);
            $imgData = base64_decode(end($array));
            $res = $this->uploadData($name, $imgData, (bool)$fileName);
        }

        return $res ? $res['key'] : '';
    }

}