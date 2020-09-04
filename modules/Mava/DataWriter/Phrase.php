<?php

/**
 * Data writer for phrases.
 *
 * @package Mava_Phrase
 */
class Mava_DataWriter_Phrase extends Mava_DataWriter
{

    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__phrase' => array(
                'phrase_id'    => array('type' => self::TYPE_UINT,   'autoIncrement' => true),
                'language_id'  => array('type' => self::TYPE_UINT,   'required' => true),
                'title'        => array('type' => self::TYPE_BINARY, 'required' => true, 'maxLength' => 75,
                    'verification' => array('$this', '_verifyTitle'),
                    'requiredError' => 'please_enter_valid_title'
                ),
                'phrase_text'  => array('type' => self::TYPE_STRING, 'default' => '', 'noTrim' => true),
                'addon_id'     => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => '')
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
        if (!$phrase_id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__phrase' => $this->_getPhraseModel()->getPhraseById($phrase_id));
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'phrase_id = ' . $this->_db->quote($this->getExisting('phrase_id'));
    }

    /**
     * Verifies that the phrase title ID is valid.
     *
     * @param string $title
     *
     * @return boolean
     */
    protected function _verifyTitle(&$title)
    {
        if (preg_match('/[^a-zA-Z0-9_]/', $title))
        {
            $this->error(__('please_enter_title_using_only_alphanumeric'), 'title');
            return false;
        }

        return true;
    }
}