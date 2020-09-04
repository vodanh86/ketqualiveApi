<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 1:02 AM
 */
class Megabook_Model_Menu extends Mava_Model {
    public function getById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__menu WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getAll(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__menu ORDER BY `sort_order` ASC");
    }

    public function getMaxSortOrder(){
        $sort_order = $this->_getDb()->fetchRow("SELECT MAX(sort_order) as 'max_sort_order' FROM #__menu");
        return (int)$sort_order['max_sort_order'];
    }
}