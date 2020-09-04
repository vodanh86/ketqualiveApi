<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/11/15
 * @Time: 10:21 AM
 */
class Mava_DataWriter_PhoneQueue extends Mava_DataWriter {
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__queue_phone' => array(
                'queue_id'    => array('type' => self::TYPE_UINT,   'autoIncrement' => true),
                'type'  => array('type' => self::TYPE_STRING,   'required' => true),
                'number'        => array(
                    'type' => self::TYPE_STRING,
                    'required' => true,
                    'maxLength' => 20,
                    'verification' => array('$this', '_verifyPhone'),
                    'requiredError' => 'phone_invalid'
                ),
                'content'  => array('type' => self::TYPE_STRING, 'required' => true, 'maxLength' => 50),
                'created_date' => array('type' => self::TYPE_UINT, 'default' => time())
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
        if (!$queue_id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__phrase' => $this->_getPhoneQueueModel()->getPhoneQueueById($queue_id));
    }

    /**
     * @return Mava_Model_PhoneQueue
     */
    protected function _getPhoneQueueModel(){
        return Mava_Model::create('Mava_Model_PhoneQueue');
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'queue_id = ' . $this->_db->quote($this->getExisting('queue_id'));
    }

    /**
     * Verifies that the phrase title ID is valid.
     *
     * @param string $title
     *
     * @return boolean
     */
    protected function _verifyPhone($phone)
    {
        return Mava_String::isPhoneNumber($phone);
    }
}