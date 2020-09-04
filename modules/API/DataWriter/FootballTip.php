<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_FootballTip extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__football_tip' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'pack'        => array('type' => self::TYPE_UINT),
                'tip_date'        => array('type' => self::TYPE_STRING),
                'tip'        => array('type' => self::TYPE_STRING),
                'reg_count'        => array('type' => self::TYPE_UINT)
            )
        );
    }

    /**
     * @return API_Model_FootballTip
     */
    protected function _getFootballTipModel(){
        return $this->getModelFromCache('API_Model_FootballTip');
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

        return array('#__football_tip' => $this->_getFootballTipModel()->getById($id));
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