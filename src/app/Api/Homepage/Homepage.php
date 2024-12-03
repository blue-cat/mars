<?php

namespace App\Api\Homepage;

use Phalapi\Api;
use App\Domain\Misc\Qiniu;
use App\Common\Common\Util;
use App\Domain\User\User as UserDomain;
use App\Domain\User\UserSession as UserSessionDomain;
use App\Domain\Media\Media as MediaDomain;

class Homepage extends Api {

    public $domain = 'https://h5store.nearby.dulcim.com';

    private function getUserIdByToken() {
        $token = \PhalApi\DI()->cookie->get('token');
        $user_id = \PhalApi\DI()->cookie->get('user_id');

        if (!$token || !$user_id) {
            return 0;
        }

        $token = addslashes($token);
        $userSession = new UserSessionDomain();
        
        return $userSession->checkSession((int)$user_id, $token) ? $user_id : 0;
    }

    public function index() {
        $selfUid = $this->getUserIdByToken();
        header("Content-type: text/html; charset=utf-8");

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $user_id = (int)Util::uidToString($id, false);

            if (!$user_id) {
                $this->showError("用户不存在");
            }

            $userInfo = UserDomain::getUserInfoById($user_id);
            if (!$userInfo || $userInfo['status'] != 1) {
                $this->showError("用户异常");
            }

            // 拉取用户的多媒体信息
            $media = new MediaDomain();
            $mediaList = $media->getMediaByObjId(1, $user_id);
            $images = array_fill(0, 6, "");

            foreach ($mediaList as $md) {
                $images[$md['order']] = $this->domain . '/' . $md['dir'];
            }

            $qrcodeImage = $this->domain . '/' . $media->getMediaByObjIdAndOrder(2, $user_id, 0)['dir'];
        }

        include(API_ROOT . '/src/view/homepage/index.php');
        exit(0);
    }

    private function showError($message) {
        echo "<div style='display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;background-color:#f5f5f5;'>
                <div style='font-size:26px;color:#999;font-weight:bold;'>{$message}</div>
              </div>";
        exit(0);
    }

    public function updateUserinfo() {
        $typeMap = [
            '1' => 'nickname',
            '2' => 'slogan',
        ];

        $selfUid = $this->getUserIdByToken();
        if (!$selfUid) {
            throw new \Exception('用户未登录', 401);
        }

        if (!isset($_POST['type']) || !array_key_exists($_POST['type'], $typeMap)) {
            throw new \Exception('类型错误', 400);
        }

        $data = $_POST['content'] ?? null;
        if (!$data) {
            throw new \Exception('参数错误', 400);
        }

        $user = new UserDomain();
        $ret = $user->update([$typeMap[$_POST['type']] => addslashes($data)], $selfUid);
        if (!$ret) {
            throw new \Exception('修改失败', 500);
        }

        return "";
    }

    public function upload() {
        $typeMap = [
            '1' => 'homepage',
            '2' => 'qrcode',
            '3' => 'mavatar',
        ];

        $selfUid = $this->getUserIdByToken();
        if (!$selfUid) {
            throw new \Exception('用户未登录', 401);
        }

        if (empty($_FILES['file'])) {
            throw new \Exception('请选择文件', 400);
        }

        if (!isset($_POST['type']) || !array_key_exists($_POST['type'], $typeMap)) {
            throw new \Exception('上传类型错误', 400);
        }

        $file = $_FILES['file'];
        $dir = $typeMap[$_POST['type']];
        $key = $file['name'];
        $size = $file['size'];
        $order = (int)$_POST['index'];

        // 验证图片信息
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            throw new \Exception('文件不是有效的图片', 400);
        }
        [$width, $height, $type] = [$imageInfo[0], $imageInfo[1], $imageInfo["mime"]];

        // 生成唯一文件名
        $name = date('YmdHis') . substr(md5(microtime()), 0, 10);
        $filePath = "$dir/$name." . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $qiniu = new Qiniu();
        $ret = $qiniu->uploadFile($filePath, $file['tmp_name']);

        if ($dir === 'mavatar') {
            (new UserDomain())->update(['avatar' => $ret['key']], $selfUid);
        } else {
            $media = new MediaDomain();
            $data = [
                'name' => addslashes($key),
                'user_id' => $selfUid,
                'obj_id' => $selfUid,
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
            ];
            $media->save((int)$_POST['type'], $selfUid, $order, $data);
        }

        return $this->domain . "/" . $ret['key'];
    }
}