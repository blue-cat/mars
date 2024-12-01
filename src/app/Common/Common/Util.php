<?php

namespace App\Common\Common;

/**
 * 工具类
 * @author dogstar 20200331
 *
 */
class Util
{
    /**
     * 下载url到local地址
     * @param $url
     * @param $localFile
     * @return mixed
     */
    public static function fetchContent($url, $localFile)
    {
        $fp_output = fopen($localFile, 'w+');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp_output);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');

        curl_exec($ch);

        $ret = curl_getinfo($ch);

        curl_close($ch);
        fclose($fp_output);

        return $ret;
    }

    /**
     * 使用post curl
     * @param $data
     * @param $url
     * @return bool|mixed|string
     */
    public static function curlPost($data, $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        $str = http_build_query($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $str);
        //如果ssl
        // 是否检测服务器的域名与证书上的是否一致
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        //是否检测服务器的证书是否由正规浏览器认证过的授权CA颁发的
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        if ($result === false) {
//            echo 'Curl error: ' . curl_error($curl);
        }

        curl_close($curl);

        return $result;
    }


    public static function substr($str, $length, $start = 0, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr")) {
            if ($suffix) {
                if (strlen($str) > $length)
                    return mb_substr($str, $start, $length, $charset) . "...";
                else
                    return mb_substr($str, $start, $length, $charset);
            } else {
                return mb_substr($str, $start, $length, $charset);
            }
        } elseif (function_exists('iconv_substr')) {
            return iconv_substr($str, $start, $length, $charset);
        }
    }

    /**
     * 格式：xxxxxxxx-xxxx-xxxx-xxxxxx-xxxxxxxxxx(8-4-4-4-12)
     * @param $type 短型后面8位，长形的最后12位
     * @return string
     */
    public static function uuid($type = 0)
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-'
            . substr($chars, 8, 4) . '-'
            . substr($chars, 12, 4) . '-'
            . substr($chars, 16, 4) . '-'
            . substr($chars, 20, $type ? 12 : 8);
        return $uuid;
    }

    /**
     * 生成一个静态方法，将数字uid转成一个10位的字符串
     * 能将uid转成字符串，也能将字符串转成uid
     * 通过一种简单的对称加密算法，然后设置一个值作为密钥，加密解密
     */
    public static function uidToStr($uid, $key = 'uid_key') {
        $str = '';
        $uid = (int)$uid;
        if ($uid > 0) {
            $str = base_convert($uid, 10, 36);
        }
        $str = self::encrypt($str, $key);
        return $str;
    }
    
    public static function strToUid($str, $key = 'uid_key') {
        $uid = 0;
        $str = self::decrypt($str, $key);
        if ($str) {
            $uid = base_convert($str, 36, 10);
        }
        return $uid;
    }
    
    public static function encrypt($str, $key) {
        // 对输入字符串进行 base64 编码
        $str = base64_encode($str);
        
        // 生成 MD5 密钥
        $key = md5($key);
        $len = strlen($str);
        $key_len = strlen($key);
        $rnd_key = $box = array();
        $result = '';
        
        for ($i = 0; $i < 256; $i++) {
            $rnd_key[$i] = ord($key[$i % $key_len]);
            $box[$i] = $i;
        }
        
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rnd_key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        
        for ($a = $j = $i = 0; $i < $len; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($str[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        
        // 返回 base64 编码的结果，而不是 MD5 哈希
        return base64_encode($result);
    }
    
    public static function decrypt($str, $key) {
        // 对输入字符串进行 base64 解码
        $str = base64_decode($str);
        
        // 生成 MD5 密钥
        $key = md5($key);
        $len = strlen($str);
        $key_len = strlen($key);
        $rnd_key = $box = array();
        $result = '';
        
        for ($i = 0; $i < 256; $i++) {
            $rnd_key[$i] = ord($key[$i % $key_len]);
            $box[$i] = $i;
        }
        
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rnd_key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        
        for ($a = $j = $i = 0; $i < $len; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($str[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        
        // 返回解密后的 base64 字符串
        return base64_decode($result);
    }
    
    
}
