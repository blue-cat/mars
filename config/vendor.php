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

    'cur_cdn' => 1,

    'cdn_id' => [
        'qiniu' => 1,
        'tencent' => 2,
        'ali' => 3
    ],

    'cdn_url' => [//图片视频等资源cdn地址
        '1' => 'http://*.cdnol.nearby.dulcim.com',//七牛
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

    /**
     * 微信登陆
     */
    'weixin' => [
        'h5' => [
            'appId' => 'wx0ceebdb1622784ce',
            'appSecret' => '51a7bc44f46067d6c588e08ddf32fdaa',
        ],
        'applet' => [
            'appId' => 'wxb19ec993073f35e9',
            'appSecret' => '5b71d46d5aa16fff3676774ed636b046',
        ],
    ],

];
