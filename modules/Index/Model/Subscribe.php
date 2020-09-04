<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 1:14 PM
 */
class Index_Model_Subscribe extends Mava_Model {
    public function getById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__subscribes WHERE `id`=". (int)$id);
        }else{
            return false;
        }
    }

    public function getByEmail($email){
        if($email != ""){
            return $this->_getDb()->fetchRow("SELECT * FROM #__subscribes WHERE `email`='". addslashes($email) ."' ORDER BY `created_time` DESC");
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10, $status = 'all'){
        if(in_array($status, array('subscribe','unsubscribe'))){
            $where = 'WHERE `status`="'. $status .'"';
        }else{
            $where = '';
        }
        $subscribes = $this->_getDb()->fetchAll("SELECT * FROM #__subscribes ". $where ." ORDER BY `created_time` DESC LIMIT ". $skip .','. $limit);
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__subscribes ". $where);
        return array(
            'items' => $subscribes,
            'total' => $count['total']
        );
    }
}