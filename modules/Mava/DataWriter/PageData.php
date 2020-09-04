<?php
class Mava_DataWriter_PageData extends Mava_DataWriter {
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__page_data' => array(
                'id'               => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'page_id'                 => array('type' => self::TYPE_INT, 'required' => true),
                'long_title'                 => array('type' => self::TYPE_STRING, 'maxLength' => 250),
                'short_title'                 => array('type' => self::TYPE_STRING, 'maxLength' => 250),
                'content_html'                 => array('type' => self::TYPE_STRING),
                'content_css'                 => array('type' => self::TYPE_STRING),
                'content_js'                 => array('type' => self::TYPE_STRING),
                'language_code'                 => array('type' => self::TYPE_STRING),
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

        return array('#__page_data' => $this->_getPageDataModel()->getPageDataById($id));
    }

    /**
     * @return Mava_Model_PageData
     */
    protected function _getPageDataModel(){
        return $this->getModelFromCache('Mava_Model_PageData');
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