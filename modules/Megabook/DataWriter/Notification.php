<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_Notification extends Mava_DataWriter {
    const TYPE_GENERAL = 'general';
    const TYPE_NEW_ORDER = 'new_order';
    const TYPE_ORDER_FINISHED = 'order_finished';
    const TYPE_WITHDRAW_ACCEPT = 'withdraw_accept';
    const TYPE_WITHDRAW_REJECT = 'withdraw_reject';
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__agency_notifications' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'agency_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'type'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'text'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'link'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'created_date'   => array('type' => self::TYPE_UINT, 'default' => time()),
                'has_read'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'has_seen'   => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
     * @return Megabook_Model_Notification
     */
    protected function _getNotificationModel(){
        return $this->getModelFromCache('Megabook_Model_Notification');
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

        return array('#__agency_notifications' => $this->_getNotificationModel()->getById($id));
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