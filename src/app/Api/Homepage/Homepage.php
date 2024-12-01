<?php

namespace App\Api\Homepage;

use Phalapi\Api;
use App\Domain\Misc\Qiniu;
use App\Common\Common\Util;


class Homepage extends Api {

    public $domain = 'https://h5store.nearby.dulcim.com';

    public function index() {
        // 改为页面展示
        header("Content-type: text/html; charset=utf-8");
        // 根据uid获取用户信息
        $id = $_GET['id'];
        echo $id;
        $user_id = Util::strToUid($id);
        echo $user_id;

        include(API_ROOT . '/src/view/homepage/index.php');
        exit(0);
    }

    /**
     * 上传接口，用户提交内容在$_FILES中，然后调用七牛云的接口，将图片内容传到七牛云上，并返回图片的URL地址
     */
    public function upload($type = "homepage") {
        $file = $_FILES['file'];
        $key = $file['name'];

        //生成一个全局唯一的文件名，以日期为前缀，后面全部为随机字符串，不少于20位
        $name = date('YmdHis'). substr(md5(microtime()), 0, 10);
        // 文件名后缀必须为小写
        $filePath = $type. '/' . $name . '.' . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $qiniu = new Qiniu();
        $ret = $qiniu->uploadFile($filePath, $file['tmp_name']);
        return $this->domain . "/" . $ret['key'];
    }
}

