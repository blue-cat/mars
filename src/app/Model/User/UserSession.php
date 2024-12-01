<?php

namespace App\Model\User;
//use App\Domain\Misc\Es;
use PhalApi\Exception\BadRequestException;
use PhalApi\Model\NotORMModel as NotORM;
use PhalApi\Model\DataModel;

class UserSession extends NotORM {

    public static function getSessionExpireTime() {
        return \PhalApi\DI()->config->get('const.session_expire_time');
    }

    protected function getTableName($id = 0) {
        return 'mars_user_session';
    }

    public function generateSession($data) {
        $cache = \Phalapi\DI()->cache;

        //删除多余session,保证单用户登录
        $this->delSession($data['user_id']);

        if (isset($cache)) {
            $rdsKey = self::getSessionRdsKey($data['user_id']);
            $cache->hMset($rdsKey, $data);
            $cache->expire($rdsKey, self::getSessionExpireTime());
        }

        //db的更新一般要改成异步,但是注册登录这次问题不大
        $this->insert($data);
    }

    /**
     * @param $userId 用户id
     * 用户注册或者重新登陆的时候需要删掉之前的记录,用来保证唯一用户在线
     */
    public function delSession($userId) {
        $rdsKey = self::getSessionRdsKey($userId);
        $cache = \Phalapi\DI()->cache;

        if (isset($cache)) {
            $cache->delete($rdsKey);
        }
        $count = $this->getORM($userId)->where('user_id', $userId)->delete();
        return $count;
    }

    /**
     * 取自己!!的session信息,包含ip及用户归属
     * @param $userId
     * @return array
     */
    public function getSession($userId, $field = '*') {
        $rdsKey = self::getSessionRdsKey($userId);
        $cache = \Phalapi\DI()->cache;
        $session = [];
        $field = is_array($field) ? implode(',', $field) : $field;

        if (isset($cache)) {
            if ($field == '*') {
                $session = $cache->hGetAll($rdsKey);
            } else {
                $fields = explode(',', $field);
                $session = $cache->hMGet($rdsKey, $fields);
            }
            //这儿不用考虑session为空的情况,因为只能取自己的!!!
        } else {
            $session = $this->getORM($userId)
                ->select('*')
                ->where('user_id', $userId)
                ->fetchOne();
        }

        return $session;
    }

    public function getSessionInfoByIds(array $userIds, $select = '*') {
        $ret = self::getORM()
            ->select($select)
            ->where('user_id', $userIds)
            ->fetchAll();
        return array_column($ret, null, 'user_id');
    }

    /**
     * session检查及更新方法
     * @param $userId
     * @param $token
     * @param int $lon
     * @param int $lat
     * @return int
     */
    public function checkSession($userId, $token, $lon = 0, $lat = 0) {
        $rdsKey = self::getSessionRdsKey($userId);
        $expiresTime = 0;
        $cache = \Phalapi\DI()->cache;
//        $util = \PhalApi\DI()->util;
        $checked = false;//当前传入的userId和token是否匹配,且只在本次从db中重新拉取后为true
        $time = time();
        $ip = '';
        $location = '';
echo $rdsKey;
        if (isset($cache)) {
            $sessionRds = $cache->hMget($rdsKey, ['ip', 'token', 'expires_time']);

            //如果拉倒为空了,则从db中拉取来更新
            if (!isset($sessionRds) || empty($sessionRds) || !$sessionRds['token']) {
                $row = $this->getORM($userId)
                    ->select('ip,token,expires_time')
                    ->where('user_id', $userId)
                    ->where('token', $token)
                    ->fetchOne();
                if (empty($row)) {
                    return $expiresTime;
                }

                $expiresTime = $row['expires_time'];
                $checked = true;
                $cache->hIncrBy($rdsKey, 'times', 1);
//                $ip = $util->getIP();
                $row['update_time'] = $time;
                //ip归属查询
//                $location = $util->ip2Region($ip);
                $row['ip'] = $ip;
//                $row['location'] = $location;

                $cache->hMset($rdsKey, $row);
                $cache->expire($rdsKey, self::getSessionExpireTime());
            }

            //校验token是否一致,如果不一致说明已经过期,或者非法token,或者是被人挤下线的
            elseif (isset($sessionRds) && !empty($sessionRds) && $sessionRds['token']) {

                //当被人挤下线或者过期的情况下,客户端要做退出登录处理
                if ($sessionRds['token'] != $token) {
                    return $expiresTime;
                }

                //$checked = true;
                //获取过期时间
                $expiresTime = $sessionRds['expires_time'];
                $cache->hIncrBy($rdsKey, 'times', 1);
//                $ip = $util->getIP();
                //ip归属查询
                if ($sessionRds['ip'] != $ip) {
//                    $location = $util->ip2Region($ip);
                    $data['ip'] = $ip;
//                    $data['location'] = $location;
                }
                $data['update_time'] = $time;

                $cache->hMset($rdsKey, $data);
                $cache->expire($rdsKey, self::getSessionExpireTime());
            }

            //更新经纬度.上面已经把未验证登录的返回了,这儿的确认都是验证过的用户.
            if ($lon && $lat) {
                $geoKey = \PhalApi\DI()->config->get('phalapi_user.rds_geo_key');
                $geoExpire = \PhalApi\DI()->config->get('talk.geo_expire');
                $cache->geoAdd($geoKey, $lon, $lat, $userId);
                $cache->expire($geoKey, $geoExpire);//其实没啥用

                $geoExpireKey = \PhalApi\DI()->config->get('phalapi_user.rds_geo_expire_time_key');
                $cache->hSet($geoExpireKey, $userId, $time + $geoExpire);
                $cache->expire($geoExpireKey, $geoExpire);//其实没啥用
            }

            //添加es索引
//            $es = new Es();
//            try {
//                $es->updateNearby($userId, $lon, $lat);
//            } catch (\Exception $e) {
////                throw new BadRequestException($e->getMessage(), 210);
//            }
        }

        //db的更新,只在本次新建了新的缓存,或者不存在缓存的时候更新.
        //正常情况下,10分钟更新一次db,
        //其实不更新也可,有必要可以移除下面这段更新
        //当缓存出问题后,这块可能刷爆
        if ($checked/* || !isset($cache)*/) {
//            if (!isset($cache)) {
//                $row = $this->getORM($userId)
//                    ->select('expires_time')
//                    ->where('user_id', $userId)
//                    ->where('token', $token)
//                    ->fetchOne('expires_time');
//                if (empty($row)) {
//                    return $expiresTime;
//                }
//
//                $expiresTime = $row;
//            }

            $data = [
                'ip' => $ip,
                'location' => $location,
                'update_time' => $time
            ];

            $this->getORM($userId)
                ->where('user_id', $userId)
                ->where('token', $token)
                ->update($data);
        }

        return $expiresTime;
    }

    public static function getSessionRdsKey($userId) {
        return sprintf('u_ses:%s', $userId);
    }
}
