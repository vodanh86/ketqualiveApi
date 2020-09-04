<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/4/15
 * @Time: 10:30 AM
 */
class Mava_DataWriter_Notification extends Mava_DataWriter {
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__notification' => array(
                'notify_id'     => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'type'          => array('type' => self::TYPE_STRING, 'required' => true),
                'href'          => array('type' => self::TYPE_STRING, 'default' => ''),
                'content'       => array('type' => self::TYPE_STRING, 'maxLength' => 250, 'required' => true),
                'user_id'       => array('type' => self::TYPE_UINT, 'default' => 0),
                'created_date'  => array('type' => self::TYPE_UINT, 'default' => time()),
                'read'          => array('type' => self::TYPE_UINT, 'default' => 0)
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
        if (!$notify_id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__notification' => $this->_getNotificationModel()->getNotifyById($notify_id));
    }

    /**
     * @return Mava_Model_Notification
     */
    protected function _getNotificationModel(){
        return Mava_Model::create('Mava_Model_Notification');
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'notify_id = ' . $this->_db->quote($this->getExisting('notify_id'));
    }
}