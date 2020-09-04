<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 4/9/15
 * @Time: 4:57 PM
 */
class Mava_Model_EmailQueue extends Mava_Model {
    protected $_db = null;
    const TYPE_ACTIVE = 'active'; // kích hoạt tài khoản
    const TYPE_NEW_ORDER = 'new_order'; // có đơn hàng mới
    const TYPE_ORDER_CONFIRM = 'order_confirm'; // gửi thông tin đơn hàng cho khách
    const TYPE_SYSTEM_ERROR = 'system_error'; // có lỗi hệ thống
    const TYPE_NEW_SUBSCRIBE = 'new_subscribe'; // đăng ký nhận tin
    const TYPE_NEW_RECALL = 'new_recall'; // yêu cầu gọi lại
    const TYPE_FORGOT = 'forgot'; // lấy lại mật khẩu
    const TYPE_NEW_ASK = 'new_ask'; // thêm câu hỏi mới

    public function __construct()
    {
        if (!$this->_db) {
            $this->_db = Mava_Application::get('db');
        }
    }

    public function getEmailQueueList($limit = 10){
        return $this->_getDb()->fetchAll("SELECT * FROM #__queue_email ORDER BY `queue_id` ASC LIMIT 0,". (int)$limit);
    }

    public function getEmailQueueById($id){
        if($id > 0){
            $db = $this->_getDb();
            $queue = $db->getSingleTableRow('#__queue_email','*',array(array('queue_id','=',$id)),0,1);
            if($queue->num_rows > 0){
                return $queue->row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function deleteDuplicateEmailQueue($type, $email){
        if($type != "" && $email != ""){
            $this->_getDb()->delete('#__queue_email',"`type`='". addslashes($type) ."' AND `email`='". addslashes($email) ."'");
            return true;
        }else{
            return false;
        }
    }

    public function getSingleEmailQueue($type, $email){
        if($type != "" && $email != ""){
            $queue = $this->_getDb()->getSingleTableRow('#__queue_email', '*', array(
                array('type', '=', $type),
                array('email', '=', $email)
            ));
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