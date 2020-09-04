<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 1:17 PM
 */
class Index_Controller_Subscribe extends Mava_Controller {
    public function indexAction(){
        $email = Mava_Url::getParam('email');
        if(Mava_String::isEmail($email)){
            $subscribeModel = $this->_getSubscribeModel();
            $subscribeDW = $this->_getSubscribeDataWriter();
            if($subscribe = $subscribeModel->getByEmail($email)){
                if($subscribe['status'] == $subscribeDW::STATUS_UNSUBSCRIBE){
                    $subscribeDW->setExistingData($subscribe['id']);
                    $subscribeDW->set('status', $subscribeDW::STATUS_SUBSCRIBE);
                    $subscribeDW->save();
                }
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('subscribe_success')
                ));
            }
            $subscribeDW->bulkSet(array(
                'email' => $email,
                'created_time' => time(),
                'created_ip' => ip(),
                'status' => $subscribeDW::STATUS_SUBSCRIBE
            ));
            if($subscribeDW->save()){
                $email_id = 0;
                $email_token = '';
                $email_notify = Mava_Application::getOptions()->emailReceiveOrderNotify;
                if($email_notify != ''){
                    $body = __('new_subscribe_request_from_x', array('email' => $email));
                    $body .= '<h2>'. __('information') .'</h2>
                                <ul>
                                <li>'. __('email') .': '. $email .'</li>
                                <li>'. __('send_time') .': '. date('d/m/Y H:i', time()) .'</li>
                                <li>'. __('created_ip') .': '. ip() .'</li>
                                </ul>';
                    $body .= '<p><a href="'. Mava_Url::getPageLink('admin/subscribes/index') .'" style="display: inline-block;padding: 7px 15px;color: #FFF;background: #080;font-size: 13px;font-weight: bold;text-decoration: none;margin: 10px 0;">'. __('view_all_subscribe') .'</a></p>';
                    $body .= '<span style="color: #FFF;display: none;">'. microtime() .'</span>';
                    $emailQueueDw = $this->_getEmailQueueDataWriter();
                    $emailQueueDw->bulkSet(array(
                        'type' => Mava_Model_EmailQueue::TYPE_NEW_SUBSCRIBE,
                        'email' => $email_notify,
                        'content' => json_encode(array(
                            'title' => __('email_new_subscribe_request', array('date' => date('d/m/Y',time()))),
                            'body' => $body,
                        )),
                        'created_date' => time()
                    ));
                    $emailQueueDw->save();
                    $email_id = $emailQueueDw->get('queue_id');
                    $email_token = Mava_String::createToken($email_id);
                }
                return $this->responseJson(array(
                    'status' => 1,
                    'message' => __('subscribe_success'),
                    'email_id' => $email_id,
                    'email_token' => $email_token
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_subscribe')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('email_invalid')
            ));
        }
    }

    /**
     * @return Index_Model_Subscribe
     */
    protected function _getSubscribeModel(){
        return $this->getModelFromCache('Index_Model_Subscribe');
    }

    /**
     * @return Index_DataWriter_Subscribe
     * @throws Mava_Exception
     */
    protected function _getSubscribeDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_Subscribe');
    }

    /**
     * @return Mava_DataWriter_EmailQueue
     * @throws Mava_Exception
     */
    protected function _getEmailQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_EmailQueue');
    }
}