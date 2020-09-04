<?php
class Megabook_DataWriter_LinkStats extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__agency_link_stats' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'agency_id'   => array('type' => self::TYPE_UINT, 'required' => true),
                'link'   => array('type' => self::TYPE_STRING, 'required' => true),
                'link_hash'   => array('type' => self::TYPE_STRING, 'required' => true),
                'visitor'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'pageview'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'order_count'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'total_revenue'   => array('type' => self::TYPE_UINT, 'default' => 0),
            )
        );
    }

    /**
     * @return Megabook_Model_LinkStats
     */
    protected function _getLinkStatsModel(){
        return $this->getModelFromCache('Megabook_Model_LinkStats');
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

        return array('#__agency_link_stats' => $this->_getLinkStatsModel()->getById($id));
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