<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_BookSet extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__book_set' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'section_id'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'landing_page'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'cover'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'seo_config'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'sort_order'   => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
     * @return Megabook_Model_BookSet
     */
    protected function _getBookSetModel(){
        return $this->getModelFromCache('Megabook_Model_BookSet');
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

        return array('#__book_set' => $this->_getBookSetModel()->getById($id));
    }

    /**
     * @param $tableName
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'id = ' . $this->_db->quote($this->getExisting('id'));
    }
}