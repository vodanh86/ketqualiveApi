<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/29/15
 * @Time: 10:41 AM
 */
class News_DataWriter_News extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__news' => array(
                'news_id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'category_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'created_by'   => array('type' => self::TYPE_UINT, 'required' => true),
                'created_date'   => array('type' => self::TYPE_UINT, 'required' => true),
                'updated_date'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'updated_by'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'publish_date'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'views'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'deleted'   => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
     * @return Novel_Model_Novel
     */
    protected function _getNewsModel(){
        return $this->getModelFromCache('Novel_Model_Novel');
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

        return array('#__news' => $this->_getNewsModel()->getNewsById($id));
    }

    /**
     * @param $tableName
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'news_id = ' . $this->_db->quote($this->getExisting('news_id'));
    }
}