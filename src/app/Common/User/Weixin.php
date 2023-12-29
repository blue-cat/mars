<?php

namespace App\Common\User;

class Weixin
{
    protected $appletAppId = '';
    protected $appletAppSecret = '';
    protected $h5AppId = '';
    protected $h5AppSecret = '';

    public function __construct() {
        list($this->appletAppId, $this->appletAppSecret) = \PhalApi\DI()->config->get('vendor.weixin.applet');
        list($this->h5AppId, $this->h5AppSecret) = \PhalApi\DI()->config->get('vendor.weixin.h5');
    }

    public function appletCode2Openid($code) {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $this->appletAppId
            . "&secret=" . $this->appletAppSecret . "&js_code=${code}&grant_type=authorization_code";
        return file_get_contents($url);
    }

    public function h5Code2UserInfo($code) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->h5AppId
            . '&secret=' . $this->h5AppSecret . '&code=' . $code . '&grant_type=authorization_code';
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        //print_r($data);exit;
        //这儿要是拿到错误的说明code无效了，重定向到无code的页面

        $userInfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $data['access_token'] . '&openid=' . $data['openid'];
        return file_get_contents($userInfoUrl);
    }
}