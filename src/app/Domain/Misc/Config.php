<?php

namespace App\Domain\Misc;

use PhalApi\Exception;


class Config
{
    public function getConfig($app)
    {

        $common = \PhalApi\DI()->config->get('client.common');
        $app = \PhalApi\DI()->config->get('client.' . $app);
//        $vendor = \PhalApi\DI()->config->get('vendor');

        //user类型中需要传递给前端的
//        $vendorEnum = [
//            'cdn_url' => $vendor['cdn_url'],
//        ];

        $config = array_merge($common, $app/*, $vendorEnum*/);
        return $this->camelize($config);
    }

    /**
     * 下划线转驼峰
     * @param $uncamelized_words
     * @param string $separator
     * @return string
     */
    static private function _camelize($uncamelized_words, $separator = '_')
    {

        if (strpos($uncamelized_words, $separator) === false) {
            return $uncamelized_words;
        }

        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));

        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    private function camelize($array)
    {
        if (empty($array)) {
            return $array;
        }

        foreach ($array as $k => $v) {
            $kk = self::_camelize($k);
            $array[$kk] = is_array($v) ? $this->camelize($v) : $v;
            if ($k != $kk) {
                unset($array[$k]);
            }
        }

        return $array;
    }

}