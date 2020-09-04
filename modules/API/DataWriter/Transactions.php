<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_Transactions extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__transactions' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'code'        => array('type' => self::TYPE_STRING),
                'token'        => array('type' => self::TYPE_STRING),
                'card_number'        => array('type' => self::TYPE_STRING),
                'card_serial'        => array('type' => self::TYPE_STRING),
                'card_value'        => array('type' => self::TYPE_UINT),
                'status'        => array('type' => self::TYPE_UINT, 'default' => 0),
                'is_received'        => array('type' => self::TYPE_UINT, 'default' => 0),
                'created_at'        => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return API_Model_Transactions
     */
    protected function _getTransactionsModel(){
        return $this->getModelFromCache('API_Model_Transactions');
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

        return array('#__transactions' => $this->_getTransactionsModel()->getById($id));
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