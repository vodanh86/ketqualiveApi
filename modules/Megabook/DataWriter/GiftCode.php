<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_GiftCode extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__gift_code' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'code'   => array('type' => self::TYPE_STRING, 'required' => true),
                'value_int'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'value_type'   => array('type' => self::TYPE_STRING, 'default' => 'fixed'),
                'cond_num_product'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'cond_total_amount'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'cond_start_time'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'cond_end_time'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'num_of_use'   => array('type' => self::TYPE_UINT, 'default' => 1),
                'used_count'   => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
     * @return Megabook_Model_GiftCode
     */
    protected function _getGiftCodeModel(){
        return $this->getModelFromCache('Megabook_Model_GiftCode');
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

        return array('#__gift_code' => $this->_getGiftCodeModel()->getById($id));
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