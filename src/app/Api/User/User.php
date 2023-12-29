<?php


namespace App\Api\User;

use PhalApi\Api;
use App\Domain\User\User as UserDomain;
use PhalApi\Exception\BadRequestException;

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
            'userInit' => array(
                'code' => array('name' => 'code', 'require' => true, 'min' => 1, 'max' => 40, 'desc' => '微信初始化code'),
                'scene' => array('name' => 'scene', 'require' => true, 'min' => 1, 'max' => 2, 'desc' => '场景,1微信h5 2微信小程序'),
            ),
        ];
    }

    /**
     * 第三方用户code换信息
     * @desc 第三方用户code换信息
     * @return mixed
     */
    public function userInit()
    {
        $scenes = array_keys(\PhalApi\DI()->config->get('const.loginScene'));
        if (!in_array($this->scene, $scenes)) {
            throw new BadRequestException('不存在的场景', 1000);
        }

        try {
            $domain = new UserDomain();
            return $domain->userInit($this->code, $this->scene);
        } catch (\Exception $e) {
            throw new BadRequestException($e->getMessage(), 1001);
        }

    }
}