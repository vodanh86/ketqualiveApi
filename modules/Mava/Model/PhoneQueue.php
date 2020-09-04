<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/11/15
 * @Time: 10:27 AM
 */
class Mava_Model_PhoneQueue extends Mava_Model {
    protected $_db = null;
    const TYPE_ACTIVE = 'active'; // sms kích hoạt tài khoản
    const TYPE_PROMOTE = 'promote'; // sms quảng cáo
    const TYPE_NEWS = 'news'; // sms thông báo chung
    const TYPE_COURSE = 'course'; // sms nhắc nhở, thông tin liên quan đến khóa học của học viên
    const TYPE_ACCOUNT = 'account'; // sms các thông tin liên quan đến tài khoản
    const TYPE_FORGOT = 'forgot'; // sms lấy lại mật khẩu

    public function __construct()
    {
        if (!$this->_db) {
            $this->_db = Mava_Application::get('db');
        }
    }

    public function getPhoneQueueById($id){
        if($id > 0){
            $db = $this->_getDb();
            $queue = $db->getSingleTableRow('#__queue_phone','*',array(array('queue_id','=',$id)),0,1);
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