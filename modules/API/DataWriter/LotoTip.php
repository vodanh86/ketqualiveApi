<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_LotoTip extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__loto_tip' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'region_code'        => array('type' => self::TYPE_STRING),
                'tip_date'        => array('type' => self::TYPE_STRING),
                'pack'        => array('type' => self::TYPE_UINT),
                'num_1'        => array('type' => self::TYPE_UINT),
                'num_2'        => array('type' => self::TYPE_UINT),
                'num_3'        => array('type' => self::TYPE_UINT),
                'reg_count'        => array('type' => self::TYPE_UINT)
            )
        );
    }

    /**
     * @return API_Model_LotoTip
     */
    protected function _getLotoTipModel(){
        return $this->getModelFromCache('API_Model_LotoTip');
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

        return array('#__loto_tip' => $this->_getLotoTipModel()->getById($id));
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