<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/9/15
 * @Time: 4:55 PM
 */
class Mava_DataWriter_EmailQueue extends Mava_DataWriter {
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__queue_email' => array(
                'queue_id'    => array('type' => self::TYPE_UINT,   'autoIncrement' => true),
                'type'  => array('type' => self::TYPE_STRING,   'required' => true),
                'email'        => array(
                    'type' => self::TYPE_STRING,
                    'required' => true,
                    'maxLength' => 150,
                    'verification' => array('$this', '_verifyEmail'),
                    'requiredError' => 'email_invalid'
                ),
                'content'  => array('type' => self::TYPE_STRING, 'required' => true),
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

        return array('#__queue_email' => $this->_getEmailQueueModel()->getEmailQueueById($queue_id));
    }

    /**
     * @return Mava_Model_EmailQueue
     */
    protected function _getEmailQueueModel(){
        return Mava_Model::create('Mava_Model_EmailQueue');
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
    protected function _verifyEmail($email)
    {
        return Mava_String::isEmail($email);
    }
}