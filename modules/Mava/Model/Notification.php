<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/4/15
 * @Time: 10:26 AM
 */
class Mava_Model_Notification extends Mava_Model {

    const TYPE_CHECKOUT_CONFIRMED = 'checkout_confirmed'; // đã xác nhận nộp học phí
    const TYPE_NEW_ANSWER = 'new_answer'; // có câu trả lời cho câu hỏi của bạn
    const TYPE_LESSON_COMMING = 'lesson_comming'; // sắp đến giờ học
    const TYPE_FRIEND_JOINED = 'friend_joined'; // bạn bè đã đăng ký thành viên
    const TYPE_FRIEND_CHECKOUT = 'friend_checkout'; // bạn bè đã nộp học phí
    const TYPE_SYSTEM = 'system'; // thông báo từ hệ thống

    public function getUserNotify($userId, $skip  = 0, $limit = 50){
        if($userId > 0){
            $notify = $this->_getDb()->query('
                SELECT
                    *
                FROM
                    #__notification
                WHERE
                    `user_id` IN (0,'. (int)$userId .')
                ORDER BY
                    `notify_id` DESC
                LIMIT '. $skip .','. $limit
            );

            $count = $this->_getDb()->query('
                SELECT
                    count(*) as "total"
                FROM
                    #__notification
                WHERE
                    `user_id` IN (0,'. (int)$userId .')'
            );

            $unread = $this->_getDb()->query('
                SELECT
                    count(*) as "total"
                FROM
                    #__notification
                WHERE
                    `user_id` IN (0,'. (int)$userId .') AND
                    `read` = 0'
            );

            return array(
                'rows' => $notify->rows,
                'total' => $count->row['total'],
                'unread' => $unread->row['total']
            );
        }else{
            return array(
                'rows' => array(),
                'total' => 0,
                'unread' => 0
            );
        }
    }

    public function getUnreadNotify($userId = 0){
        if($userId ==0){
            $userId = (int)Mava_Visitor::getUserId();
        }
        if($userId > 0){
            $count = $this->_getDb()->query("
                SELECT
                    count(*) as 'total'
                FROM
                    #__notification
                WHERE
                    `user_id`='". (int)$userId ."' AND
                    `read` = 0
            ");
            return $count->row['total'];
        }else{
            return 0;
        }
    }

    public function maskAllAsRead($userId){
        if($userId > 0){
            return $this->_getDb()->update('#__notification', array(
                'read' => 1
            ), "`user_id`=". (int)$userId);
        }else{
            return false;
        }
    }
    /**
     * @param int | array $userId
     * @param string $type
     * @param string $content
     * @param string $href
     * @return bool
     * @throws Exception
     * @throws Mava_Exception
     */
    public function add($userId, $type, $content, $href = ''){
        if(is_array($userId)){
            foreach($userId as $item){
                if($item > 0){
                    $notifyDW = $this->_getNotificationDataWriter();
                    $notifyDW->bulkSet(array(
                        'type' => $type,
                        'href' => $href,
                        'content' => $content,
                        'user_id' => (int)$item,
                        'created_date' => time(),
                        'read' => 0
                    ));
                    $notifyDW->save();
                }
            }
            return true;
        }else if((int)$userId > 0){
            $notifyDW = $this->_getNotificationDataWriter();
            $notifyDW->bulkSet(array(
                'type' => $type,
                'href' => $href,
                'content' => $content,
                'user_id' => (int)$userId,
                'created_date' => time(),
                'read' => 0
            ));
            if($notifyDW->save()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @param int $notifyId
     * @return bool | array
     */
    public function getNotifyById($notifyId){
        if($notifyId > 0){
            return $this->_getDb()->getSingleTableRow('#__notification', array(
                array('notify_id','=',$notifyId)
            ));
        }else{
            return false;
        }
    }

    /**
     * @return Mava_DataWriter_Notification
     * @throws Mava_Exception
     */
    protected function _getNotificationDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_Notification');
    }
}