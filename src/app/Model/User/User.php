<?php

namespace App\Model\User;

use PhalApi\Model\DataModel;
use App\Model\User\UserFollow as UserFollowModel;
use App\Model\User\UserSession as UserSessionModel;

class User extends DataModel
{

    const OK = 1;

    const BAN = 2;

    const DEL = 3;

    /**
     * 设置一个没人会出生的年月日作为默认
     * @var int
     */
    public static $birthMin = 19000101;

    public static $birthMinSep = '1900-01-01';

    protected function getTableName($id)
    {
        return 'mars_user';
    }

    public function getInfoByOpenId($openId, $scene, $select)
    {
        $scenes = array_keys(\PhalApi\DI()->config->get('const.loginSceneGroup'));
        return $this->getORM()->select($select)
            ->where([$scenes[$scene] => $openId,/* 'scene' => $scene*/])
            ->fetchOne();
    }

    public function getInfoByMobile($mobile, $telPre, $select)
    {
        return $this->getORM()->select($select)
            ->where(['mobile' => $mobile, 'tel_pre' => $telPre])
            ->fetchOne();
    }

    public function getMaxId()
    {
        return $this->getORM()->select('max(id) as id')->fetch();
    }

    public function getUids($page, $size)
    {
        $offset = ($page - 1) * $size;
        return $this->getORM()->select('id,status')->order('id ASC')->limit($offset, $size)->fetchAll();
    }

    public function getInfo($userId)
    {
        return $this->getORM()->select('*')->where('id = ?', $userId)->fetch();
    }

    /**
     * @param array $userIds
     * @param $fullMode 默认简易模式，
     * @return array
     */
    public function getInfosByUserIds(array $userIds, $fullMode = false)//todo 从缓存拿
    {
        $rs = array();
        if (empty($userIds)) {
            return $rs;
        }

        $moreFields = $fullMode ? ',sex,birth,height,weight,sexuality,fa_num,fo_num' : '';
        $rows = self::getORM()
            ->select('id,nickname,avatar,bg,slogan,cdn_id,status' . $moreFields)
            ->where('id', $userIds)
            ->fetchAll();

        $sessionMap = [];
        if ($fullMode) {
            $sessionModel = new UserSessionModel();
            $sessionMap = $sessionModel->getSessionInfoByIds($userIds, 'user_id,location,update_time');
        }

        $cdnDomains = \PhalApi\DI()->config->get('vendor.cdn_url');
        $enum = array_flip(\PhalApi\DI()->config->get('const.sexuality'));

        foreach ($rows as $row) {
            //头像拼接地址
            if ($row['avatar']) {
                $cdnDomain = str_replace('*', 'avatar', $cdnDomains[$row['cdn_id']]);
                $row['avatar'] = $cdnDomain . $row['avatar'];
            }

            //bg拼接地址
            if ($row['bg']) {
                $cdnDomain = str_replace('*', 'bg', $cdnDomains[$row['cdn_id']]);
                $row['bg'] = $cdnDomain . $row['bg'];
            }

            if ($fullMode) {
                //年龄
                if (!$row['birth'] || $row['birth'] == self::$birthMinSep) {
                    $row['age'] = 0;
                } else {
                    list($year, $month, $day) = explode('-', $row['birth']);
                    $row['age'] = (date('Y') - $year) + (date('md') - ($month . $day) > 0 ? 0 : -1);
                    $row['age'] = max($row['age'], 0);
                }

                if ($row['birth'] == self::$birthMinSep) {
                    $row['birth'] = date('Y-m-d');
                }
                //取向
                $row['sexuality'] = (string)$row['sexuality'];
                $row['sexualityDesc'] = $enum[$row['sexuality']]??'';
                //session
                $row['loc'] = $sessionMap[$row['id']]['location'];
                $row['upTs'] = $sessionMap[$row['id']]['update_time'];
            }

            $rs[$row['id']] = $row;
        }

        return $rs;
    }
}