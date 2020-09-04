<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/29/15
 * @Time: 10:41 AM
 */
class News_DataWriter_NewsCategory extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__news_category' => array(
                'category_id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'parent_id'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'sort_order'   => array('type' => self::TYPE_UINT, 'default' => 9999)
            )
        );
    }

    /**
     * @return News_Model_News
     */
    protected function _getNewsModel(){
        return $this->getModelFromCache('News_Model_News');
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _getExistingData($data)
    {
        if (!$id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__news_category' => $this->_getNewsModel()->getNewsCategoryById($id));
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