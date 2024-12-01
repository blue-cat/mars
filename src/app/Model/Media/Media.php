<?php
namespace App\Model\Media;

use PhalApi\Model\DataModel;

class Media extends DataModel {

    protected function getTableName($id) {
        return 'mars_media';
    }

    public function getInfo($id) {
        return $this->getORM()->select('*')->where('id = ?', $id)->fetch();
    }

    public function getListByCoverId($coverIds) {
        $where = [
            'id' => $coverIds,
        ];
        return $this->getORM()->where($where)->select('id, width, height, dir, v_dir, length, cdn_id, status')->fetchAll();
    }

    public function getListByObjId($objType, $objId, $select = '*') {
        $where = [
            'obj_id' => $objId,
           'obj_type' => $objType
        ];
        $order = '`order` ASC';
        return $this->getORM()->where($where)->order($order)->select($select)->fetchAll();
    }

    public function getListByObjIdAndOrder($objType, $objId, $order) {
        $where = [
            'obj_id' => $objId,
            'order' => $order,
            'obj_type' => $objType,
        ];
        return $this->getORM()->where($where)->select('*')->fetch();
    }

    public function getListById($ids, $select = '*') {
        $where = [
            'id' => $ids,
        ];
        return $this->getORM()->where($where)->select($select)->fetchAll();
    }

}