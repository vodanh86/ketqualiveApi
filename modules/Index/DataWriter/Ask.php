<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 2:20 PM
 */
class Index_DataWriter_Ask extends Mava_DataWriter {
    const STATUS_NEW = 'new';
    const STATUS_ANSWERED = 'answered';
    const STATUS_DELETED = 'deleted';
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__ask' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'name'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'email'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'phone'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'created_time'   => array('type' => self::TYPE_STRING, 'default' => time()),
                'created_ip'   => array('type' => self::TYPE_STRING, 'default' => ip()),
                'question'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'answer'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'answer_by'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'sort_order'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'status'   => array('type' => self::TYPE_STRING, 'default' => 'new')
            )
        );
    }

    /**
     * @return Index_Model_Ask
     */
    protected function _getAskModel(){
        return $this->getModelFromCache('Index_Model_Ask');
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

        return array('#__ask' => $this->_getAskModel()->getById($id));
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