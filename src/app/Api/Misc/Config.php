<?php
namespace App\Api\Misc;

use PhalApi\Api;
use App\Domain\Misc\Config as ConfigDomain;
use PhalApi\Exception\BadRequestException;

/**
 * 客户端拉取配置
 */
class Config extends Api
{
    public function getRules()
    {
        return [
            'getConfig' => array(
                'app' => array('name' => 'app', 'require' => true, 'min' => 1, 'max' => 100, 'desc' => 'app类别'),
            ),
        ];
    }

    /**
     * 客户端配置拉取
     * @desc 全站配置拉取
     * @return mixed
     */
    public function getConfig()
    {
        $apps = array_keys(\PhalApi\DI()->config->get('client.app'));
        if (!in_array($this->app, $apps)) {
            throw new BadRequestException('不存在的APP', 1000);
        }
        $config = new ConfigDomain();
        return $config->getConfig($this->app);
    }

}
