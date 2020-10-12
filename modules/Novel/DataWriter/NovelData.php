<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/29/15
 * @Time: 10:41 AM
 */
class News_DataWriter_NewsData extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__news_data' => array(
                'data_id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'news_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'sapo'   => array('type' => self::TYPE_STRING, 'required' => true),
                'content'   => array('type' => self::TYPE_STRING, 'required' => true),
                'language_code'   => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'required' => true)
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

        return array('#__news_data' => $this->_getNewsModel()->getNewsDataById($id));
    }

    /**
     * @param $tableName
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'data_id = ' . $this->_db->quote($this->getExisting('data_id'));
    }
}