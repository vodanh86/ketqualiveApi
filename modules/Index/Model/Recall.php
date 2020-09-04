<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 11:59 AM
 */
class Index_Model_Recall extends Mava_Model {
    public function markReadRecall(){
        return $this->_getDb()->update('#__recall', array('status' => 'read'),"`status`='new'");
    }

    public function getById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__recall WHERE `id`=". (int)$id);
        }else{
            return false;
        }
    }

    public function getLatestByPhone($phone){
        if($phone != ""){
            return $this->_getDb()->fetchRow("SELECT * FROM #__recall WHERE `phone`='". addslashes($phone) ."' ORDER BY `created_time` DESC");
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10, $type = 'recall', $status = 'all'){
        if($type==''){
            $type = 'recall';
        }
        $where = 'WHERE `type`="'. $type .'"';
        if(in_array($status, array('new','read','done','deleted'))){
            $where .= 'AND `status`="'. $status .'"';
        }else{
            $where .= '';
        }
        $recall = $this->_getDb()->fetchAll("SELECT * FROM #__recall ". $where ." ORDER BY `created_time` DESC LIMIT ". $skip .','. $limit);
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__recall ". $where);
        return array(
            'items' => $recall,
            'total' => $count['total']
        );
    }
}