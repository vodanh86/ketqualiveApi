<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 1/17/17
 * @Time: 3:20 PM
 */
class Megabook_DataWriter_AdsCampaign extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__ads_campaign' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'note'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'group_id'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'click_count'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'order_count'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'total_revenue'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'created_by'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'created_time'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'deleted'   => array('type' => self::TYPE_STRING, 'default' => 'no')
            )
        );
    }

    /**
         * @return Megabook_Model_AdsCampaign
         */
        protected function _getAdsCampaignModel(){
            return $this->getModelFromCache('Megabook_Model_AdsCampaign');
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

        return array('#__ads_campaign' => $this->_getAdsCampaignModel()->getById($id));
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