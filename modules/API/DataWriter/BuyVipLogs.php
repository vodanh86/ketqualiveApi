<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_BuyVipLogs extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__buy_vip_logs' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'token'        => array('type' => self::TYPE_STRING),
                'num'        => array('type' => self::TYPE_UINT),
                'price'        => array('type' => self::TYPE_UINT),
                'total_amount'        => array('type' => self::TYPE_UINT),
                'coin'        => array('type' => self::TYPE_UINT),
                'error'        => array('type' => self::TYPE_UINT),
                'created_at'    => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return API_Model_BuyVipLogs
     */
    protected function _getBuyVipLogsModel(){
        return $this->getModelFromCache('API_Model_BuyVipLogs');
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

        return array('#__buy_vip_logs' => $this->_getBuyVipLogsModel()->getById($id));
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