<?php

namespace App\Common\User;

use Ip2Region;

/**
 * 用户插件-工具类
 * @author dogstar 20200331
 *
 */
class UserUtil
{

    const AUDIT_OK = 0;

    const AUDIT_BAN = 1;

    const AUDIT_PRE = 2;

    public function isMobile($mobile)
    {

        if (preg_match("/^1[3456789]{1}\d{9}$/", $mobile)) {
            return true;
        }

        return false;
    }

    public function isOtherMobile($mobile)
    {

        if (preg_match("/^[0-9]\d*$/", $mobile)) {
            return true;
        }

        return false;
    }

    public function convertNum($num)
    {
        if ($num >= 100000) {
            $num = round($num / 10000) . 'w+';
        } else if ($num >= 10000) {
            $num = round($num / 10000, 1) . 'w+';
        } else if ($num >= 1000) {
            $num = round($num / 1000, 1) . 'k+';
        }
        return $num;
    }

    public function pDate($time = NULL)
    {
        $text = '';
        $time = $time === NULL || $time > time() ? time() : intval($time);
        $t = time() - $time; //时间差 （秒）

        $y = date('Y', $time) - date('Y', time());//是否跨年
        switch ($t) {
            case $t == 0:
                $text = '刚刚';
                break;
            case $t < 60:
                $text = $t . '秒前'; // 一分钟内
                break;
            case $t < 60 * 60:
                $text = floor($t / 60) . '分钟前'; //一小时内
                break;
            case $t < 60 * 60 * 24:
                $text = floor($t / (60 * 60)) . '小时前'; // 一天内
                break;
            case $t < 60 * 60 * 24 * 3:
                $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); //昨天和前天
                break;
            case $t < 60 * 60 * 24 * 30:
                $text = date('m月d日 H:i', $time); //一个月内
                break;
            case $t < 60 * 60 * 24 * 365 && $y == 0:
                $text = date('m月d日', $time); //一年内
                break;
            default:
                $text = date('Y年m月d日', $time); //一年以前
                break;
        }

        return $text;
    }

    /**
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public function getIP($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * ip查询归属地
     * @param $ip
     * @return
     */
    public function ip2Region($ip)
    {
        $location = 'IP未知';
        $ip2region = new Ip2Region();
        $info = $ip2region->memorySearch($ip);
        $citys = \PhalApi\DI()->config->get('const.city');
        if (isset($info) && $info['region']) {
            $array = explode('|', $info['region']);
            if ($array[0] != '中国') {
                $location = $array[0];
            } else {
                $loc = '';
                for ($i = 3; $i >= 2; $i--) {
                    if ($array[$i]) {
                        $loc = $array[$i];
                        if (in_array($loc, $citys)) {
                            break;
                        }
                        //break;
                    }
                }
                $location = $loc;
                if (empty($location)) {
                    $location = '中国';
                }
            }
        }
        return $location;
    }

    /**
     * 敏感词检测
     * @param $content
     * @return 返回1为违规,返回2为待审核,返回0为正常
     */
    public function sensitiveWordsCheck($content)
    {
        //todo
        return self::AUDIT_OK;
    }

}
