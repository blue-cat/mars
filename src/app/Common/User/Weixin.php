<?php

namespace App\Common\User;

use PhalApi\Exception;

class Weixin
{
    protected $appletAppId = '';
    protected $appletAppSecret = '';
    protected $h5AppId = '';
    protected $h5AppSecret = '';

    public function __construct() {
        list($this->appletAppId, $this->appletAppSecret) = array_values(\PhalApi\DI()->config->get('vendor.weixin.applet'));
        list($this->h5AppId, $this->h5AppSecret) = array_values(\PhalApi\DI()->config->get('vendor.weixin.h5'));
    }

    public function appletCode2Openid($code) {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $this->appletAppId
            . "&secret=" . $this->appletAppSecret . "&js_code=${code}&grant_type=authorization_code";
        return json_decode(file_get_contents($url), true);
    }

    public function h5Code2UserInfo($code) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->h5AppId
            . '&secret=' . $this->h5AppSecret . '&code=' . $code . '&grant_type=authorization_code';
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        if (isset($data['errcode']) && $data['errcode']) {
            throw new Exception($data['errmsg'], $data['errcode']);
        }

        $userInfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $data['access_token']
            . '&openid=' . $data['openid'];
        $userInfo = file_get_contents($userInfoUrl);

        print_r($userInfo);exit;
        $userInfo['access_token'] = $data['access_token'];
        $userInfo['refresh_token'] = $data['refresh_token'];
        return json_decode($userInfo, true);
    }
}