<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 12:00 PM
 */
class Index_DataWriter_Recall extends Mava_DataWriter {
    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_DONE = 'done';
    const STATUS_DELETED = 'deleted';

    const RECALL_TYPE_RECALL = 'recall';
    const RECALL_TYPE_QUICK_ORDER = 'quick_order';
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__recall' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'phone'   => array('type' => self::TYPE_STRING, 'required' => true),
                'title'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'url'   => array('type' => self::TYPE_STRING, 'default' => Mava_Url::getCurrentAddress()),
                'type'   => array('type' => self::TYPE_STRING, 'default' => 'recall'),
                'created_time'   => array('type' => self::TYPE_UINT, 'default' => time()),
                'created_ip'   => array('type' => self::TYPE_STRING, 'default' => ip()),
                'status'   => array('type' => self::TYPE_STRING, 'default' => 'new'),
            )
        );
    }

    /**
     * @return Index_Model_Recall
     */
    protected function _getRecallModel(){
        return $this->getModelFromCache('Index_Model_Recall');
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

        return array('#__recall' => $this->_getRecallModel()->getById($id));
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