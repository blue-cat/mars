<?php


namespace App\Api\User;

use PhalApi\Api;

/**
 * 用户
 * @author luca
 */
class User extends Api
{

    public function __construct()
    {
    }

    public function getRules()
    {
        return [
            'user_init' => array(
                'code' => array('name' => 'code', 'require' => true, 'min' => 1, 'max' => 40, 'desc' => '微信初始化code'),
                'scene' => array('name' => 'scene', 'require' => true, 'min' => 1, 'max' => 2, 'desc' => '场景,1微信h5 2微信小程序'),
            ),
        ];
    }

    /**
     * 注册账号
     * @desc 注册一个新账号
     * @return int user_id 新账号的ID
     */
    public function register()
    {
        return [
            $this->code,
            $this->scene
        ];
    }
}