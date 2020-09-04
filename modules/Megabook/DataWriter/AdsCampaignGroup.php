<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 1/17/17
 * @Time: 3:20 PM
 */
class Megabook_DataWriter_AdsCampaignGroup extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__ads_campaign_group' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'color'   => array('type' => self::TYPE_STRING, 'default' => '#333333'),
                'sort_order'   => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
         * @return Megabook_Model_AdsCampaignGroup
         */
        protected function _getAdsCampaignGroupModel(){
            return $this->getModelFromCache('Megabook_Model_AdsCampaignGroup');
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

        return array('#__ads_campaign_group' => $this->_getAdsCampaignGroupModel()->getById($id));
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