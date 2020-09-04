<?php
class Mava_DataWriter_PageGroup extends Mava_DataWriter
{

    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__page_group' => array(
                'id'               => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'                 => array('type' => self::TYPE_STRING, 'maxLength' => 250, 'required' => true),
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

        return array('#__page_group' => $this->_getPageGroupModel()->getPageGroupById($id));
    }

    /**
     * @return Mava_Model_PageGroup
     */
    protected function _getPageGroupModel(){
        return $this->getModelFromCache('Mava_Model_PageGroup');
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