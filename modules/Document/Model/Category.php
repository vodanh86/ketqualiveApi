<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/9/15
 * @Time: 10:36 AM
 */
class Document_Model_Category extends Mava_Model {
    /**
     * @return array
     * @throws Mava_Exception
     */
    public function getAllCategory(){
        $category = $this->_getDb()->query("SELECT * FROM #__document_category ORDER BY `sort_order` ASC");
        return $category->rows;
    }

    /**
     * @param int $categoryId
     * @return bool | array
     */
    public function getCategoryById($categoryId){
        if($categoryId > 0){
            return $this->_getDb()->fetchRow("SELECT * FROM #__document_category WHERE `category_id`='". $categoryId ."'");
        }else{
            return false;
        }
    }
}