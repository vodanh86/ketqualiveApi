<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/21/15
 * @Time: 10:17 AM
 */
class Mava_DataWriter_UserGroup extends Mava_DataWriter
{

    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__user_group' => array(
                'group_id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'group_title'   => array('type' => self::TYPE_STRING, 'maxLength' => 150, 'required' => true),
                'sort_order' => array('type' => self::TYPE_UINT, 'default' => 9999)
            )
        );
    }

    /**
     * @return Mava_Model_UserGroup
     */
    protected function _getUserGroupModel(){
        return $this->getModelFromCache('Mava_Model_UserGroup');
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

        return array('#__user_group' => $this->_getUserGroupModel()->getUserGroupById($id));
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'group_id = ' . $this->_db->quote($this->getExisting('group_id'));
    }
}