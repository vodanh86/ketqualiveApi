<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/9/15
 * @Time: 11:15 AM
 */
class Document_DataWriter_Post extends Mava_DataWriter
{
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            '#__document_post' => array(
                'post_id'       => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'title'         => array('type' => self::TYPE_STRING, 'maxLength' => 250, 'required' => true),
                'lead'          => array('type' => self::TYPE_STRING, 'maxLength' => 500),
                'content'       => array('type' => self::TYPE_STRING, 'required' => true),
                'created_by'    => array('type' => self::TYPE_UINT),
                'created_date'  => array('type' => self::TYPE_UINT),
                'updated_date'  => array('type' => self::TYPE_UINT, 'default' => 0),
                'updated_by'    => array('type' => self::TYPE_UINT, 'default' => 0),
                'category_id'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'cover_image'   => array('type' => self::TYPE_STRING, 'default' => ''),
                'view_count'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'download_count'   => array('type' => self::TYPE_UINT, 'default' => 0),
                'deleted'       => array('type' => self::TYPE_UINT, 'default' => 0)
            )
        );
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _getExistingData($data)
    {
        if (!$post_id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('#__document_post' => $this->_getDocumentPostModel()->getPostById($post_id));
    }

    /**
     * @return Document_Model_Post
     * @throws Mava_Exception
     */
    protected function _getDocumentPostModel(){
        return $this->getModelFromCache('Document_Model_Post');
    }

    /**
     * @param $tableName
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'post_id = ' . $this->_db->quote($this->getExisting('post_id'));
    }
}