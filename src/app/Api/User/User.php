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
            'register' => array(
                'username' => array('name' => 'username', 'min' => 1, 'max' => 40, 'desc' => '账号'),
                'password' => array('name' => 'password', 'min' => 6, 'max' => 20, 'desc' => '密码'),
                'avatar' => array('name' => 'avatar', 'default' => '', 'max' => 500, 'desc' => '头像链接'),
                'sex' => array('name' => 'sex', 'type' => 'int', 'default' => 0, 'desc' => '性别，1男2女0未知'),
                'email' => array('name' => 'email', 'default' => '', 'max' => 50, 'desc' => '邮箱'),
                'mobile' => array('name' => 'mobile', 'default' => '', 'max' => 11, 'desc' => '手机号'),
                'tel_pre' => array('name' => 'tel_pre', 'require' => false, 'min' => 1, 'max' => 2000, 'desc' => '国家号码前缀'),
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
            'data' => 'test'
        ];
    }
}