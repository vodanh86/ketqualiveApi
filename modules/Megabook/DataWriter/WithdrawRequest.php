<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_WithdrawRequest extends Mava_DataWriter {
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_PAID = 'paid';
    const STATUS_REJECT = 'reject';
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__agency_withdraw_request' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'agency_id'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'user_id'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'amount'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'agency_balance'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'created_date'   => array('type' => self::TYPE_UINT, 'default' => time()),
                'created_ip'   => array('type' => self::TYPE_STRING, 'default' => ip()),
                'reject_reason'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'status'   => array('type' => self::TYPE_STRING, 'default' => self::STATUS_PENDING)
            )
        );
    }

    /**
     * @return Megabook_Model_WithdrawRequest
     */
    protected function _getWithdrawRequestModel(){
        return $this->getModelFromCache('Megabook_Model_WithdrawRequest');
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

        return array('#__agency_withdraw_request' => $this->_getWithdrawRequestModel()->getById($id));
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