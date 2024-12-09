<?php

namespace App\Api\Homepage;

use Phalapi\Api;
use App\Domain\Misc\Qiniu;
use App\Common\Common\Util;
use App\Domain\User\User as UserDomain;
use App\Domain\User\UserSession as UserSessionDomain;
use App\Domain\Media\Media as MediaDomain;
use App\Domain\Misc\Filter;
use App\Common\User\Weixin;

class Homepage extends Api {

    public $domain;

    //obj_type 1 homepage列表 2 二维码 3 头像

    public function __construct() {
        $this->domain = \PhalApi\DI()->config->get('vendor.cdn_url');
    }
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

        $selfId = '';
        if ($selfUid > 0) {
            $selfId = Util::uidToString($selfUid, true);
        }

        $images = [];
        for ($i = 0; $i < 6; $i++) {
            $images[] = "";
        }
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $user_id = (int) Util::uidToString($id, false);
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

            $details = $userInfo['slogan'];

            // 拉取用户的图片
            $media = new MediaDomain();
            $mediaList = $media->getMediaByObjId(1, $user_id);
            // 一定会返回6张图片
            foreach ($mediaList as $md) {
                if ($md['status'] == $media::MEDIA_OK || $md['status'] == $media::MEDIA_PRE) {
                    $images[$md['order']] = $this->domain[$md['cdn_id']]. '/'. $md['dir'];
                }
            }

            // 拉取用户的二维码
            $qrcodeImage = "";
            $qrCodeInfo = $media->getMediaByObjIdAndOrder(2, $user_id, 0);
            if ($qrCodeInfo && ($qrCodeInfo['status'] == $media::MEDIA_OK || $qrCodeInfo['status'] == $media::MEDIA_PRE)) {
                $qrcodeImage = $this->domain[$qrCodeInfo['cdn_id']] . "/" . $qrCodeInfo['dir'];
            }
        }

        // 返回签名等
        list($appid, $h5AppSecret) = array_values(\PhalApi\DI()->config->get('vendor.weixin.h5'));
        $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $jsapiTicket = $this->getJsapiTicket();
        $nonceStr = bin2hex(random_bytes(16));
        $timestamp = time();
        $signature = $this->generateSignature($jsapiTicket, $timestamp, $nonceStr, $url);

        include(API_ROOT . '/src/view/homepage/index.php');
        exit(0);
    }

    /**
     * 修改用户资料接口
     */
    public function updateUserinfo() {
        $typeMap = [
            '1' => 'nickname',
            '2' => 'slogan',
        ];
        // 判断用户是否登录
        $selfUid = $this->getUserIdByToken();
        if (!$selfUid) {
            throw new \Exception('用户未登录', 401);
        }

        if (!in_array($_POST['type'], array_keys($typeMap))) {
            throw new \Exception('类型错误', 400);
        } 

        $data = $_POST['content'];
        if (!$data) {
            throw new \Exception('内容不能为空！', 400);
        }

        //检测敏感词
        var_dump($data);exit;
        if (!Filter::check($data)) {
            throw new \Exception('包含违规内容！', 422);
        }

        $user = new UserDomain();
        $ret = $user->update([$typeMap[$_POST['type']] => addslashes($data)], $selfUid);
        if (!$ret || $ret <= 0) {
            throw new \Exception('修改失败', 500);
        }

        return "";
    }

    /**
     * 上传接口，用户提交内容在$_FILES中，然后调用七牛云的接口，将图片内容传到七牛云上，并返回图片的URL地址
     */
    public function upload() {
        $typeMap = [
            '1' => 'homepage',
            '2' => 'qrcode',
            '3' => 'mavatar',
        ];
        // 判断用户是否登录
        $selfUid = $this->getUserIdByToken();
        if (!$selfUid) {
            throw new \Exception('用户未登录', 401);
        }

        $file = $_FILES['file'];
        if (!$file) {
            throw new \Exception('请选择文件', 400);
        }

        if (!in_array($_POST['type'], array_keys($typeMap))) {
            throw new \Exception('上传类型错误', 400);
        }
        $dir = $typeMap[$_POST['type']];

        $key = $file['name'];
        $size = $file['size'];        

        $order = (int)$_POST['index'];
        //获取file的宽高和类型
        $imageInfo = getimagesize($file['tmp_name']);
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo["mime"];

        //生成一个全局唯一的文件名，以日期为前缀，后面全部为随机字符串，不少于20位
        $name = date('YmdHis'). substr(md5(microtime()), 0, 10);
        // 文件名后缀必须为小写
        $filePath = $dir. '/' . $name . '.' . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $qiniu = new Qiniu();
        $ret = $qiniu->uploadFile($filePath, $file['tmp_name']);

        if ($dir == 'mavatar') {
            // 调用userDomain中的modify方法
            $user = new UserDomain();
            $user->update(['avatar' => $ret['key']], $selfUid);
            return $this->domain[1] . "/" . $ret['key'];
        }

        // 往media表写
        $media = new MediaDomain();
        $data = [
            'name' => addslashes($key),
            'user_id' => $selfUid,
            'obj_id' => $selfUid,
            // 'obj_type' => 1,
            'order' => $order,
            'dir' => $ret['key'],
            'width' => $width,
            'height' => $height,
            'size' => $size,
            'type' => $type,
            'is_video' => 0,
            'length' => 0,
            'v_dir' => "",
            'cdn_id' => 1,
            'status' => 0,
        ];
        $media->save((int)$_POST['type'], $selfUid, $order, $data);

        return $this->domain[1] . "/" . $ret['key'];
    }

    /**
     * 删除qr图片，实际是将status设置为2
     */
    public function deleteQrcode() {
        // 判断用户是否登录
        $selfUid = $this->getUserIdByToken();
        if (!$selfUid) {
            throw new \Exception('用户未登录', 401);
        }

        $media = new MediaDomain();
        $qrCodeInfo = $media->getMediaByObjIdAndOrder(2, $selfUid, 0);
        if ($qrCodeInfo) {
            $data = [
                'status' => $media::MEDIA_DEL,
                'update_time' => time(),
            ];
            $media->update($qrCodeInfo['id'], $data);
        }
        return "";
    }

    /**
     * 获取jsapi_ticket
     */
    public function getJsapiTicket() {
        // 先从redis中取，如果有就直接返回，如果没有就重新生成，然后写入redis，再返回
        $cache = \Phalapi\DI()->cache;
        if (isset($cache)) {
            $signature = $cache->get('h5_jsapi_ticket');
            if ($signature) {
                return $signature;
            }
        }

        $weixin = new Weixin();
        $signature = $weixin->getH5JsapiTicket();
        $cache->set('h5_jsapi_ticket', $signature, 7200);
        return $signature;
    }

    function generateSignature($ticket, $timestamp, $nonceStr, $url) {
        $string = "jsapi_ticket={$ticket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
        return sha1($string);
    }
}

