<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_LotoLucky extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__loto_lucky' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'lucky_date'        => array('type' => self::TYPE_STRING),
                'lucky_number'        => array('type' => self::TYPE_STRING)            )
        );
    }

    /**
     * @return API_Model_LotoLucky
     */
    protected function _getLotoLuckyModel(){
        return $this->getModelFromCache('API_Model_LotoLucky');
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

        return array('#__loto_lucky' => $this->_getLotoLuckyModel()->getById($id));
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