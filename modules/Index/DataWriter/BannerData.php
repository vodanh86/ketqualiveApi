<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/4/16
 * @Time: 10:39 PM
 */
class Index_DataWriter_BannerData extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__banner_data' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'banner_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'title'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'subtitle'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'href'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'background'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'image'   => array('type' => self::TYPE_STRING, 'required' => true),
                'language_code'   => array('type' => self::TYPE_STRING, 'required' => true)
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

        return array('#__banner_data' => $this->_getBannerModel()->getDataById($id));
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