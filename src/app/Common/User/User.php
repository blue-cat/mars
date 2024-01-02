<?php
namespace App\Common\User;

use App\Domain\User\User as UserDomain;
use App\Domain\User\UserSession as UserSessionDomain;

/**
 * 用户插件-用户服务,当用户登录成功后,一般涉及用户相关操作时引入此类.
 * @author dogstar 20200331
 *
 */
class User {
    protected $id = 0;
    protected $profile = array();
    
    public function __construct() {
        $user_id = \PhalApi\DI()->request->get('user_id');
        if (!isset($user_id) || empty($user_id)) {
            $user_id = \PhalApi\DI()->request->getHeader('User-Id');
        }
        $user_id = intval($user_id);
        $token = \PhalApi\DI()->request->get('token');
        if (!isset($token) || empty($token)) {
            $token = \PhalApi\DI()->request->getHeader('Token');
        }
        $lon = \PhalApi\DI()->request->get('lon');
        $lat = \PhalApi\DI()->request->get('lat');


        if ($user_id && $token) {
            $domain = new UserSessionDomain();
            $is_login = $domain->checkSession($user_id, $token, $lon, $lat);

            if ($is_login) {
                $this->login($user_id);
            }
        }
    }
    
    // 登录用户
    public function login($user_id) {
        $userDomain = new UserDomain();
        //当checksession时不拉取用户信息,当传入lon和lat的时候认为是checksession
        $service  = \PhalApi\DI()->request->get('s');
        $isService = $service != 'App.User_User.CheckSession';
        $fields = $isService ? 'id,salt,password,status,username,reg_time,avatar,bg,mobile,sex,email,slogan,birth,height,weight,inclination' : 'id';
        $profile = $isService ?  $userDomain->getUserInfo($user_id, $fields) : [];
        $this->profile = $profile ?: $this->profile;
        $this->id = $user_id;
    }
    
    // 是否已登录
    public function isLogin() {
        return $this->id > 0 ? true : false;
    }
    
    // 获取用户ID
    public function getUserId() {
        return $this->id;
    }
    
    // 获取个人资料
    public function getProfile() {
        return $this->profile;
    }

    // 获取脱敏个人资料
    public function getSafeProfile() {
        $profile = $this->profile;
        unset($profile['salt']);
        unset($profile['password']);
        unset($profile['mobile']);
        unset($profile['email']);
        unset($profile['reg_time']);

        return $profile;
    }
    
    // 获取指定字段
    public function getProfileBy($filed, $default = NULL) {
        return isset($this->profile[$filed]) ? $this->profile[$filed] : $default;
    }
    
    // 获取资料
    public function __get($name) {
        return isset($this->profile[$name]) ? $this->profile[$name] : NULL;
    }
}
