<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/9/15
 * @Time: 11:15 AM
 */
class Blog_DataWriter_Category extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__blog_category' => array(
                'category_id'   => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'         => array('type' => self::TYPE_STRING, 'maxLength' => 250, 'required' => true),
                'description'   => array('type' => self::TYPE_STRING, 'maxLength' => 250),
                'sort_order'    => array('type' => self::TYPE_UINT, 'default' => 9999)
            )
        );
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _getExistingData($data)
    {
        if (!$category_id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__blog_category' => $this->_getBlogCategoryModel()->getCategoryById($category_id));
    }

    /**
     * @return Blog_Model_Category
     * @throws Mava_Exception
     */
    protected function _getBlogCategoryModel(){
        return $this->getModelFromCache('Blog_Model_Category');
    }

    /**
     * @param $tableName
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'category_id = ' . $this->_db->quote($this->getExisting('category_id'));
    }
}