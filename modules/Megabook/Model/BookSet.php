<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 1:02 AM
 */
class Megabook_Model_BookSet extends Mava_Model {
    public function getById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__book_set WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getSectionById($id = 0){
        if($id > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__book_set_section WHERE `id`='". (int)$id ."'");
        }else{
            return false;
        }
    }

    public function getAllSection(){
        return $this->_getDb()->fetchAll("SELECT * FROM #__book_set_section ORDER BY `sort_order` ASC");
    }

    public function getAll(){
        return $this->_getDb()->fetchAll("SELECT s.*, sc.`title` AS 'section_title' FROM #__book_set s LEFT JOIN #__book_set_section sc ON s.`section_id`=sc.`id` ORDER BY sc.`sort_order` ASC, s.`sort_order` ASC");
    }

    public function getBySection($sectionID){
        return $this->_getDb()->fetchAll("SELECT s.*, sc.`title` AS 'section_title' FROM #__book_set s LEFT JOIN #__book_set_section sc ON s.`section_id`=sc.`id` WHERE s.`section_id`='". (int)$sectionID ."' ORDER BY sc.`sort_order` ASC, s.`sort_order` ASC");
    }

    public function getMaxSortOrderSection(){
        $sort_order = $this->_getDb()->fetchRow("SELECT MAX(sort_order) as 'max_sort_order' FROM #__book_set_section");
        return (int)$sort_order['max_sort_order'];
    }

    public function getMaxSortOrder(){
        $sort_order = $this->_getDb()->fetchRow("SELECT MAX(sort_order) as 'max_sort_order' FROM #__book_set");
        return (int)$sort_order['max_sort_order'];
    }
}