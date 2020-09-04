<?php

class Manager_Model_ManagerActivity extends Mava_Model
{
	public function getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__manager_activity_logs WHERE `id`=" . (int)$id);
    }

    public function getActivities($skip, $limit){
        $items = $this->_getDb()->fetchAll("
            SELECT *
            FROM #__manager_activity_logs
            ORDER BY id DESC
            LIMIT
            ". $skip .",". $limit ."
        ");
        $count = $this->_getDb()->fetchRow("
            SELECT COUNT(*) AS 'total'
            FROM #__manager_activity_logs
        ");
        return array(
            'rows' => $items,
            'total' => $count['total']
        );
    }

    public function saveActivityLog($data){
        $activityLogDW = $this->_getManagerActivityDataWriter();
        $activityLogDW->bulkSet($data);
        if($activityLogDW->save()){
            return $activityLogDW->get('id');
        }
        return false;
    }

    public function deleteActivityLog($id){
        return $this->_getDb()->delete('#__manager_activity_logs','id='. "'".(int)$id."'");
    }

    protected function _getManagerActivityDataWriter()
    {
        return Mava_DataWriter::create('Manager_DataWriter_ManagerActivity');
    }
}