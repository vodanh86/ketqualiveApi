<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/11/15
 * @Time: 10:39 AM
 */
class Mava_DataWriter_AccountActiveQueue extends Mava_DataWriter {
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__user_active_queue' => array(
                'queue_id'    => array('type' => self::TYPE_UINT,   'autoIncrement' => true),
                'user_id'  => array('type' => self::TYPE_UINT,   'required' => true),
                'created_date' => array('type' => self::TYPE_UINT, 'default' => time()),
                'approved' => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
     * Gets the actual existing data out of data that was passed in. See parent for explanation.
     *
     * @param mixed
     *
     * @return array|false
     */
    protected function _getExistingData($data)
    {
        if (!$queue_id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__phrase' => $this->_getActiveQueueModel()->getActiveQueueById($queue_id));
    }

    /**
     * @return Mava_Model_AccountActiveQueue
     */
    protected function _getActiveQueueModel(){
        return Mava_Model::create('Mava_Model_AccountActiveQueue');
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'queue_id = ' . $this->_db->quote($this->getExisting('queue_id'));
    }
}