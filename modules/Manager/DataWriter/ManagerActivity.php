<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class Manager_DataWriter_ManagerActivity extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__manager_activity_logs' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'manager_id'        => array('type' => self::TYPE_UINT),
                'activity'        => array('type' => self::TYPE_STRING),
                'created_at'    => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return Manager_Model_ManagerActivity
     */
    protected function _getManagerActivityModel(){
        return $this->getModelFromCache('Manager_Model_ManagerActivity');
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

        return array('#__manager_activity_logs' => $this->_getManagerActivityModel()->getById($id));
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