<?php
namespace App\Model\User;
use PhalApi\Model\DataModel;
//use App\Model\User\UserBlack as UserBlackModel;
use App\Model\User\User as UserModel;


class UserFollow extends DataModel {

    const STATUS_OK = 1;//单向关注

    const STATUS_NO = 2;//不关注

    const FOLLOW_NONE = 0;//相互不关注

    const FOLLOW_MUTUAL = 1;//相互关注

    const FOLLOW_FOLLOW = 2;//关注了

    const FOLLOW_FANS = 3;//粉丝

    protected function getTableName($id = 0) {
        return 'mars_user_follow';
    }

    public function follow($userId, $followId, $source = 0) {
        $time = time();

        //检查拉黑
//        $black = new UserBlackModel();
//        $blackInfo = $black->getRelationship($userId, $followId);
//        if (!$blackInfo == $black::BLACK_NONE) {
//            return 0;
//        }

        $insert = [
            "user_id" => $userId,
            "follow_id" => $followId,
            "create_time" => $time,
            "update_time" => $time,
            "status" => self::STATUS_OK,
            "source" => $source
        ];
        $unique = [
            "user_id" => $userId,
            "follow_id" => $followId,
        ];
        $update = [
            "update_time" => $time,
            "status" => self::STATUS_OK,
        ];
        $ret = $this->getORM()->insert_update($unique, $insert, $update);
        if ($ret) {
            $this->foNum($userId, $followId, 1);
        }

        return $ret;
    }

    /**
     * 取消关注或移除粉丝
     * @param $userId
     * @param $followId
     */
    public function unfollow($userId, $followId) {
        $data = [
            'update_time' => time(),
            'status' => self::STATUS_NO
        ];
        $where = [
            'user_id' => $userId,
            'follow_id' => $followId,
            'status' => self::STATUS_OK
        ];
        $ret = $this->getORM()
            ->where($where)
            ->update($data);

        if ($ret) {
            $this->foNum($userId, $followId, -1);
        }

        return $ret;
    }

    /**
     * 获取用户关注的uid，只查100个，用来做关注的人最新的列表，作为临时方案//todo
     * @param $userId
     * @return mixed
     */
    public function getListByUserId($userId) {
        $where = [
            'user_id' => $userId,
            'status' => self::STATUS_OK
        ];
        $size = 100;
        return $this->getORM()->where($where)->select('follow_id')->limit($size)->fetchAll();
    }

    /**
     * 获取两人之间的关注关系
     * @param $userId
     * @param $followId
     * @return int
     */
    public function getRelationship($userId, $followId) {
        $sql = "SELECT 1 AS f FROM " . $this->getTableName() . " WHERE user_id = $userId AND follow_id = $followId AND status = " . self::STATUS_OK
            . " UNION "
            . "SELECT 2 FROM " . $this->getTableName() . " WHERE user_id = $followId AND follow_id = $userId AND status = " . self::STATUS_OK ;
        $info = $this->getORM()->query($sql, [])->fetchAll();

        return empty($info) ? self::FOLLOW_NONE : (
            count($info) > 1 ? self::FOLLOW_MUTUAL : (
                $info[0]['f'] == 1 ? self::FOLLOW_FOLLOW : self::FOLLOW_FANS
            )
        );
    }

    /**
     * 关注某人后给其num进行变化
     * @param $userId
     * @param $followId
     * @param $num
     * @return void
     */
    public function foNum($userId, $followId, $num) {

        $userModel = new UserModel();
        $userModel->updateCounter(['id' => $userId], ['fo_num' => $num]);
        $userModel->updateCounter(['id' => $followId], ['fa_num' => $num]);

    }

}