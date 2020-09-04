<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 10/23/15
 * @Time: 10:36 AM
 */
class Mava_DataWriter_Permission extends Mava_DataWriter
{

    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__permission' => array(
                'perm_id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'   => array('type' => self::TYPE_STRING, 'maxLength' => 250, 'required' => true),
                'perm_key'   => array('type' => self::TYPE_STRING, 'maxLength' => 50, 'required' => true)
            )
        );
    }

    /**
     * @return Mava_Model_Permission
     */
    protected function _getPermissionModel(){
        return $this->getModelFromCache('Mava_Model_Permission');
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

        return array('#__permission' => $this->_getPermissionModel()->getPermissionById($id));
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'perm_id = ' . $this->_db->quote($this->getExisting('perm_id'));
    }
}