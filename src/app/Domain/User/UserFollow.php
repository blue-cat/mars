<?php

namespace App\Domain\User;

use App\Model\User\UserFollow as UserFollowModel;
use App\Model\User\User as UserModel;


class UserFollow
{

    public static $SOURCE_MAP = [
        1 => '推荐',
        2 => '广场',
    ];

    public function getContactList($userId, $groupByIndex = true)
    {
        $user = new UserModel();
        //管理员无限制
        $adminIds = \PhalApi\DI()->config->get('admin.admin_ids');
        if (in_array($userId, $adminIds)) {
            $where = [
            ];
            $list = $user->getList($where, [], 'id as user_id, 2 as source, update_time', 'update_time DESC', 1, 1000);
        } else {
            $userFollow = new UserFollowModel();
            //这儿获取所有粉丝
            $where = [
                'follow_id' => $userId,
                'status' => $userFollow::STATUS_OK
            ];
            $list = $userFollow->getList($where, [], 'user_id, source, update_time', null, 1, 1000);
        }

        if ($list) {
            $userIds = array_column($list, 'user_id');
            $userInfos = $user->getSnapshotByUserIds($userIds);

            foreach ($list as &$v) {
                $v += $userInfos[$v['user_id']];
                $v['selected'] = false;
            }
        }

        return empty($list) ? null : ($groupByIndex ? self::groupBy($list, 'index') : $list);
    }

    public static function groupBy($arr, $key_field)
    {
        $ret = [];

        foreach ($arr as $row) {
            $key = $row[$key_field];
            $ret[$key][] = $row;
        }

        return $ret;
    }

    /**
     * 获取关注的人
     * @param $userId
     * @return array
     */
    public function getFollowingList($userId, $page, $size)
    {
        $util = \PhalApi\DI()->util;
        $userFollow = new UserFollowModel();
        $list = [];
        $where = [
            'user_id' => $userId,
            'status' => $userFollow::STATUS_OK
        ];
        $list = $userFollow->getList($where, [], 'follow_id, source, update_time', null, $page, $size);

        if ($list) {
            $userIds = array_column($list, 'follow_id');

            //查相互关注
            $where = [
                'user_id' => $userIds,//这儿是IN操作,后面是数组
                'follow_id' => $userId,
                'status' => $userFollow::STATUS_OK
            ];
            $listRevBase = $userFollow->getList($where, [], 'user_id', null, $page, $size);
            $listRev = array_column($listRevBase, null, 'user_id');

            $user = new UserModel();
            $userInfos = $user->getSnapshotByUserIds($userIds);
            //$userInfos = array_column($userInfosBase, null, 'id');

            foreach ($list as &$v) {
                $v += $userInfos[$v['follow_id']];
                $v['followState'] = isset($listRev[$v['follow_id']]) ? UserFollowModel::FOLLOW_MUTUAL : UserFollowModel::FOLLOW_FOLLOW;
            }
        }

        return empty($list) ? null : $list;
    }

    /**
     * 获取粉丝
     * @param $userId
     * @return array
     */
    public function getFollowedList($userId, $page, $size)
    {
//        $util = \PhalApi\DI()->util;
        $userFollow = new UserFollowModel();
        $list = [];
        $where = [
            'follow_id' => $userId,
            'status' => $userFollow::STATUS_OK
        ];
        $list = $userFollow->getList($where, [], 'user_id, source, update_time', null, $page, $size);

        if ($list) {
            $userIds = array_column($list, 'user_id');

            //查相互关注
            $where = [
                'user_id' => $userId,
                'follow_id' => $userIds,//这儿是IN操作,后面是数组
                'status' => $userFollow::STATUS_OK
            ];
            $listRevBase = $userFollow->getList($where, [], 'follow_id', null, $page, $size);
            $listRev = array_column($listRevBase, null, 'follow_id');

            $user = new UserModel();
            $userInfos = $user->getSnapshotByUserIds($userIds);
            //$userInfos = array_column($userInfosBase, null, 'id');

            foreach ($list as &$v) {
                $v += $userInfos[$v['user_id']];
                $v['followState'] = isset($listRev[$v['user_id']]) ? UserFollowModel::FOLLOW_MUTUAL : UserFollowModel::FOLLOW_FANS;
            }
        }

        return empty($list) ? null : $list;
    }
}