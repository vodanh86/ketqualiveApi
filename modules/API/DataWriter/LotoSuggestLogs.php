<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_LotoSuggestLogs extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__loto_suggest_logs' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'tip_id'        => array('type' => self::TYPE_UINT),
                'token'        => array('type' => self::TYPE_STRING),
                'price'        => array('type' => self::TYPE_UINT),
                'platform_type'        => array('type' => self::TYPE_STRING),
                'platform_version'        => array('type' => self::TYPE_STRING),
                'created_at'    => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return API_Model_LotoSuggestLogs
     */
    protected function _getLotoSuggestLogsModel(){
        return $this->getModelFromCache('API_Model_LotoSuggestLogs');
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

        return array('#__loto_suggest_logs' => $this->_getLotoSuggestLogsModel()->getById($id));
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