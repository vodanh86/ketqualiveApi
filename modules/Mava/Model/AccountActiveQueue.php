<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/11/15
 * @Time: 10:38 AM
 */
class Mava_Model_AccountActiveQueue extends Mava_Model {
    protected $_db = null;

    public function __construct()
    {
        if (!$this->_db) {
            $this->_db = Mava_Application::get('db');
        }
    }

    public function getActiveQueueById($id){
        if($id > 0){
            $db = $this->_getDb();
            $queue = $db->getSingleTableRow('#__user_active_queue','*',array(array('queue_id','=',$id)),0,1);
            if($queue->num_rows > 0){
                return $queue->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}