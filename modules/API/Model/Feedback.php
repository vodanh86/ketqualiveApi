<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Huy HOA
 * Date: 4/12/2019
 * Time: 11:45 AM
 */
class API_Model_Feedback extends Mava_Model
{
    /**
     * @param int $id
     * @return bool | array
     */
    public function getById($id){
        return $this->_getDb()->fetchRow("SELECT * FROM #__feedback WHERE `id`=" . (int)$id);
    }

    public function getTotalFeedbackByToken($token) {
        $count = $this->_getDb()->query("SELECT count(*) as total, FROM_UNIXTIME(created_at, '%d-%m-%Y') as group_date  FROM #__feedback WHERE `token`='". $token ."' AND FROM_UNIXTIME(created_at, '%d-%m-%Y') ='". date('d-m-Y') ."'  GROUP BY group_date");
        if(count($count->row) > 0) {
            return $count->row['total'];
        }
        return 0;
    }

    public function saveFeedback($data){
        $feedbackDW = $this->_getFeedbackDataWriter();
        $feedbackDW->bulkSet($data);
        if($feedbackDW->save()) {
            return true;
        }
        return false;
    }

    /**
     * @return API_DataWriter_Feedback
     */
    protected function _getFeedbackDataWriter()
    {
        return Mava_DataWriter::create('API_DataWriter_Feedback');
    }
}