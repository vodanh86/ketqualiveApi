<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_Menu extends Mava_DataWriter {
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__menu' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'link'   => array('type' => self::TYPE_STRING, 'required' => true),
                'open_in_new_tab'   => array('type' => self::TYPE_STRING, 'default' => 'no'),
                'text_color'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'sort_order'   => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
     * @return Megabook_Model_Menu
     */
    protected function _getMenuModel(){
        return $this->getModelFromCache('Megabook_Model_Menu');
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

        return array('#__menu' => $this->_getMenuModel()->getById($id));
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