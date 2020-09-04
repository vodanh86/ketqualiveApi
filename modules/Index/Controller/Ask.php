<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 6/23/16
 * @Time: 8:48 AM
 */
class Index_Controller_Ask extends Mava_Controller {
    public function indexAction(){
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = 20;
        $skip = ($page-1)*$limit;
        $askModel = $this->_getAskModel();
        $questions = $askModel->getList($skip, $limit, 'answered');
        $viewParams = array(
            'questions' => $questions['items'],
            'total' => $questions['total'],
            'skip' => $skip,
            'limit' => $limit,
            'page' => $page
        );
        return $this->responseView('Index_View_Ask_Index', $viewParams);
    }

    public function addAction(){
        $fullname = Mava_Url::getParam('fullname');
        $phone = Mava_Url::getParam('phone');
        $email = Mava_Url::getParam('email');
        $question = Mava_Url::getParam('question');
        if($question != ""){
            $askModel = $this->_getAskModel();
            $askDW = $this->_getAskDataWriter();
            $askDW->bulkSet(array(
                'name' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'question' => $question,
                'answer' => '',
                'answer_by' => 0,
                'sort_order' => 0,
                'status' => $askDW::STATUS_NEW
            ));
            if($askDW->save()){
                $email_id = 0;
                $email_token = '';
                $email_notify = Mava_Application::getOptions()->emailReceiveOrderNotify;
                if($email_notify != ''){
                    $body = __('email_body_new_ask', array(
                        'microtime' => microtime(),
                        'phone' => $phone,
                        'fullname' => $fullname,
                        'email' => $email,
                        'question' => $question,
                        'view_all_link' => Mava_Url::getPageLink('admin/qa/index'),
                    ));
                    $emailQueueDw = $this->_getEmailQueueDataWriter();
                    $emailQueueDw->bulkSet(array(
                        'type' => Mava_Model_EmailQueue::TYPE_NEW_ASK,
                        'email' => $email_notify,
                        'content' => json_encode(array(
                            'title' => __('email_new_ask', array('date' => date('d/m/Y',time()))),
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
                    'message' => __('ask_sent'),
                    'email_id' => $email_id,
                    'email_token' => $email_token
                ));
            }else{
                return $this->responseJson(array(
                    'status' => -1,
                    'message' => __('can_not_ask')
                ));
            }
        }else{
            return $this->responseJson(array(
                'status' => -1,
                'message' => __('question_content_empty')
            ));
        }
    }

    /**
     * @return Mava_DataWriter_EmailQueue
     */
    protected function _getEmailQueueDataWriter(){
        return $this->getModelFromCache('Mava_DataWriter_EmailQueue');
    }
    /**
     * @return Index_DataWriter_Ask
     */
    protected function _getAskDataWriter(){
        return $this->getModelFromCache('Index_DataWriter_Ask');
    }

    /**
     * @return Index_Model_Ask
     */
    protected function _getAskModel(){
        return $this->getModelFromCache('Index_Model_Ask');
    }
}