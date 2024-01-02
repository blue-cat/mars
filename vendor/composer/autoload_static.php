<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6835957bc29912b8a4cd2dd758af2e80
{
    public static $files = array (
        'b0655c4b47b25ec49f0e931fe41ab7a3' => __DIR__ . '/..' . '/phalapi/kernal/src/bootstrap.php',
        '5cab427b0519bb4ddb2f894b03d1d957' => __DIR__ . '/..' . '/phalapi/kernal/src/functions.php',
        '841780ea2e1d6545ea3a253239d59c05' => __DIR__ . '/..' . '/qiniu/php-sdk/src/Qiniu/functions.php',
        '5dd19d8a547b7318af0c3a93c8bd6565' => __DIR__ . '/..' . '/qiniu/php-sdk/src/Qiniu/Http/Middleware/Middleware.php',
        'dee36c56d6bb319b2a744b267373bb4b' => __DIR__ . '/../..' . '/src/app/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'Q' => 
        array (
            'Qiniu\\' => 6,
        ),
        'P' => 
        array (
            'Portal\\' => 7,
            'PhalApi\\Task\\' => 13,
            'PhalApi\\QrCode\\' => 15,
            'PhalApi\\NotORM\\' => 15,
            'PhalApi\\CLI\\' => 12,
            'PhalApi\\' => 8,
        ),
        'O' => 
        array (
            'Overtrue\\Pinyin\\' => 16,
        ),
        'M' => 
        array (
            'MyCLabs\\Enum\\' => 13,
        ),
        'G' => 
        array (
            'GetOpt\\' => 7,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Qiniu\\' => 
        array (
            0 => __DIR__ . '/..' . '/qiniu/php-sdk/src/Qiniu',
        ),
        'Portal\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/portal',
        ),
        'PhalApi\\Task\\' => 
        array (
            0 => __DIR__ . '/..' . '/phalapi/task/src',
        ),
        'PhalApi\\QrCode\\' => 
        array (
            0 => __DIR__ . '/..' . '/phalapi/qrcode/src',
        ),
        'PhalApi\\NotORM\\' => 
        array (
            0 => __DIR__ . '/..' . '/phalapi/notorm/src',
        ),
        'PhalApi\\CLI\\' => 
        array (
            0 => __DIR__ . '/..' . '/phalapi/cli/src',
        ),
        'PhalApi\\' => 
        array (
            0 => __DIR__ . '/..' . '/phalapi/kernal/src',
        ),
        'Overtrue\\Pinyin\\' => 
        array (
            0 => __DIR__ . '/..' . '/overtrue/pinyin/src',
        ),
        'MyCLabs\\Enum\\' => 
        array (
            0 => __DIR__ . '/..' . '/myclabs/php-enum/src',
        ),
        'GetOpt\\' => 
        array (
            0 => __DIR__ . '/..' . '/ulrichsg/getopt-php/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Ip2Region' => __DIR__ . '/..' . '/zoujingli/ip2region/Ip2Region.php',
        'Stringable' => __DIR__ . '/..' . '/myclabs/php-enum/stubs/Stringable.php',
        'XdbSearcher' => __DIR__ . '/..' . '/zoujingli/ip2region/XdbSearcher.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6835957bc29912b8a4cd2dd758af2e80::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6835957bc29912b8a4cd2dd758af2e80::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6835957bc29912b8a4cd2dd758af2e80::$classMap;

        }, null, ClassLoader::class);
    }
}
