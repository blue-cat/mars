<?php
namespace App\Domain\Media;

use App\Model\Media\Media as MediaModel;
use PhalApi\Exception;


class Media {

    const MEDIA_OK = 1;

    const MEDIA_BAN = 2;

    const MEDIA_DEL = 3;

    const MEDIA_PRE = 0;//待审核,只有命中敏感才进入待审核

    public function getMediaById($id, $select = '*') {
        $model = new MediaModel();
        $res = $model->getDataById($id, $select);//这是个魔术方法,自动找到getDataBy方法

        if (strpos($select, 'dir') !== false && isset($res['cdn_id'])) {
            $cdnDomains = \PhalApi\DI()->config->get('client')['base_url']['cdn_url'];
            //拼接域名
            $cdnDomain = $cdnDomains[$res['cdn_id']];
            if (isset($res['dir']) && $res['dir']) {
                $keyNum = substr($res['dir'], -10, 2);
                $res['dir'] = str_replace('*', 's'.$keyNum, $cdnDomain) . $res['dir'];
            }

            if (isset($res['v_dir']) && $res['v_dir']) {
                $keyNum = substr($res['v_dir'], -10, 2);
                $res['v_dir'] = str_replace('*', 's' . $keyNum, $cdnDomain) . $res['v_dir'];
            }
        }

        return $res;
    }

    /**
     * 插入或更新
     */
    public function save($obj_type, $obj_id, $order, $array) {
        // 先查一下是否存在
        $model = new MediaModel();
        $mediaInfo = $model->getListByObjIdAndOrder($obj_type, $obj_id, $order);

        if ($mediaInfo) {
            // 存在, 更新
            return $this->update($mediaInfo['id'], $array);
        } else {
            // 不存在, ��入
            return $this->add($array['user_id'], $obj_type, $array['user_id'], $array['order'], $array['width'], $array['height'], $array['type'], $array['dir'], $array['is_video'], $array['v_dir'], $array['length'], $array['cdn_id']);
        }
    }

    public function add($userId, $obj_type, $obj_id, $order, $width, $height, $type, $dir, $is_video = 0, $v_dir = '', $length = 0, $cdn_id = 0) {
        $status = self::MEDIA_OK;
        $time = time();

        $data = [
            'user_id' => $userId,
            'obj_type' => (int) $obj_type,
            'obj_id' => (int) $obj_id,
            'order' => (int) $order,
            'width' => (int) $width,
            'height' => (int) $height,
            'type' => $type,
            'is_video' => (int) $is_video,
            'dir' => $dir ?: '',
            'v_dir' => $v_dir ?: '',
            'cdn_id' => (int) $cdn_id,
            'length' => (int) $length,
            'create_time' => $time,
            'update_time' => $time,
            'status' => $status
        ];

        print_r($data);

        $model = new MediaModel();
        $ret = $model->insert($data);

        return $ret;
    }

    public function update($id, $array) {
        $model = new MediaModel();
        $array['update_time'] = time();
        return $model->update($id, $array);
    }

    public function del($user_id, $media_id) {
        $mediaInfo = $this->getMediaById($media_id, 'user_id, status');
        if (empty($mediaInfo)) {
            throw new Exception('要删除的资源不存在', 420);
        }

        if ($mediaInfo['status'] != self::MEDIA_OK) {
            throw new Exception('要删除的资源不存在', 421);
        }

        if ($mediaInfo['user_id'] != $user_id && !in_array($user_id, \PhalApi\DI()->config->get('admin.admin_ids'))) {
            throw new Exception('无权限删除', 422);
        }

        $data = [
            'status' => self::MEDIA_DEL,
            'update_time' => time(),
        ];
        $model = new MediaModel();
        $ret = $model->update($media_id, $data);
        
        return $ret;
    }

}