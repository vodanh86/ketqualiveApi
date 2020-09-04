<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 7/30/16
 * @Time: 12:04 PM
 */
class Index_Controller_Recall extends Mava_Controller {
    public function indexAction(){
        $phone = Mava_Url::getParam('phone');
        $title = Mava_Url::getParam('title');
        $url = Mava_Url::getParam('url');
        $type = Mava_Url::getParam('type');
        if(Mava_String::isPhoneNumber($phone)){
            $recallModel = $this->_getRecallModel();
            $recallDW = $this->_getRecallDataWriter();
            if($recall = $recallModel->getLatestByPhone($phone)){
                if($type == $recallDW::RECALL_TYPE_RECALL){
                    if($recall['created_time']+86400 > time() && $recall['phone'] == $phone && $recall['status'] == Index_DataWriter_Recall::STATUS_NEW){
                        return $this->responseJson(array(
                            'status' => 1,
                            'message' => __('recall_sent_before', array('phone' => $recall['phone']))
                        ));
                    }
                }else if($type == $recallDW::RECALL_TYPE_QUICK_ORDER){
                    if($recall['created_time']+86400 > time() && $recall['url'] == $url && $recall['phone'] == $phone && $recall['status'] == Index_DataWriter_Recall::STATUS_NEW){
                        return $this->responseJson(array(
                            'status' => 1,
                            'message' => __('recall_sent_before', array('phone' => $recall['phone']))
                        ));
                    }
                }
            }
            $recallDW->bulkSet(array(
                'phone' => $phone,
                'title' => $title,
                'url' => $url,
                'type' => (in_array($type,array($recallDW::RECALL_TYPE_RECALL,$recallDW::RECALL_TYPE_QUICK_ORDER))?$type:$recallDW::RECALL_TYPE_RECALL),
                'created_time' => time(),
                'created_ip' => ip(),
                'status' => $recallDW::STATUS_NEW
            ));
            if($recallDW->save()){
                $email_id = 0;
                $email_token = '';
                $email_notify = Mava_Application::getOptions()->emailReceiveOrderNotify;
                if($email_notify != ''){
                    if($type == $recallDW::RECALL_TYPE_QUICK_ORDER){
                        $body = __('new_quick_order_request_from_x', array('phone' => $phone));
                    }else{
                        $body = __('new_recall_request_from_x', array('phone' => $phone));
                    }

                    $body .= '<h2>'. __('information') .'</h2>
                                <ul>
                                <li>'. __('phone') .': '. $phone .'</li>
                                <li>'. __('website_title') .': '. $title .'</li>
                                <li>'. __('website_url') .': '. $url .'</li>
                                <li>'. __('send_time') .': '. date('d/m/Y H:i', time()) .'</li>
                                <li>'. __('created_ip') .': '. ip() .'</li>
                                </ul>';
                    if($type == $recallDW::RECALL_TYPE_QUICK_ORDER){
                        $body .= '<p><a href="'. Mava_Url::getPageLink('admin/callback/index', array('type' => $recallDW::RECALL_TYPE_QUICK_ORDER)) .'" style="display: inline-block;padding: 7px 15px;color: #FFF;background: #080;font-size: 13px;font-weight: bold;text-decoration: none;margin: 10px 0;">'. __('view_all_quick_order_request') .'</a></p>';
                    }else{
                        $body .= '<p><a href="'. Mava_Url::getPageLink('admin/callback/index', array('type' => $recallDW::RECALL_TYPE_RECALL)) .'" style="display: inline-block;padding: 7px 15px;color: #FFF;background: #080;font-size: 13px;font-weight: bold;text-decoration: none;margin: 10px 0;">'. __('view_all_callback_request') .'</a></p>';
                    }
                    $body .= '<span style="color: #FFF;display: none;">'. microtime() .'</span>';
                    $emailQueueDw = $this->_getEmailQueueDataWriter();
                    $emailQueueDw->bulkSet(array(
                        'type' => Mava_Model_EmailQueue::TYPE_NEW_RECALL,
                        'email' => $email_notify,
                        'content' => json_encode(array(
                            'title' => __(($type==$recallDW::RECALL_TYPE_QUICK_ORDER?'email_new_quick_order_request':'email_new_recall_request'), array('date' => date('d/m/Y',time()))),
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
                    'message' => __('recall_sent_before', array('phone' => $phone)),
                    'email_id' => $email_id,
                    'email_token' => $email_token
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_send_recall_request')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('phone_invalid')
            ));
        }
    }

    /**
     * @return Index_Model_Recall
     */
    protected function _getRecallModel(){
        return $this->getModelFromCache('Index_Model_Recall');
    }

    /**
     * @return Index_DataWriter_Recall
     * @throws Mava_Exception
     */
    protected function _getRecallDataWriter(){
        return Mava_DataWriter::create('Index_DataWriter_Recall');
    }

    /**
     * @return Mava_DataWriter_EmailQueue
     * @throws Mava_Exception
     */
    protected function _getEmailQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_EmailQueue');
    }
}