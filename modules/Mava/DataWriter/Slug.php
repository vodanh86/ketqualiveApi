<?php
class Mava_DataWriter_Slug extends Mava_DataWriter {
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__slug' => array(
                'id'               => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'slug'                 => array('type' => self::TYPE_STRING, 'maxLength' => 250, 'required' => true),
                'app'                 => array('type' => self::TYPE_STRING, 'maxLength' => 50),
                'controller'                 => array('type' => self::TYPE_STRING, 'maxLength' => 50),
                'action'                 => array('type' => self::TYPE_STRING, 'maxLength' => 50),
                'params'                 => array('type' => self::TYPE_STRING),
                'key'                 => array('type' => self::TYPE_STRING, 'maxLength' => 32),
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

        return array('#__slug' => $this->_getSlugModel()->getSlugById($id));
    }

    /**
     * @return Mava_Model_Slug
     */
    protected function _getSlugModel(){
        return $this->getModelFromCache('Mava_Model_Slug');
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