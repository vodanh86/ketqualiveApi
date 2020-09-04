<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:39 AM
 */
class API_DataWriter_Feedback extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__feedback' => array(
                'id'            => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'token'        => array('type' => self::TYPE_STRING),
                'title'        => array('type' => self::TYPE_STRING),
                'content'        => array('type' => self::TYPE_STRING),
                'platform_type'        => array('type' => self::TYPE_STRING),
                'platform_version'        => array('type' => self::TYPE_STRING),
                'closed'        => array('type' => self::TYPE_UINT, 'default' => 0),
                'created_at'    => array('type' => self::TYPE_UINT, 'default' => time())
            )
        );
    }

    /**
     * @return API_Model_Feedback
     */
    protected function _getFeedbackModel(){
        return $this->getModelFromCache('API_Model_Feedback');
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

        return array('#__feedback' => $this->_getFeedbackModel()->getById($id));
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