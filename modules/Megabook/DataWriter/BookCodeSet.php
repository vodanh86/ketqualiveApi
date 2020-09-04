<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_BookCodeSet extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__book_code_set' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'code_count'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'product_name'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'product_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'deleted'   => array('type' => self::TYPE_STRING, 'default' => 'no')
            )
        );
    }

    /**
     * @return Megabook_Model_BookCodeSet
     */
    protected function _getBookCodeSetModel(){
        return $this->getModelFromCache('Megabook_Model_BookCodeSet');
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

        return array('#__book_code_set' => $this->_getBookCodeSetModel()->getById($id));
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