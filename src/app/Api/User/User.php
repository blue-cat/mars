<?php


namespace App\Api\User;

use App\Domain\Notice\Notice as NoticeDomain;
use App\Domain\User\UserFollow as UserFollowDomain;
use App\Domain\User\UserSession as UserSessionDomain;
use App\Model\Notice\Notice as NoticeModel;
use App\Model\User\UserFollow as UserFollowModel;
use PhalApi\Api;
use App\Domain\User\User as UserDomain;
use PhalApi\Exception;
use PhalApi\Exception\BadRequestException;
use App\Common\Common\Util;

/**
 * 用户
 * 常见的流程是使用token获取用户信息
 * 如果不存在token则使用一次code换用户信息
 * @author luca
 */
class User extends Api
{

    public function __construct()
    {
    }

    public function getRules()
    {
        return [
            'code2UserInfo' => array(
                'code' => array('name' => 'code', 'require' => true, 'min' => 1, 'max' => 40, 'desc' => '微信初始化code'),
                'scene' => array('name' => 'scene', 'require' => true, 'min' => 1, 'max' => 2, 'desc' => '场景,1微信h5 2微信小程序'),
                'state' => array('name' => 'state', 'desc' => '成功后state原样返回'),
                'avatar' => array('name' => 'avatar', 'default' => '', 'desc' => 'base64'),
                'nickname' => array('name' => 'nickname', 'default' => '', 'max' => 40, 'desc' => '昵称'),
            ),
            'follow' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'desc' => '会话token'),
                'follow_id' => array('name' => 'follow_id', 'type' => 'int', 'require' => true, 'desc' => '关注的ID'),
                'source' => array('name' => 'source', 'default' => '0', 'min' => 1, 'max' => 1, 'desc' => '关注来源'),
            ),
            'unfollow' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'desc' => '会话token'),
                'follow_id' => array('name' => 'follow_id', 'type' => 'int', 'require' => true, 'desc' => '要取消关注的ID'),
            ),
            'removeFollow' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'desc' => '会话token'),
                'follow_id' => array('name' => 'follow_id', 'type' => 'int', 'require' => true, 'desc' => '要取消的粉丝ID'),
            ),
            'getRelationship' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'desc' => '会话token'),
                'follow_id' => array('name' => 'follow_id', 'type' => 'int', 'require' => true, 'desc' => '对方的ID'),
            ),
            'getFollowList' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'desc' => '会话token'),
                'type' => array('name' => 'type', 'default' => '0', 'require' => true, 'desc' => '类型,0获取我的关注,1获取我的粉丝'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'max' => 99, 'default' => 1, 'desc' => '页码'),
                'size' => array('name' => 'size', 'type' => 'int', 'min' => 1, 'max' => 100, 'default' => 20, 'desc' => '每页大小')
            ),
            'modify' => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'desc' => '会话token'),
                'nickname' => array('name' => 'nickname', 'min' => 1, 'max' => 40, 'desc' => '账号'),
                'avatar' => array('name' => 'avatar', 'default' => '', 'max' => 500, 'desc' => '头像链接'),//todo 需要检测格式
                'bg' => array('name' => 'bg', 'default' => '', 'max' => 500, 'desc' => 'bg链接'),//todo 需要检测格式
                'sex' => array('name' => 'sex', 'type' => 'int', 'default' => 0, 'desc' => '性别，1男2女0未知'),
                'email' => array('name' => 'email', 'type' => 'callable', 'default' => '', 'max' => 50, 'desc' => '邮箱', 'callback' => 'App\\Common\\Request\\Rule::formatEmail'),
                'slogan' => array('name' => 'slogan', 'ipdefault' => '', 'max' => 200, 'desc' => '简介'),
                'birth' => array('name' => 'birth', 'type' => 'callable', 'default' => 0, 'min' => 0, 'callback' => 'App\\Common\\Request\\Rule::formatBirth', 'desc' => '出生日期'),
                'height' => array('name' => 'height', 'type' => 'int', 'min' => 0, 'max' => 250, 'desc' => '身高'),
                'weight' => array('name' => 'weight', 'type' => 'int', 'min' => 0, 'max' => 250, 'desc' => '体重'),
                'sexuality' => array('name' => 'sexuality', 'type' => 'int', 'min' => 0, 'max' => 10, 'desc' => '取向'),
                'cdn_id' => array('name' => 'cdn_id', 'type' => 'int', 'min' => 0, 'max' => 100, 'desc' => 'cdnid'),
            ),
        ];
    }

    /**
     * 当客户端无token时，第三方用户code换信息
     * @desc 第三方用户code换信息
     * @return mixed
     */
    public function code2UserInfo()
    {
        $scenes = array_keys(\PhalApi\DI()->config->get('const.loginScene'));
        if (!in_array($this->scene, $scenes)) {
            throw new BadRequestException('不存在的场景', 1000);
        }

        try {
            $domain = new UserDomain();
            $user_id = $domain->code2Uid($this->code, $this->scene, $this->nickname, $this->avatar);

        } catch (\Exception $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode());
        }

        $session = new UserSessionDomain();
        $token = $session->generate($user_id);

        return array('user_str' => Util::uidToString($user_id), 'user_id' => $user_id, 'token' => $token, 'profile' => $domain->getUserInfoById($user_id, true));
    }

    /**
     * 关注
     * @desc (1)关注动作
     * @return array
     * @throws BadRequestException
     */
    public function follow()
    {
        $user = \PhalApi\DI()->user;

        if (!$user->isLogin()) {
            throw new BadRequestException('账号未登录或登录token已过期', 1999);
        }

        if ($this->follow_id == $user->getUserId()) {
            throw new BadRequestException('不能关注自己', 800);
        }

        $domain = new UserDomain();
        if (!$domain->getUserById($this->follow_id, 'id')) {
            throw new BadRequestException('要关注的用户不存在', 801);
        }

        $follow = new UserfollowModel();
        $ret = $follow->follow($user->getUserId(), $this->follow_id, $this->source);
        if ($ret <= 0) {
            throw new BadRequestException('关注失败', 802);
        }

        //notice通知
//        $noticeDomain = new NoticeDomain;
//        $noticeDomain->add($this->follow_id, $user->getUserId(), $user->getProfile()['username'], NoticeModel::ACT["FOLLOW"],
//            0, 0
//        );
        return [];
    }

    /**
     * 移除关注
     * @desc (1)移除我的关注
     * @return array
     * @throws BadRequestException
     */
    public function unfollow()
    {
        $user = \PhalApi\DI()->user;

        if (!$user->isLogin()) {
            throw new BadRequestException('账号未登录或登录token已过期', 1999);
        }

        $follow = new UserfollowModel();
        $ret = $follow->unfollow($user->getUserId(), $this->follow_id);
        if ($ret <= 0) {
            throw new BadRequestException('取消关注失败', 900);
        }

        return [];
    }

    /**
     * 移除我的粉丝
     * @desc (1)移除我的粉丝
     * @return array
     * @throws BadRequestException
     */
    public function removefollow()
    {
        $user = \PhalApi\DI()->user;

        if (!$user->isLogin()) {
            throw new BadRequestException('账号未登录或登录token已过期', 1999);
        }

        $follow = new UserfollowModel();
        $ret = $follow->unfollow($this->follow_id, $user->getUserId()) > 0;
        if ($ret <= 0) {
            throw new BadRequestException('移除粉丝失败', 1000);
        }

        return [];
    }

    /**
     * 获取两人关注关系
     * @desc (1)0无关注关系 1互相关注 2关注了他 3是她粉丝
     * @return int
     * @throws BadRequestException
     */
    public function getRelationship()
    {
        $user = \PhalApi\DI()->user;

        if (!$user->isLogin()) {
            throw new BadRequestException('账号未登录或登录token已过期', 1999);
        }

        $follow = new UserfollowModel();
        $ret = $follow->getRelationship($user->getUserId(), $this->follow_id);
        return $ret;
    }

    /**
     * 获取我的关注或粉丝列表
     * @desc (1)获取我的关注或粉丝列表
     * @return array
     * @throws BadRequestException
     */
    public function getFollowList()
    {
        $user = \PhalApi\DI()->user;

        if (!$user->isLogin()) {
            throw new BadRequestException('账号未登录或登录token已过期', 1999);
        }

        if (!$this->page) {
            $this->page = 1;
        }

        if (!$this->size) {
            $this->size = 20;
        }

        $follow = new UserFollowDomain();
        $ret = [];
        if ($this->type == self::$FOLLOW_TYPE['FOLLOW']) {
            $ret = $follow->getFollowingList($user->getUserId(), $this->page, $this->size);
        } else {
            $ret = $follow->getFollowedList($user->getUserId(), $this->page, $this->size);
        }

        return $ret;
    }

    /**
     * 修改用户信息
     * @desc (0.8)修改用户信息
     */
    public function modify()
    {
        $user = \PhalApi\DI()->user;
        if (!$user->isLogin()) {
            throw new BadRequestException('账号未登录或登录token已过期', 1999);
        }

        $domain = new UserDomain();

        $data = [];

        if ($this->nickname) {
            $data['nickname'] = $this->nickname;
        }

        if ($this->avatar) {
            $data['avatar'] = $this->avatar;
        }

        if ($this->bg) {
            $data['bg'] = $this->bg;
        }

        if ($this->sex) {
            $data['sex'] = $this->sex;
        }

        if ($this->email) {
            $data['email'] = $this->email;
        }

        if ($this->slogan) {
            $data['slogan'] = $this->slogan;
        }

        if ($this->birth) {
            $data['birth'] = $this->birth <= UserModel::$birthMin || $this->birth > date('Ymd') ? UserModel::$birthMin : $this->birth;
        }

        if ($this->height) {
            $data['height'] = $this->height;
        }

        if ($this->weight) {
            $data['weight'] = $this->weight;
        }

        if ($this->sexuality) {
            $data['sexuality'] = $this->sexuality;
        }

        if ($this->cdn_id) {
            $data['cdn_id'] = $this->cdn_id;
        }

        if (empty($data)) {
            throw new BadRequestException('没有任何需要修改的字段??', 500);
        }

        $data['update_time'] = time();

        $updateRows = $domain->update($data, $user->getProfileBy('id', 0));

        if ($updateRows <= 0) {
            throw new BadRequestException('修改失败', 501);
        }

        //添加es索引
        $es = new Es();
        try {
            $es->updateNearby($user->getUserId(), 0, 0, $this->sexuality,
                $this->height, $this->weight,
                $this->birth ? date('Y-m-d', strtotime($data['birth'])) : 0,
                0, $this->nickname
            );
        } catch (\Exception $e) {
            throw new BadRequestException($e->getMessage(), 510);
        }

        $domain = new UserDomain();
        return ['profile' => $domain->getUserInfo($user->getUserId(), 'id,nickname,status,mobile,tel_pre,avatar,bg,sex,slogan,birth,height,weight,sexuality,fo,fa,lk')];
    }

}