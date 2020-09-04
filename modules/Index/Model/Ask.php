<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 2:16 PM
 */
class Index_Model_Ask extends Mava_Model {
    public function getById($id){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__ask WHERE `id`=". (int)$id);
        }else{
            return false;
        }
    }

    public function getList($skip = 0, $limit = 10, $status = 'all'){
        if(in_array($status, array('new','answered','deleted'))){
            $where = 'WHERE `status`="'. $status .'"';
        }else{
            $where = '';
        }
        $questions = $this->_getDb()->fetchAll("SELECT * FROM #__ask ". $where ." ORDER BY `sort_order` ASC, `created_time` DESC LIMIT ". $skip .','. $limit);
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS 'total' FROM #__ask ". $where);
        return array(
            'items' => $questions,
            'total' => $count['total']
        );
    }
}