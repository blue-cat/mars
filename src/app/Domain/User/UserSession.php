<?php
namespace App\Domain\User;
use App\Common\User\UserUtil;
use App\Model\User\UserSession as UserSessionModel;

/**
 * 会话领域类
 */

class UserSession {

    /**
     * 创建新的会话
     * @param int $userId 用户ID
     * @return string 会话token
     */
    public function generate($userId)
    {
        if ($userId <= 0) {
            return '';
        }

        $token = strtoupper(substr(sha1(uniqid(NULL, TRUE)), 0, 32));
        $time = time();

        $util = new UserUtil();
        $newSession = array();
        $newSession['user_id'] = $userId;
        $newSession['token'] = $token;
        $newSession['ip'] = $util->getIP();
        $newSession['location'] =  $util->ip2Region($newSession['ip']);
        $newSession['login_time'] = $time;
        $newSession['update_time'] = $time;
        $newSession['expires_time'] = $time + self::getMaxExpireTime();

        $sessionModel = new UserSessionModel();
        $sessionModel->generateSession($newSession);

        return $token;
    }

    public function checkSessionSimple($user_id, $token) {
        $model = new UserSessionModel();
        return $model->checkSessionSimple($user_id, $token);
    }

    public function checkSession($user_id, $token, $lon = 0, $lat = 0) {
        $model = new UserSessionModel();
        $et = $model->checkSession($user_id, $token, $lon, $lat);
        return $et > time() ? true : false;
    }

    public static function getMaxExpireTime() {
        return \PhalApi\DI()->config->get('const.max_expire_time');
    }
}