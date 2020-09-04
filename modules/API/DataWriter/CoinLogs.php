<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_CoinLogs extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__coin_logs' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'token'        => array('type' => self::TYPE_STRING),
                'coin_before'        => array('type' => self::TYPE_UINT),
                'coin_change'        => array('type' => self::TYPE_UINT),
                'coin_after'        => array('type' => self::TYPE_UINT),
                'type'        => array('type' => self::TYPE_STRING),
                'data'        => array('type' => self::TYPE_STRING),
                'created_at'    => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return API_Model_CoinLogs
     */
    protected function _getCoinLogsModel(){
        return $this->getModelFromCache('API_Model_CoinLogs');
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

        return array('#__coin_logs' => $this->_getCoinLogsModel()->getById($id));
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