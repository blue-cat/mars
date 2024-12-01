<?php

namespace App\Api\Homepage;

use Phalapi\Api;
use App\Domain\Misc\Qiniu;
use App\Common\Common\Util;
use App\Domain\User\User as UserDomain;
use App\Domain\User\UserSession as UserSessionDomain;

class Homepage extends Api {

    public $domain = 'https://h5store.nearby.dulcim.com';

    /**
     * 从用户cookie中拿到token，然后根据token获取用户信息
     */
    private function getUserIdByToken() {

        $token = \PhalApi\DI()->cookie->get('token');
        $user_id = \PhalApi\DI()->cookie->get('user_id');

        if (!$token ||!$user_id) {
            return 0;
        }

        // 对token进行防注入处理
        $token = addslashes($token);

        $userSession = new UserSessionDomain();
        $isSuccess = $userSession->checkSession((int)$user_id, $token);

        return $isSuccess? $user_id : 0;
    }

    public function index() {
        // 判断用户是否登录
        $selfUid = $this->getUserIdByToken();

        // 改为页面展示
        header("Content-type: text/html; charset=utf-8");
        // 根据uid获取用户信息
        $isMe = $selfUid > 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $user_id = Util::uidToString($id, false);
            if (!$user_id) {
                // 给个简单样式，文字在整个屏幕上下左右居中显示，并使用灰色文字，使用16号字体.使用flex布局.
                echo "<div style='display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;background-color:#f5f5f5;'><div style='font-size:26px;color:#999;font-weight:bold;'>用户不存在</div></div>";
                exit(0);
            }

            $userInfo = UserDomain::getUserInfoById($user_id);
            if (!$userInfo || $userInfo['status']!= 1) {
                echo "<div style='display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;background-color:#f5f5f5;'><div style='font-size:26px;color:#999;font-weight:bold;'>用户异常</div></div>";
                exit(0);
            }

            $isMe = $user_id == $selfUid;
        }

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

