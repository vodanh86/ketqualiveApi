<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 1:15 PM
 */
class Index_DataWriter_Subscribe extends Mava_DataWriter {
    const STATUS_SUBSCRIBE = 'subscribe';
    const STATUS_UNSUBSCRIBE = 'unsubscribe';
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__subscribes' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'email'   => array('type' => self::TYPE_STRING, 'required' => true),
                'created_time'   => array('type' => self::TYPE_UINT, 'default' => time()),
                'created_ip'   => array('type' => self::TYPE_STRING, 'default' => ip()),
                'status'   => array('type' => self::TYPE_STRING, 'default' => 'subscribe'),
            )
        );
    }

    /**
     * @return Index_Model_Subscribe
     */
    protected function _getSubscribeModel(){
        return $this->getModelFromCache('Index_Model_Subscribe');
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

        return array('#__subscribes' => $this->_getSubscribeModel()->getById($id));
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