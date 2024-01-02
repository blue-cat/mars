<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return [

    /**
     * cdn服务商id
     */

    'cdn_id' => [
        'qiniu' => 1,
        'tencent' => 2,
        'ali' => 3
    ],

    /**
     * 七牛云存储
     */
    'qiniu_store' => [
        'accessKey' => 'QDJzcYZeKnYvjUtXnmHGO-rhH3WG_SzZYWhjYeMc',
        'secretKey' => 'iKrFROSNYmrj3MFfbfe9QrPbgCkW7hPk1HUSMmvf',
        'bucket' => 'nearby-teenage-ol',
        'confBucket' => 'nearby-teenage-config',
    ],

    'cdn_url' => [//图片视频等资源cdn地址
        '1' => 'http://*.cdnol.nearby.dulcim.com',//七牛
    ],

    /**
     * 微信登陆
     */
    'weixin' => [
        'h5' => [
            'appId' => 'wx152b40f9261a2e2b',
            'appSecret' => '57710077eb3a26827c7fd97fe9b33a36',
        ],
        'applet' => [
            'appId' => 'wxb19ec993073f35e9',
            'appSecret' => '5b71d46d5aa16fff3676774ed636b046',
        ],
    ],
    
];
