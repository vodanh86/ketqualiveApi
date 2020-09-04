<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 6/29/16
 * @Time: 11:30 AM
 */
class Index_DataWriter_BannerPosition extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__banner_position' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'position'   => array('type' => self::TYPE_STRING, 'required' => true)
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

        return array('#__banner_position' => $this->_getBannerModel()->getPositionById($id));
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