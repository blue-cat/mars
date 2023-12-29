<?php

namespace App\Domain\User;

use App\Common\User\Weixin;

class User
{
    public function userInit($code, $scene) {
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
        return $return;
    }
}
