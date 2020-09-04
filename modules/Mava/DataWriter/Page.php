<?php
class Mava_DataWriter_Page extends Mava_DataWriter {
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__pages' => array(
                'id'               => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'slug'                 => array('type' => self::TYPE_STRING, 'maxLength' => 250, 'required' => true),
                'group_id'                 => array('type' => self::TYPE_INT,'required' => true),
                'layout'                 => array('type' => self::TYPE_STRING),
                'publish_time'                 => array('type' => self::TYPE_INT),
                'unpublish_time'                 => array('type' => self::TYPE_INT),
                'created_by'                 => array('type' => self::TYPE_INT),
                'created_time'                 => array('type' => self::TYPE_INT),
                'sort_order'                 => array('type' => self::TYPE_INT),
                'show_title'                 => array('type' => self::TYPE_STRING),
            )
        );
    }

    /**
     * Gets the actual existing data out of data that was passed in. See parent for explanation.
     *
     * @param mixed
     *
     * @return array|false
     */
    protected function _getExistingData($data)
    {
        if (!$id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__pages' => $this->_getPageModel()->getPageById($id));
    }

    /**
     * @return Mava_Model_Page
     */
    protected function _getPageModel(){
        return $this->getModelFromCache('Mava_Model_Page');
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'id = ' . $this->_db->quote($this->getExisting('id'));
    }
}