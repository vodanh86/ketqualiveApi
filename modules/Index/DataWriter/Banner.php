<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 6/29/16
 * @Time: 11:30 AM
 */
class Index_DataWriter_Banner extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__banner' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'position_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'sort_order'   => array('type' => self::TYPE_UINT, 'required' => true)
            )
        );
    }

    /**
     * @return Index_Model_Banner
     */
    protected function _getBannerModel(){
        return $this->getModelFromCache('Index_Model_Banner');
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

        return array('#__banner' => $this->_getBannerModel()->getById($id));
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