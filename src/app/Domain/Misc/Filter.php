<?php

namespace App\Domain\Misc;

use PhalApi\Exception;

class Filter
{
    //现在有个接口http://localhost:8800/check?text=文本，如果包含敏感词，返回true，否则返回false
    //请通过访问这个接口来获取结果，并在结果中看到是否有敏感词。
    private static $url = 'http://localhost:8800/check?text=';

    public static function check($text)
    {

        $url = self::$url. $text;
        $result = file_get_contents($url);
        if ($result == 'true') {
            return true;
        } else {
            return false;
        }
    }
}
