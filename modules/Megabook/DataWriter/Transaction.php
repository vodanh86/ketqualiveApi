<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_Transaction extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__agency_transaction_log' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'agency_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'transaction_amount'   => array('type' => self::TYPE_UINT, 'required' => true),
                'transaction_type'   => array('type' => self::TYPE_STRING, 'default' => 'add'),
                'reason'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'before_change'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'after_change'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'created_date'   => array('type' => self::TYPE_STRING, 'default' => time()),
                'created_ip'   => array('type' => self::TYPE_STRING, 'default' => ip())
            )
        );
    }

    /**
     * @return Megabook_Model_Transaction
     */
    protected function _getTransactionModel(){
        return $this->getModelFromCache('Megabook_Model_Transaction');
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

        return array('#__agency_transaction_log' => $this->_getTransactionModel()->getById($id));
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