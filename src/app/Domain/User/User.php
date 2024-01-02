<?php

namespace App\Domain\User;

use App\Common\User\Weixin;
use App\Domain\Misc\Es;
use App\Domain\Misc\Qiniu;
use App\Domain\Misc\UniSmsClient;
use App\Domain\Thread\Thread;
use App\Model\Talk\GroupRelate as GroupRelateModel;
use App\Model\User\User as UserModel;
use App\Model\User\UserCode as UserCodeModel;
use App\Model\User\UserFollow as UserFollowModel;
use App\Model\User\UserSession as UserSessionModel;
use App\Model\User\UserSetting as UserSettingModel;
use PhalApi\Exception;
use PhalApi\Exception\BadRequestException;

class User
{
    protected $qn;

    public function code2Uid($code, $scene, $nickname = '', $avatar = '')
    {
        $weixin = new Weixin();
        $return = [];
        //类型参考const中的配置
        switch ($scene) {
            case 1:
                $return = $weixin->h5Code2UserInfo($code);
                break;
            case 2:
                $return = $weixin->appletCode2Openid($code);
                break;
        }

        //查询用户是否存在,不存在就注册新的
        if (!$userId = $this->loginInOpenId($return['openid'], $scene)) {

            //头像换成第三方云头像
            if (($avatar || isset($return['headimgurl'])) && !get_class($this->qn)) {
                $this->qn = new Qiniu();
            }

            $avatarFinal = '';
            if ($avatar) {
                if (strpos($avatar, 'base64') !== false) {
                    $array = explode(',', $avatar);
                    $imgData = base64_decode(end($array));
                    $avatarFinal = $this->qn->savePic27niu($imgData);
                }
            } else if(isset($return['headimgurl'])) {
                $avatarFinal = $this->qn->savePic27niu($return['headimgurl']);
            }
            $userInfo = [
                'nickname' => $return['nickname'] ?? $nickname,
                'avatar' => $avatarFinal,
            ];
            $userId = $this->register($return['openid'], $scene, $userInfo);
        }

        //返回session的token
        return $userId;
    }


    public function getUserByOpenId($openId, $scene, $select)
    {
        $model = new UserModel();
        return $model->getInfoByOpenId($openId, $scene, $select);
    }

    public function getUserById($id, $select = '*')
    {
        $model = new UserModel();
        return $model->getDataById($id, $select);
    }

    public function getUserInfoById($id, $fullMode = false)
    {
        $model = new UserModel();
        return $model->getInfosByUserIds($id, $fullMode)[$id];
    }

    public function register($openId, $scene, $moreInfo = array())
    {
        $newUserInfo = $moreInfo;
        $scenes = array_keys(\PhalApi\DI()->config->get('const.loginSceneGroup'));

        $newUserInfo[$scenes[$scene]] = $openId;
        $newUserInfo['scene'] = $scene;
        $time = time();

        $newUserInfo['create_time'] = $time;
        $newUserInfo['update_time'] = $time;

        $userModel = new UserModel();
        $newUserInfo['status'] = $userModel::OK;

        $id = $userModel->insert($newUserInfo);

        return intval($id);
    }

    /**
     * 使用openId登录
     * @param $openId
     * @param $scene
     * @param $allowNotExists 是否允许不存在时不报错
     * @return mixed
     * @throws BadRequestException
     */
    public function loginInOpenId($openId, $scene, $allowNotExists = true)
    {
        $user = $this->getUserByOpenId($openId, $scene, 'id, status');
        if (!$user) {
            if ($allowNotExists) {
                return 0;
            }
            throw new BadRequestException('不存在的用户', 310);
        }

        if ($user['status'] != UserModel::OK) {
            throw new BadRequestException('用户已被封禁或已删除', 312);
        }

        return $user['id'];
    }

    public function update($data, $user_id)
    {

        $userModel = new UserModel();

        return $userModel->update($user_id, $data);
    }

    // 密码加密算法
    public function encryptPassword($password, $salt)
    {
        return md5(md5(\PhalApi\DI()->config->get('const.common_salt')) . md5($password) . sha1($salt));
    }
}
