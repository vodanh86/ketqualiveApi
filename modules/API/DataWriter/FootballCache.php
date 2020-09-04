<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/26/2019
 * Time: 3:32 PM
 */

class API_DataWriter_FootballCache extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__football_cache' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'request_key'        => array('type' => self::TYPE_STRING),
                'response_data'        => array('type' => self::TYPE_STRING),
                'created_at'    => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return API_Model_FootballCache
     */
    protected function _getFootballCacheModel(){
        return $this->getModelFromCache('API_Model_FootballCache');
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

        return array('#__football_cache' => $this->_getFootballCacheModel()->getById($id));
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