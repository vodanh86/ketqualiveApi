<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/9/15
 * @Time: 10:36 AM
 */
class Blog_Model_Category extends Mava_Model {
    /**
     * @return array
     * @throws Mava_Exception
     */
    public function getAllCategory(){
        $category = $this->_getDb()->query("SELECT * FROM #__blog_category ORDER BY `sort_order` ASC");
        return $category->rows;
    }

    /**
     * @param int $categoryId
     * @return bool | array
     */
    public function getCategoryById($categoryId){
        if($categoryId > 0){
            $category = $this->_getDb()->getSingleTableRow('#__blog_category','*', array(
                array('category_id','=',$categoryId)
            ));
            if($category->num_rows > 0){
                return $category->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}