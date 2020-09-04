<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 11/25/16
 * @Time: 12:58 AM
 */
class Megabook_DataWriter_Agency extends Mava_DataWriter {
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVATED = 'activated';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_DELETED = 'deleted';
    /**
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__agency' => array(
                'id'      => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'agency_code'   => array('type' => self::TYPE_STRING, 'maxLength' => 45, 'required' => true),
                'title'   => array('type' => self::TYPE_STRING, 'required' => true),
                'user_id'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'balance'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'email'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'phone'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'address'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'bank_fullname'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'bank_name'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'bank_id_string'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'bank_branch'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'created_date'   => array('type' => self::TYPE_UINT, 'default' => time()),
                'created_ip'   => array('type' => self::TYPE_STRING, 'default' => ip()),
                'status'   => array('type' => self::TYPE_STRING, 'default' => 'pending')
            )
        );
    }

    /**
     * @return Megabook_Model_Agency
     */
    protected function _getAgencyModel(){
        return $this->getModelFromCache('Megabook_Model_Agency');
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

        return array('#__agency' => $this->_getAgencyModel()->getById($id));
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