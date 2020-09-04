<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_RequestCoinLogs extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__request_coin_logs' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'token'        => array('type' => self::TYPE_STRING),
                'client_request'        => array('type' => self::TYPE_STRING),
                'api_request'        => array('type' => self::TYPE_STRING),
                'api_response'        => array('type' => self::TYPE_STRING),
                'error'        => array('type' => self::TYPE_UINT),
                'created_at'    => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return API_Model_RequestCoinLogs
     */
    protected function _getRequestCoinLogsModel(){
        return $this->getModelFromCache('API_Model_RequestCoinLogs');
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

        return array('#__request_coin_logs' => $this->_getRequestCoinLogsModel()->getById($id));
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