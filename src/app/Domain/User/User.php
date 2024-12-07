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

        print_r($return);exit;
        //查询用户是否存在,不存在就注册新的
        $isNew = false;
        if (!$userId = $this->loginInOpenId($return['openid'], $scene)) {

            $avatarFinal = $this->_avatar2path($avatar, $return['headimgurl']??'');

            $userInfo = [
                'nickname' => $nickname ?: $return['nickname'],
                'avatar' => $avatarFinal,
                'sex' => $return['sex'],
                'access_token' => $return['access_token'],
                'refresh_token' => $return['refresh_token'],
                'cdn_id' => \PhalApi\DI()->config->get('vendor.cur_cdn'),
            ];
            $userId = $this->register($return['openid'], $scene, $userInfo);
            $isNew = true;
        }

        //如果存在用户但是有新头像等，则修改
        if ($userId && !$isNew) {
            if ($nickname) {
                $update['nickname'] = $nickname;
            }
            if ($avatar) {
                $update['avatar'] = $this->_avatar2path($avatar, '');
                $update['cdn_id'] = \PhalApi\DI()->config->get('vendor.cur_cdn');
            }

            $update['access_token'] = $return['access_token'];
            $update['refresh_token'] = $return['refresh_token'];
            $update['update_time'] = time();

            $this->update($update, $userId);
        }

        //返回session的token
        return $userId;
    }

    public function _avatar2path($avatar, $headimgurl)
    {
        //头像换成第三方云头像
        if (($avatar || isset($headimgurl)) && !get_class($this->qn)) {
            $this->qn = new Qiniu();
        }

        return $this->qn->savePic27niu($avatar ?: $headimgurl, "mavatar");
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
        return $model->getInfosByUserIds([$id], $fullMode)[$id];
    }

    public function register($openId, $scene, $moreInfo = array())
    {
        $newUserInfo = $moreInfo;
        $scenes = \PhalApi\DI()->config->get('const.loginSceneGroup');

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

    /**
     * 获取用户信息,目前只用来查看自己的信息
     * @param unknown $userId
     * @return array|unknown
     */
    public function getUserInfo($userId, $select = '*')
    {
        $noneFields = ['fo', 'fa', 'lk'];//不存在的表字段
        $rs = array();

        $userId = intval($userId);
        if ($userId <= 0) {
            return $rs;
        }

        //如果有查头像,则同时查cdn_id
        if ((strpos($select, 'avatar') !== false || strpos($select, 'bg') !== false) && strpos($select, 'cdn_id') === false) {
            $select .= ',cdn_id';
        }

        $model = new UserModel();
        $selectAry = explode(',', $select);
        $rs = $model->get($userId, $select == '*' ? '*' : array_diff($selectAry, $noneFields));

        if (empty($rs)) {
            return $rs;
        }

        $rs['id'] = intval($rs['id']);

        //适配其他字段
        //年龄
        if (strpos($select, 'birth') !== false) {
            if (!$rs['birth'] || $rs['birth'] == UserModel::$birthMinSep) {
                $rs['age'] = 0;
            } else {
                list($year, $month, $day) = explode('-', $rs['birth']);
                $rs['age'] = (date('Y') - $year) + (date('md') - ($month . $day) > 0 ? 0 : -1);
                $rs['age'] = max($rs['age'], 0);
            }

            if ($rs['birth'] == UserModel::$birthMinSep) {
                $rs['birth'] = date('Y-m-d');
            }
        }
        //取向
        if (strpos($select, 'inclination') !== false) {
            $rs['inclination'] = (string)$rs['inclination'];
            $enum = array_flip(\PhalApi\DI()->config->get('list_enum')['inclination']);
            $rs['inclDesc'] = $enum[$rs['inclination']];
        }
        //头像拼接地址
        if (strpos($select, 'avatar') !== false && $rs['avatar']) {
            $cdnDomain = \PhalApi\DI()->config->get('client')['base_url']['cdn_url'][$rs['cdn_id']];
            $rs['avatar'] = str_replace('*', 'avatar', $cdnDomain) . $rs['avatar'];
        }
        //bg拼接地址
        if (strpos($select, 'bg') !== false && $rs['bg']) {
            $cdnDomain = \PhalApi\DI()->config->get('client')['base_url']['cdn_url'][$rs['cdn_id']];
            $rs['bg'] = str_replace('*', 'bg', $cdnDomain) . $rs['bg'];
        }
        //统计数量
        if (strpos($select, 'lk') !== false) {
            $lkInfo = $model->getLkNum([$userId]);
            $rs['lk'] = isset($lkInfo[$userId]) ? (int)$lkInfo[$userId] : 0;
        }
        $followModel = new UserFollowModel();
        if (strpos($select, 'fo') !== false) {
            $foInfo = $followModel->getFoNum([$userId]);
            $rs['fo'] = isset($foInfo[$userId]) ? (int)$foInfo[$userId] : 0;
        }
        if (strpos($select, 'fa') !== false) {
            $faInfo = $followModel->getFaNum([$userId]);
            $rs['fa'] = isset($faInfo[$userId]) ? (int)$faInfo[$userId] : 0;
        }

        $rs['ol'] = true;
        //获取地域信息
        $rs['loc'] = '';
        $cache = \Phalapi\DI()->cache;
        if (isset($cache)) {
            $rdsKey = UserSessionModel::getSessionRdsKey($userId);
            $rs['loc'] = $cache->hGet($rdsKey, 'location');
        }
        //获取半隐藏手机号
        if (strpos($select, 'mobile') !== false) {
            $rs['mobileHide'] = substr_replace($rs['mobile'], '****', 3, 4);
            unset($rs['mobile']);
        }

        return $rs;
    }

    public function update($data, $user_id)
    {

        $userModel = new UserModel();

        $data['update_time'] = $data['update_time'] ?? time();

        return $userModel->update($user_id, $data);
    }

    // 密码加密算法
    public function encryptPassword($password, $salt)
    {
        return md5(md5(\PhalApi\DI()->config->get('const.common_salt')) . md5($password) . sha1($salt));
    }
}
