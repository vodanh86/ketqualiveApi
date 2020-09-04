<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/13/15
 * @Time: 1:46 PM
 */
class Mava_DataWriter_User extends Mava_DataWriter
{

    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__user' => array(
                'user_id'               => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'token'          => array('type' => self::TYPE_STRING, 'maxLength' => 32, 'required' => true),
                'cover'          => array('type' => self::TYPE_STRING, 'maxLength' => 255),
                'avatar'          => array('type' => self::TYPE_STRING, 'maxLength' => 255),
                'coin'                => array('type' => self::TYPE_UINT, 'default' => 0),
                'expired_vip'           => array('type' => self::TYPE_UINT, 'default' => time()),
                'is_supervip'                => array('type' => self::TYPE_UINT, 'default' => 0),
                'lock_account'                => array('type' => self::TYPE_UINT, 'default' => 0),
                'last_edit_name'                => array('type' => self::TYPE_UINT, 'default' => 0),
                'email'                 => array('type' => self::TYPE_STRING, 'maxLength' => 120, 'required' => false),
                'phone'                 => array('type' => self::TYPE_STRING, 'maxLength' => 20, 'required' => false),
                'gender'                => array('type' => self::TYPE_STRING),
                'balance'                => array('type' => self::TYPE_UINT, 'default' => 0),
                'register_date'         => array('type' => self::TYPE_UINT, 'required' => true, 'default' => time()),
                'last_activity'         => array('type' => self::TYPE_UINT, 'required' => true, 'default' => time()),
                'is_active'             => array('type' => self::TYPE_UINT),
                'username'              => array('type' => self::TYPE_STRING),
                'password'              => array('type' => self::TYPE_STRING, 'maxLength' => 32, 'minLength' => 5, 'required' => false),
                'unique_token'          => array('type' => self::TYPE_STRING, 'required' => false),
                'custom_title'          => array('type' => self::TYPE_STRING, 'required' => false),
                'language_id'           => array('type' => self::TYPE_UINT, 'default' => 0),
                'language_code'         => array('type' => self::TYPE_STRING, 'default' => 'vi-VN'),
                'timezone'              => array('type' => self::TYPE_STRING, 'required' => false),
                'user_group_id'         => array('type' => self::TYPE_UINT, 'required' => false),
                'is_banned'             => array('type' => self::TYPE_UINT),
                'banned_reason'         => array('type' => self::TYPE_STRING, 'maxLength' => 250),
                'birthday'              => array('type' => self::TYPE_UINT),
                'city_id'               => array('type' => self::TYPE_UINT),
                'email_verified'        => array('type' => self::TYPE_UINT),
                'phone_verified'        => array('type' => self::TYPE_UINT),
                'active_code'           => array('type' => self::TYPE_STRING),
                'forgotpassword_token'  => array('type' => self::TYPE_STRING)
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
        if (!$user_id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__user' => $this->_getUserModel()->getUserById($user_id));
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'user_id = ' . $this->_db->quote($this->getExisting('user_id'));
    }
}